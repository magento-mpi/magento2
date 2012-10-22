<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model of customer address types
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Source_Customer_Address_Type
{
    /**
     * Retrieve possible customer address types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Mage_Customer_Model_Address_Abstract::TYPE_BILLING => Mage::helper('Mage_Backend_Helper_Data')->__('Billing Address'),
            Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING => Mage::helper('Mage_Backend_Helper_Data')->__('Shipping Address')
        );
    }
}
