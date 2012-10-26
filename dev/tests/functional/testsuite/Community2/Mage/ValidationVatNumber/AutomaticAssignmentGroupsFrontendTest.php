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
class Community2_Mage_ValidationVatNumber_AutomaticAssignmentGroupsFrontendTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        //Data
        $storeInfo = $this->loadDataSet('VatID', 'store_information_data');
        //Filling "Store Information" data and Validation VAT  Number
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
        //Steps
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
            'group_invalid_vat'          => 'Invalid VAT_%randomize%',
            'group_default'              => 'Default Group_%randomize%');
        $processedGroupNames = array();
        //Creating three Customer  Groups
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

    protected function tearDownAfterTest()
    {
        $this->frontend();
        $this->logoutCustomer();
    }

    protected function tearDownAfterTestClass()
    {
        $accountOptions = $this->loadDataSet('VatID', 'create_new_account_options_disable');
        $this->loginAdminUser();
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
     * @param array|string $vatNumber
     * @param array|string $customerGroup
     *
     * @test
     * @depends preconditionsForTests
     * @dataProvider dataForCustomersDataProvider
     *
     * @TestlinkId TL-MAGE-6203, TL-MAGE-6204,  TL-MAGE-6205
     * @author andrey.vergeles
     */
    public function AutomaticAssignmentGroupsBackendTest($vatNumber, $customerGroup, $vatGroup)
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $addressData = $this->loadDataSet('Customers', 'generic_address', $vatNumber);
        //Steps
        //Creating customer on front-end
        $this->goToArea('frontend');
        $this->navigate('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'vat_number_message');
        //Filling Address Book and VAT Number
        $this->navigate('adding_new_address_book');
        $this->fillFieldset($addressData, 'address_book');
        $this->clickButton('save_address');
        //Verifying Customer Group on back-end
        $userDataParam = $userData['first_name'] . ' ' . $userData['last_name'];
        $this->addParameter('customer_first_last_name', $userDataParam);
        $this->ValidationVatNumberHelper()->verifyCustomerGroup($userDataParam, $userData);
        $verificationData = $vatGroup[$customerGroup];
        $this->verifyForm(array('group' => $verificationData, 'account_information'));
    }

    public function dataForCustomersDataProvider()
    {
        return array(
            array(array('country'                 => 'Germany',
                        'state'                   => 'Berlin',
                        'billing_vat_number'      => '%noValue%',
                        'default_billing_address' => '%noValue%'), 'group_default'),
            array(array('country'                 => 'Germany',
                        'state'                   => 'Berlin',
                        'billing_vat_number'      => '111607872',
                        'default_billing_address' => '%noValue%'), 'group_valid_vat_domestic'),
            array(array('country'                 => 'Germany',
                        'state'                   => 'Berlin',
                        'billing_vat_number'      => '11111111',
                        'default_billing_address' => '%noValue%'), 'group_invalid_vat'),
            array(array('country'                 => 'United Kingdom',
                        'billing_vat_number'      => '584451913',
                        'default_billing_address' => '%noValue%'), 'group_valid_vat_intraunion'),
        );
    }

    /**
     * <p>Validation VAT with invalid number</p>
     * <p>Steps:</p>
     * <p>1. Goto on back-end</p>
     * <p>2. Open Store Information tab</p>
     * <p>3. Enter invalid VAT number</p>
     * <p>4. Click button "Validate VAT Number"</p>
     * <p>Expected result:</p>
     * <p>Button "Validate VAT Number" should be changed on red button with text "VAT Number is Invalid"</p>
     *
     * @test
     * @depends preconditionsForTests
     *
     * @TestlinkId TL-MAGE-3801
     * @author andrey.vergeles
     */
    public function validationVatNumber()
    {
        //Data
        $storeInfo = $this->loadDataSet('VatID', 'store_information_data',
            array('billing_vat_number' => 'invalid_number'));
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
        $this->assertTrue($this->controlIsPresent('button', 'vat_number_is_invalid'));
    }
}