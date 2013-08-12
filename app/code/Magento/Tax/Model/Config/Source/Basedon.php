<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Tax_Model_Config_Source_Basedon implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'shipping', 'label'=>Mage::helper('Magento_Tax_Helper_Data')->__('Shipping Address')),
            array('value'=>'billing', 'label'=>Mage::helper('Magento_Tax_Helper_Data')->__('Billing Address')),
            array('value'=>'origin', 'label'=>Mage::helper('Magento_Tax_Helper_Data')->__("Shipping Origin")),
        );
    }

}
