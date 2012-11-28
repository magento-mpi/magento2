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
            array('username' => $newUserTestData['user_name'])), 'locked_user_grid');
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

