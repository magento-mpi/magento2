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
class Core_Mage_Captcha_FrontendLoginTest extends Mage_Selenium_TestCase
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
        $this->loginAdminUser();
    }

    public function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/default_frontend_captcha');
    }

    /**
     * Create customer and product
     *
     * @return array
     *
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();

        return array('email' => $userData['email'], 'password' => $userData['password']);
    }

    /**
     * <p>Enable Captcha at Login page</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2620
     */
    public function enableCaptcha()
    {
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/enable_front_login_captcha');
        $this->frontend('customer_login');
        //Verification
        $this->assertTrue($this->controlIsVisible('field', 'captcha'));
        $this->assertTrue($this->controlIsVisible('pageelement', 'captcha'));
        $this->assertTrue($this->controlIsVisible('button', 'captcha_reload'));
    }

    /**
     * <p>Refreshing CAPTCHA image for Login Form</p>
     *
     * @test
     * @depends enableCaptcha
     * @TestlinkId TL-MAGE-3790
     */
    public function refreshCaptcha()
    {
        //Steps
        $this->logoutCustomer();
        $this->frontend('customer_login');
        $captchaUrl1 = $this->getControlAttribute('pageelement', 'captcha', 'src');
        $this->clickControl('button', 'captcha_reload', false);
        $this->waitForAjax();
        $captchaUrl2 = $this->getControlAttribute('pageelement', 'captcha', 'src');
        //Verification
        $this->assertNotEquals($captchaUrl1, $captchaUrl2, 'Captcha is not refreshed');
    }

    /**
     * <p>Refreshing CAPTCHA image for Login Form</p>
     *
     * @test
     * @depends enableCaptcha
     * @TestlinkId TL-MAGE-5781
     */
    public function refreshCaptchaWithMergeJS()
    {
        $this->markTestIncomplete('BUG: Fatal error on page');
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('MergeJS/enable_merge_js');
        $this->clearInvalidedCache();
        $this->frontend('customer_login');
        $captchaUrl1 = $this->getControlAttribute('pageelement', 'captcha', 'src');
        $this->clickControl('button', 'captcha_reload', false);
        $this->waitForAjax();
        $captchaUrl2 = $this->getControlAttribute('pageelement', 'captcha', 'src');
        //Verification
        $this->assertNotEquals($captchaUrl1, $captchaUrl2, 'Captcha is not refreshed');
        //PostConditions
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('MergeJS/disable_merge_js');
        $this->clearInvalidedCache();
    }

    /**
     *
     * <p>Correct CAPTCHA in Register Customer page</p>
     *
     * @param array $testUser
     *
     * @test
     * @depends preconditionsForTests
     * @depends enableCaptcha
     * @TestlinkId TL-MAGE-5667
     */
    public function correctCaptcha($testUser)
    {
        //Data
        $testUser['captcha'] = '1111';
        //Steps
        $this->customerHelper()->frontLoginCustomer($testUser);
        $this->validatePage('customer_account');
    }

    /**
     * <p>Wrong CAPTCHA in Register Customer page</p>
     *
     * @param array $testUser
     *
     * @test
     * @depends preconditionsForTests
     * @depends enableCaptcha
     * @TestlinkId TL-MAGE-5665
     */
    public function wrongCaptcha($testUser)
    {
        ///Data
        $testUser['captcha'] = '1234';
        //Steps
        $this->customerHelper()->frontLoginCustomer($testUser, false);
        $this->waitForPageToLoad();
        //Verification
        $this->validatePage('customer_login');
        $this->assertMessagePresent('error', 'incorrect_captcha');
    }

    /**
     * <p>Empty CAPTCHA in Login Customer page</p>
     *
     * @param array $testUser
     *
     * @test
     * @depends preconditionsForTests
     * @depends enableCaptcha
     * @TestlinkId TL-MAGE-5666
     */
    public function emptyCaptcha($testUser)
    {
        //Steps
        $this->customerHelper()->frontLoginCustomer($testUser, false);
        //Verification
        $this->validatePage('customer_login');
        $this->addFieldIdToMessage('field', 'captcha');
        $this->assertMessagePresent('validation', 'empty_required_field');
    }

    /**
     * <p>CAPTCHA showing in Login page after specified number</p>
     *
     * @test
     * @TestlinkId TL-MAGE-2671
     */
    public function showCaptchaAfterFewAttemptsLogin()
    {
        //Data
        $incorrectUser = array('email' => $this->generate('email', 20, 'valid'), 'password' => 'password');
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Captcha/front_captcha_after_attempts_to_login');
        //Steps
        $this->frontend('customer_login');

        for ($i = 0; $i < 2; $i++) {
            $this->assertFalse($this->controlIsVisible('field', 'captcha'));
            $this->assertFalse($this->controlIsVisible('pageelement', 'captcha'));
            $this->assertFalse($this->controlIsVisible('button', 'captcha_reload'));
            $this->customerHelper()->frontLoginCustomer($incorrectUser, false);
        }
        //Verification
        $this->assertTrue($this->controlIsVisible('field', 'captcha'));
        $this->assertTrue($this->controlIsVisible('pageelement', 'captcha'));
        $this->assertTrue($this->controlIsVisible('button', 'captcha_reload'));
    }
}