<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ApiUsers
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * API Users Admin Page
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_ApiUsers_CreateTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->markTestIncomplete('MAGETWO-11393');
    }

    /**
     * <p>Preconditions:</p>
     * <p>1. Login to Admin page</p>
     * <p>2. Disable Secret key</p>
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
     * <p>Create API User</p>
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-6296
     */
    public function withRequiredFieldsCreateUser()
    {
        //Create new Role
        $roleData = $this->loadDataSet('ApiRoles', 'api_role_new');

        $this->navigate('api_roles_management');
        $this->clickButton('add_new_role', true);
        $this->fillField('role_name', $roleData['role_name']);
        $this->openTab('resources');
        $this->fillDropdown('role_access', 'All');
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_role');

        $userData = $this->loadDataSet('ApiUsers', 'new_api_users_create');
        //Create Data and open APi Users page
        $this->navigate('api_users');
        $this->clickButton('add_new_api_user', true);
        $this->fillField('api_user_contact_email', $userData['api_user_contact_email']);
        $this->fillField('api_user_api_key', $userData['api_user_api_key']);
        $this->fillField('api_user_api_secret', $userData['api_user_api_secret']);

        // Set role
        $this->openTab('user_role');
        $this->fillField('role_name', $roleData['role_name']);
        $this->clickButton('search', false);
        $this->waitForAjax();
        $this->addParameter('roleName', $roleData['role_name']);
        $this->clickControl('radiobutton', 'select_role', false);

        //Save data
        $this->clickButton('save', true);
        $this->assertMessagePresent('success', 'success_user_saved');

        // Check that user was saved with role
        $this->addParameter('email', $userData['api_user_contact_email']);
        $this->addParameter('role', $roleData['role_name']);
        $this->assertTrue($this->controlIsPresent('link', 'check_user'));

        return $userData;
    }

    /**
     * <p>Create API User</p>
     *
     * @param array $userData
     *
     * @test
     * @depends withRequiredFieldsCreateUser
     * @TestlinkId TL-MAGE-6359
     */
    public function createUserAlreadyExists($userData)
    {
        //Open API Users page and add new user
        $this->navigate('api_users');
        $this->clickButton('add_new_api_user', true);

        $this->fillField('api_user_contact_email', $userData['api_user_contact_email']);
        $this->fillField('api_user_api_key', $userData['api_user_api_key']);
        $this->fillField('api_user_api_secret', $userData['api_user_api_secret']);

        //Save data
        $this->clickButton('save', false);
        $this->waitForPageToLoad();
        $this->addParameter('id', $this->defineParameterFromUrl('user_id'));
        $this->waitForAjax();
        $this->assertMessagePresent('error', 'user_exist');
    }

    /**
     * <p>Check required field</p>
     *
     * @param string $emptyField
     * @param string $messageCount
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @depends withRequiredFieldsCreateUser
     * @TestlinkId TL-MAGE-6364
     */
    public function absentRequiredFields($emptyField, $messageCount)
    {
        //Loading data from data file
        $fieldData = $this->loadDataSet('ApiUsers', 'new_api_users_create', array($emptyField => '%noValue%'));

        $this->navigate('api_users');
        $this->clickButton('add_new_api_user', true);

        //Fill required fields except one
        $this->fillForm($fieldData);
        $this->clickButton('save', false);
        $this->waitForAjax();
        $this->validatePage();

        //Verifying
        $this->addFieldIdToMessage('field', $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount($messageCount), $this->getParsedMessages());
    }

    /**
     * @return array
     */
    public function withRequiredFieldsEmptyDataProvider()
    {
        return array (
            array ('api_user_api_key', 1),
            array ('api_user_api_secret', 1),
            array ('api_user_contact_email', 1),
        );
    }

    /**
     * <p>Delete API User</p>
     *
     * @param array $userData
     *
     * @test
     * @depends withRequiredFieldsCreateUser
     * @TestlinkId TL-MAGE-6365
     */
    public function deleteUser($userData)
    {
        //Open APi users page and find in the grid users
        $this->navigate('api_users');
        $userSearch = array('filter_api_users_api_key' => $userData['api_user_api_key']);
        $this->searchAndOpen($userSearch, 'api_users_grid', false);
        $this->waitForPageToLoad();
        $this->addParameter('id', $this->defineParameterFromUrl('user_id'));
        $this->addParameter('apiKey', $userData['api_user_api_key']);
        $this->validatePage();

        //User delete
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete');

        //Verifying
        $this->assertMessagePresent('success', 'success_user_deleted');
    }
}