<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Core_Mage_Customer_AddressFormTest extends Mage_Selenium_TestCase
{
    /**
     *<p>Verify that Region field corresponds selected Country</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6446
     */
    public function verifyRegionFieldInAddressForm()
    {
        $countryOptions = $this->loadDataSet('General', 'general_default_country_options', array(
            'configuration_scope' => 'Main Website',
            'default_country_use_default' => 'No',
            'default_country' => 'Thailand'
        ));
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
        $this->systemConfigurationHelper()->configure($countryOptions);
        $this->navigate('manage_customers');
        $this->clickButton('add_new_customer');
        $this->fillDropdown('associate_to_website', 'Main Website');
        $this->openTab('addresses');
        $this->clickButton('add_new_address', false);
        $this->addParameter('address_number', '1');
        $this->waitForControlVisible('fieldset', 'edit_address');
        //Verification
        $this->assertTrue($this->controlIsVisible('field', 'region'), 'Input field state/province is missing');
    }
}