<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Model_Config_Source_Price_Step implements Mage_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Catalog_Model_Layer_Filter_Price::RANGE_CALCULATION_AUTO,
                'label' => __('Automatic (equalize price ranges)')
            ),
            array(
                'value' => Mage_Catalog_Model_Layer_Filter_Price::RANGE_CALCULATION_IMPROVED,
                'label' => __('Automatic (equalize product counts)')
            ),
            array(
                'value' => Mage_Catalog_Model_Layer_Filter_Price::RANGE_CALCULATION_MANUAL,
                'label' => __('Manual')
            ),
        );
    }
}
