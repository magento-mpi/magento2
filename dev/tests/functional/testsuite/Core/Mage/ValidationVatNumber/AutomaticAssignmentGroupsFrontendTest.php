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
class Core_Mage_ValidationVatNumber_AutomaticAssignmentGroupsFrontendTest extends Mage_Selenium_TestCase
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

    protected function tearDownAfterTest()
    {
        $this->frontend();
        $this->logoutCustomer();
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
        $names = array(
            'group_valid_vat_domestic' => 'Valid VAT Domestic_%randomize%',
            'group_valid_vat_intraunion' => 'Valid VAT IntraUnion_%randomize%',
            'group_invalid_vat' => 'Invalid VAT_%randomize%',
            'group_default' => 'Default Group_%randomize%'
        );
        $processedGroupNames = array();
        //Creating three Customer  Groups
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
     * <p>Customer registration. With valid VAT Number for domestic country</p>
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
     */
    public function AutomaticAssignmentGroupsBackendTest($vatNumber, $customerGroup, $vatGroup)
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $addressData = $this->loadDataSet('Customers', 'generic_address', $vatNumber);
        //Steps
        //Creating customer on front-end
        $this->frontend();
        $this->navigate('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'vat_number_message');
        //Filling Address Book and VAT Number
        $this->navigate('adding_new_address_book');
        $this->fillFieldset($addressData, 'address_book');
        $this->clickButton('save_address');
        //Verifying Customer Group on back-end
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => $userData['email']));
        $this->openTab('account_information');
        $this->verifyForm(array('group' => $vatGroup[$customerGroup], 'account_information'));
        $this->assertEmptyVerificationErrors();
    }

    public function dataForCustomersDataProvider()
    {
        return array(
            array(array('country' => 'Germany', 'state' => 'Berlin', 'vat_number' => '%noValue%',
                'default_billing_address' => '%noValue%'),
                'group_default'),
            array(array('country' => 'Germany', 'state' => 'Berlin', 'vat_number' => '111607872',
                'default_billing_address' => '%noValue%'),
                'group_valid_vat_domestic'),
            array(array('country' => 'Germany', 'state' => 'Berlin', 'vat_number' => '11111111',
                'default_billing_address' => '%noValue%'),
                'group_invalid_vat'),
            array(array('country' => 'United Kingdom', 'state' => '%noValue%', 'vat_number' => '584451913',
                'default_billing_address' => '%noValue%'),
                'group_valid_vat_intraunion')
        );
    }

    /**
     * <p>Customer registration. With invalid VAT Number for domestic country</p>
     *
     * @param array $processedGroupNames
     *
     * @test
     * @depends preconditionsForTests
     * @return array
     *
     * @TestlinkId TL-MAGE-4041
     */
    public function customerWithValidVatIntraUnion($processedGroupNames)
    {
        //Data
        $userRegisterData = $this->loadDataSet('Customers', 'customer_account_register');
        $userAddressData = $this->loadDataSet('Customers', 'generic_address', array(
            'country' => 'United Kingdom',
            'state' => '%noValue%',
            'vat_number' => '584451913',
            'default_billing_address' => '%noValue%'
        ));
        //Creating customer on front-end
        $this->goToArea('frontend');
        $this->navigate('customer_login');
        $this->customerHelper()->registerCustomer($userRegisterData);
        $this->assertMessagePresent('success', 'vat_number_message');
        //Filling Address Book and VAT Number
        $this->navigate('adding_new_address_book');
        $this->addParameter('VatNumber', $userAddressData['vat_number']);
        $this->fillFieldset($userAddressData, 'address_book');
        $this->clickButton('save_address');
        $this->assertMessagePresent('success', 'success_validate_intraunion_vat');
        //Verifying Customer Group on back-end
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer(array('email' => $userRegisterData['email']));
        $this->openTab('account_information');
        $this->verifyForm(array('group' => $processedGroupNames['group_valid_vat_intraunion']), 'account_information');
    }

    /**
     * <p>Validation VAT with invalid number</p>
     *
     * @test
     * @depends preconditionsForTests
     *
     * @TestlinkId TL-MAGE-3801
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
        $this->systemConfigurationHelper()->expandFieldSet('store_information');
        $this->clickControl('button', 'validate_vat_number', false);
        $this->pleaseWait();
        $this->assertTrue($this->controlIsVisible('pageelement', 'vat_number_is_invalid'));
    }
}
