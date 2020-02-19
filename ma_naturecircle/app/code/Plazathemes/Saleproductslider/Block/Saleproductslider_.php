<?php 
namespace Plazathemes\Saleproductslider\Block;
use Magento\Catalog\Model\Resource\Product\Collection;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Customer\Model\Context;
class Saleproductslider extends \Magento\Catalog\Block\Product\AbstractProduct 
{
		/**
     * Default value for products count that will be shown
     */
    const DEFAULT_PRODUCTS_COUNT = 10;

    /**
     * Products count
     *
     * @var int
     */
    protected $_productsCount;
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\Resource\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
	
	protected $productFactory;
    /**
     * @param Context $context
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
		public function __construct(
			\Magento\Catalog\Block\Product\Context $context,
			\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
			\Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
			\Magento\Framework\App\Http\Context $httpContext,
			  \Magento\Catalog\Model\ProductFactory $productFactory,
			array $data = []
		) {
			$this->_productCollectionFactory = $productCollectionFactory;
			$this->_catalogProductVisibility = $catalogProductVisibility;
			$this->httpContext = $httpContext;
			$this->productFactory = $productFactory;
			parent::__construct(
				$context,
				$data
			);
			$this->_isScopePrivate = true;
		}
		public function _prepareLayout()
		{ 

			return parent::_prepareLayout();
		}
		
		public function getConfig($value=''){

		   $config =  $this->_scopeConfig->getValue('saleproductslider/new_status/'.$value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		   return $config; 
		 
		}
			
		  protected function getCustomerGroupId()
		{
			$customerGroupId =   (int) $this->getRequest()->getParam('cid');
			if ($customerGroupId == null) {
				$customerGroupId = $this->httpContext->getValue(Context::CONTEXT_GROUP);
			}
			return $customerGroupId;
		}
		
		public function getSaleProduct() {
			
			 $storeId = $this->_storeManager->getStore()->getId(); 
			 $customerGroupId = $this->getCustomerGroupId();
			 $websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId();
			 // var_dump($customerGroupId);
			 // var_dump($websiteId);die;

			/** @var $product \Magento\Catalog\Model\Product */
			$product = $this->productFactory->create();
			$product->setStoreId($storeId);

			$collection = $product->getResourceCollection()
				->addPriceDataFieldFilter('%s < %s', ['final_price', 'price'])
				->addPriceData($customerGroupId, $websiteId)
				->addAttributeToSelect(
					[
						'name',
						'short_description',
						'description',
						'price',
						'thumbnail',
						'special_price',
						'special_to_date',
						'msrp_display_actual_price_type',
						'msrp',
						'small_image',
					],
					'left'
				)->addAttributeToSort('name', 'asc');
				
			 $qty = $this->getConfig('qty');	
			 if($qty<1) $qty = 8;
			 $collection ->setPageSize($qty); 	
			return $collection;
		}
	/**
     * Get short description
     *
     * @param $string
     * @return string
     */
    public function getShortString($string) {
        $limit = 40;

        if($limit) {
            if(strlen($string) <= $limit) {
                return $string;
            } else {
                if(strpos($string, " ", $limit) > $limit) {
                    $new_space = strpos($string, " ", $limit);
                    $new_string = substr($string, 0, $new_space)."..";
                    return $new_string;
                }

                $new_string = substr($string, 0, $limit)."..";
                return $new_string;
            }
        } else {
            return $string;
        }
    }

}