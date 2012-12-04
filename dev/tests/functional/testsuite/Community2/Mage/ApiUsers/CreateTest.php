<?php
/**
 * Test class Community2_Mage_ApiUsers_CreateTest
 *
 * @copyright {}
 */
class Community2_Mage_ApiUsers_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>1. Login to Admin page</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $windowQty = $this->getAllWindowNames();
        if (count($windowQty) > 1 && end($windowQty) != 'null') {
            $this->selectWindow("name=" . end($windowQty));
            $this->close();
            $this->selectWindow(null);
        }
    }

    /**
     * @return array
     * @test
     * @author denis.poloka
     * @TestlinkId TL-MAGE-6296
     */
    public function withRequiredFieldsCreateUser()
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
        $this->click($this->_getControlXpath('radiobutton', 'select_role'));

        //Save data
        $this->clickButton('save', true);
        $this->assertMessagePresent('success', 'success_user_saved');

        // Check that user was saved with role
        $this->addParameter('email', $userData['api_user_contact_email']);
        $this->addParameter('role', $userData['role_name']);
        $xpath = $this->_getControlXpath('link', 'check_user');
        $this->assertTrue($this->isElementPresent($xpath));

        return $userData;
    }

    /**
     * @param array $userData
     * @test
     * @depends withRequiredFieldsCreateUser
     * @author denis.poloka
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
        $this->addParameter('userId', $this->defineParameterFromUrl('user_id'));
        $this->waitForAjax();
        $this->assertMessagePresent('error', 'user_exist');
    }

    /**
     * @param string $emptyField
     * @test
     * @author denis.poloka
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @depends withRequiredFieldsCreateUser
     * @TestlinkId TL-MAGE-6364
     */
    public function absentRequiredFields($emptyField)
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
        $xpath = $this->_getControlXpath('field', $emptyField);
        $this->addParameter('fieldXpath', $xpath);
        $this->assertMessagePresent('error', 'api_user_empty_required_field');
    }

    /**
     * @return array
     */
    public function withRequiredFieldsEmptyDataProvider()
    {
        return array (
            array ('api_user_api_key'),
            array ('api_user_api_secret'),
            array ('api_user_contact_email'),
        );
    }

    /**
     * @param array $userData
     * @test
     * @author denis.poloka
     * @depends withRequiredFieldsCreateUser
     * @TestlinkId TL-MAGE-6365
     */
    public function deleteUser($userData)
    {
        //Open APi users page and find in the grid users
        $this->navigate('api_users');
        $userSearch = array('filter_api_users_api_key' => $userData['api_user_api_key']);
        $this->searchAndOpen($userSearch, false);
        $this->waitForPageToLoad();
        $this->addParameter('userId', $this->defineParameterFromUrl('user_id'));
        $this->addParameter('apiKey', $userData['api_user_api_key']);
        $this->validatePage();

        //User delete
        $this->clickButtonAndConfirm('delete', 'confirmation_for_delete');

        //Verifying
        $this->assertMessagePresent('success', 'success_user_deleted');
    }
}
