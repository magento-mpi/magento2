<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for buyer countries supported by PayPal
 */
class Magento_Paypal_Model_System_Config_Source_BuyerCountry implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Paypal_Model_ConfigFactory
     */
    protected $_configFactory;

    /**
     * @var Magento_Directory_Model_Resource_Country_CollectionFactory
     */
    protected $_countryCollFactory;

    /**
     * @param Magento_Paypal_Model_ConfigFactory $configFactory
     * @param Magento_Directory_Model_Resource_Country_CollectionFactory $countryCollFactory
     */
    public function __construct(
        Magento_Paypal_Model_ConfigFactory $configFactory,
        Magento_Directory_Model_Resource_Country_CollectionFactory $countryCollFactory
    ) {
        $this->_configFactory = $configFactory;
        $this->_countryCollFactory = $countryCollFactory;
    }

    /**
     * @param bool $isMultiselect
     * @return array
     */
    public function toOptionArray($isMultiselect = false)
    {
        $supported = $this->_configFactory->create()->getSupportedBuyerCountryCodes();
        $options = $this->_countryCollFactory->create()->addCountryCodeFilter($supported, 'iso2')
            ->loadData()
            ->toOptionArray($isMultiselect ? false : __('--Please Select--'));

        return $options;
    }
}
