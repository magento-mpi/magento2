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
namespace Magento\CustomerCustomAttributes\Helper;

class Data extends \Magento\CustomAttribute\Helper\Data
{
    /**
     * Customer customer
     *
     * @var \Magento\CustomerCustomAttributes\Helper\Customer
     */
    protected $_customerCustomer = null;

    /**
     * Customer address
     *
     * @var \Magento\CustomerCustomAttributes\Helper\Address
     */
    protected $_customerAddress = null;

    /**
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\CustomerCustomAttributes\Helper\Address $customerAddress
     * @param \Magento\CustomerCustomAttributes\Helper\Customer $customerCustomer
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Core\Helper\Context $context
     */
    public function __construct(
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\CustomerCustomAttributes\Helper\Address $customerAddress,
        \Magento\CustomerCustomAttributes\Helper\Customer $customerCustomer,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Core\Helper\Context $context
    ) {
        $this->_customerAddress = $customerAddress;
        $this->_customerCustomer = $customerCustomer;
        parent::__construct($eavConfig, $locale, $context);
    }

    /**
     * Return available customer attribute form as select options
     *
     * @throws \Magento\Core\Exception
     */
    public function getAttributeFormOptions()
    {
        throw new \Magento\Core\Exception(__('Use helper with defined EAV entity.'));
    }

    /**
     * Default attribute entity type code
     *
     * @throws \Magento\Core\Exception
     */
    protected function _getEntityTypeCode()
    {
        throw new \Magento\Core\Exception(__('Use helper with defined EAV entity.'));
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
