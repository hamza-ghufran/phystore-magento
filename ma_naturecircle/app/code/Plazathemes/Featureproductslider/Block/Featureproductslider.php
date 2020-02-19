<?php 
namespace Plazathemes\Featureproductslider\Block;
use Magento\Catalog\Model\Resource\Product\Collection;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Customer\Model\Context;
class Featureproductslider extends \Magento\Catalog\Block\Product\AbstractProduct 
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

		   $config =  $this->_scopeConfig->getValue('featureproductslider/new_status/'.$value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
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

		public function getFeaturedProduct() {
			 $storeId = $this->_storeManager->getStore()->getId();
			 $customerGroupId = $this->getCustomerGroupId();
			 $websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId();

			/** @var $product \Magento\Catalog\Model\Product */
			$product = $this->productFactory->create();
			$product->setStoreId($storeId);

			$collection = $product->getResourceCollection()
				->addPriceData($customerGroupId, $websiteId)
				->addAttributeToSelect('*'
				)
				->addAttributeToFilter('featured',1)
				->addAttributeToSort('name', 'asc');
				 $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());	
				 $qty = $this->getConfig('qty');
				 if($qty<1) $qty = 8;
				 $collection ->setPageSize($qty); 	
			return $collection;
			
		}
			


}