<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_GoogleCheckout_Model_Source_Shipping_Virtual_Method
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'email', 'label' => Mage::helper('googlecheckout')->__('Email delivery')),
            // array('value'=>'key_url', 'label'=> Mage::helper('googlecheckout')->__('Key/URL delivery')),
            // array('value'=>'description_based', 'label'=> Mage::helper('googlecheckout')->__('Description-based delivery'))
        );
    }
}