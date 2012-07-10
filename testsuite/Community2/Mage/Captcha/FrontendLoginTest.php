<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Captcha tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_Captcha_FrontendLoginTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->logoutCustomer();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('Captcha', 'disable_frontend_captcha'));
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
        $this->loginAdminUser();
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
        //Data
        $config = $this->loadDataSet('Captcha', 'enable_front_login_captcha');
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
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
        $xpath = $this->_getControlXpath('pageelement', 'captcha') . '@src';
        $captchaUrl1 = $this->getAttribute($xpath);
        $this->clickControl('button', 'captcha_reload', false);
        $this->waitForAjax();
        $captchaUrl2 = $this->getAttribute($xpath);
        //Verification
        $this->assertNotEquals($captchaUrl1, $captchaUrl2, 'Captcha is not refreshed');
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
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
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
        $config = $this->loadDataSet('Captcha', 'front_captcha_after_attempts_to_login');
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        //Steps
        $this->frontend('customer_login');

        for ($i = 0; $i < 2; $i++) {
            $this->assertFalse($this->controlIsVisible('field', 'captcha'));
            $this->assertFalse($this->controlIsVisible('pageelement', 'captcha'));
            $this->assertFalse($this->controlIsVisible('button', 'captcha_reload'));
            $this->customerHelper()->frontLoginCustomer($incorrectUser, false);
            $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        }
        //Verification
        $this->assertTrue($this->controlIsVisible('field', 'captcha'));
        $this->assertTrue($this->controlIsVisible('pageelement', 'captcha'));
        $this->assertTrue($this->controlIsVisible('button', 'captcha_reload'));
    }
}
