<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Wishlist_Model_Config_Source_Summary implements Mage_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('Mage_Checkout_Helper_Data')->__('Display number of items in wish list')),
            array('value'=>1, 'label'=>Mage::helper('Mage_Checkout_Helper_Data')->__('Display item quantities')),
        );
    }
}
