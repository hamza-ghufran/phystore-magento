<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Plazathemes\Override\Controller\Adminhtml\Category;

class Save extends \Magento\Catalog\Controller\Adminhtml\Category\Save
{
	private $storeManager;
	
    /**
     * Filter category data
     *
     * @param array $rawData
     * @return array
     */
    protected function _filterCategoryPostData(array $rawData)
    {
        $data = $rawData;
        // @todo It is a workaround to prevent saving this data in category model and it has to be refactored in future
        if (isset($data['image']) && is_array($data['image'])) {
            if (!empty($data['image']['delete'])) {
                $data['image'] = null;
            } else {
                if (isset($data['image'][0]['name']) && isset($data['image'][0]['tmp_name'])) {
                    $data['image'] = $data['image'][0]['name'];
                } else {
                    unset($data['image']);
                }
            }
        }
		
		if (isset($data['thumb_nail']) && is_array($data['thumb_nail'])) {
            if (!empty($data['thumb_nail']['delete'])) {
                $data['thumb_nail'] = null;
            } else {
                if (isset($data['thumb_nail'][0]['name']) && isset($data['thumb_nail'][0]['tmp_name'])) {
                    $data['thumb_nail'] = $data['thumb_nail'][0]['name'];
                } else {
                    unset($data['thumb_nail']);
                }
            }
        }
		
		if (isset($data['cate_tab']) && is_array($data['cate_tab'])) {
            if (!empty($data['cate_tab']['delete'])) {
                $data['cate_tab'] = null;
            } else {
                if (isset($data['cate_tab'][0]['name']) && isset($data['cate_tab'][0]['tmp_name'])) {
                    $data['cate_tab'] = $data['cate_tab'][0]['name'];
                } else {
                    unset($data['cate_tab']);
                }
            }
        }
		
		if (isset($data['thumb_popular']) && is_array($data['thumb_popular'])) {
            if (!empty($data['thumb_popular']['delete'])) {
                $data['thumb_popular'] = null;
            } else {
                if (isset($data['thumb_popular'][0]['name']) && isset($data['thumb_popular'][0]['tmp_name'])) {
                    $data['thumb_popular'] = $data['thumb_popular'][0]['name'];
                } else {
                    unset($data['thumb_popular']);
                }
            }
        }
		
		if (isset($data['thumb_nail_hover']) && is_array($data['thumb_nail_hover'])) {
            if (!empty($data['thumb_nail_hover']['delete'])) {
                $data['thumb_nail_hover'] = null;
            } else {
                if (isset($data['thumb_nail_hover'][0]['name']) && isset($data['thumb_nail_hover'][0]['tmp_name'])) {
                    $data['thumb_nail_hover'] = $data['thumb_nail_hover'][0]['name'];
                } else {
                    unset($data['thumb_nail_hover']);
                }
            }
        }
		
        return $data;
    }

    /**
     * Image data preprocessing
     *
     * @param array $data
     *
     * @return array
     */
    public function imagePreprocessing($data)
    {
        if (empty($data['image'])) {
            unset($data['image']);
            $data['image']['delete'] = true;
        }
		
		if (empty($data['thumb_nail'])) {
            unset($data['thumb_nail']);
            $data['thumb_nail']['delete'] = true;
        }
		
		if (empty($data['cate_tab'])) {
            unset($data['cate_tab']);
            $data['cate_tab']['delete'] = true;
        }
		
		if (empty($data['thumb_nail_hover'])) {
            unset($data['thumb_nail_hover']);
            $data['thumb_nail_hover']['delete'] = true;
        }
        return $data;
    }
}
