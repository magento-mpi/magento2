<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for merchant countries supported by PayPal
 */
class Mage_Paypal_Model_System_Config_Source_MerchantCountry
{
    public function toOptionArray($isMultiselect = false)
    {
        $supported = Mage::getModel('Mage_Paypal_Model_Config')->getSupportedMerchantCountryCodes();
        $options = Mage::getResourceModel('Mage_Directory_Model_Resource_Country_Collection')
            ->addCountryCodeFilter($supported, 'iso2')
            ->loadData()
            ->toOptionArray($isMultiselect ? false : __('--Please Select--'));

        return $options;
    }
}
