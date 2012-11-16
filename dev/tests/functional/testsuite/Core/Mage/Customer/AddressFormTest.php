<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Customer
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

class Core_Mage_Customer_AddressFormTest extends Mage_Selenium_TestCase
{
    /**
     * Verify that Region field corresponds selected Country
     *
     * @test
     * @TestlinkId TL-MAGE-6446
     */
    public function verifyRegionFieldInAddressForm()
    {
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $accordionXpath = $this->_getControlXpath('link', 'countries_options_link');
        $this->systemConfigurationHelper()->changeConfigurationScope('current_configuration_scope', 'Main Website');
        if (!$this->isElementPresent($accordionXpath . "[@class='open']")) {
            $this->clickControl('link', 'countries_options_link', false);
        }
        $xpathUseDefault = $this->_getControlXpath('checkbox', 'default_country_use_default');
        if ($this->isElementPresent($xpathUseDefault . "[@checked='checked']")) {
            $this->clickControl('checkbox', 'default_country_use_default', false);
        }
        $this->fillDropdown('default_country', 'Thailand');
        $this->clickButton('save_config');
        $this->navigate('manage_customers');
        $this->clickButton('add_new_customer');
        $this->fillDropdown('associate_to_website', 'Main Website');
        $this->openTab('addresses');
        $this->clickButton('add_new_address', false);
        $this->pleaseWait();
        $this->addParameter('address_number', '1');
        //Verification
        $this->assertTrue($this->controlIsPresent('field', 'state'), 'Input field state/province is missing');
    }
}
