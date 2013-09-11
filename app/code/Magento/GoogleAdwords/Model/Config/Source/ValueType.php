<?php
/**
 * Google AdWords conversation value type source
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleAdwords\Model\Config\Source;

class ValueType implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Get conversation value type option
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => \Magento\GoogleAdwords\Helper\Data::CONVERSION_VALUE_TYPE_DYNAMIC,
                'label' => __('Dynamic'),
            ),
            array(
                'value' => \Magento\GoogleAdwords\Helper\Data::CONVERSION_VALUE_TYPE_CONSTANT,
                'label' => __('Constant'),
            ),
        );
    }
}
