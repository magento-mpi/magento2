<?php
/**
 * Google AdWords conversation value type source
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleAdwords_Model_Config_Source_ValueType implements Magento_Core_Model_Option_ArrayInterface
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
                'value' => Magento_GoogleAdwords_Helper_Data::CONVERSION_VALUE_TYPE_DYNAMIC,
                'label' => __('Dynamic'),
            ),
            array(
                'value' => Magento_GoogleAdwords_Helper_Data::CONVERSION_VALUE_TYPE_CONSTANT,
                'label' => __('Constant'),
            ),
        );
    }
}
