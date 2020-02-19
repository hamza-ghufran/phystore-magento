<?php
/**
* Copyright © 2015 PlazaThemes.com. All rights reserved.

* @author PlazaThemes Team <contact@plazathemes.com>
*/

namespace Plazathemes\Recentproductslider\Block;

class Products extends \Magento\Framework\View\Element\Template {

	protected $_scopeConfig;


	/**
	 * [__construct description]
	 * @param \Magento\Framework\View\Element\Template\Context                $context                 [description]
	 * @param \Magento\Framework\Registry                                     $coreRegistry            [description]
	 * @param array                                                           $data                    [description]
	 */
	public function __construct(
		\Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
		\Magento\Catalog\Block\Product\ListProduct $block,
        array $data = []
	) {
		$this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->httpContext = $httpContext;
        $this->block = $block;
        parent::__construct(
            $context,
            $data
        );
	}
	
	public function getBlockProduct()
	{
		return $this->block;
	}
	
	public function getProducts(){
		$fieldorder = 'entity_id';
		$order = 'desc';
		$collection = $this->_productCollectionFactory->create();
		$collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
		$collection->addMinimalPrice()
			->addFinalPrice()
			->addTaxPercents()
			->setPageSize($this->getConfig('qty'))
			->addAttributeToSelect('*')
			->setOrder ($fieldorder,$order);

        return $collection;
   }
   
	public function getConfig($config)
	{
		return $this->_scopeConfig->getValue('recentproductslider/general/'.$config, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Add elements in layout
	 *
	 * @return
	 */
	protected function _prepareLayout() {
		return parent::_prepareLayout();
	}
}
