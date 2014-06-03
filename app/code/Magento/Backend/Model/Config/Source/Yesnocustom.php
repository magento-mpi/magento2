<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Used in creating options for Yes|No|Specified config value selection
 *
 */
namespace Magento\Backend\Model\Config\Source;

class Yesnocustom implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => __('Yes')),
            array('value' => 0, 'label' => __('No')),
            array('value' => 2, 'label' => __('Specified'))
        );
    }
}
