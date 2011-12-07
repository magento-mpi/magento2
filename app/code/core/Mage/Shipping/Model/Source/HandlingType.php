<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Shipping_Model_Source_HandlingType
{
    public function toOptionArray()
    {
        return array(
            array('value' => Mage_Shipping_Model_Carrier_Abstract::HANDLING_TYPE_FIXED, 'label' => Mage::helper('Mage_Shipping_Helper_Data')->__('Fixed')),
            array('value' => Mage_Shipping_Model_Carrier_Abstract::HANDLING_TYPE_PERCENT, 'label' => Mage::helper('Mage_Shipping_Helper_Data')->__('Percent')),
        );
    }
}
