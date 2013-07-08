<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Captcha
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Enable captcha in the Login and Forgot Password forms
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Captcha_EnableTest extends Mage_Selenium_TestCase
{
    public function assertPreConditions()
    {
        $this->admin('log_in_to_admin');
        $loginData = array(
            'user_name' => $this->getConfigHelper()->getDefaultLogin(),
            'password' => $this->getConfigHelper()->getDefaultPassword()
        );
        if ($this->controlIsPresent('field', 'captcha')) {
            $loginData['captcha'] = '1111';
        }
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage($this->pageAfterAdminLogin), $this->getMessagesOnPage());
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/default_admin_captcha');
    }

    public function tearDownAfterTest()
    {
        $this->admin('log_in_to_admin', false);
        $this->logoutAdminUser();
    }

    public function tearDownAfterTestClass()
    {
        $this->assertPreConditions();
    }

    /**
     * <p>Enabled - works for Login, Forgot Password in one time</p>
     *
     * @test
     * @TestlinkId TL-MAGE-2619
     */
    public function forAllForms()
    {
        $this->systemConfigurationHelper()->configure('Captcha/enable_admin_captcha');
        $this->logoutAdminUser();
        $this->assertTrue($this->controlIsVisible('field', 'captcha'), 'There is no "Captcha" field on the page');
        $this->assertTrue($this->controlIsVisible('pageelement', 'captcha'),
            'There is no "Captcha" pageelement on the page');
        $this->assertTrue($this->controlIsVisible('button', 'captcha_reload'),
            'There is no "Captcha_reload" button on the page');
        $this->clickControl('link', 'forgot_password');
        $this->assertTrue($this->controlIsVisible('field', 'captcha_field'),
            'There is no "Captcha" field on the page');
        $this->assertTrue($this->controlIsVisible('pageelement', 'captcha'),
            'There is no "Captcha" pageelement on the page');
        $this->assertTrue($this->controlIsVisible('button', 'captcha_reload'),
            'There is no "Captcha_reload" button on the page');
    }

    /**
     * <p>Enabled for Admin Login form</p>
     *
     * @test
     * @TestlinkId TL-MAGE-2614, TL-MAGE-2616
     */
    public function forAdminLoginForm()
    {
        $this->systemConfigurationHelper()->configure('Captcha/choose_login_form');
        $this->logoutAdminUser();
        $this->admin('log_in_to_admin');
        $this->assertTrue($this->controlIsVisible('field', 'captcha'), 'There is no "Captcha" field form on the page');
        $this->clickControl('link', 'forgot_password');
        $this->assertFalse($this->controlisVisible('field', 'captcha_field'), 'There is "Captcha" field on the page');
    }

    /**
     * <p>Enabled  for Admin Forgot Password </p>
     * @test
     * @TestlinkId TL-MAGE-2614, TL-MAGE-2616
     */
    public function forForgotPasswordForm()
    {
        $this->systemConfigurationHelper()->configure('Captcha/choose_admin_forgot_password');
        $this->logoutAdminUser();
        $this->assertFalse($this->controlisVisible('field', 'captcha'), 'There is "Captcha" field on the page');
        $this->clickControl('link', 'forgot_password');
        $this->assertTrue($this->controlIsVisible('field', 'captcha_field'), 'There is no "Captcha" field on the page');
    }
}