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

class Community2_Mage_ACL_SystemPermissionTest extends Mage_Selenium_TestCase
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
     * <p>1.1 Resource Access = Custom.</p>
     * <p>1.2 Resource checkboxes = 'System-Permissions'.</p>
     * <p>2. Create test Admin user with test Role(Full permissions for Permissions menu).</p>
     *
     * @test
     * @return array
     */
    public function createAdminWithTestRole()
    {
        $this->loginAdminUser();
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

        return $testData;
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
     * @depends createAdminWithTestRole
     *
     * @param $testData
     *
     * @test
     * @TestlinkId TL-MAGE-6071
     */
    public function systemPermissions($testData)
    {
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($testData);
        // Verify that navigation menu has only 2 child elements
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $this->assertEquals('1', count($this->getElementsByXpath($xpath)),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        $xpath = $this->_getControlXpath('pageelement', 'navigation_children_menu_items');
        $this->assertEquals('1', count($this->getElementsByXpath($xpath)),
            'Count of child Navigation Menu not equal 1, should be equal 1');
        $this->navigate('manage_roles');
        $editedRole = $this->loadDataSet('AdminUserRole', 'edit_admin_user_role_name', null,
            array('roleName'    => $testData['role_name']));
        $editedRole['role_resources_tab']['role_resources']['resource_2'] = 'System/Configuration';
        //Data
        $this->adminUserHelper()->editRole($editedRole);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('system_configuration');
        $this->assertEquals('2', count($this->getElementsByXpath($xpath)),
            'Count of child Navigation Menu not equal 2, should be equal 2');
        $tabElement = $this->loadDataSet('SystemConfigurationMenu', 'configuration_menu_default');
        //verify that this tab equal to resource from ACL tree
        foreach ($tabElement as $tab=> $tabName) {
            $this->systemConfigurationHelper()->openConfigurationTab($tab);
        }
    }

    /**
     * <p> Actions available for user with Permission System-Permissions </p>
     * <p>Preconditions</p>
     * <p>1. Role "Role1" with Role resource System-Permissions is created.</p>
     * <p>2. Admin user "User1" with "Role1" is created.</p>
     * <p>Steps:</p>
     * <p>1. Log in to backend using newly created "User1" credentials.</p>
     * <p>2. Navigate to System-Permissions-Role.</p>
     * <p>3. Create "Role2"</p>
     * <p>4. Navigate to System-Permissions-Users.</p>
     * <p>5. Create "User2"</p>
     * <p>6. Navigate to System-Permissions-Role.</p>
     * <p>7. Delete "Role2".</p>
     * <p>Expected result</p>
     * <p>The Role2 is successfully deleted.</p>
     * <p>8. Try to delete "Role1".</p>
     * <p>Expected result:</p>
     * <p>The "Role1" is not deleted. The message "Self-assigned roles cannot be deleted." is displayed.</p>
     *
     * @depends createAdminWithTestRole
     *
     * @param $testData
     *
     * @test
     * @TestlinkId TL-MAGE-6072
     */
    public function systemPermissionsActions($testData)
    {
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($testData);
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $testDataForNewUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testDataForNewUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->navigate('manage_roles');
        $dataForDelete = $this->loadDataSet('AdminUserRole', 'edit_admin_user_role_name', null,
            array('roleName'=> $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->deleteRole($dataForDelete);
        $this->assertMessagePresent('success', 'success_deleted_role');
        $this->navigate('manage_roles');
        $dataForDeleteOwnRole = $this->loadDataSet('AdminUserRole', 'edit_admin_user_role_name', null,
            array('roleName'=> $testData['role_name']));
        $this->adminUserHelper()->deleteRole($dataForDeleteOwnRole);
        $this->assertMessagePresent('error', 'delete_self_assigned_role');
    }
}

