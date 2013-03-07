<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for available settlement report fetching intervals
 */
class Mage_Paypal_Model_System_Config_Source_FetchingSchedule
{
    public function toOptionArray()
    {
        return array (
            1 => Mage::helper('Mage_Paypal_Helper_Data')->__("Daily"),
            3 => Mage::helper('Mage_Paypal_Helper_Data')->__("Every 3 days"),
            7 => Mage::helper('Mage_Paypal_Helper_Data')->__("Every 7 days"),
            10 => Mage::helper('Mage_Paypal_Helper_Data')->__("Every 10 days"),
            14 => Mage::helper('Mage_Paypal_Helper_Data')->__("Every 14 days"),
            30 => Mage::helper('Mage_Paypal_Helper_Data')->__("Every 30 days"),
            40 => Mage::helper('Mage_Paypal_Helper_Data')->__("Every 40 days"),
        );
    }
}
