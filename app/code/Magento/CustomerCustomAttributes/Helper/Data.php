<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerCustomAttributes\Helper;

/**
 * Enterprise Customer Data Helper
 *
 * @category   Magento
 * @package    Magento_CustomerCustomAttributes
 */
class Data extends \Magento\CustomAttribute\Helper\Data
{
    /**
     * Customer customer
     *
     * @var Customer
     */
    protected $_customerCustomer = null;

    /**
     * Customer address
     *
     * @var Address
     */
    protected $_customerAddress = null;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\LocaleInterface $locale
     * @param \Magento\Filter\FilterManager $filterManager
     * @param Address $customerAddress
     * @param Customer $customerCustomer
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\LocaleInterface $locale,
        \Magento\Filter\FilterManager $filterManager,
        Address $customerAddress,
        Customer $customerCustomer
    ) {
        $this->_customerAddress = $customerAddress;
        $this->_customerCustomer = $customerCustomer;
        parent::__construct($context, $eavConfig, $locale, $filterManager);
    }

    /**
     * Return available customer attribute form as select options
     *
     * @return void
     * @throws \Magento\Core\Exception
     */
    public function getAttributeFormOptions()
    {
        throw new \Magento\Core\Exception(__('Use helper with defined EAV entity.'));
    }

    /**
     * Default attribute entity type code
     *
     * @return void
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
