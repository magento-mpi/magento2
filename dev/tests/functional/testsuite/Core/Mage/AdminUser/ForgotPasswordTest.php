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
class Core_Mage_AdminUser_ForgotPasswordTest extends Mage_Selenium_TestCase
{
    /**
     * <p>"Forgot your user name or password?" form is displayed</p>
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
        $this->assertTrue($this->checkCurrentPage($this->pageAfterAdminLogin), $this->getParsedMessages());
    }
}