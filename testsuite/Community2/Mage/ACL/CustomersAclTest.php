<?php
# Magento
#
# {license_notice}
#
# @category    Magento
# @package     Mage_ACL
# @subpackage  functional_tests
# @copyright   {copyright}
# @license     {license_link}
#
/**
 * Creating Admin User
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_ACL_CustomersAclTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Post conditions:</p>
     * <p>Log out from Backend.</p>
     */
    protected function tearDownAfterTestClass()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p>Preconditions</p>
     * <p>1. Login to backend as admin</p>
     * <p>2. Go to System>Permissions>Role and click "Add New Role" button</p>
     * <p>3. Fill "Role Name" field</p>
     * <p>4. Click Role Resource Tab</p>
     * <p>5. In Role Resources fieldset  select only one test scope checkbox[Customers>Manage Customers]</p>
     * <p>6. Click "Save Role" button for save roleSource</p>
     * <p>7. Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>8. Fill all required fields (User Info Tab)</p>
     * <p>9. Click User Role Tab</p>
     * <p>10. Select testRole</p>
     * <p>11. Click "Save User" button for save testAdminUser</p>
     * <p>12. Log out </p>
     * <p>Steps:</p>
     * <p>1. Log in as testAdminUser</p>
     * <p>Expected results:</p>
     * <p>1. Manage customers page is available </p>
     *
     * @test
     *
     * @TestlinkId TL-MAGE-5729
     */
    public function roleResourceAccessManageCustomer()
    {
        $this->admin('log_in_to_admin', false);
        $this->logoutAdminUser();
        $this->loginAdminUser();
        //Preconditions
        //create specific role with test roleResource
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Customers/Manage Customers'));
        $this->adminUserHelper()->createRole($roleSource);
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->logoutAdminUser();
        //Steps
        //login as admin user with specific(test) role
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_customers');
        //Verifying  count of main menu elements
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $navigationElements = $this->getElementsByXpath($xpath);
        $this->assertEquals('1', count($navigationElements));
        //Verifying that Global Search fieldset is present or not present
        $globSearchXpath = $this->_getControlXpath('field', 'global_record_search');
        $globSearchCount = $this->getElementsByXpath($globSearchXpath, 'value');
        $this->assertEquals('0', count($globSearchCount));
    }

    /**
     * <p>Test navigation.</p>
     * <p>Steps:</p>
     * <p>1. Verify that 'Add New Customer' button is present and click her.</p>
     * <p>2. Verify that the create customer page is opened.</p>
     * <p>3. Verify that 'Back' button is present.</p>
     * <p>4. Verify that 'Save Customer' button is present.</p>
     * <p>5. Verify that 'Reset' button is present.</p>
     *
     * @test
     *
     * @depends roleResourceAccessManageCustomer
     *
     * @TestlinkId TL-MAGE-5729
     */
    public function navigation()
    {
        $this->navigate('manage_customers');
        $this->assertTrue($this->buttonIsPresent('add_new_customer'),
            'There is no "Add New Customer" button on the page');
        $this->clickButton('add_new_customer');
        $this->assertTrue($this->checkCurrentPage('create_customer'), $this->getParsedMessages());
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_customer'), 'There is no "Save" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_and_continue_edit'),
            'There is no "Save and Continue Edit" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
    }

    /**
     * <p>Create customer by filling in only required fields</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add New Customer' button.</p>
     * <p>2. Fill in required fields.</p>
     * <p>3. Click 'Save Customer' button.</p>
     * <p>Expected result:</p>
     * <p>Customer is created.</p>
     * <p>Success Message is displayed</p>
     *
     * @test
     *
     * @return array
     *
     * @depends navigation
     *
     * @TestlinkId TL-MAGE-3587
     */
    public function withRequiredFieldsOnly()
    {
        $this->navigate('manage_customers');
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');
        return $userData;
    }

    /**
     * <p>Create customer. Use email that already exist</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add New Customer' button.</p>
     * <p>2. Fill in 'Email' field by using email that already exist.</p>
     * <p>3. Fill other required fields by regular data.</p>
     * <p>4. Click 'Save Customer' button.</p>
     * <p>Expected result:</p>
     * <p>Customer is not created.</p>
     * <p>Error Message is displayed.</p>
     *
     * @test
     *
     * @param array $userData
     *
     * @depends withRequiredFieldsOnly
     *
     * @TestlinkId TL-MAGE-3582
     */
    public function withEmailThatAlreadyExists(array $userData)
    {
        $this->navigate('manage_customers');
        //Steps
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->assertMessagePresent('error', 'customer_email_exist');
    }

    /**
     * <p>Preconditions</p>
     * <p>1. Log in to backend as admin</p>
     * <p>2. Go to System>Permissions>Role and click "Add New Role" button</p>
     * <p>3. Fill "Role Name" field</p>
     * <p>4. Click Role Resource Tab</p>
     * <p>5. In Role Resources fieldset  select only one test scope checkbox[Customers>Manage Groups]</p>
     * <p>6. Click "Save Role" button for save roleSource</p>
     * <p>7. Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>8. Fill all required fields (User Info Tab)</p>
     * <p>9. Click User Role Tab</p>
     * <p>10. Select testRole</p>
     * <p>11. Click "Save User" button for save testAdminUser</p>
     * <p>12. Log out </p>
     * <p>Steps:</p>
     * <p>1. Log in as testAdminUser</p>
     * <p>Expected results:</p>
     * <p>1. Manage groups page is available </p>
     *
     * @test
     *
     * @TestlinkId TL-MAGE-5730
     */
    public function roleResourceAccessCustomerGroups()
    {
        $this->admin('log_in_to_admin', false);
        $this->logoutAdminUser();
        $this->loginAdminUser();
        //Preconditions
        //create specific role with test roleResource
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Customers/Customer Groups'));
        $this->adminUserHelper()->createRole($roleSource);
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->logoutAdminUser();
        //Steps
        //login as admin user with specific(test) role
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_customer_groups');
        //Verifying  count of main menu elements
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $navigationElements = $this->getElementsByXpath($xpath);
        $this->assertEquals('1', count($navigationElements));
        //Verifying that Global Search fieldset is present or not present
        $globSearchXpath = $this->_getControlXpath('field', 'global_record_search');
        $globSearchCount = $this->getElementsByXpath($globSearchXpath, 'value');
        $this->assertEquals('0', count($globSearchCount));
    }

    /**
     * <p>Create customer group</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add New Customer' button.</p>
     * <p>2. Fill all required field with correct data.</p>
     * <p>3. Click 'Save Customer Group' button.</p>
     * <p>Expected result:</p>
     * <p>Customer group is  created.</p>
     * <p>Success message is displayed.</p>
     *
     * @test
     *
     * @depends roleResourceAccessCustomerGroups
     *
     * @TestlinkId TL-MAGE-5732
     */
    public function createGroup()
    {
        $this->navigate('manage_customer_groups');
        $customerGroupData = $this->loadDataSet('CustomerGroup', 'new_customer_group');
        //Steps
        $this->customerGroupsHelper()->createCustomerGroup($customerGroupData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer_group');
    }

    /**
     * <p>Preconditions</p>
     * <p>1. Login to backend as admin</p>
     * <p>2. Go to System>Permissions>Role and click "Add New Role" button</p>
     * <p>3. Fill "Role Name" field</p>
     * <p>4. Click Role Resource Tab</p>
     * <p>5. In Role Resources fieldset  select only one test scope checkbox[Customers>Online Customers]</p>
     * <p>6. Click "Save Role" button for save roleSource</p>
     * <p>7. Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>8. Fill all required fields (User Info Tab)</p>
     * <p>9. Click User Role Tab</p>
     * <p>10. Select testRole</p>
     * <p>11. Click "Save User" button for save testAdminUser</p>
     * <p>12. Log out </p>
     * <p>Steps:</p>
     * <p>1. Log in as testAdminUser</p>
     * <p>Expected results:</p>
     * <p>1. Online customers page is available </p>
     *
     * @test
     *
     * @TestlinkId TL-MAGE-5731
     */
    public function roleResourceAccessOnlineCustomers()
    {
        $this->admin('log_in_to_admin', false);
        $this->logoutAdminUser();
        $this->loginAdminUser();
        //Preconditions
        //create specific role with test roleResource
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Customers/Online Customers'));
        $this->adminUserHelper()->createRole($roleSource);
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->logoutAdminUser();
        //Steps
        //login as admin user with specific(test) role
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('online_customers');
        //Verifying  count of main menu elements
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $navigationElements = $this->getElementsByXpath($xpath);
        $this->assertEquals('1', count($navigationElements));
        //Verifying that Global Search fieldset is present or not present
        $globSearchXpath = $this->_getControlXpath('field', 'global_record_search');
        $globSearchCount = $this->getElementsByXpath($globSearchXpath, 'value');
        $this->assertEquals('0', count($globSearchCount));
    }
}
