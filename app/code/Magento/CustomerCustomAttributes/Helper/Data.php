<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Enterprise Customer Data Helper
 *
 * @category   Magento
 * @package    Magento_CustomerCustomAttributes
 */
class Magento_CustomerCustomAttributes_Helper_Data extends Magento_CustomAttribute_Helper_Data
{
    /**
     * Customer customer
     *
     * @var Magento_CustomerCustomAttributes_Helper_Customer
     */
    protected $_customerCustomer = null;

    /**
     * Customer address
     *
     * @var Magento_CustomerCustomAttributes_Helper_Address
     */
    protected $_customerAddress = null;

    /**
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_CustomerCustomAttributes_Helper_Address $customerAddress
     * @param Magento_CustomerCustomAttributes_Helper_Customer $customerCustomer
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Core_Helper_Context $context
     */
    public function __construct(
        Magento_Core_Model_LocaleInterface $locale,
        Magento_CustomerCustomAttributes_Helper_Address $customerAddress,
        Magento_CustomerCustomAttributes_Helper_Customer $customerCustomer,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Core_Helper_Context $context
    ) {
        $this->_customerAddress = $customerAddress;
        $this->_customerCustomer = $customerCustomer;
        parent::__construct($eavConfig, $locale, $context);
    }

    /**
     * Return available customer attribute form as select options
     *
     * @throws Magento_Core_Exception
     */
    public function getAttributeFormOptions()
    {
        throw new Magento_Core_Exception(__('Use helper with defined EAV entity.'));
    }

    /**
     * Default attribute entity type code
     *
     * @throws Magento_Core_Exception
     */
    protected function _getEntityTypeCode()
    {
        throw new Magento_Core_Exception(__('Use helper with defined EAV entity.'));
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
