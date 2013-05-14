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

class Enterprise_Mage_Acl_SystemPermissionTest extends Mage_Selenium_TestCase
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
     * <p>Admin user with Role System-Permissions is created.</p>
     *
     * @test
     * @return array
     */
    public function createAdminWithTestRole()
    {
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'system-permissions'));
        $testData = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        //create role and user
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($testData);
        $this->assertMessagePresent('success', 'success_saved_user');
        // set configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('LockedUser/login_failures');

        return array('user_name' => $testData['user_name'], 'password' => $testData['password']);
    }

    /**
     * <p> Verifying unlock action for user with Permission System-Permissions </p>
     *
     * @param array $testData
     *
     * @test
     * @depends createAdminWithTestRole
     * @TestlinkId TL-MAGE-6065
     */
    public function systemPermissionsLockedUser($testData)
    {
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'system-permissions'));
        $newUserTestData = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $loginData = array(
            'user_name' => $newUserTestData['user_name'],
            'password' => $this->generate('string', 4, ':punct:')
        );
        $rightLoginData = array(
            'user_name' => $newUserTestData['user_name'],
            'password' => $newUserTestData['password']
        );
        $search = $this->loadDataSet('AdminUserRole', 'search__locked_user',
            array('username' => $newUserTestData['user_name']));
        //crete user to verify possibility to unlock user
        $this->adminUserHelper()->loginAdmin($testData);
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($newUserTestData);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //log in to backend with newly created "User2" and wrong password
        for ($i = 0; $i <= 2; $i++) {
            $this->admin('log_in_to_admin');
            $this->adminUserHelper()->loginAdmin($loginData);
            $this->assertMessagePresent('error', 'wrong_credentials');
        }
        //verify that "User2" is locked and impossible log in to backend with "User2"
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertMessagePresent('error', 'wrong_credentials');
        //log in to backend with "User1"
        $this->admin('log_in_to_admin');
        $this->adminUserHelper()->loginAdmin($testData);
        $this->navigate('permissions_locked_users');
        //perform unlock action for "User2"
        $this->searchAndChoose($search, 'locked_user_grid');
        $this->fillDropdown('locked_actions', 'Unlock');
        $this->clickControlAndWaitMessage('button', 'submit');
        //add quantity of unlocked users
        $this->addParameter('unLockedUser', 1);
        $this->assertMessagePresent('success', 'user_unlocked');
        $this->logoutAdminUser();
        $this->adminUserHelper()->loginAdmin($rightLoginData);
        $this->assertMessageNotPresent('error');
    }
}

