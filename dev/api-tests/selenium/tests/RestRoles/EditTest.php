<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
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
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test editing REST Role from Backend
 *
 * @method RestRoles_Helper restRolesHelper()
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class RestRoles_EditTest extends Mage_Selenium_TestCase
{
     /**
     * Rest Role Name
     *
     * @var string
     */
    protected $_restRoleToBeDeleted;

     /**
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /*
     * Function for deleting after test execution
     */
     protected function tearDown()
    {
        if ($this->_restRoleToBeDeleted) {
            $this->restRolesHelper()->deleteRestRole($this->_restRoleToBeDeleted);
            $this->_restRoleToBeDeleted = null;
        }
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Web Secvices -> REST Roles.</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_rest_roles');
    }

     /**
     * <p>Test navigation.</p>
     * <p>Steps:</p>
     * <p>1. Verify that 'Back' button is present.</p>
     * <p>2. Verify that 'Save Role' button is present.</p>
     * <p>3. Verify that 'Delete Role' button is present.</p>
     * <p>4. Verify that 'Reset' button is present.</p>
     * <p>5. Verify that 'Role Info' tab is present.</p>
     * <p>6. Verify that 'Role API Resources' tab is present.</p>
     * <p>7. Verify that 'Role Users' tab is present.</p>
     *
     * @test
     */
    public function navigation()
    {
        //preconditions
        $restRoleData = $this->loadData('generic_rest_role');
        $this->restRolesHelper()->createRestRole($restRoleData);
        $this->restRolesHelper()->openRestRoleByName($restRoleData['rest_role_name']);
        //save Role name for clean up
        $this->_restRoleToBeDeleted = $restRoleData['rest_role_name'];
        //verifying
        $this->assertTrue($this->checkCurrentPage('edit_rest_role'), $this->getParsedMessages());
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
        $this->assertTrue($this->buttonIsPresent('delete_role'), 'There is no "Delete Role" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_role'), 'There is no "Save Role" button on the page');
        $this->assertTrue($this->restRolesHelper()->tabIsPresent('rest_role_info'),
            'There is no "Role Info" tab on the page');
        $this->assertTrue($this->restRolesHelper()->tabIsPresent('rest_role_resources'),
            'There is no "Role API Resources" tab on the page');
        $this->assertTrue($this->restRolesHelper()->tabIsPresent('rest_role_users'),
            'There is no "Role Users" tab on the page');
    }

    /**
     * <p>Edit REST Role with one empty reqired field.</p>
     * <p>Preconditions:</p>
     * <p>REST Role creation.</p>
     * <p>Steps:</p>
     * <p>1. Find Role from preconditions.</p>
     * <p>2. Clear Role Name field.</p>
     * <p>3. Click 'Save Role' button.</p>
     * <p> Expected result:</p>
     * <p> REST Role isn't saved. Message under field "Role Name":</p>
     * <p> "This is a required field."</p>
     * <p> Edit Role page is still opened.</p>
     *
     * @test
     */
    public function withEmptyRoleName()
    {
        //preconditions
        $restRoleData = $this->loadData('generic_rest_role');
        $this->restRolesHelper()->createRestRole($restRoleData);
        //edit rest role with empty Rest Role name field
        $this->restRolesHelper()->editRestRole($restRoleData['rest_role_name'], array('rest_role_name' => ''));
        //save Role name for clean up
        $this->_restRoleToBeDeleted = $restRoleData['rest_role_name'];
        //Verifying
        $this->assertMessagePresent('error', 'error_required_field_role_name');
        $this->assertTrue($this->checkCurrentPage('edit_rest_role'), $this->getParsedMessages());
    }

     /**
     * <p>Edit REST Role with asining two Role Users.</p>
     * <p>Preconditions:</p>
     * <p>REST Role creation.</p>
     * <p>Steps:</p>
     * <p>1. Find Role from preconditions.</p>
     * <p>2. Click Reset Filter button in Role Users tab.</p>
     * <p> Expected result:</p>
     * <p> Role Users tab contains the list of all admin Users.</p>
     * <p>3. Select Role User in Role Users tab (first User from preconditions). Confirm promt dialog.</p>
     * <p> Expected result:</p>
     * <p> The “Warning! This action will remove this user from already assigned role Are you sure?” </p>
     * <p> prompt dialog is showed. </p>
     * <p> 4. Repeat step 1-3 and click Save Role</p>
     * <p> Verify that two Role Users are saved.</p>
     *
     * @test
     */
    public function withRoleUsers()
    {
        //preconditions
        //create two Admin Users
        $this->navigate('manage_admin_users');
        $firstUserData = $this->loadData('generic_admin_user', null, array('email', 'user_name'));
        $this->adminUserHelper()->createAdminUser($firstUserData);
        $this->navigate('manage_admin_users');
        $secondUserData = $this->loadData('generic_admin_user', null, array('email', 'user_name'));
        $this->adminUserHelper()->createAdminUser($secondUserData);
        //create REST Role
        $this->navigate('manage_rest_roles');
        $restRoleData = $this->loadData('generic_rest_role');
        $this->restRolesHelper()->createRestRole($restRoleData);

        //save Role name for clean up
        $this->_restRoleToBeDeleted = $restRoleData['rest_role_name'];
        //Steps
        $this->openTab('rest_role_users');
        $this->searchAndChoose(array('user_name' => $firstUserData['user_name']), 'role_users');
        $this->searchAndChoose(array('user_name' => $secondUserData['user_name']), 'role_users');
        $this->saveForm('save_role');
        $this->clickButton('save_role');

        //Validate
        $this->assertMessagePresent('success', 'success_save_rest_role');
        $this->assertTrue($this->checkCurrentPage('edit_rest_role'), $this->getParsedMessages());

        //Verify value of User Roles
        $this->openTab('rest_role_users');
        $this->assertTrue($this->restRolesHelper()->isGridItemChecked(
            array('user_name' => $firstUserData['user_name']), 'role_users'), 'First user is not assigned.');
        $this->assertTrue($this->restRolesHelper()->isGridItemChecked(
            array('user_name' => $secondUserData['user_name']), 'role_users'), 'Second user is not assigned.');
    }

    /**
     * Edit Rest Role with all valid data
     * <p>Preconditions:</p>
     * <p>REST Role creation.</p>
     * <p>Steps:</p>
     * <p>1. Find Role from preconditions.</p>
     * <p>2. Fill Role Name field in Role Info tab using new value.</p>
     * <p>3. Select Role Resources=All in Role Resources tab.</p>
     * <p>4. Click Save Role button.</p>
     * <p> Expected result:</p>
     * <p> The role has been saved.
     * Success message is appeared in the page:
     * "The role has been successfully saved."
     * Edit REST Role page is still opened.</p>
     *
     * @test
     * @dataProvider restRoleName
     */
    public function withAllValidData($restRoleName)
    {
        //preconditions
        $restRoleData = $this->loadData('generic_rest_role');
        $this->restRolesHelper()->createRestRole($restRoleData);
        //edit rest role with empty Rest Role name field
        $newRestRoleData = $this->loadData('rest_role_withAllResources', array('rest_role_name' => $restRoleName));
        $this->restRolesHelper()->editRestRole($restRoleData['rest_role_name'], $newRestRoleData);
        //save Role name for clean up
        $this->_restRoleToBeDeleted = $restRoleName;
        //Verify message and page
        $this->assertMessagePresent('success', 'success_save_rest_role');
        $this->assertTrue($this->checkCurrentPage('edit_rest_role'), $this->getParsedMessages());
        //Verify Role Name value
        $this->assertEquals($restRoleName,
            $this->restRolesHelper()->getFieldValue('rest_role_info', 'rest_role_information',
            'fields', 'rest_role_name'), 'Rest role name does not match.');
        //Verify Resources value
        $this->openTab('rest_role_resources');
        $this->assertEquals($newRestRoleData['resource_access'],
            $this->restRolesHelper()->getFieldText('rest_role_resources', 'role_resources',
            'dropdowns', 'resource_access'), 'Rest role resources does not match.');
    }

    /**
     * @return string REST Role Name
     */
    public function restRoleName()
    {
        return array(
            array('Normal Name'),
            array('!@#$%^&*()_+'),
            array('发展改革委介绍价格形势以及生猪市场调控情况')
        );
    }
}
