<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model of customer address types
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Config_Source_Address_Type implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Retrieve possible customer address types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Mage_Customer_Model_Address_Abstract::TYPE_BILLING => Mage::helper('Mage_Customer_Helper_Data')->__('Billing Address'),
            Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING => Mage::helper('Mage_Customer_Helper_Data')->__('Shipping Address')
        );
    }
}
