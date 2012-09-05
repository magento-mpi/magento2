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
class Community2_Mage_ValidationVatNumber_AutomaticAssignmentGroups extends Mage_Selenium_TestCase
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
        //Creating three Customer  Groups
        $this->loginAdminUser();
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
        $accountOptions = $this->loadDataSet('VatID', 'create_new_account_options',
            array('group_valid_vat_domestic'   => $processedGroupNames[0],
                  'group_valid_vat_intraunion' => $processedGroupNames[1],
                  'group_invalid_vat'          => $processedGroupNames[2]));
        $this->systemConfigurationHelper()->configure($accountOptions);
        return $processedGroupNames;
    }

    protected function tearDownAfterTest()
    {
        $this->frontend();
        $this->logoutCustomer();
    }

    /**
     * <p>Customer registration. Without VAT Number</p>
     * <p>Steps:</p>
     * <p>1. Goto on front-end</p>
     * <p>2. Create new customer</p>
     * <p>3. Field "VAT Number" leave empty</p>
     * <p>4. Goto back-end and open "Manage Customers"</p>
     * <p>5. Select your customer and verify value of Group</p>
     * <p>Expected result:</p>
     * <p>Customer should be assigned to Default Group</p>
     *
     * @test
     * @return array
     * @TestlinkId TL-MAGE-4039
     */
    public function customerWithoutVatNumber()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $userDataParam = $userData['first_name'] . ' ' . $userData['last_name'];
        //Creating customer on front-end
        $this->goToArea('frontend');
        $this->navigate('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'vat_number_message');
        //Verifying Customer Group on back-end
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->addParameter('customer_first_last_name', $userDataParam);
        $this->customerHelper()->openCustomer(array('email' => $userData['email']));
        $this->openTab('account_information');
        //Verifying
        $this->verifyForm(array('group' => 'General'),'account_information');
    }

    /**
     * <p>Customer registration. With valid VAT Number for domestic country</p>
     * <p>Steps:</p>
     * <p>1. Goto on front-end</p>
     * <p>2. Create new customer</p>
     * <p>3. Goto tab Address and enter the same country as country of your store</p>
     * <p>4. Enter valid VAT Number</p>
     * <p>4. Goto back-end and open "Manage Customers"</p>
     * <p>5. Select your customer and verify value of Group</p>
     * <p>Expected result:</p>
     * <p>Customer should be assigned to group which was specified as "Group for Valid VAT ID - Domestic"</p>
     *
     * @param array $processedGroupNames
     *
     * @test
     * @depends preconditionsForTests
     * @return array
     *
     * @TestlinkId TL-MAGE-3802
     */
    public function customerWithValidVatDomestic($processedGroupNames)
    {
        //Data
        $userRegisterData = $this->loadDataSet('Customers', 'customer_account_register');
        $userAddressData = $this->loadDataSet('Customers', 'generic_address',
            array('country'    => 'Germany',
                  'state'      => 'Berlin',
                  'vat_number' => '111607872'));
        $userDataParam = $userRegisterData['first_name'] . ' ' . $userRegisterData['last_name'];
        //Creating customer on front-end
        $this->goToArea('frontend');
        $this->navigate('customer_login');
        $this->customerHelper()->registerCustomer($userRegisterData);
        $this->assertMessagePresent('success', 'vat_number_message');
        //Filling Address Book and VAT Number
        $this->navigate('adding_new_address_book');
        $this->fillFieldset($userAddressData, 'address_book');
        $this->clickButton('save_address');
        $this->assertMessagePresent('success', 'success_validate_vat');
        //Verifying Customer Group on back-end
        $this->ValidationVatNumberHelper()->verifyCustomerGroup($userDataParam, $userRegisterData);
        $this->verifyForm(array('group' => $processedGroupNames[0]),'account_information');
    }

    /**
     * <p>Customer  registration. With invalid VAT Number for domestic country</p>
     * <p>Steps:</p>
     * <p>1. Goto on front-end</p>
     * <p>2. Create new customer</p>
     * <p>3. Goto tab Address and enter the same country as country of your store</p>
     * <p>4. Enter invalid VAT Number</p>
     * <p>4. Goto back-end and open "Manage Customers"</p>
     * <p>5. Select your customer and verify value of Group</p>
     * <p>Expected result:</p>
     * <p>Customer should be assigned to group which was specified as "Group for Invalid VAT ID"</p>
     *
     * @param array $processedGroupNames
     *
     * @test
     * @depends preconditionsForTests
     * @return array
     *
     * @TestlinkId TL-MAGE-4042
     */
    public function customerWithInvalidVat($processedGroupNames)
    {
        //Data
        $userRegisterData = $this->loadDataSet('Customers', 'customer_account_register');
        $userAddressData = $this->loadDataSet('Customers', 'generic_address',
            array('country'    => 'Germany',
                  'state'      => 'Berlin',
                  'vat_number' => '11111111'));
        $userDataParam = $userRegisterData['first_name'] . ' ' . $userRegisterData['last_name'];
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
        $this->assertMessagePresent('success', 'invalid_vat_number');
        //Verifying Customer Group on back-end
        $this->ValidationVatNumberHelper()->verifyCustomerGroup($userDataParam, $userRegisterData);
        $this->verifyForm(array('group' => $processedGroupNames[2]),'account_information');
    }

    /**
     * <p>Customer registration. With invalid VAT Number for domestic country</p>
     * <p>Steps:</p>
     * <p>1. Goto on front-end</p>
     * <p>2. Create new customer</p>
     * <p>3. Goto tab Address and select some country from Europe Union (but not the same as store country)</p>
     * <p>4. Enter valid VAT Number</p>
     * <p>4. Goto back-end and open "Manage Customers"</p>
     * <p>5. Select your customer and verify value of Group</p>
     * <p>Expected result:</p>
     * <p>Customer should be assigned to group which was specified as "Group for Valid VAT ID - Intra-Union"</p>
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
        $userAddressData = $this->loadDataSet('Customers', 'generic_address',
            array('country'    => 'United Kingdom',
                  'vat_number' => '584451913'));
        $userDataParam = $userRegisterData['first_name'] . ' ' . $userRegisterData['last_name'];
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
        $this->ValidationVatNumberHelper()->verifyCustomerGroup($userDataParam, $userRegisterData);
        $this->verifyForm(array('group' => $processedGroupNames[1]),'account_information');
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
     */
    public function validationVatNumber()
    {
        //Data
        $storeInfo = $this->loadDataSet('VatID', 'store_information_data', array('vat_number' => 'invalid_number'));
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