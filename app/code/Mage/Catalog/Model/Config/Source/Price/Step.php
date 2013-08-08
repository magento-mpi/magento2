<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Model_Config_Source_Price_Step implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Catalog_Model_Layer_Filter_Price::RANGE_CALCULATION_AUTO,
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Automatic (equalize price ranges)')
            ),
            array(
                'value' => Mage_Catalog_Model_Layer_Filter_Price::RANGE_CALCULATION_IMPROVED,
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Automatic (equalize product counts)')
            ),
            array(
                'value' => Mage_Catalog_Model_Layer_Filter_Price::RANGE_CALCULATION_MANUAL,
                'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Manual')
            ),
        );
    }
}
