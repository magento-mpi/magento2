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
 * Forgot Password tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_AdminUser_ForgotPasswordTest extends Mage_Selenium_TestCase
{
    /**
     * <p>"Forgot your user name or password?" form is displayed</p>
     * <p>Steps:</p>
     * <p>1.Go to admin Login page. Click on the "Forgot your password?" link.</p>
     * <p>Expected result</p>
     * <p>"Forgot Your Password?" form is opened. </p>
     * <p>«Email Address» edit filed, «Back» button and «Back to Login» button are present.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-2018
     */
    public function validateForgotPasswordPage()
    {
        //Steps
        $this->admin('forgot_password');
        //Verification
        $this->assertTrue($this->controlIsVisible('field', 'email'));
        $this->assertTrue($this->controlIsVisible('button', 'retrieve_password'));
        $this->assertTrue($this->controlIsVisible('link', 'back_to_login'));
        $this->assertTrue($this->checkCurrentPage('forgot_password'), $this->getParsedMessages());
    }

    /**
     * <p>Not valid email address</p>
     * <p>Steps:</p>
     * <p>1.Go to backend</p>
     * <p>2.Click «Forgot Your Password» link.</p>
     * <p>3.Enter not valid email address into the «Email Address» edit field and click «SUBMIT» button.</p>
     * <p>Expected result:</p>
     * <p>Error message with text "Please enter a valid email address. For example johndoe@domain.com." should appear.</p>
     *
     * @depends validateForgotPasswordPage
     * @test
     */
    public function invalidEmailAddress()
    {
        //Data
        $emailData = array('email' => $this->generate('email', 15, 'invalid'));
        //Steps
        $this->admin('log_in_to_admin');
        //Verification
        $this->adminUserHelper()->forgotPassword($emailData);
        $this->assertMessagePresent('validation', 'forgot_password_invalid_email');
    }

    /**
     * <p>Valid email address</p>
     * <p>Steps:</p>
     * <p>1.Go to admin Login page. Click on the "Forgot your password?" link.</p>
     * <p>2.Enter email registered for any of admin users and click on the "Retreive Password" button.</p>
     * <p>Expected result:</p>
     * <p>"If there is an account associated with <email_address> you will receive an email with a link to reset your password" message is appeared on the form.</p>
     *
     * @test
     * @depends validateForgotPasswordPage
     * @TestlinkId TL-MAGE-2030
     */
    public function validEmailAddress()
    {
        //Data
        $userData = $this->loadDataSet('AdminUsers', 'generic_admin_user');
        $emailData = array('email' => $userData['email']);
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($userData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_user');
        //Steps
        $this->logoutAdminUser();
        $this->adminUserHelper()->forgotPassword($emailData);
        //Verifying
        $this->addParameter('adminEmail', $emailData['email']);
        $this->assertMessagePresent('success', 'success_forgot_password');
    }

    /**
     * <p>Admin can login with old password till he clicks on the reset link password</p>
     * <p>Steps:</p>
     * <p>1.Go to backend and click on the «Forgot Your Password» link.</p>
     * <p>2.Enter email registered for any of admin users and click on the "Retreive Password" button.</p>
     * <p>3.Try to login with old credentials</p>
     * <p>Expected result:</p>
     * <p>Admin successfully login to backend.</p>
     *
     * @depends validateForgotPasswordPage
     * @test
     */
    public function oldPasswordTillResetNew()
    {
        //Data
        $userData = $this->loadDataSet('AdminUsers', 'generic_admin_user', array('role_name' => 'Admin'));
        $emailData = array('email' => $userData['email']);
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($userData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_user');
        //Steps
        $this->logoutAdminUser();
        $this->adminUserHelper()->forgotPassword($emailData);
        //Verification
        $this->addParameter('adminEmail', $emailData['email']);
        $this->assertMessagePresent('success', 'success_forgot_password');
        //Steps
        $this->loginAdminUser();
        //Verification
        $this->assertTrue($this->checkCurrentPage('dashboard'), $this->getParsedMessages());
    }
}
