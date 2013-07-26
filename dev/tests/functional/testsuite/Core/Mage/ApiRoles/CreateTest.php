<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ApiRoles
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * API Roles Admin Page
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_ApiRoles_CreateTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->markTestIncomplete('MAGETWO-11393');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>API Role Required fields</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6358
     */
    public function requiredFields()
    {
        //Open API Roles Management page
        $this->navigate('api_roles_management');
        //Click Add New Role button
        $this->clickButton('add_new_role');
        //Click Save API Role button
        $this->saveForm('save');
        //Verify that validation message appear
        $this->addFieldIdToMessage('field', 'role_name');
        $this->assertMessagePresent('validation', 'empty_required_field');
    }

    /**
     * <p>Create API Role (Full Access)</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6291
     */
    public function roleWithAllAccess()
    {
        //Load data
        $fieldData = $this->loadDataSet('ApiRoles', 'api_role_new');
        //Open API Roles Management page
        $this->navigate('api_roles_management');
        //Click Add New Role button
        $this->clickButton('add_new_role');
        //Fill Role name field
        $this->fillField('role_name', $fieldData['role_name']);
        //Open Resources Tab
        $this->openTab('resources');
        //Selecting All at Role Access Dropdown
        $this->fillDropdown('role_access', 'All');
        //Saving API Role
        $this->saveForm('save');
        //Verify that role is saved
        $this->assertMessagePresent('success', 'success_saved_role');
    }

    /**
     * <p>Create API Role (Custom Access)</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6292
     */
    public function roleWithCustomAccess()
    {
        $fieldData = $this->loadDataSet('ApiRoles', 'api_role_new');
        //Open API Roles Management page
        $this->navigate('api_roles_management');
        //Click Add New Role button
        $this->clickButton('add_new_role');
        //Fill Role name field
        $this->fillField('role_name', $fieldData['role_name']);
        //Open Resources Tab
        $this->openTab('resources');
        //Selecting Custom at Role Access Dropdown
        $this->fillDropdown('role_access', 'Custom');
        //Selecting Get Customer checkbox
        $this->addParameter('subName', 'Create');
        $this->clickControl('link', 'sub_root', false);
        //Saving API Role
        $this->saveForm('save');
        //Verify that role is saved
        $this->assertMessagePresent('success', 'success_saved_role');
    }

    /**
     * <p>Delete API Role</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6366
     */
    public function roleDelete()
    {
        //Load data
        $fieldData = $this->loadDataSet('ApiRoles', 'api_role_new');
        //Open API Roles Management page
        $this->navigate('api_roles_management');
        //Click Add New Role button
        $this->clickButton('add_new_role');
        //Fill Role name field
        $this->fillField('role_name', $fieldData['role_name']);
        //Open Resources Tab
        $this->openTab('resources');
        //Selecting All at Role Access Dropdown
        $this->fillDropdown('role_access', 'All');
        //Saving API Role
        $this->saveForm('save');
        //Verify that role is saved
        $this->assertMessagePresent('success', 'success_saved_role');
        //Open created role from the role grid
        $this->navigate('api_roles_management');
        $userSearch = array('filter_role_name' => $fieldData['role_name']);
        $this->searchAndOpen($userSearch, 'api_roles_grid');
        //Click Delete API Role button
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete');
        //Verify that message "You deleted the role." is displayed
        $this->assertMessagePresent('success', 'success_deleted_role');
    }
}
