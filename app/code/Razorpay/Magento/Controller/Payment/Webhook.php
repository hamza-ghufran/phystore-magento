<?php 

namespace Razorpay\Magento\Controller\Payment;

use Razorpay\Api\Api;
use Razorpay\Api\Errors;
use Razorpay\Magento\Model\Config;
use Razorpay\Magento\Model\PaymentMethod;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;

class Webhook extends \Razorpay\Magento\Controller\BaseController implements CsrfAwareActionInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    protected $order;

    protected $api;

    protected $logger;

    protected $quoteManagement;

    protected $objectManagement;

    protected $storeManager;

    protected $customerRepository;

    const STATUS_APPROVED = 'APPROVED';

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Razorpay\Model\CheckoutFactory $checkoutFactory
     * @param \Razorpay\Magento\Model\Config $config
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository,
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Razorpay\Magento\Model\CheckoutFactory $checkoutFactory,
        \Razorpay\Magento\Model\Config $config,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
        \Magento\Store\Model\StoreManagerInterface $storeManagement,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Psr\Log\LoggerInterface $logger
    ) 
    {
        parent::__construct(
            $context,
            $customerSession,
            $checkoutSession,
            $config
        );

        $keyId                 = $this->config->getConfigData(Config::KEY_PUBLIC_KEY);
        $keySecret             = $this->config->getConfigData(Config::KEY_PRIVATE_KEY);

        $this->api             = new Api($keyId, $keySecret);
        $this->order           = $order;
        $this->logger          = $logger;

        $this->objectManagement   = \Magento\Framework\App\ObjectManager::getInstance();
        $this->quoteManagement    = $quoteManagement;
        $this->checkoutFactory    = $checkoutFactory;
        $this->catalogSession     = $catalogSession;
        $this->quoteRepository    = $quoteRepository;
        $this->storeManagement    = $storeManagement;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * Processes the incoming webhook
     */
    public function execute()
    {       
        $post = $this->getPostData(); 

        if (json_last_error() !== 0)
        {
            return;
        }

        $this->logger->warning("Razorpay Webhook processing started.");
       
        if (($this->config->isWebhookEnabled() === true) && 
            (empty($post['event']) === false))
        { 
            if (isset($_SERVER['HTTP_X_RAZORPAY_SIGNATURE']) === true)
            {
                $webhookSecret = $this->config->getWebhookSecret();

                //
                // To accept webhooks, the merchant must configure 
                // it on the magento backend by setting the secret
                // 
                if (empty($webhookSecret) === true)
                {
                    return;
                }

                try
                { 
                    $this->rzp->utility->verifyWebhookSignature(json_encode($post), $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'], $webhookSecret);
                }
                catch (Errors\SignatureVerificationError $e)
                {
                    $this->logger->warning(
                        $e->getMessage(), 
                        [
                            'data'  => $post,
                            'event' => 'razorpay.wc.signature.verify_failed'
                        ]);

                    //Set the validation error in response
                    header('Status: 400 Signature Verification failed', true, 400);    
                    exit;
                }

                switch ($post['event'])
                {
                    case 'payment.authorized':
                        return;

                    case 'order.paid':
                        return $this->orderPaid($post);    

                    default:
                        return;
                }
            }
        }

        $this->logger->warning("Razorpay Webhook processing completed.");
    }

    /**
     * Order Paid webhook
     * 
     * @param array $post
     */
    protected function orderPaid(array $post)
    {
        $quoteId   = $post['payload']['payment']['entity']['notes']['merchant_quote_id'];
        $amount    = number_format($post['payload']['payment']['entity']['amount']/100, 2, ".", "");
        $paymentId = $post['payload']['payment']['entity']['id'];

        $this->logger->warning("Razorpay Webhook processing started for Razorpay payment_id(:$paymentId)");

        //validate if the quote Order is still active
        $quote = $this->quoteRepository->get($quoteId);

        //exit if quote is not active
        if(!$quote->getIsActive())
        {
            $this->logger->warning("Razorpay Webhook: Quote order is inactive for quoteID: $quoteId and Razorpay payment_id(:$paymentId)");
                return;
        }

        //validate amount before placing order
        $quoteAmount = (int) (round($quote->getGrandTotal(), 2) * 100);

        if($quoteAmount !== $post['payload']['payment']['entity']['amount'])
        {
            $this->logger->warning("Razorpay Webhook: Amount paid doesn't match with store order amount for Razorpay payment_id(:$paymentId)");
                return;
        }

        # fetch the related sales order and verify the payment ID with rzp payment id
        # To avoid duplicate order entry for same quote 
        $collection = $this->_objectManager->get('Magento\Sales\Model\Order')
                                           ->getCollection()
                                           ->addFieldToSelect('entity_id')
                                           ->addFilter('quote_id', $quoteId)
                                           ->getFirstItem();
        
        $salesOrder = $collection->getData();
        
        if(empty($salesOrder['entity_id']) === false)
        {
            $order = $this->order->load($salesOrder['entity_id']);
            $orderRzpPaymentId = $order->getPayment()->getLastTransId();
            if($orderRzpPaymentId === $paymentId)
            {
                $this->logger->warning("Razorpay Webhook: Sales Order and payment already exist for Razorpay payment_id(:$paymentId)");
                return;
            }
        }


        $quote = $this->getQuoteObject($post, $quoteId);

        $order = $this->quoteManagement->submit($quote);

        $payment = $order->getPayment();        
        
        $payment->setAmountPaid($amount)
                ->setLastTransId($paymentId)
                ->setTransactionId($paymentId)
                ->setIsTransactionClosed(true)
                ->setShouldCloseParentTransaction(true);
        $order->save();
        
        $this->logger->warning("Razorpay Webhook Processed successfully for Razorpay payment_id(:$paymentId)");
    }

    protected function getQuoteObject($post, $quoteId)
    {
        $email = $post['payload']['payment']['entity']['email'];

        $quote = $this->quoteRepository->get($quoteId);


        $firstName = $quote->getBillingAddress()['customer_firstname'] ?? 'null';
        $lastName = $quote->getBillingAddress()['customer_lastname'] ?? 'null';

        $quote->getPayment()->setMethod(PaymentMethod::METHOD_CODE);

        $store = $this->storeManagement->getStore();

        $websiteId = $store->getWebsiteId();

        $customer = $this->objectManagement->create('Magento\Customer\Model\Customer');
        
        $customer->setWebsiteId($websiteId);

        //get customer from quote , otherwise from payment email
        if(empty($quote->getBillingAddress()['email']) === false)
        {
            $customer = $customer->loadByEmail($quote->getBillingAddress()['email']);
        }
        
        //if quote billing address doesn't contains address, set it as customer default billing address
        if(empty($quote->getBillingAddress()['customer_firstname']) === true)
        {   
            $quote->getBillingAddress()->setCustomerAddressId($customer->getDefaultBillingAddress()['id']);
        }

        //If need to insert new customer 
        if (empty($customer->getEntityId()) === true)
        {
            $customer->setWebsiteId($websiteId)
                     ->setStore($store)
                     ->setFirstname($firstName)
                     ->setLastname($lastName)
                     ->setEmail($email);

            $customer->save();
        }

        $customer = $this->customerRepository->getById($customer->getEntityId());

        $quote->assignCustomer($customer);

        $quote->setStore($store);

        $quote->save();

        return $quote;
    }

    /**
     * @return Webhook post data as an array
     */
    protected function getPostData() : array
    {
        $request = file_get_contents('php://input');

        return json_decode($request, true);
    }
}