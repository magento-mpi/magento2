<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Catalog_Model_Config_Source_TimeFormat implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => '12h', 'label' => Mage::helper('Magento_Catalog_Helper_Data')->__('12h AM/PM')),
            array('value' => '24h', 'label' => Mage::helper('Magento_Catalog_Helper_Data')->__('24h')),
        );
    }
}
