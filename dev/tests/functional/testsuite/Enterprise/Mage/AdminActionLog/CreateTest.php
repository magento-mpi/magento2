<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AdminActionLog
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * API Roles Action Log
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_AdminActionLog_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->closeLastWindow();
    }

    /**
     * <p>Admin action log for API Role (Save action)</p>
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-6377
     */
    public function saveRoleActionLog()
    {
        //Load data
        $fieldData = $this->loadDataSet('ApiRoles', 'api_role_new');
        //Open API Roles Management page
        $this->navigate('api_roles_management');
        //Click Add New Role button
        $this->clickButton('add_new_role', true);
        //Fill Role name field
        $this->fillField('role_name', $fieldData['role_name']);
        //Open Resources Tab
        $this->openTab('resources');
        //Selecting All at Role Access Dropdown
        $this->fillDropdown('role_access', 'All');
        //Saving API Role
        $this->clickButton('save');
        //Verify that role is saved
        $this->assertMessagePresent('success', 'success_saved_role');
        $roleId = $this->defineParameterFromUrl('role_id');
        //Open Admin Actions Logs page
        $this->navigate('admin_action_log_report');
        //Use filter with Role data and open it
        $userSearch = array('filter_role_name' => $roleId, 'action' => 'Save');
        $this->searchAndOpen($userSearch, 'action_logs_grid');
        //Check that log info page is opened
        $this->assertEquals('View Entry / Report / Admin Actions Logs / System / Magento Admin',
            $this->title(), 'Wrong page');

        return ($fieldData);
    }

    /**
     * <p>Admin action log for API Role (Edit action)</p>
     *
     * @param array $fieldData
     *
     * @test
     * @depends saveRoleActionLog
     * @TestlinkId TL-MAGE-6378
     */
    public function editRoleActionLog($fieldData)
    {
        //Open API Roles Management page
        $this->navigate('api_roles_management');
        //Open created role from the role grid
        $userSearch = array('filter_role_name' => $fieldData['role_name']);
        $this->searchAndOpen($userSearch, 'api_roles_grid');
        $this->refresh();
        $roleId = $this->defineParameterFromUrl('role_id');
        //Open Admin Actions Logs page
        $this->navigate('admin_action_log_report');
        //Use filter with Role data and open it
        $userSearch = array('filter_role_name' => $roleId, 'action' => 'Edit');
        $this->searchAndOpen($userSearch, 'action_logs_grid');
        //Check that log info page is opened
        $this->assertEquals('View Entry / Report / Admin Actions Logs / System / Magento Admin',
            $this->title(), 'Wrong page');
    }

    /**
     * <p>Admin action log for API Role (Delete action)</p>
     *
     * @param array $fieldData
     *
     * @test
     * @depends saveRoleActionLog
     * @TestlinkId TL-MAGE-6379
     */
    public function deleteRoleActionLog($fieldData)
    {
        //Open API Roles Management page
        $this->navigate('api_roles_management');
        //Open created role from the role grid
        $userSearch = array('filter_role_name' => $fieldData['role_name']);
        $this->searchAndOpen($userSearch, 'api_roles_grid');
        $roleId = $this->defineParameterFromUrl('role_id');
        //Click Delete API Role button
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete', true);
        //Verify that message "The role has been deleted." is displayed
        $this->assertMessagePresent('success', 'success_deleted_role');
        //Open Admin Actions Logs page
        $this->navigate('admin_action_log_report');
        //Use filter with Role data and open it
        $userSearch = array('filter_role_name' => $roleId, 'action' => 'Delete');
        $this->searchAndOpen($userSearch, 'action_logs_grid');
        //Check that log info page is opened
        $this->assertEquals('View Entry / Report / Admin Actions Logs / System / Magento Admin',
            $this->title(), 'Wrong page');
    }

    /**
     * <p>Admin action log for API User (Save action)</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6449
     */
    public function saveUserActionLog ()
    {
        //Create new Role
        $userData = $this->loadDataSet('ApiUsers', 'new_api_users_create');

        $this->navigate('api_roles_management');
        $this->clickButton('add_new_role', true);
        $this->fillField('role_name', $userData['role_name']);
        $this->openTab('resources');
        $this->fillDropdown('role_access', 'All');
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_role');

        //Create Data and open APi Users page
        $this->navigate('api_users');
        $this->clickButton('add_new_api_user', true);
        $this->fillField('api_user_contact_email', $userData['api_user_contact_email']);
        $this->fillField('api_user_api_key', $userData['api_user_api_key']);
        $this->fillField('api_user_api_secret', $userData['api_user_api_secret']);

        // Set role
        $this->openTab('user_role');
        $this->fillField('role_name', $userData['role_name']);
        $this->clickButton('search', false);
        $this->waitForAjax();
        $this->addParameter('roleName', $userData['role_name']);
        $this->clickControl('radiobutton', 'select_role', false);

        //Save data
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_user_saved');
        $userId = $this->defineParameterFromUrl('user_id');

        //Open Admin Action Log Page
        $this->navigate('admin_action_log_report');
        $this->validatePage();
        $userSearch = array('filter_user_id' => $userId, 'action_name' => 'adminhtml_webapi_user_save');
        $this->searchAndOpen($userSearch, 'action_logs_grid');
        $this->validatePage();

        //Check page title
        $this->assertSame('View Entry / Report / Admin Actions Logs / System / Magento Admin',
            $this->title(), 'Wrong page');

        return $userData;
    }

    /**
     * <p>Admin action log for API User (Edit action)</p>
     *
     * @param array $userData
     *
     * @test
     * @depends saveUserActionLog
     * @TestlinkId TL-MAGE-6451
     */
    public function editUserActionLog ($userData)
    {
        $this->navigate('api_users');
        $userSearch = array('filter_api_users_name' => $userData['api_user_api_key']);
        $this->searchAndOpen($userSearch, false);
        $this->waitForPageToLoad();
        $this->refresh();
        $this->addParameter('userId', $this->defineParameterFromUrl('user_id'));
        $this->addParameter('apiKey', $userData['api_user_api_key']);
        $this->validatePage();
        $userId = $this->defineParameterFromUrl('user_id');
        //Open Admin Action Log Page
        $this->navigate('admin_action_log_report');
        $userSearch = array('filter_user_id' => $userId, 'action' => 'Edit');
        $this->searchAndOpen($userSearch, 'action_logs_grid');
        $this->validatePage();

        //Check page title
        $this->assertSame('View Entry / Report / Admin Actions Logs / System / Magento Admin',
            $this->title(), 'Wrong page');
    }

    /**
     * <p>Admin action log for API User (Delete action)</p>
     *
     * @param array $userData
     *
     * @test
     * @depends saveUserActionLog
     * @TestlinkId TL-MAGE-6450
     */
    public function deleteUserActionLog ($userData)
    {
        $this->navigate('api_users');
        $userSearch = array('filter_api_users_name' => $userData['api_user_api_key']);
        $this->searchAndOpen($userSearch, false);
        $this->waitForPageToLoad();

        $this->addParameter('userId', $this->defineParameterFromUrl('user_id'));
        $this->addParameter('apiKey', $userData['api_user_api_key']);
        $this->validatePage('edit_api_user');
        $userId = $this->defineParameterFromUrl('user_id');
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete', true);
        //Open Admin Action Log Page
        $this->navigate('admin_action_log_report');
        $userSearch = array('filter_user_id' => $userId, 'action' => 'Delete');
        $this->searchAndOpen($userSearch, 'action_logs_grid');
        $this->validatePage();

        //Check page title
        $this->assertSame('View Entry / Report / Admin Actions Logs / System / Magento Admin',
            $this->title(), 'Wrong page');
    }
}
