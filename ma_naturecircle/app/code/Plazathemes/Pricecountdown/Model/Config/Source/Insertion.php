<?php
/**
 * Copyright © 2016 PlazaThemes.com. All rights reserved.
 *
 * @author PlazaThemes Team <contact@plazathemes.com>
 */

/**
 * Used in creating options for Before|After config value selection
 *
 */
namespace Plazathemes\Pricecountdown\Model\Config\Source;

class Insertion implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'before', 'label' => __('Before')], ['value' => 'after', 'label' => __('After')]];
    }

}