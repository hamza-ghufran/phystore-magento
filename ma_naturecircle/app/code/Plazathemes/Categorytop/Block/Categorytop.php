<?php 
namespace Plazathemes\Categorytop\Block;
use Magento\Catalog\Model\Resource\Product\Collection;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Catalog\Model\Category;
class Categorytop extends \Magento\Catalog\Block\Product\AbstractProduct 
{
 /**
     * Default value for products count that will be shown
     */
    const DEFAULT_PRODUCTS_COUNT = 10;

    /**
     * Name of request parameter for page number value
     */
    const PAGE_VAR_NAME = 'np';

    /**
     * Default value for products per page
     */
    const DEFAULT_PRODUCTS_PER_PAGE = 5;

    /**
     * Default value whether show pager or not
     */
    const DEFAULT_SHOW_PAGER = false;

    /**
     * Instance of pager block
     *
     * @var \Magento\Catalog\Block\Product\Widget\Html\Pager
     */
    protected $pager;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $catalogProductVisibility;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\Resource\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Rule\Model\Condition\Sql\Builder
     */
    protected $sqlBuilder;

    /**
     * @var \Magento\CatalogWidget\Model\Rule
     */
    protected $rule;

    /**
     * @var \Magento\Widget\Helper\Conditions
     */
    protected $conditionsHelper;
	
	protected $_categoryFactory;
	protected $productFactory;
	
	protected $_catalogCategory;
    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder
     * @param \Magento\CatalogWidget\Model\Rule $rule
     * @param \Magento\Widget\Helper\Conditions $conditionsHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Catalog\Helper\Category $catalogCategory,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->httpContext = $httpContext;
        $this->sqlBuilder = $sqlBuilder;
        $this->rule = $rule;
        $this->conditionsHelper = $conditionsHelper;
		$this->_categoryFactory = $categoryFactory;
		$this->productFactory = $productFactory;
		 $this->_catalogCategory = $catalogCategory;
        parent::__construct(
            $context,
            $data
        );
        $this->_isScopePrivate = true;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {  
        parent::_construct();
        $this->addColumnCountLayoutDepend('empty', 6)
            ->addColumnCountLayoutDepend('1column', 5)
            ->addColumnCountLayoutDepend('2columns-left', 4)
            ->addColumnCountLayoutDepend('2columns-right', 4)
            ->addColumnCountLayoutDepend('3columns', 3);

        $this->addData([
            'cache_lifetime' => 86400,
            'cache_tags' => [\Magento\Catalog\Model\Product::CACHE_TAG,
        ], ]);
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
  
        return [
            'CATEGORY_Top_LIST_QTY',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
            intval($this->getRequest()->getParam(self::PAGE_VAR_NAME, 1)),
            $this->getProductsPerPage()
  
        ];
    }

  
	
	protected function _getDefaultStoreId(){
        return \Magento\Store\Model\Store::DEFAULT_STORE_ID;
    }
	
	 public function getCatRootId()
    {
        return  (int) $this->_storeManager->getStore()->getRootCategoryId();
    }
	function getThumbUrl($thumb=null) {
		
		return   $this->_storeManager->getStore()->getBaseUrl(
					\Magento\Framework\UrlInterface::URL_TYPE_MEDIA
				) . 'catalog/category/' . $thumb;
	}
	
    public function getCatListTop()
    {
		$store = $this->_storeManager->getStore();
		$qty = $this->getConfig('items_show');
        $collection = $this->_categoryFactory->create()
                            ->getCollection()
                            // ->addRootLevelFilter()
                            ->addAttributeToSelect('entity_id')
                            ->addAttributeToSelect('name')
                            ->addAttributeToSelect('thumb_popular')
                            ->addAttributeToSelect('url_path')
                            // ->addFieldToFilter('parent_id', array('eq'=>$this->getCatRootId()))
                            ->addFieldToFilter('cate_popular', array('eq'=>'1'))
                            ->addFieldToFilter('is_active', array('eq'=>'1'))
							->setPageSize($qty);
							
							
        return $collection;
    }
	
	public function getCategoryLink($category) {
		$link =  $this->_catalogCategory->getCategoryUrl($category);	
		return $link ;
	}

    public function getCatByPath($parentId, $path)
    {
	    if(!$parentId) return ; 		
        return $this->_categoryFactory->create()->load($parentId)->getChildrenCategories();
    }
	
	public function getCategoryLevel2() {

			$collection = $this->_categoryInstance->getCollection()
							   -> addAttributeToFilter('level',2)
							   -> addAttributeToFilter('is_active',1);
			return $collection ; 
	}


	public function getCategory($id) {
		return 	$_category =  $this->_categoryFactory->create()->load($id);
	}
	
	public function getConfig($value=''){

	   $config =  $this->_scopeConfig->getValue('categorytop/new_status/'.$value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	   return $config; 
	 
	}
		public function getListCfg($cfg)
	{
        $config = array(5,6,7);
	}



   
    public function getIdentities()
    {
        return [\Magento\Catalog\Model\Product::CACHE_TAG];
    }

    /**
     * Get value of widgets' title parameter
     *
     * @return mixed|string
     */
    public function getTitle()
    {
        return $this->getData('title');
    }
	 public function getCategoryIds()
    {
        return $this->getData('category_id');
    }


}