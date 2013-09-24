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
    public function toOptionArray($isMultiselect = false)
    {
        $supported = Mage::getModel('Magento_Paypal_Model_Config')->getSupportedBuyerCountryCodes();
        $options = Mage::getResourceModel('Magento_Directory_Model_Resource_Country_Collection')
            ->addCountryCodeFilter($supported, 'iso2')
            ->loadData()
            ->toOptionArray($isMultiselect ? false : __('--Please Select--'));

        return $options;
    }
}
