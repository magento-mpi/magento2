<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Config\Source\Apply;

class On implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label' => __('Custom price if available')),
            array('value' => 1, 'label' => __('Original price only'))
        );
    }
}
