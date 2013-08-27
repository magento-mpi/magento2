<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Enterprise Customer Data Helper
 *
 * @category   Enterprise
 * @package    Enterprise_Customer
 */
class Enterprise_Customer_Helper_Data extends Enterprise_Eav_Helper_Data
{
    /**
     * Return available customer attribute form as select options
     *
     * @throws Magento_Core_Exception
     */
    public function getAttributeFormOptions()
    {
        Mage::throwException(__('Use helper with defined EAV entity.'));
    }

    /**
     * Default attribute entity type code
     *
     * @throws Magento_Core_Exception
     */
    protected function _getEntityTypeCode()
    {
        Mage::throwException(__('Use helper with defined EAV entity.'));
    }

    /**
     * Return available customer attribute form as select options
     *
     * @return array
     */
    public function getCustomerAttributeFormOptions()
    {
        return Mage::helper('Enterprise_Customer_Helper_Customer')->getAttributeFormOptions();
    }

    /**
     * Return available customer address attribute form as select options
     *
     * @return array
     */
    public function getCustomerAddressAttributeFormOptions()
    {
        return Mage::helper('Enterprise_Customer_Helper_Address')->getAttributeFormOptions();
    }

    /**
     * Returns array of user defined attribute codes for customer entity type
     *
     * @return array
     */
    public function getCustomerUserDefinedAttributeCodes()
    {
        return Mage::helper('Enterprise_Customer_Helper_Customer')->getUserDefinedAttributeCodes();
    }

    /**
     * Returns array of user defined attribute codes for customer address entity type
     *
     * @return array
     */
    public function getCustomerAddressUserDefinedAttributeCodes()
    {
        return Mage::helper('Enterprise_Customer_Helper_Address')->getUserDefinedAttributeCodes();
    }
}
