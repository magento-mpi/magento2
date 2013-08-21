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
class Core_Mage_Customer_ForgotPasswordTest extends Mage_Selenium_TestCase
{
    /**
     * <p>"Forgot Your Password?" form</p>
     *
     * @test
     * @TestlinkId TL-MAGE-2035
     */
    public function validateForgotPasswordPage()
    {
        //Steps
        $this->frontend('home_page');
        $this->frontend('forgot_customer_password');
        //Verification
        $this->assertTrue($this->controlIsVisible('field', 'email'));
        $this->assertTrue($this->controlIsVisible('button', 'submit'));
        $this->assertTrue($this->controlIsVisible('link', 'back_to_login'));
        $this->assertTrue($this->checkCurrentPage('forgot_customer_password'), $this->getParsedMessages());
    }

    /**
     * <p>Not valid email address</p>
     *
     * @depends validateForgotPasswordPage
     * @test
     * @TestlinkId TL-MAGE-2036
     */
    public function invalidEmailAddress()
    {
        //Data
        $emailData = array('email' => $this->generate('email', 15, 'invalid'));
        //Steps
        $this->frontend('forgot_customer_password');
        //Verification
        $this->customerHelper()->frontForgotPassword($emailData);
        $this->assertMessagePresent('validation', 'forgot_password_invalid_email');
    }

    /**
     * <p>Valid email address</p>
     *
     * @test
     * @depends validateForgotPasswordPage
     */
    public function validEmailAddress()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $emailData = array('email' => $userData['email']);
        //Steps
        $this->frontend('forgot_customer_password');
        //Verification
        $this->customerHelper()->frontForgotPassword($emailData);
        $this->addParameter('email', $userData['email']);
        $this->assertMessagePresent('success', 'success_forgot_password');
    }

    /**
     * <p>Customer can login with old password till he click on the reset link password</p>
     *
     * @depends validateForgotPasswordPage
     * @test
     * @TestlinkId TL-MAGE-2038
     */
    public function oldPasswordTillResetNew()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $data = array('email' => $userData['email'], 'password' => $userData['password']);
        //Steps
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();
        $this->frontend('forgot_customer_password');
        $this->customerHelper()->frontForgotPassword(array('email' => $userData['email']));
        $this->addParameter('email', $userData['email']);
        //Verification
        $this->assertMessagePresent('success', 'success_forgot_password');
        $this->customerHelper()->frontLoginCustomer($data);
        $this->assertTrue($this->checkCurrentPage('customer_account'), $this->getParsedMessages());
    }
}