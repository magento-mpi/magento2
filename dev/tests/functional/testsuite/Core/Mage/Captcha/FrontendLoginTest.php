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
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        return array('email' => $userData['email'], 'password' => $userData['password']);

    }

    /**
     * <p>Enable Captcha at Login page</p>
     * <p>Steps:</p>
     * <p>1.Enable CAPTCHA on frontend option is set to Yes</p>
     * <p>2.Display mode is set to Always</p>
     * <p>3.Forms - Login is selected</p>
     * <p>4.Open Customer Login page</p>
     * <p>Expected result</p>
     * <p>CAPTCHA image is present</p>
     * <p>"Please type the letters below" field is present</p>
     * <p>Reload Captcha image is present</p>
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
     * <p>Preconditions:</p>
     * <p>1.Enable CAPTCHA on frontend option is set to Yes</p>
     * <p>2.Display mode is set to Always</p>
     * <p>3.Forms - Login User is selected</p>
     * <p>Steps:</p>
     * <p>1.Open Login customer page</p>
     * <p>2.Click "Refresh" icon on Captcha image </p>
     * <p>Expected result</p>
     * <p>CAPTCHA image should be refreshed</p>
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
     * <p>Preconditions:</p>
     * <p>1.Enable CAPTCHA on frontend option is set to Yes</p>
     * <p>2.Display mode is set to Always</p>
     * <p>3.Forms - Login User is selected</p>
     * <p>Steps:</p>
     * <p>1. Set "Merge JavaScript Files" in System->Configuration->Advanced->Developer->JavaScript Settings</p>
     * <p>2.Clear Magento cache</p>
     * <p>3.Open Login customer page</p>
     * <p>4.Click "Refresh" icon on Captcha image </p>
     * <p>Expected result</p>
     * <p>CAPTCHA image should be refreshed</p>
     *
     * @test
     * @depends enableCaptcha
     * @TestlinkId TL-MAGE-5781
     */
    public function refreshCaptchaWithMergeJS()
    {
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
     * <p>Preconditions:</p>
     * <p>1.Enable CAPTCHA on frontend option is set to Yes</p>
     * <p>2.Display mode is set to Always</p>
     * <p>3.Forms - "Create user" is selected</p>
     * <p>Steps:</p>
     * <p>1.Open Register Customer page</p>
     * <p>2.Input all requirement field and correct captcha</p>
     * <p>3.Click "Submit" button</p>
     * <p>Expected result</p>
     * <p>Customer successfully registered</p>
     * <p>Customer Account page is open</p>
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
     * <p>Preconditions:</p>
     * <p>1.Enable CAPTCHA on frontend option is set to Yes</p>
     * <p>2.Display mode is set to Always</p>
     * <p>3.Forms - "Create user" is selected</p>
     * <p>Steps:</p>
     * <p>1.Open Register Customer page</p>
     * <p>2.Fill all requirement field and wrong captcha</p>
     * <p>3.Click "Submit" button</p>
     * <p>Expected result</p>
     * <p>Show message "Incorrect CAPTCHA."</p>
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
     * <p>Preconditions:</p>
     * <p>1.Enable CAPTCHA on frontend option is set to Yes</p>
     * <p>2.Display mode is set to Always</p>
     * <p>3.Forms - "Create user" is selected</p>
     * <p>Steps:</p>
     * <p>1.Open Login Customer page</p>
     * <p>2.Fill all requirement field and wrong captcha</p>
     * <p>3.Click "Submit" button</p>
     * <p>Expected result</p>
     * <p>Show validation message "This is a required field."</p>
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
        $this->assertMessagePresent('validation', 'empty_captcha');
    }

    /**
     * <p>CAPTCHA showing in Login page after specified number</p>
     * <p>Preconditions:</p>
     * <p>1.Enable CAPTCHA on frontend option is set to Yes</p>
     * <p>2.Display mode is set to "After Number of Attempts to Login"</p>
     * <p>3.Number of Unsuccessful Attempts to Login - "2"</p>
     * <p>4.Forms - "Login user" is selected</p>
     * <p>Steps:</p>
     * <p>1.Open Register Customer page</p>
     * <p>2.Try to login with wrong password twice</p>
     * <p>Expected result</p>
     * <p>Captcha show after second wrong login</p>
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
            $this->waitForPageToLoad();
        }
        //Verification
        $this->assertTrue($this->controlIsVisible('field', 'captcha'));
        $this->assertTrue($this->controlIsVisible('pageelement', 'captcha'));
        $this->assertTrue($this->controlIsVisible('button', 'captcha_reload'));
    }
}
