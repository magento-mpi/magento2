<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
        return array(
            \Magento\TargetRule\Model\Rule::ROTATION_NONE => __('Do not rotate'),
            \Magento\TargetRule\Model\Rule::ROTATION_SHUFFLE => __('Shuffle')
        );
    }
}
