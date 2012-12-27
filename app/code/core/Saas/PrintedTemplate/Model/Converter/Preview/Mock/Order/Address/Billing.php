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
     * @var Saas_PrintedTemplate_Helper_Data
     */
    protected $_helper;

    /**
     * Initialize order billing address with mock data
     */
    protected function _construct()
    {
        $this->_helper = Mage::helper('Saas_PrintedTemplate_Helper_Data');
        $this->setData($this->_getMockData());
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
            'region' => $this->_helper->__('Texas'),
            'postcode' => $this->_helper->__('33065'),
            'lastname' => $this->_helper->__('Hill'),
            'street' => $this->_helper->__('22 Sycamore Fork Road'),
            'city' => $this->_helper->__('Coral Springs, FL'),
            'email' => $this->_helper->__('RamonaKHill@teleworm.com'),
            'telephone' => $this->_helper->__('+19542274713'),
            'country_id' => $this->_helper->__('US'),
            'firstname' => $this->_helper->__('Ramona'),
            'address_type' => 'billing',
            'prefix' => $this->_helper->__('Mrs.'),
            'middlename' => $this->_helper->__('J.'),
            'suffix' => $this->_helper->__('K.'),
            'company' => $this->_helper->__('Magento'),
            'address_id' => NULL,
            'tax_id' => NULL,
            'gift_message_id' => NULL,
        );
    }
}
