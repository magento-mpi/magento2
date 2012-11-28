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
class Core_Mage_Acl_CustomersAclTest extends Mage_Selenium_TestCase
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
     *
     * @test
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
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'));
        //Verifying that Global Search fieldset is present or not present
        $this->assertEquals(0, $this->getControlCount('field', 'global_record_search'));
    }

    /**
     * <p>Test navigation.</p>
     *
     * @test
     * @depends roleResourceAccessManageCustomer
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
     *
     * @return array $userData
     *
     * @test
     * @depends navigation
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
     *
     * @param array $userData
     *
     * @test
     * @depends withRequiredFieldsOnly
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
     *
     * @test
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
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'));
        //Verifying that Global Search fieldset is present or not present
        $this->assertEquals(0, $this->getControlCount('field', 'global_record_search'));
    }

    /**
     * <p>Create customer group</p>
     *
     * @test
     * @depends roleResourceAccessCustomerGroups
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
     *
     * @test
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
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'));
        //Verifying that Global Search fieldset is present or not present
        $this->assertEquals(0, $this->getControlCount('field', 'global_record_search'));
    }
}
