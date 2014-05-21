<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for logging frequency
 */
namespace Magento\Logging\Model\Source;

class Frequency implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get options as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => __('Daily')),
            array('value' => 7, 'label' => __('Weekly')),
            array('value' => 30, 'label' => __('Monthly'))
        );
    }
}
