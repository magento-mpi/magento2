<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\TargetRule\Model\Source;

class Rotation implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get data for Rotation mode selector
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            \Magento\TargetRule\Model\Rule::ROTATION_NONE => __('Do not rotate'),
            \Magento\TargetRule\Model\Rule::ROTATION_SHUFFLE => __('Shuffle')
        ];
    }
}
