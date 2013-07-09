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
class Core_Mage_ValidationVatNumber_AutomaticAssignmentGroupsBackendTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->markTestIncomplete(
            'BUG: An error occurred while saving the customer in automaticAssignmentGroupsBackendTest'
        );
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
        $names = array('group_valid_vat_domestic'   => 'Valid VAT Domestic_%randomize%',
                       'group_valid_vat_intraunion' => 'Valid VAT IntraUnion_%randomize%',
                       'group_invalid_vat'          => 'Invalid VAT_%randomize%');
        $processedGroupNames = array();
        //Creating three Customer Groups
        $this->loginAdminUser();
        $this->navigate('manage_customer_groups');
        foreach ($names as $groupKey => $groupName) {
            $group = $this->loadDataSet('CustomerGroup', 'new_customer_group', array('group_name' => $groupName));
            $this->customerGroupsHelper()->createCustomerGroup($group);
            //Verifying
            $this->assertMessagePresent('success', 'success_saved_customer_group');
            $processedGroupNames[$groupKey] = $group['group_name'];
        }
        //Configuring "Create New Account Options" tab
        $this->navigate('system_configuration');
        $accountOptions = $this->loadDataSet('VatID', 'create_new_account_options', $processedGroupNames);
        $this->systemConfigurationHelper()->configure($accountOptions);
        $this->reindexInvalidedData();
        $this->flushCache();
        return $processedGroupNames;
    }

    /**
     * <p>Creating customers from back-end with VAT Number</p>
     *
     * @param array $vatGroup
     * @param array|string $vatType
     * @param array $vatNumber
     * @param array|string $customerGroup
     *
     * @test
     * @depends preconditionsForTests
     * @dataProvider dataForCustomersDataProvider
     *
     * @TestlinkId TL-MAGE-6203, TL-MAGE-6204,  TL-MAGE-6205
     */
    public function automaticAssignmentGroupsBackendTest($vatType, $vatNumber, $customerGroup, $vatGroup)
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $addressData = $this->loadDataSet('Customers', $vatType, $vatNumber);
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData, $addressData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->openCustomer(array('email' => $userData['email']));
        $this->openTab('account_information');
        $this->verifyForm(array('group' => $vatGroup[$customerGroup], 'account_information'));
        $this->assertEmptyVerificationErrors();
    }

    public function dataForCustomersDataProvider()
    {
        return array(
            array('generic_address_vat_valid_domestic', array('vat_number' => '111607872'),
                'group_valid_vat_domestic'),
            array('generic_address_vat_invalid', array('vat_number' => '1111111111'),
                'group_invalid_vat'),
            array('generic_address_vat_valid_intraunion',
                array('vat_number' => '37441119989', 'country' => 'France', 'state' => 'Ain'),
                'group_valid_vat_intraunion')
        );
    }
}
