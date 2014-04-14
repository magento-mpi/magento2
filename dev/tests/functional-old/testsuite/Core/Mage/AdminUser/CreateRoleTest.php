<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AdminUser
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

/**
 * Creating User Role
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_AdminUser_CreateRoleTest extends Mage_Selenium_TestCase
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
     *
     * @test
     */
    public function navigationTest()
    {
        $this->assertTrue($this->buttonIsPresent('add_new_role'), 'There is no "Add New Role" button on the page');
        $this->clickButton('add_new_role');
        $this->assertTrue($this->checkCurrentPage('new_admin_role'), $this->getParsedMessages());
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_role'), 'There is no "Save Role" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
    }

    /**
     * <p>Create Admin Role (all required fields are filled).</p>
     *
     * @return array $roleData
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5470
     */
    public function withRequiredFields()
    {
        //Data
        $roleData = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role',
            array('role_name' => $this->generate('string', 15, ':alnum:')));
        //Steps
        $this->adminUserHelper()->createRole($roleData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_role');

        return $roleData;
    }

    /**
     * <p>Create Admin Role (role name field is empty).</p>
     *
     * @return array
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5472
     */
    public function withEmptyRequiredFields()
    {
        //Data
        $roleData = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role', array('role_name' => ''));
        //Steps
        $this->adminUserHelper()->createRole($roleData);
        //Verifying
        $this->addFieldIdToMessage('field', 'role_name');
        $this->assertMessagePresent('validation', 'empty_required_field');
    }

    /**
     * <p>Create Admin User Role with special symbols in role name field.</p>
     *
     * @return array
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5474
     */
    public function withSpecialSymbolsInField()
    {
        //Data
        $roleData = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role',
            array('role_name' => $this->generate('string', 32, ':punct:')));
        //Steps
        $this->adminUserHelper()->createRole($roleData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_role');
    }

    /**
     * <p>Create Admin User  role with long name(50symbols).</p>
     *
     * @return array
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-5475
     */
    public function withLongRoleName()
    {
        //Data
        $roleData = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role',
            array('role_name' => $this->generate('string', 50, ':alnum:')));
        //Steps
        $this->adminUserHelper()->createRole($roleData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_role');
    }

    /**
     * <p>Edit Admin User Role</p>
     *
     * @param array $roleData
     *
     * @return array
     *
     * @depends withRequiredFields
     * @test
     * @TestlinkId TL-MAGE-5477
     */
    public function editRoleName($roleData)
    {
        $editedRole = $this->loadDataSet('AdminUserRole', 'edit_admin_user_role_name', null,
            array('roleName'    => $roleData['role_info_tab']['role_name'],
                  'newRoleName' => $roleData['role_info_tab']['role_name'] . '_edited'));
        //Data
        $this->adminUserHelper()->editRole($editedRole);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_role');

        return $roleData['role_info_tab']['role_name'] . '_edited';
    }

    /**
     * <p>Delete Admin User Role</p>
     *
     * @param array $newRoleName
     *
     * @test
     * @depends editRoleName
     * @TestlinkId TL-MAGE-5478
     */
    public function deleteRole($newRoleName)
    {
        $roleToDelete =
            $this->loadDataSet('AdminUserRole', 'edit_admin_user_role_name', null, array('roleName' => $newRoleName));
        $this->adminUserHelper()->deleteRole($roleToDelete);
        $this->assertMessagePresent('success', 'success_deleted_role');
    }
}