<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Catalog_Model_Config_Source_Price_Step implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Magento_Catalog_Model_Layer_Filter_Price::RANGE_CALCULATION_AUTO,
                'label' => __('Automatic (equalize price ranges)')
            ),
            array(
                'value' => Magento_Catalog_Model_Layer_Filter_Price::RANGE_CALCULATION_IMPROVED,
                'label' => __('Automatic (equalize product counts)')
            ),
            array(
                'value' => Magento_Catalog_Model_Layer_Filter_Price::RANGE_CALCULATION_MANUAL,
                'label' => __('Manual')
            ),
        );
    }
}
