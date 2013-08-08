<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Checkout_Model_Config_Source_Cart_Summary implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('Mage_Checkout_Helper_Data')->__('Display number of items in cart')),
            array('value'=>1, 'label'=>Mage::helper('Mage_Checkout_Helper_Data')->__('Display item quantities')),
        );
    }
}
