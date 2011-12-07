<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_GoogleCheckout_Model_Source_Shipping_Units
{
    public function toOptionArray()
    {
        $hlp = Mage::helper('Mage_GoogleCheckout_Helper_Data');
        return array(
            array('value' => 'IN', 'label' => $hlp->__('Inches')),
        );
    }
}
