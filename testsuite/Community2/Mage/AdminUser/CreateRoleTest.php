<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file isthemosti subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests;
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Creating User Role
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_AdminUser_CreateRoleTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     * <p>Navigate to System -> Permissions -> Roles.</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_roles');
    }

    /**
     * <p>Test navigation.</p>
     * <p>Preconditions</p>
     * <p>1. Log in to Backend.</p>
     * <p>2. Navigate to System -> Permissions -> Roles.</p>
     * <p>Steps:</p>
     * <p>1. Verify that 'Add New Role' button is present and click her.</p>
     * <p>2. Verify that the create role page is opened.</p>
     * <p>3. Verify that 'Back' button is present.</p>
     * <p>4. Verify that 'Save Role' button is present.</p>
     * <p>5. Verify that 'Reset' button is present.</p>
     * <p>Expected result:</p>
     * <p>'Add New Role', 'Back', 'Save Role', 'Reset' buttons are present<p>
     * <p>Current page is new role page<p>
     *
     * @test
     */
    public function navigationTest()
    {
        $this->assertTrue($this->buttonIsPresent('add_new_role'), 'There is no "Add New Role" button on the page');
        $this->clickButton('add_new_role');
        $this->assertTrue($this->checkCurrentPage('new_role'), $this->getParsedMessages());
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_role'), 'There is no "Save Role" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
    }

    /**
     * <p>Create Admin Role (all required fields are filled).</p>
     * <p>Steps:</p>
     * <p>1.Go to System-Permissions-Roles.</p>
     * <p>2.Press "Add New Role" button.</p>
     * <p>3.Fill Role name field.</p>
     * <p>4.Go to Role Resources tab</p>
     * <p>5.Set Role Scope = All</p>
     * <p>6.Set Resource Access = All</p>
     * <p>7.Press "Save Role" button.</p>
     * <p>Expected result:</p>
     * <p>New role successfully saved.</p>
     * <p>Message "The role has been saved." is displayed.</p>
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5470
     */
    public function withRequiredFields()
    {
        //Data
        $roleData = $this->loadDataSet('AdminUsersRole', 'generic_admin_user_role',
            array('role_name' => $this->generate('string', 15, ':alnum:')));
        //Steps
        $this->adminUserHelper()->createRole($roleData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_role');

        return $roleData;
    }

    /**
     * <p>Create Admin Role (role name field is empty).</p>
     * <p>Steps:</p>
     * <p>1.Go to System-Permissions-Role.</p>
     * <p>2.Press "Add New Role" button.</p>
     * <p>3.Role name field is empty</p>
     * <p>4.Press "Save Role" button.</p>
     * <p>Expected result:</p>
     * <p>New role is not saved.</p>
     * <p>Message "This is a required field.." is displayed.</p>
     * @test
     * @return array
     *
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5472
     */
    public function withEmptyRequiredFields()
    {
        //Data
        $roleData = $this->loadDataSet('AdminUsersRole', 'generic_admin_user_role', array('role_name' => ''));
        //Steps
        $this->adminUserHelper()->createRole($roleData);
        //Verifying
        $this->assertMessagePresent('error', 'error_required_field_role_name');
    }

    /**
     * <p>Create Admin User Role with special symbols in role name field.</p>
     * <p>Steps:</p>
     * <p>1.Go to System-Permissions-Role.</p>
     * <p>2.Press "Add New Role" button.</p>
     * <p>3.Fill special symbols in Role Name field .</p>
     * <p>4.Press "Save Role" button.</p>
     * <p>Expected result:</p>
     * <p>New Role successfully saved.</p>
     * <p>Message "The role has been saved." is displayed.</p>
     * @test
     * @return array
     *
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5474
     */
    public function withSpecialSymbolsInField()
    {
        //Data
        $roleData = $this->loadDataSet('AdminUsersRole', 'generic_admin_user_role',
            array('role_name' => $this->generate('string', 32, ':punct:')));
        //Steps
        $this->adminUserHelper()->createRole($roleData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_role');
    }

    /**
     * <p>Create Admin User  role with long name(50symb).</p>
     * <p>Steps:</p>
     * <p>1.Go to System-Permissions-Role.</p>
     * <p>2.Press "Add New Role" button.</p>
     * <p>3.Fill 50 symbols in Role Name field.</p>
     * <p>4.Press "Save Role" button.</p>
     * <p>Expected result:</p>
     * <p>New Role successfully saved.</p>
     * <p>Message "The user has been saved." is displayed.</p>
     * @test
     * @return array
     *
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5475
     */
    public function withLongRoleName()
    {
        //Data
        $roleData = $this->loadDataSet('AdminUsersRole', 'generic_admin_user_role',
            array('role_name' => $this->generate('string', 50, ':alnum:')));
        //Steps
        $this->adminUserHelper()->createRole($roleData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_role');
    }

    /**
     * <p>Edit Admin User Role</p>
     * <p>Steps:</p>
     * <p>1.Go to System-Permissions-Role.</p>
     * <p>2.Find Role in grid and open.</p>
     * <p>3.Add to Role name "_edited".</p>
     * <p>4.Press "Save Role" button.</p>
     * <p>Expected result:</p>
     * <p>Role successfully saved.</p>
     * <p>Message "The role has been successfully saved." is displayed.</p>
     *
     * @param array $roleData
     *
     * @test
     * @depends withRequiredFields
     * @return array
     * @TestlinkId TL-MAGE-5477
     */
    public function editRoleName($roleData)
    {
        //Data
        $newRoleName = array('role_name' => $roleData['role_info_tab']['role_name'] . '_edited');
        //Steps
        $this->searchAndOpen(array('role_name'=> $roleData['role_info_tab']['role_name']));
        $this->fillField('role_name', $newRoleName['role_name']);
        $this->saveForm('save_role');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_role');

        return $newRoleName;
    }

    /**
     * <p>Delete Admin User Role</p>
     * <p>Steps:</p>
     * <p>1.Go to System-Permissions-Role.</p>
     * <p>2.Find Role in grid and open.</p>
     * <p>3.Press "Delete Role" button.</p>
     * <p>Expected result:</p>
     * <p>The Role successfully deleted.</p>
     * <p>Message "The role has been deleted." is displayed.</p>
     *
     * @param array $newRoleName
     *
     * @test
     * @depends editRoleName
     * @TestlinkId TL-MAGE-5478
     */
    public function deleteRole($newRoleName)
    {
        $this->searchAndOpen($newRoleName);
        $this->clickButton('delete_role');
        $this->assertMessagePresent('success', 'success_deleted_role');
    }
}