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
class Core_Mage_AdminUser_LoginTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->admin('log_in_to_admin', false);
        if ($this->_findCurrentPageFromUrl() != 'log_in_to_admin' && $this->controlIsPresent('link', 'log_out')) {
            $this->logoutAdminUser();
        }
        $this->validatePage('log_in_to_admin');
        $this->clickControl('link', 'forgot_password');
        if ($this->controlIsPresent('pageelement', 'captcha')) {
            $this->loginAdminUser();
            $this->navigate('system_configuration');
            $this->systemConfigurationHelper()->configure('Captcha/disable_admin_captcha');
            $this->logoutAdminUser();
        }
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Login Admin Page</p>
     */
    protected function assertPreConditions()
    {
        $this->admin('log_in_to_admin', false);
        if ($this->_findCurrentPageFromUrl() != 'log_in_to_admin' && $this->controlIsPresent('link', 'log_out')) {
            $this->logoutAdminUser();
        }
        $this->validatePage('log_in_to_admin');
    }

    /**
     * Login to Admin
     *
     * @test
     * @return array
     */
    public function loginValidUser()
    {
        //Data
        $loginData = array('user_name' => $this->getConfigHelper()->getDefaultLogin(),
                           'password'  => $this->getConfigHelper()->getDefaultPassword());
        //Steps
        $this->loginAdminUser();
        //Verifying
        $this->assertTrue($this->checkCurrentPage($this->pageAfterAdminLogin), $this->getParsedMessages());
        $this->logoutAdminUser();

        return $loginData;
    }

    /**
     * <p>Login with empty "Username"/"Password"</p>
     *
     * @param string $emptyField
     * @param array $fieldId
     * @param array $loginData
     *
     * @test
     * @dataProvider loginEmptyOneFieldDataProvider
     * @depends loginValidUser
     * @TestlinkId TL-MAGE-3154
     */
    public function loginEmptyOneField($emptyField, $fieldId, $loginData)
    {
        //Data
        $loginData[$emptyField] = '%noValue%';
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verifying
        $this->addParameter('fieldId', $fieldId);
        $this->assertMessagePresent('validation', 'empty_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function loginEmptyOneFieldDataProvider()
    {
        return array(
            array('user_name', 'username'),
            array('password', 'login')
        );
    }

    /**
     * <p>Login with not existing user</p>
     *
     * @param array $loginData
     *
     * @test
     * @depends loginValidUser
     * @TestlinkId TL-MAGE-3157
     */
    public function loginNonExistentUser($loginData)
    {
        //Data
        $loginData['user_name'] = 'nonExistentUser';
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verifying
        $this->assertMessagePresent('error', 'wrong_credentials');
    }

    /**
     * <p>Login with incorrect password</p>
     *
     * @param array $loginData
     *
     * @test
     * @depends loginValidUser
     * @TestlinkId TL-MAGE-3156
     */
    public function loginIncorrectPassword($loginData)
    {
        //Data
        $loginData['password'] = $this->generate('string', 9, ':punct:');
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verifying
        $this->assertMessagePresent('error', 'wrong_credentials');
    }

    /**
     * <p>Login with inactive Admin User account</p>
     *
     * @test
     * @depends loginValidUser
     * @TestlinkId TL-MAGE-3155
     */
    public function loginInactiveAdminAccount()
    {
        //Data
        $user = $this->loadDataSet('AdminUsers', 'generic_admin_user', array('this_account_is' => 'Inactive',
                                                                             'role_name'       => 'Administrators'));
        $loginData = array('user_name' => $user['user_name'],
                           'password'  => $user['password']);
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($user);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_user');
        //Steps
        $this->logoutAdminUser();
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verifying
        $this->assertMessagePresent('error', 'inactive_account');
    }

    /**
     * <p>Login without any permissions</p>
     *
     * @test
     * @depends loginValidUser
     * @TestlinkId TL-MAGE-3158
     */
    public function loginWithoutPermissions()
    {
        //Data
        $userData = $this->loadDataSet('AdminUsers', 'generic_admin_user');
        $loginData = array('user_name' => $userData['user_name'],
                           'password'  => $userData['password']);
        //Pre-Conditions
        $this->loginAdminUser();
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_user');
        //Steps
        $this->logoutAdminUser();
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verifying
        $this->assertMessagePresent('error', 'access_denied');
    }

    /**
     * <p>Empty field "Forgot password"</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3150
     */
    public function forgotEmptyPassword()
    {
        //Data
        $emailData = array('email' => '%noValue%');
        //Steps
        $this->adminUserHelper()->forgotPassword($emailData);
        //Verifying
        $this->addParameter('fieldId', 'email');
        $this->assertMessagePresent('error', 'empty_field');
        $this->assertTrue($this->checkCurrentPage('forgot_password'), $this->getParsedMessages());
    }

    /**
     * <p>Invalid e-mail used in "Forgot password" field</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3152
     */
    public function forgotPasswordInvalidEmail()
    {
        //Data
        $emailData = array('email' => $this->generate('email', 15));
        //Steps
        $this->adminUserHelper()->forgotPassword($emailData);
        //Verifying
        $this->addParameter('adminEmail', $emailData['email']);
        $this->assertMessagePresent('success', 'retrieve_password');
    }

    /**
     * <p>Valid e-mail used in "Forgot password" field</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3151
     */
    public function forgotPasswordCorrectEmail()
    {
        //Data
        $userData = $this->loadDataSet('AdminUsers', 'generic_admin_user');
        $emailData = array('email' => $userData['email']);
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_user');
        //Steps
        $this->logoutAdminUser();
        $this->adminUserHelper()->forgotPassword($emailData);
        //Verifying
        $this->addParameter('adminEmail', $emailData['email']);
        $this->assertMessagePresent('success', 'retrieve_password');
    }

    /**
     * <p>Valid e-mail used in "Forgot password" field, login with old password</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3153
     */
    public function forgotPasswordOldPassword()
    {
        //Data
        $userData = $this->loadDataSet('AdminUsers', 'generic_admin_user', array('role_name' => 'Administrators'));
        $emailData = array('email' => $userData['email']);
        $loginData = array('user_name' => $userData['user_name'],
                           'password'  => $userData['password']);
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_user');
        //Steps
        $this->logoutAdminUser();
        $this->adminUserHelper()->forgotPassword($emailData);
        //Verifying
        $this->addParameter('adminEmail', $emailData['email']);
        $this->assertMessagePresent('success', 'retrieve_password');
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verifying
        $this->assertTrue($this->checkCurrentPage($this->pageAfterAdminLogin), $this->getParsedMessages());
    }
}