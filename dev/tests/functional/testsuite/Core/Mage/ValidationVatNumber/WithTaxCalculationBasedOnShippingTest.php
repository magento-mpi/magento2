<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ValidationVatNumber
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_ValidationVatNumber_WithTaxCalculationBasedOnShippingTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('VatID/store_information_data');
        $this->clickControl('button', 'validate_vat_number', false);
        $this->pleaseWait();
        //Verification
        if (!$this->controlIsVisible('pageelement', 'vat_number_is_valid')){
            $this->skipTestWithScreenshot('VAT Number is not valid');
        }
    }

    public function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('VatID/create_new_account_options_disable');
        $this->systemConfigurationHelper()->configure('ShippingSettings/store_information_empty');
    }

    /**
     * @test
     * @return array
     */
    public function preconditionsForTests()
    {
        //Data
        $names = array('Valid VAT Domestic_%randomize%', 'Valid VAT IntraUnion_%randomize%', 'Invalid VAT_%randomize%');
        $processedGroupNames = array();
        //Creating three Customer Groups
        $this->navigate('manage_customer_groups');
        foreach ($names as $groupName) {
            $customerGroup = $this->loadDataSet('CustomerGroup', 'new_customer_group',
                array('group_name' => $groupName));
            $this->customerGroupsHelper()->createCustomerGroup($customerGroup);
            //Verifying
            $this->assertMessagePresent('success', 'success_saved_customer_group');
            $processedGroupNames[] = $customerGroup['group_name'];
        }
        //Configuring "Create New Account Options" tab
        $this->navigate('system_configuration');
        $accountOptions = $this->loadDataSet('VatID', 'create_new_account_options', array(
            'group_valid_vat_domestic' => $processedGroupNames[0],
            'group_valid_vat_intraunion' => $processedGroupNames[1],
            'group_invalid_vat' => $processedGroupNames[2],
            'tax_calculated_based_on' => 'Shipping Address'
        ));
        $this->systemConfigurationHelper()->configure($accountOptions);
        $this->reindexInvalidedData();
        $this->flushCache();
        return $processedGroupNames;
    }

    /**
     * <p>Backend customer registration. With "Tax Calculation Based On" - Shipping Address</p>
     *
     * @param array $customerData
     * @test
     *
     * @dataProvider withShippingTaxCalculationSettingTestDataProvider
     * @TestlinkId TL-MAGE-6225
     */
    public function withShippingTaxCalculationSettingTest($customerData)
    {
        //Data
        $userRegisterData = $this->loadDataSet('Customers', 'generic_customer_account');
        $addressData = $this->loadDataSet('Customers', 'generic_address', $customerData);
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userRegisterData, $addressData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userRegisterData['email']));
        $this->openTab('account_information');
        $this->verifyForm(array('group' => 'General'), 'account_information');
        $this->assertEmptyVerificationErrors();
    }

    public function withShippingTaxCalculationSettingTestDataProvider()
    {
        return array(
            array(array('country' => 'Germany', 'state' => 'Berlin',
                'default_billing_address' => 'Yes')),
            array(array('country' => 'Germany', 'state' => 'Berlin', 'vat_number' => '111607872',
                'default_billing_address' => 'Yes')),
            array(array('country' => 'Germany', 'state' => 'Berlin', 'vat_number' => 'invalid_vat',
                'default_billing_address' => 'Yes')),
            array(array('country' => 'United Kingdom', 'state' => '%noValue%', 'vat_number' => '584451913',
                'default_billing_address' => 'Yes')),
        );
    }
}