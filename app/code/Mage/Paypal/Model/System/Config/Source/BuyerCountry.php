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
 * Source model for buyer countries supported by PayPal
 */
class Mage_Paypal_Model_System_Config_Source_BuyerCountry
{
    protected $_items;

    public function toOptionArray($isMultiselect = false)
    {
        if (is_null($this->_items)) {
            $supported = Mage::getModel('Mage_Paypal_Model_Config')->getSupportedBuyerCountryCodes();
            $this->_items = $options = Mage::getResourceModel('Mage_Directory_Model_Resource_Country_Collection')
                ->addCountryCodeFilter($supported, 'iso2')
                ->loadData()
                ->toOptionArray($isMultiselect ? false : Mage::helper('Mage_Adminhtml_Helper_Data')->__('--Please Select--'));
        }
        return $this->_items;
    }
}
