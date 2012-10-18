<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Model_System_Config_Source_Catalog_TimeFormat
{
    public function toOptionArray()
    {
        return array(
            array('value' => '12h', 'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('12h AM/PM')),
            array('value' => '24h', 'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('24h')),
        );
    }
}
