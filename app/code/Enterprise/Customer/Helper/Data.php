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
     * Customer customer
     *
     * @var Enterprise_Customer_Helper_Customer
     */
    protected $_customerCustomer = null;

    /**
     * Customer address
     *
     * @var Enterprise_Customer_Helper_Address
     */
    protected $_customerAddress = null;

    /**
     * @param Enterprise_Customer_Helper_Address $customerAddress
     * @param Enterprise_Customer_Helper_Customer $customerCustomer
     * @param Magento_Core_Helper_Context $context
     */
    public function __construct(
        Enterprise_Customer_Helper_Address $customerAddress,
        Enterprise_Customer_Helper_Customer $customerCustomer,
        Magento_Core_Helper_Context $context
    ) {
        $this->_customerAddress = $customerAddress;
        $this->_customerCustomer = $customerCustomer;
        parent::__construct($context);
    }

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
        return $this->_customerCustomer->getAttributeFormOptions();
    }

    /**
     * Return available customer address attribute form as select options
     *
     * @return array
     */
    public function getCustomerAddressAttributeFormOptions()
    {
        return $this->_customerAddress->getAttributeFormOptions();
    }

    /**
     * Returns array of user defined attribute codes for customer entity type
     *
     * @return array
     */
    public function getCustomerUserDefinedAttributeCodes()
    {
        return $this->_customerCustomer->getUserDefinedAttributeCodes();
    }

    /**
     * Returns array of user defined attribute codes for customer address entity type
     *
     * @return array
     */
    public function getCustomerAddressUserDefinedAttributeCodes()
    {
        return $this->_customerAddress->getUserDefinedAttributeCodes();
    }
}
