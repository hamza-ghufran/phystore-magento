<?php
/**
 * Copyright © 2016 PlazaThemes.com. All rights reserved.
 *
 * @author PlazaThemes Team <contact@plazathemes.com>
 */

namespace Plazathemes\Pricecountdown\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;

class Pricecountdown extends \Magento\Catalog\Block\Product\AbstractProduct
{
    const XML_PATH_SALE_SLIDER          = "pricecountdown/countdown_slider/";
    const XML_PATH_PRODUCT_COUNTDOWN    = "pricecountdown/in_product_view/";

    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;


    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $_httpContext;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    protected $_productFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->_scopeConfig = $scopeConfig;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_httpContext = $httpContext;
        $this->_productFactory = $productFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get current product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getCurrentProduct() {
        return $this->_registry->registry('current_product');
    }

    /**
     * Get Sale products
     *
     * @return \Magento\Catalog\Model\ProductFactory
     */
    public function getSaleProducts() {
        $todayDate = date('m/d/y');
        $tomorrow = mktime(0, 0, 0, date('m'), date('d'), date('y'));
        $tomorrowDate = date('m/d/y', $tomorrow);

        $quantity = (int) $this->getSliderOptions()['quantity'];

        $storeId = 1;
        $customerGroupId = 1;
        $websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId();

        $productModel = $this->_productFactory->create();
        $productModel->setStoreId($storeId);

        $collection = $productModel->getResourceCollection()
            ->addPriceData($customerGroupId, $websiteId)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('special_price', array('gt' => 0))
            ->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $todayDate))
            ->addAttributeToFilter('special_to_date', array('or'=> array(
                0 => array('date' => true, 'from' => $tomorrowDate),
                1 => array('is' => new \Zend_Db_Expr('null')))
            ), 'left');
        $collection->setPageSize($quantity);

        return $collection;
    }

    /**
     * Get Countdown in product view Configuration
     *
     * @param $attr
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public function getProductViewCountdownConfig($attr) {
        $config =  $this->_scopeConfig->getValue(self::XML_PATH_PRODUCT_COUNTDOWN . $attr, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $config;
    }

    /**
     * Get Countdown in product view Options
     *
     * @return array
     */
    public function getProductViewCountdownOptions() {
        $view_countbox_options = array();

        if($this->getProductViewCountdownConfig('enabled') == 1) {
            $view_countbox_options['enabled'] = true;
        } else {
            $view_countbox_options['enabled'] = false;
        }

        if($this->getProductViewCountdownConfig('insertion')) {
            $view_countbox_options['insertion'] = $this->getProductViewCountdownConfig('insertion');
        } else {
            $view_countbox_options['insertion'] = 'after';
        }

        if($this->getProductViewCountdownConfig('parent_element')) {
            $view_countbox_options['parent_element'] = $this->getProductViewCountdownConfig('parent_element');
        } else {
            $view_countbox_options['parent_element'] = '.product-info-main';
        }

        if($this->getProductViewCountdownConfig('children_element')) {
            $view_countbox_options['children_element'] = $this->getProductViewCountdownConfig('children_element');
        } else {
            $view_countbox_options['children_element'] = '.product-info-price';
        }

        return $view_countbox_options;
    }

    /**
     * Get Countdown slider Configuration
     *
     * @param $attr
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public function getSliderConfig($attr) {
        $config =  $this->_scopeConfig->getValue(self::XML_PATH_SALE_SLIDER . $attr, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $config;
    }

    /**
     * Get Countdown slider Options
     *
     * @return array
     */
    public function getSliderOptions() {
        $slider_options = array();

        if($this->getSliderConfig('enabled') == 1) {
            $slider_options['enabled'] = true;
        } else {
            $slider_options['enabled'] = false;
        }

        if($this->getSliderConfig('use_countdown') == 1) {
            $slider_options['use_countdown'] = true;
        } else {
            $slider_options['use_countdown'] = false;
        }

        $slider_options['title'] = $this->getSliderConfig('title');

        if($this->getSliderConfig('show_price') == 1) {
            $slider_options['show_price'] = true;
        } else {
            $slider_options['show_price'] = false;
        }

        if($this->getSliderConfig('show_add_cart') == 1) {
            $slider_options['show_add_cart'] = true;
        } else {
            $slider_options['show_add_cart'] = false;
        }

        if($this->getSliderConfig('show_add_wishlist') == 1) {
            $slider_options['show_add_wishlist'] = true;
        } else {
            $slider_options['show_add_wishlist'] = false;
        }

        if($this->getSliderConfig('show_add_compare') == 1) {
            $slider_options['show_add_compare'] = true;
        } else {
            $slider_options['show_add_compare'] = false;
        }

        if($this->getSliderConfig('show_rating') == 1) {
            $slider_options['show_rating'] = true;
        } else {
            $slider_options['show_rating'] = false;
        }

        if($this->getSliderConfig('show_short_description') == 1) {
            $slider_options['show_short_description'] = true;
        } else {
            $slider_options['show_short_description'] = false;
        }

        $slider_options['short_description_length'] = $this->getSliderConfig('short_description_length');

        if($this->getSliderConfig('quantity')) {
            $slider_options['quantity'] = $this->getSliderConfig('quantity');
        } else {
            $slider_options['quantity'] = '10';
        }

        if($this->getSliderConfig('auto') == 1) {
            $slider_options['auto'] = true;
        } else {
            $slider_options['auto'] = false;
        }

        if($this->getSliderConfig('speed')) {
            $slider_options['speed'] = $this->getSliderConfig('speed');
        } else {
            $slider_options['speed'] = '3000';
        }

        if($this->getSliderConfig('item_default')) {
            $slider_options['item_default'] = $this->getSliderConfig('item_default');
        } else {
            $slider_options['item_default'] = '4';
        }

        if($this->getSliderConfig('item_desktop')) {
            $slider_options['item_desktop'] = $this->getSliderConfig('item_desktop');
        } else {
            $slider_options['item_desktop'] = '4';
        }

        if($this->getSliderConfig('item_desktop_small')) {
            $slider_options['item_desktop_small'] = $this->getSliderConfig('item_desktop_small');
        } else {
            $slider_options['item_desktop_small'] = '3';
        }

        if($this->getSliderConfig('item_tablet')) {
            $slider_options['item_tablet'] = $this->getSliderConfig('item_tablet');
        } else {
            $slider_options['item_tablet'] = '2';
        }

        if($this->getSliderConfig('item_mobile')) {
            $slider_options['item_mobile'] = $this->getSliderConfig('item_mobile');
        } else {
            $slider_options['item_mobile'] = '1';
        }

        if($this->getSliderConfig('row_number')) {
            $slider_options['row_number'] = (int) $this->getSliderConfig('row_number');
        } else {
            $slider_options['row_number'] = 1;
        }

        if($this->getSliderConfig('show_navigation') == 1) {
            $slider_options['show_navigation'] = true;
        } else {
            $slider_options['show_navigation'] = false;
        }

        if($this->getSliderConfig('show_pagination') == 1) {
            $slider_options['show_pagination'] = true;
        } else {
            $slider_options['show_pagination'] = false;
        }

        return $slider_options;
    }

    /**
     * Get short description
     *
     * @param $string
     * @return string
     */
    public function getShortString($string) {
        $limit = (int) $this->getSliderOptions()['short_description_length'];

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
