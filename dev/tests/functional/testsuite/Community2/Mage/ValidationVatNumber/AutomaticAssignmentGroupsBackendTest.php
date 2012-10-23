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
class Community2_Mage_ValidationVatNumber_AutomaticAssignmentGroupsBackendTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        //Data
        $storeInfo = $this->loadDataSet('VatID', 'store_information_data');
        //Filling "Store Information" data and Validation VAT Number
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($storeInfo);
        $xpath = $this->_getControlXpath('link','store_information_link');
        if (!$this->isElementPresent($xpath . "[@class='open']")) {
            $this->clickControl('link','store_information_link', false);
        }
        $this->clickControl('button', 'validate_vat_number', false);
        $this->pleaseWait();
        //Verification
        $this->assertTrue($this->controlIsPresent('button', 'vat_number_is_valid'), 'VAT Number is not valid');
    }

    /**
     * @test
     * @return array
     */
    public function preconditionsForTests()
    {
        //Data
        $names = array(
            'group_valid_vat_domestic'   => 'Valid VAT Domestic_%randomize%',
            'group_valid_vat_intraunion' => 'Valid VAT IntraUnion_%randomize%',
            'group_invalid_vat'          => 'Invalid VAT_%randomize%');
        $processedGroupNames = array();
        //Creating three Customer Groups
        $this->loginAdminUser();
        $this->navigate('manage_customer_groups');
        foreach ($names as $groupKey => $groupName) {
            $customerGroup = $this->loadDataSet('CustomerGroup', 'new_customer_group',
                array('group_name' => $groupName));
            $this->customerGroupsHelper()->createCustomerGroup($customerGroup);
        //Verifying
            $this->assertMessagePresent('success', 'success_saved_customer_group');
            $processedGroupNames[$groupKey] = $customerGroup['group_name'];
        }
        //Configuring "Create New Account Options" tab
        $this->navigate('system_configuration');
        $accountOptions = $this->loadDataSet('VatID', 'create_new_account_options', $processedGroupNames);
        $this->systemConfigurationHelper()->configure($accountOptions);

        return $processedGroupNames;
    }

    protected function tearDownAfterTestClass()
    {
        $accountOptions = $this->loadDataSet('VatID', 'create_new_account_options_disable');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($accountOptions);
    }

    /**
     * <p>Creating customers from back-end with VAT Number</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Goto Manage Customer Page</p>
     * <p>2. Click button "Add New Customer"</p>
     * <p>3. Fill required fields and save customer</p>
     * <p>4. Open your customer and goto tab "Addresses"</p>
     * <p>5. Click button "Add New Address" and fill address fields</p>
     * <p>6. Enter VAT Number</p>
     * <p>7. Save customer</p>
     * <p>Expected result:</p>
     * <p>customer should be saved. Customer should be assigned to corresponding group</p>
     *
     * @param array $vatGroup
     * @param array|string $accountType
     * @param array|string $vatType
     * @param array $vatNumber
     * @param array|string $customerGroup
     *
     * @test
     * @depends preconditionsForTests
     * @dataProvider dataForCustomersDataProvider
     *
     * @TestlinkId TL-MAGE-6203, TL-MAGE-6204,  TL-MAGE-6205
     * @author andrey.vergeles
     */
    public function automaticAssignmentGroupsBackendTest($accountType, $vatType, $vatNumber, $customerGroup, $vatGroup)
    {
        //Data
        $userData = $this->loadDataSet('Customers', $accountType);
        $addressData = $this->loadDataSet('Customers', $vatType, $vatNumber);
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $userDataParam = $userData['first_name'] . ' ' . $userData['last_name'];
        $this->addParameter('customer_first_last_name', $userDataParam);
        $this->customerHelper()->openCustomer(array('email' => $userData['email']));
        $this->customerHelper()->addAddress($addressData);
        $this->saveForm('save_customer');
        //Verifying
        $this->ValidationVatNumberHelper()->verifyCustomerGroup($userDataParam, $userData);
        $verificationData = $vatGroup[$customerGroup];
        $this->verifyForm(array('group' => $verificationData, 'account_information'));
    }

    public function dataForCustomersDataProvider()
    {
        return array(
            array('generic_customer_account', 'generic_address_vat_valid_domestic',
                  array('billing_vat_number'   => '111607872'), 'group_valid_vat_domestic'),
            array('generic_customer_account', 'generic_address_vat_invalid',
                  array('billing_vat_number'   => '1111111111'), 'group_invalid_vat'),
            array('generic_customer_account', 'generic_address_vat_valid_intraunion',
                  array('billing_vat_number'   => '37441119989',
                        'country'              => 'France',
                        'state'                => 'Ain'), 'group_valid_vat_intraunion')
        );
    }
}
