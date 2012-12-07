<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test assigning REST Role to admin User from Backend
 *
 * @method AdminUser_Helper adminUserHelper()
 * @method RestRoles_Helper restRolesHelper()
 * @package     selenium
 * @subpackage  tests
 * @license     {license_link}
 */
class RestRoles_AssignTest extends Mage_Selenium_TestCase
{
     /**
     * Rest Role Name
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
     * <p>Navigate to System -> Permissions -> REST Roles.</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_admin_users');
    }
     /**
     * <p>Test navigation.</p>
     * <p>Steps:</p>
     * <p>1. Verify that 'Back' button is present.</p>
     * <p>2. Verify that 'Save Admin Role' button is present.</p>
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
        $this->clickButton('add_new_admin_user');
        $this->assertTrue($this->restRolesHelper()->tabIsPresent('rest_role'),
            'There is no "REST Role" tab on the page');
    }

    /**
     * Test assigning
     * Steps:
     * 1. Create REST Role for assigning.
     * 2. Create Admin User in System -> Permissions -> Users.
     * 3. Select Admin User from Step 2.
     * 4. Open REST Roles tab.
     * 5. Set REST Role from Step 1.
     * 6. Click Save User button.
     * 7. Select and open Admin User from Step 6.
     * 8. Open REST Role tab.
     * 9. Verify that REST Role from Step 1 assigned for Admin User from Step 2.
     *
     * @test
     * @depends navigation
     */
    public function assignTest()
    {
        //create REST Role
        $this->navigate('manage_rest_roles');
        $restRoleData = $this->loadData('generic_rest_role');
        $this->_restRoleToBeDeleted = $restRoleData['rest_role_name'];
        $this->restRolesHelper()->createRestRole($restRoleData);
        //create User
        $this->navigate('manage_admin_users');
        $userData = $this->loadData('generic_admin_user', null, array('email', 'user_name'));
        $this->addParameter('id', '0');
        $this->adminUserHelper()->createAdminUser($userData);
        //search and open created User in permissionsUserGrid
        $loadUserData = array(
            'user_name'  => $userData['user_name'],
            'first_name' => $userData['first_name'],
            'last_name'  => $userData['last_name'],
            'email'      => $userData['email']
        );
        $searchUserData = $this->loadData('search_admin_user', $loadUserData, null);
        $this->searchAndOpen($searchUserData, true, 'permissionsUserGrid');
        //steps
        $this->openTab('rest_role');
        $this->searchAndChoose(array('role_name' => $restRoleData['rest_role_name']), 'rest_user_roles');
        $this->clickButton('save_admin_user');
        //verifying
        $this->searchAndOpen($searchUserData, true, 'permissionsUserGrid');
        $this->openTab('rest_role');
        $isGridItemChecked = $this->restRolesHelper()->isGridItemChecked(
            array('role_name' => $restRoleData['rest_role_name']), 'rest_user_roles');
        $this->assertTrue($isGridItemChecked, 'There is no assigned roles');
    }
}
