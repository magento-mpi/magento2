<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_Model_System_Config_Source_Tax_Class_Customer
{
    public function toOptionArray()
    {
        $taxClasses = Mage::getModel('Mage_Tax_Model_Class_Source_Customer')->toOptionArray();
        array_unshift($taxClasses, array('value' => '0', 'label' => Mage::helper('Mage_Tax_Helper_Data')->__('None')));
        return $taxClasses;
    }
}
