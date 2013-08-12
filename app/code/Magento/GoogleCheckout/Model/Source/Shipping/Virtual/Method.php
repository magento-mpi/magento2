<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_GoogleCheckout_Model_Source_Shipping_Virtual_Method
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'email', 'label' => Mage::helper('Magento_GoogleCheckout_Helper_Data')->__('Email delivery')),
            // array('value'=>'key_url', 'label'=> Mage::helper('Magento_GoogleCheckout_Helper_Data')->__('Key/URL delivery')),
            // array('value'=>'description_based', 'label'=> Mage::helper('Magento_GoogleCheckout_Helper_Data')->__('Description-based delivery'))
        );
    }
}
