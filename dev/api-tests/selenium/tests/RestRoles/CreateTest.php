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
 * Test creation new REST Role from Backend
 *
 * @method RestRoles_Helper restRolesHelper()
 * @package     selenium
 * @subpackage  tests
 * @license     {license_link}
 */
class RestRoles_CreateTest extends Mage_Selenium_TestCase
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
     * <p>1. Verify that 'Add Role' button is present and click her.</p>
     * <p>2. Verify that the Add new Rest Role page is opened.</p>
     * <p>3. Verify that 'Back' button is present.</p>
     * <p>4. Verify that 'Save' button is present.</p>
     * <p>5. Verify that 'Reset' button is present.</p>
     * <p>6. Verify that 'Role Info' tab is present.</p>
     * <p>7. Verify that 'Rest Resources' tab is present.</p>
     *
     * @test
     */
    public function navigation()
    {
        $this->assertTrue($this->checkCurrentPage('manage_rest_roles'), $this->getParsedMessages());
        $this->assertTrue($this->buttonIsPresent('add_new_rest_role'), 'There is no "Add Role" button on the page');
        $this->clickButton('add_new_rest_role');
        $this->assertTrue($this->checkCurrentPage('new_rest_role'), $this->getParsedMessages());
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_role'), 'There is no "Save Role" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
        $this->assertTrue($this->restRolesHelper()->tabIsPresent('rest_role_info'),
            'There is no "Role Info" tab on the page');
        $this->assertTrue($this->restRolesHelper()->tabIsPresent('rest_role_resources'),
            'There is no "Rest Resources" tab on the page');
    }

    /**
     * Create Rest Role with all valid data
     * <p>Preconditions:</p>
     * <p>REST Role creation.</p>
     * <p>Steps:</p>
     * <p>1. Find Role from preconditions.</p>
     * <p>2. Fill Role Name field in Role Info tab.</p>
     * <p>3. Select Role Resources=Custom in Role Resources tab.</p>
     * <p>4. Click Save Role button.</p>
     * <p> Expected result:</p>
     * <p> The role has been saved.
     * Success message is appeared in the page:
     * "The role has been successfully saved."
     * Edit REST Role page is still opened.</p>
     *
     * @test
     * @dataProvider restRoleName
     * @param restRoleName
     */
    public function allValidData($restRoleName)
    {
        //Load data
        $restRoleData = $this->loadData('generic_rest_role', array('rest_role_name' => $restRoleName));
        //Saving Rest Role Name for tearDown
        $this->_restRoleToBeDeleted = $restRoleName;
        //Steps
        $this->restRolesHelper()->createRestRole($restRoleData);
        //Validate
        $this->assertMessagePresent('success', 'success_save_rest_role');
        $this->assertTrue($this->checkCurrentPage('edit_rest_role'), $this->getParsedMessages());
        //Verify value Role Name
        $this->assertEquals($restRoleData['rest_role_name'],
            $this->restRolesHelper()->getFieldValue('rest_role_info', 'rest_role_information', 'fields',
            'rest_role_name'), 'Rest role name does not match.');
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

    /**
     * <p>Ceate REST Role with one empty reqired field</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add Role' button.</p>
     * <p>2. Click 'Save Role' button.</p>
     * <p> Expected result:</p>
     * <p> REST Role isn't saved. Message under field "Role Name":</p>
     * <p> "This is a required field."</p>
     * <p> Add New Role page is still opened.</p>
     *
     * @test
     */
    public function withEmptyRoleName()
    {
        //Steps
        $this->restRolesHelper()->createRestRole(array('rest_role_name' => ''));
        // Saving Rest Role Name for tearDown
        $this->_restRoleToBeDeleted = '';
        //Verifying
        $this->assertMessagePresent('error', 'error_required_field_role_name');
        $this->assertTrue($this->checkCurrentPage('new_rest_role'), $this->getParsedMessages());
    }
}
