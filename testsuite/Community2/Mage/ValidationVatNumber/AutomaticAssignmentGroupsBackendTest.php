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
     * <p>Customer registration. Without VAT Number</p>
     * <p>Steps:</p>
     * <p>1. Goto on back-end and open Manage Customers area</p>
     * <p>2. Create new customer without address and VAT Number</p>
     * <p>3. Save Customer</p>
     * <p>4. Select your customer and verify value of Group</p>
     * <p>Expected result:</p>
     * <p>Customer should be assigned to Default Group</p>
     *
     * @test
     * @return array
     *
     * @TestlinkId TL-MAGE-6202
     * @author andrey.vergeles
     */
    public function customerWithoutVatNumber()
    {
        //Data
        $userRegisterData = $this->loadDataSet('Customers', 'generic_customer_account');
        $userDataParam = $userRegisterData['first_name'] . ' ' . $userRegisterData['last_name'];
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userRegisterData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Verifying Customer Group on back-end
        $this->addParameter('customer_first_last_name', $userDataParam);
        $this->customerHelper()->openCustomer(array('email' => $userRegisterData['email']));
        $this->openTab('account_information');
        //Verifying
        $this->verifyForm(array('group' => 'General'),'account_information');

        return $userDataParam;
    }

    /**
     * <p>Customer registration. With domestic VAT Number</p>
     * <p>Steps:</p>
     * <p>1. Goto on back-end and open Manage Customers area</p>
     * <p>2. Create new customer with address and domestic VAT Number</p>
     * <p>3. Save Customer</p>
     * <p>4. Select your customer and verify value of Group</p>
     * <p>Expected result:</p>
     * <p>Customer should be assigned to "Group for Valid VAT ID - Domestic"</p>
     *
     * @param array $processedGroupNames
     * @param string $userDataParam
     *
     * @test
     * @depends preconditionsForTests
     * @depends customerWithoutVatNumber
     *
     * @TestlinkId TL-MAGE-6203
     * @author andrey.vergeles
     */
    public function customerWithValidVatDomestic(array $processedGroupNames, $userDataParam)
    {
        //Data
        $userRegisterData = $this->loadDataSet('Customers', 'generic_customer_account');
        $addressData = $this->loadDataSet('Customers', 'generic_address_vat_valid_domestic');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userRegisterData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Filling Address Book and VAT Number
        $this->customerHelper()->openCustomer(array('email' => $userRegisterData['email']));
        $this->customerHelper()->addAddress($addressData);
        $this->saveForm('save_customer');
        //Verifying Customer Group
        $this->ValidationVatNumberHelper()->verifyCustomerGroup($userDataParam, $userRegisterData);
        $this->verifyForm(array('group' => $processedGroupNames['group_valid_vat_domestic']),'account_information');
    }

    /**
     * <p>Customer registration. With invalid VAT Number</p>
     * <p>Steps:</p>
     * <p>1. Goto on back-end and open Manage Customers area</p>
     * <p>2. Create new customer with address and invalid VAT Number</p>
     * <p>3. Save Customer</p>
     * <p>4. Select your customer and verify value of Group</p>
     * <p>Expected result:</p>
     * <p>Customer should be assigned to "Group for Invalid VAT ID"</p>
     *
     * @param array $processedGroupNames
     * @param string $userDataParam
     *
     * @test
     * @depends preconditionsForTests
     * @depends customerWithoutVatNumber
     *
     * @TestlinkId TL-MAGE-6204
     * @author andrey.vergeles
     */
    public function customerWithInvalidVat(array $processedGroupNames, $userDataParam)
    {
        //Data
        $userRegisterData = $this->loadDataSet('Customers','generic_customer_account');
        $addressData = $this->loadDataSet('Customers', 'generic_address_vat_invalid');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userRegisterData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Filling Address Book and VAT Number
        $this->customerHelper()->openCustomer(array('email' => $userRegisterData['email']));
        $this->customerHelper()->addAddress($addressData);
        $this->saveForm('save_customer');
        //Verifying Customer Group
        $this->ValidationVatNumberHelper()->verifyCustomerGroup($userDataParam, $userRegisterData);
        $this->verifyForm(array('group' => $processedGroupNames['group_invalid_vat']),'account_information');
    }

    /**
     * <p>Customer registration. With valid VAT Number</p>
     * <p>Steps:</p>
     * <p>1. Goto on back-end and open Manage Customers area</p>
     * <p>2. Create new customer with country from Europe Union (but not the same as store country)</p>
     * <p>4. Enter valid VAT Number </p>
     * <p>5. Save Customer</p>
     * <p>6. Select your customer and verify value of Group</p>
     * <p>Expected result:</p>
     * <p>Customer should be assigned to "Group for Valid VAT ID - Intra-Union"</p>
     *
     * @param array $processedGroupNames
     * @param string $userDataParam
     *
     * @test
     * @depends preconditionsForTests
     * @depends customerWithoutVatNumber
     *
     * @TestlinkId TL-MAGE-6205
     * @author andrey.vergeles
     */
    public function customerWithValidVatIntraUnion(array $processedGroupNames, $userDataParam)
    {
        //Data
        $userRegisterData = $this->loadDataSet('Customers', 'generic_customer_account');
        $addressData = $this->loadDataSet('Customers', 'generic_address_vat_valid_intraunion');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userRegisterData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Filling Address Book and VAT Number
        $this->customerHelper()->openCustomer(array('email' => $userRegisterData['email']));
        $this->customerHelper()->addAddress($addressData);
        $this->saveForm('save_customer');
        //Verifying Customer Group
        $this->ValidationVatNumberHelper()->verifyCustomerGroup($userDataParam, $userRegisterData);
        $this->verifyForm(array('group' => $processedGroupNames['group_valid_vat_intraunion']),'account_information');
    }
}
