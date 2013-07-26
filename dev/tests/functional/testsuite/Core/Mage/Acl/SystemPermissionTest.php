<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Acl
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * ACL tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Acl_SystemPermissionTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->admin('log_in_to_admin');
    }

    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p>Precondition method</p>
     *
     * @test
     * @return array
     */
    public function createAdminWithTestRole()
    {
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'system-permissions'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $testData = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testData);
        $this->assertMessagePresent('success', 'success_saved_user');

        return array(
            'login' => array('user_name' => $testData['user_name'], 'password' => $testData['password']),
            'role_name' => $testData['role_name']
        );
    }

    /**
     * <p> Ability to edit own role for user with Permission System-Permissions </p>
     * <p>Preconditions</p>
     * <p>1. Role "Role1" with Role resource System-Permissions is created.</p>
     * <p>2. Admin user "User1" with "Role1" is created.</p>
     * <p>Steps:</p>
     * <p>1. Log in to backend using newly created "User1" credentials.</p>
     * <p>2. Navigate to System-Configuration-Permissions-Role.</p>
     * <p>3. Edit "Role1". Add for Role1 access for "System-Configuration" recourse.</p>
     * <p>4. Navigate to System-Configuration.</p>
     * <p>5. Click to all tab(one after the other) in configuration menu.(On the left column).</p>
     * <p>Expected result:</p>
     * <p>All configuration pages(one after other) are successfully opened.</p>
     *
     * @param $testData
     *
     * @test
     * @depends createAdminWithTestRole
     * @TestlinkId TL-MAGE-6071
     */
    public function systemPermissions($testData)
    {
        $this->adminUserHelper()->loginAdmin($testData['login']);
        // Verify that navigation menu has only 2 child elements
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        $this->navigate('manage_roles');
        $editedRole = $this->loadDataSet('AdminUserRole', 'edit_admin_user_role_name',
            array('resource_acl' => 'stores-settings-configuration'),
            array('roleName' => $testData['role_name'], 'newRoleName' => $testData['role_name'])
        );
        //Data
        $this->adminUserHelper()->editRole($editedRole);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->assertEquals(2, $this->getControlCount('pageelement', 'navigation_menu_items'),
            'Count of Top Navigation Menu elements not equal 2, should be equal');
        $this->navigate('system_configuration');
        $tabElement = $this->loadDataSet('SystemConfigurationMenu', 'configuration_menu_default');
        //verify that this tab equal to resource from ACL tree
        foreach (array_keys($tabElement) as $tabName) {
            $this->systemConfigurationHelper()->openConfigurationTab($tabName);
        }
    }

    /**
     * <p> Actions available for user with Permission System-Permissions </p>
     *
     * @param $testData
     *
     * @test
     * @depends createAdminWithTestRole
     * @TestlinkId TL-MAGE-6072
     */
    public function systemPermissionsActions($testData)
    {
        $this->adminUserHelper()->loginAdmin($testData['login']);
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'stores-settings-configuration'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $testDataForNewUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testDataForNewUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->navigate('manage_roles');
        $dataForDelete = $this->loadDataSet('AdminUserRole', 'edit_admin_user_role_name', null,
            array('roleName' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->deleteRole($dataForDelete);
        $this->assertMessagePresent('success', 'success_deleted_role');
        $this->navigate('manage_roles');
        $dataForDeleteOwnRole = $this->loadDataSet('AdminUserRole', 'edit_admin_user_role_name', null,
            array('roleName' => $testData['role_name']));
        $this->adminUserHelper()->deleteRole($dataForDeleteOwnRole);
        $this->assertMessagePresent('error', 'delete_self_assigned_role');
    }

    /**
     * <p>1. Do following checks for full-powered admin:</p>
     * <p>  - "Sing Out" link is available</p>
     * <p>  - "Account Setting" link is available</p>
     * <p>  - fields on "My Account" page are editable</p>
     * <p>2. Create role restricted to Catalog and Sales modules</p>
     *
     * @test
     */
    public function createSalesOnlyRole()
    {
        $this->loginAdminUser();
        //Additional check for powerful admins
        $this->assertTrue($this->controlIsPresent('link', 'log_out'));
        $this->assertTrue($this->controlIsPresent('link', 'account_settings'));
        $this->navigate('my_account');
        $this->assertTrue($this->controlIsEditable('field', 'user_name'));

        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        return $roleSource['role_info_tab']['role_name'];
    }

    /**
     * <p>Create admin user restricted to Catalog and Sales modules only.</p>
     *
     * @test
     * @depends createSalesOnlyRole
     */
    public function createSalesOnlyUser($roleName)
    {
        $this->loginAdminUser();
        $this->navigate('manage_admin_users');
        $userData = $this->loadDataSet('AdminUsers', 'generic_admin_user', array('role_name' => $roleName));
        $this->adminUserHelper()->createAdminUser($userData);
        $this->assertMessagePresent('success', 'success_saved_user');

        return array('user_name' => $userData['user_name'], 'password' => $userData['password']);
    }

    /**
     * <p>1. Log in as user restricted to Catalog and Sales</p>
     * <p>2. Check that link "Sign Out" is available</p>
     * <p>3. Check that link "Account Setting" is absent.</p>
     *
     * @test
     * @depends createSalesOnlyUser
     */
    public function loginSalesOnlyUser($loginData)
    {
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->controlIsPresent('link', 'log_out'));
        $this->assertFalse($this->controlIsPresent('link', 'account_settings'));
        $this->logoutAdminUser();
    }
}
