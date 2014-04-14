<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AdminUser
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Creating Admin User
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_AdminUser_CreateTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_admin_users');
    }

    /**
     * <p>Test navigation.</p>
     *
     * @test
     */
    public function navigationTest()
    {
        $this->assertTrue($this->buttonIsPresent('add_new_admin_user'),
            'There is no "Add New Customer" button on the page');
        $this->clickButton('add_new_admin_user');
        $this->assertTrue($this->checkCurrentPage('new_admin_user'), $this->getParsedMessages());
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_admin_user'), 'There is no "Save User" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'interface_locale'),
            'There is no "Interface Locale" dropdown on the page');
    }

    /**
     * <p>Create Admin User (all required fields are filled).</p>
     *
     * @return array
     *
     * @test
     * @depends navigationTest
     * @TestlinkId TL-MAGE-3144
     */
    public function withRequiredFieldsOnly()
    {
        //Data
        $userData = $this->loadDataSet('AdminUsers', 'generic_admin_user');
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_user');

        return $userData;
    }

    /**
     * <p>Create Admin User. Use user name that already exist</p>
     *
     * @param array $userData
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3148
     */
    public function withUserNameThatAlreadyExists($userData)
    {
        //Data
        $userData['email'] = $this->generate('email', 20, 'valid');
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertMessagePresent('error', 'exist_name_or_email');
    }

    /**
     * <p>Create Admin User. Use email that already exist</p>
     *
     * @param array $userData
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3147
     */
    public function withUserEmailThatAlreadyExists($userData)
    {
        //Data
        $userData['user_name'] = $this->generate('string', 5, ':lower:');
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertMessagePresent('error', 'exist_name_or_email');
    }

    /**
     * <p>Create Admin User with one empty required field.</p>
     *
     * @param string $emptyField
     * @param int $messageCount
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3143
     */
    public function withRequiredFieldsEmpty($emptyField, $messageCount)
    {
        $userData = $this->loadDataSet('AdminUsers', 'generic_admin_user', array($emptyField => '%noValue%'));
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->addFieldIdToMessage('field', $emptyField);
        if ($emptyField == 'password_confirmation') {
            $this->assertMessagePresent('validation', 'password_unmatch');
        } else {
            $this->assertMessagePresent('validation', 'empty_required_field');
        }
        $this->assertTrue($this->verifyMessagesCount($messageCount), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('user_name', 1),
            array('first_name', 1),
            array('last_name', 1),
            array('email', 1),
            array('password', 2),
            array('password_confirmation', 1)
        );
    }

    /**
     * <p>Create Admin User (all required fields are filled by special characters).</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3146
     */
    public function withSpecialCharactersExceptEmail()
    {
        //Data
        $specialCharacters = array(
            'user_name' => $this->generate('string', 32, ':punct:'),
            'first_name' => $this->generate('string', 32, ':punct:'),
            'last_name' => $this->generate('string', 32, ':punct:')
        );
        $userData = $this->loadDataSet('AdminUsers', 'generic_admin_user', $specialCharacters);
        $searchData = $this->loadDataSet('AdminUsers', 'search_admin_user',
            array('email' => $userData['email']));
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->openAdminUser($searchData);
        $this->assertTrue($this->verifyForm($userData, 'user_info', array('password', 'password_confirmation')),
            $this->getParsedMessages());
    }

    /**
     * <p>Create Admin User (all required fields are filled by long value data).</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3141
     */
    public function withLongValues()
    {
        //Data
        $password = $this->generate('string', 255, ':alnum:');
        $longValues = array(
            'user_name' => $this->generate('string', 40, ':alnum:'),
            'first_name' => $this->generate('string', 32, ':alnum:'),
            'last_name' => $this->generate('string', 32, ':alnum:'),
            'email' => $this->generate('email', 128, 'valid'),
            'password' => $password,
            'password_confirmation' => $password
        );
        $userData = $this->loadDataSet('AdminUsers', 'generic_admin_user', $longValues);
        $searchData = $this->loadDataSet('AdminUsers', 'search_admin_user', array(
            'email' => $userData['email'],
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'user_name' => $userData['user_name']
        ));
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->openAdminUser($searchData);
        $this->assertTrue($this->verifyForm($userData, 'user_info', array('password', 'password_confirmation')),
            $this->getParsedMessages());
    }

    /**
     * <p>Create Admin User. Use wrong values for 'password' fields.</p>
     *
     * @param string $wrongPasswords
     * @param string $errorMessage
     *
     * @test
     * @dataProvider withInvalidPasswordDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3140
     */
    public function withInvalidPassword($wrongPasswords, $errorMessage)
    {
        //Data
        $userData = $this->loadDataSet('AdminUsers', 'generic_admin_user', $wrongPasswords);
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertMessagePresent('error', $errorMessage);
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withInvalidPasswordDataProvider()
    {
        return array(
            array(array('password' => '1234567890', 'password_confirmation' => '1234567890'), 'invalid_password'),
            array(array('password' => 'testText', 'password_confirmation' => 'testText'), 'invalid_password'),
            array(array('password' => '123qwe', 'password_confirmation' => '123qwe'), 'invalid_password'),
            array(array('password' => '123123qwe', 'password_confirmation' => '1231234qwe'), 'password_unmatch')
        );
    }

    /**
     * <p>Create Admin User (with invalid data in the 'email' field).</p>
     *
     * @param string $invalidEmail
     *
     * @test
     * @dataProvider withInvalidEmailDataProvider
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3139
     */
    public function withInvalidEmail($invalidEmail)
    {
        //Data
        $userData = $this->loadDataSet('AdminUsers', 'generic_admin_user', array('email' => $invalidEmail));
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertMessagePresent('error', 'invalid_email');
    }

    public function withInvalidEmailDataProvider()
    {
        return array(
            array('invalid'),
            array('test@invalidDomain'),
            array('te@st@unknown-domain.com')
        );
    }

    /**
     * <p>Create Admin User  (as Inactive).</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3138
     */
    public function inactiveUser()
    {
        //Data
        $user = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('this_account_is' => 'Inactive', 'role_name' => 'Administrators'));
        $loginData = array('user_name' => $user['user_name'], 'password' => $user['password']);
        //Steps
        $this->adminUserHelper()->createAdminUser($user);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_user');
        //Steps
        $this->logoutAdminUser();
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verifying
        $this->assertMessagePresent('error', 'wrong_credentials');
    }

    /**
     * <p>Create Admin User (with Admin User Role).</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3145
     */
    public function withRole()
    {
        //Data
        $userData = $this->loadDataSet('AdminUsers', 'generic_admin_user', array('role_name' => 'Administrators'));
        $loginData = array('user_name' => $userData['user_name'], 'password' => $userData['password']);
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_user');
        //Steps
        $this->logoutAdminUser();
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verifying
        $this->assertTrue($this->checkCurrentPage($this->pageAfterAdminLogin), $this->getParsedMessages());
    }

    /**
     * <p>Create Admin User (with Admin User Role).</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3142
     */
    public function withoutRole()
    {
        //Data
        $userData = $this->loadDataSet('AdminUsers', 'generic_admin_user');
        $loginData = array('user_name' => $userData['user_name'], 'password' => $userData['password']);
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_user');
        //Steps
        $this->logoutAdminUser();
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verifying
        $this->assertMessagePresent('error', 'wrong_credentials');
    }

    /**
     * <p>Need to verify that it is possible create admin user with not default language</p>
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-6926
     */
    public function withInterfaceLocale()
    {
        //Data
        $userData = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('interface_locale' => 'Deutsch (Deutschland) / German (Germany)'));
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_user');

    }
}