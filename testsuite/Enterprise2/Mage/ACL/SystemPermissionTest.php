<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ACL
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

class Enterprise2_Mage_ACL_SystemPermissionTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p>Precondition method</p>
     * <p>Admin user with Role System-Permissions is created.</p>
     * <p>Steps:</p>
     * <p>1. Create new admin role with "Role Resources":</p>
     * <p>1.1 Resource Access = Custom</p>
     * <p>1.2 Resource checkboxes = 'System-Permissions'</p>
     * <p>2. Create test Admin user with test Role(Full permissions for Permissions menu)</p>
     * <p>3. Navigate to System-Configuration-ADVANCED-Admin.</p>
     * <p>4. Set "Maximum Login Failures to Lockout Account" = 3 on "Security" tab</p>
     *
     * @test
     * @return array
     */
    public function createAdminWithTestRole()
    {
        $this->loginAdminUser();
        //create role and user
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'System/Permissions'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $testData = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testData);
        $this->assertMessagePresent('success', 'success_saved_user');
        // set configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('LockedUser/login_failures');
        $this->logoutAdminUser();

        return $testData;
    }

    /**
     * <p> Verifying unlock action for user with Permission System-Permissions </p>
     * <p>Preconditions:</p>
     * <p>1. Role "Role1" with Role resource System-Permissions is created.</p>
     * <p>2. Admin user "User1" with "Role1" is created.</p>
     * <p>3. Configure "Maximum Login Failures to Lockout Account"=3.</p>
     * <p>Steps:</p>
     * <p>1. Log in to backend using newly created "User1" credentials.</p>
     * <p>2. Navigate to System-Permissions-Roles.</p>
     * <p>3. Create new role "Role2".</p>
     * <p>4. Navigate to System-Permissions-Users.</p>
     * <p>5. Create new "User2".</p>
     * <p>6. Log out admin user.</p>
     * <p>7. Try to log in to backend with "User2" and wrong password . Do this action three times.</p>
     * <p>Expected result:</p>
     * <p> User doesn't log in to backend. The message "Invalid Username or Password." is displayed.</p>
     * <p>8. Log in to backend with "User2" and correct password.</p>
     * <p>Expected result:</p>
     * <p>User doesn't log in to backend. The message "Invalid Username or Password." is displayed.</p>
     * <p>9. Log in to backend with "User1".</p>
     * <p>10. Navigate to System-Permissions-Locked Users.</p>
     * <p>11. Choose "User2" in the "Locked administrators" grid and check checkbox.</p>
     * <p>12. Choose "Unlocked" on Actions dropdown.</p>
     * <p>13. Click "Submit" button.</p>
     * <p>Expected result:</p>
     * <p>The message "Unlocked 1 user(s)." is presented.</p>
     * <p>14.Log out admin user.</p>
     * <p>15. Log in to backend with "User2".</p>
     * <p>Expected result:</p>
     * <p>The "User2" successfully logged.</p>
     *
     * @depends createAdminWithTestRole
     *
     * @param $testData
     *
     * @test
     * @bug MAGETWO-2789
     * @TestlinkId TL-MAGE-6065
     */
    public function systemPermissionsLockedUser($testData)
    {
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($testData);
        //crete user to verify possibility to unlock user
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'System/Permissions'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $newUserTestData = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($newUserTestData);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        $this->admin('log_in_to_admin', false);
        $loginData =
            array('user_name' => $newUserTestData['user_name'], 'password'  => $this->generate('string', 4, ':punct:'));
        //log in to backend with newly created "User2" and wrong password
        for ($i = 0; $i <= 2; $i++) {
            $this->adminUserHelper()->loginAdmin($loginData);
            $this->assertMessagePresent('error', 'wrong_credentials');
        }
        //verify that "User2" is locked and impossible log in to backend with "User2"
        $this->adminUserHelper()->loginAdmin($newUserTestData);
        $this->assertMessagePresent('error', 'wrong_credentials');
        //log in to backend with "User1"
        $this->admin('log_in_to_admin');
        $this->adminUserHelper()->loginAdmin($testData);
        $this->navigate('permissions_locked_users');
        //perform unlock action for "User2"
        $this->searchAndChoose($this->loadDataSet('AdminUserRole', 'search__locked_user',
            array('username' => $newUserTestData['user_name'])));
        $this->fillDropdown('locked_actions', 'Unlock');
        $this->clickControlAndWaitMessage('button', 'submit');
        //The test failed because of bug MAGETWO-2789
        //add quantity of unlocked users
        $this->addParameter('unLockedUser', 1);
        $this->assertMessagePresent('success', 'locked_actions');
        $this->logoutAdminUser();
        $this->adminUserHelper()->loginAdmin($newUserTestData);
    }
}

