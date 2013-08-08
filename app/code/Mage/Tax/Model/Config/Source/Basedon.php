<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Tax_Model_Config_Source_Basedon implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'shipping', 'label'=>Mage::helper('Mage_Tax_Helper_Data')->__('Shipping Address')),
            array('value'=>'billing', 'label'=>Mage::helper('Mage_Tax_Helper_Data')->__('Billing Address')),
            array('value'=>'origin', 'label'=>Mage::helper('Mage_Tax_Helper_Data')->__("Shipping Origin")),
        );
    }

}
