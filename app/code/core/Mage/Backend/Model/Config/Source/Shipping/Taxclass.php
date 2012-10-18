<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Config_Source_Shipping_Taxclass
{
    public function toOptionArray()
    {
        $options = Mage::getModel('Mage_Tax_Model_Class_Source_Product')->toOptionArray();
        //array_unshift($options, array('value'=>'', 'label' => Mage::helper('Mage_Tax_Helper_Data')->__('None')));
        return $options;
    }

}
