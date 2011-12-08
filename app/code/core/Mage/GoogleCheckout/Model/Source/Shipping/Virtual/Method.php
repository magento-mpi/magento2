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
        $hlp = Mage::helper('Mage_GoogleCheckout_Helper_Data');
        return array(
            array('value' => 'email', 'label' => $hlp->__('Email delivery')),
            // array('value'=>'key_url', 'label'=>$hlp->__('Key/URL delivery')),
            // array('value'=>'description_based', 'label'=>$hlp->__('Description-based delivery')),
        );
    }
}
