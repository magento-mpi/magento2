<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Mock object for order billing address model
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Address_Billing extends Mage_Sales_Model_Order_Address
{
    /**
     * Initialize order billing address with mock data
     */
    protected function _construct()
    {
        $this->setData($this->_getMockData());
    }

    /**
     * Returns data helper
     *
     * @return Saas_PrintedTemplate_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('Saas_PrintedTemplate_Helper_Data');
    }

    /**
     * Returns data for the order billing address
     *
     * @return array
     */
    protected function _getMockData()
    {
        return array (
            'parent_id' => '-1',
            'customer_address_id' => '4',
            'quote_address_id' => NULL,
            'region_id' => '57',
            'customer_id' => '4',
            'fax' => '',
            'region' => __('Texas'),
            'postcode' => __('33065'),
            'lastname' => __('Hill'),
            'street' => __('22 Sycamore Fork Road'),
            'city' => __('Coral Springs, FL'),
            'email' => __('RamonaKHill@teleworm.com'),
            'telephone' => __('+19542274713'),
            'country_id' => __('US'),
            'firstname' => __('Ramona'),
            'address_type' => 'billing',
            'prefix' => __('Mrs.'),
            'middlename' => __('J.'),
            'suffix' => __('K.'),
            'company' => __('Magento'),
            'address_id' => NULL,
            'tax_id' => NULL,
            'gift_message_id' => NULL,
        );
    }
}
