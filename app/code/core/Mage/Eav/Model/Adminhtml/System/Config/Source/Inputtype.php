<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Eav_Model_Adminhtml_System_Config_Source_Inputtype
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'text', 'label' => Mage::helper('Mage_Eav_Helper_Data')->__('Text Field')),
            array('value' => 'textarea', 'label' => Mage::helper('Mage_Eav_Helper_Data')->__('Text Area')),
            array('value' => 'date', 'label' => Mage::helper('Mage_Eav_Helper_Data')->__('Date')),
            array('value' => 'boolean', 'label' => Mage::helper('Mage_Eav_Helper_Data')->__('Yes/No')),
            array('value' => 'multiselect', 'label' => Mage::helper('Mage_Eav_Helper_Data')->__('Multiple Select')),
            array('value' => 'select', 'label' => Mage::helper('Mage_Eav_Helper_Data')->__('Dropdown'))
        );
    }
}
