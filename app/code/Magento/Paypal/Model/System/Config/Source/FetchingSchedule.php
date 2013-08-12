<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for available settlement report fetching intervals
 */
class Magento_Paypal_Model_System_Config_Source_FetchingSchedule
{
    public function toOptionArray()
    {
        return array (
            1 => Mage::helper('Magento_Paypal_Helper_Data')->__("Daily"),
            3 => Mage::helper('Magento_Paypal_Helper_Data')->__("Every 3 days"),
            7 => Mage::helper('Magento_Paypal_Helper_Data')->__("Every 7 days"),
            10 => Mage::helper('Magento_Paypal_Helper_Data')->__("Every 10 days"),
            14 => Mage::helper('Magento_Paypal_Helper_Data')->__("Every 14 days"),
            30 => Mage::helper('Magento_Paypal_Helper_Data')->__("Every 30 days"),
            40 => Mage::helper('Magento_Paypal_Helper_Data')->__("Every 40 days"),
        );
    }
}
