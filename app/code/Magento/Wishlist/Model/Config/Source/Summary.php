<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Wishlist_Model_Config_Source_Summary implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('Mage_Checkout_Helper_Data')->__('Display number of items in wish list')),
            array('value'=>1, 'label'=>Mage::helper('Mage_Checkout_Helper_Data')->__('Display item quantities')),
        );
    }
}
