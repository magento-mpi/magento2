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
 * Captcha tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Captcha_FrontendForgotPasswordTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/default_frontend_captcha');
    }

    public function assertPreConditions()
    {
        $this->logoutCustomer();
    }

    public function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/default_frontend_captcha');
    }

    /**
     * <p>Enable Captcha on Forgot Password page</p>
     *
     * @test
     */
    public function enableCaptcha()
    {
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/enable_front_forgot_password_captcha');
        $this->frontend('forgot_customer_password');
        //Verification
        $this->assertTrue($this->controlIsVisible('field', 'captcha'));
        $this->assertTrue($this->controlIsVisible('pageelement', 'captcha'));
        $this->assertTrue($this->controlIsVisible('button', 'captcha_reload'));
    }

    /**
     * <p>Empty CAPTCHA in Forgot Password page</p>
     *
     * @depends enableCaptcha
     * @test
     * @TestlinkId TL-MAGE-5672
     */
    public function emptyCaptcha()
    {
        //Data
        $data = array('email' => $this->generate('email', 20, 'valid'), 'captcha' => '');
        //Steps
        $this->frontend('forgot_customer_password');
        $this->fillFieldset($data, 'forgot_password');
        $this->clickButton('submit', false);
        //Verification
        $this->validatePage('forgot_customer_password');
        $this->addFieldIdToMessage('field', 'captcha');
        $this->assertMessagePresent('validation', 'empty_required_field');
    }

    /**
     * <p>Wrong CAPTCHA in Forgot Password page</p>
     *
     * @depends enableCaptcha
     * @test
     * @TestlinkId TL-MAGE-5673
     */
    public function wrongCaptcha()
    {
        $this->markTestIncomplete('BUG: Work with Wrong captcha');
        //Data
        $data = array('email' => $this->generate('email', 20, 'valid'), 'captcha' => '1234');
        //Steps
        $this->frontend('forgot_customer_password');
        $this->fillFieldset($data, 'forgot_password');
        $this->clickButton('submit');
        //Verification
        $this->validatePage('forgot_customer_password');
        $this->assertMessagePresent('error', 'incorrect_captcha');
    }

    /**
     * <p>Correct CAPTCHA in Forgot Password page</p>
     *
     * @depends enableCaptcha
     * @test
     * @TestlinkId TL-MAGE-5671
     */
    public function correctCaptcha()
    {
        //Data
        $data = array('email' => $this->generate('email', 20, 'valid'), 'captcha' => '1111');
        $this->addParameter('email', $data['email']);
        //Steps
        $this->frontend('forgot_customer_password');
        $this->fillFieldset($data, 'forgot_password');
        $this->clickButton('submit');
        //Verification
        $this->validatePage('customer_login');
        $this->assertMessagePresent('success', 'success_forgot_password');
    }

    /**
     * <p>Refreshing CAPTCHA image in Forgot Password page</p>
     *
     * @depends enableCaptcha
     * @test
     * @TestlinkId TL-MAGE-3791
     */
    public function refreshCaptcha()
    {
        //Steps
        $this->frontend('forgot_customer_password');
        $captchaUrl1 = $this->getControlAttribute('pageelement', 'captcha', 'src');
        $this->clickControl('button', 'captcha_reload', false);
        $this->waitForAjax();
        $captchaUrl2 = $this->getControlAttribute('pageelement', 'captcha', 'src');
        //Verification
        $this->assertNotEquals($captchaUrl1, $captchaUrl2, 'Captcha is not refreshed');
    }
}