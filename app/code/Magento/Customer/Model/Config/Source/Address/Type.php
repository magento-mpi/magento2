<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model of customer address types
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Model_Config_Source_Address_Type implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Retrieve possible customer address types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            Magento_Customer_Model_Address_Abstract::TYPE_BILLING => __('Billing Address'),
            Magento_Customer_Model_Address_Abstract::TYPE_SHIPPING => __('Shipping Address')
        );
    }
}
