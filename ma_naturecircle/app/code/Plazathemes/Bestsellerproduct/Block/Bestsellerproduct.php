<?php 
namespace Plazathemes\Bestsellerproduct\Block;
use Magento\Catalog\Model\Resource\Product\Collection;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Customer\Model\Context;
use Magento\Framework\App\ResourceConnection;
class Bestsellerproduct extends \Magento\Catalog\Block\Product\AbstractProduct 
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
	 protected $_collectionFactory;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\Resource\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
	
	protected $productFactory;
	protected $connection;
	protected $resource;
	protected $_categoryFactory;
	
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
				\Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $collectionFactory,
				\Magento\Catalog\Model\CategoryFactory $categoryFactory,
				 ResourceConnection $resource,
			array $data = []
		) {
			$this->_productCollectionFactory = $productCollectionFactory;
			$this->_catalogProductVisibility = $catalogProductVisibility;
			$this->httpContext = $httpContext;
			$this->productFactory = $productFactory;
			 $this->_collectionFactory = $collectionFactory;
			 $this->resource = $resource;
			$this->connection = $resource->getConnection();
			$this->_categoryFactory = $categoryFactory;
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

		   $config =  $this->_scopeConfig->getValue('bestsellerproduct/new_status/'.$value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
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

		
		 public function getBestsellerProduct()
			{
				$id = $this->_storeManager->getStore()->getRootCategoryId();
				$_category =  $this->_categoryFactory->create()->load($id);
				$children_category = explode(",", $_category->getChildren());
				$id = $children_category[0];
				
				$qty = $this->getConfig('qty');
			    $select = $this->connection->select()
					 ->from($this->resource->getTableName('sales_order_item'), 'product_id')
					 ->order('sum(`qty_ordered`) Desc')
					 ->group('product_id')
					 ->limit($qty);
				   $producIds = array(); 
				   foreach ($this->connection->query($select)->fetchAll() as $row) {
					   $producIds[] = $row['product_id'];
				   }
				  
				$products = array();
				$i = 1;
				foreach($producIds as $product_id) {
					$product = $this->productFactory->create()->load($product_id);
						if($product->getVisibility() == 2||$product->getVisibility() == 3||$product->getVisibility() == 4)
						// $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
						// $logger = new \Zend\Log\Logger();
						// $logger->addWriter($writer);
						// $logger->info($product);
						if(in_array($id, $product->getCategoryIds()))
						{
							$products[] = $product;	
							if($i > $qty)
								return $products;
							$i++;
						}
					
				}
			if(count($products)>=1)
				return $products;
				return array();
			
			}   
			

}