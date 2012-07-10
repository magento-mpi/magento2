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
class Community2_Mage_Captcha_FrontendForgotPasswordTest extends Mage_Selenium_TestCase
{
    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('Captcha', 'disable_frontend_captcha'));
    }

    /**
     * <p>Enable Captcha on Forgot Password page</p>
     * <p>Steps:</p>
     * <p>1.Enable CAPTCHA on frontend option is set to Yes</p>
     * <p>2.Display mode is set to Always</p>
     * <p>3.Forms - Forgot Password is selected</p>
     * <p>3.Open Forgot Password page</p>
     * <p>Expected result</p>
     * <p>CAPTCHA image is present</p>
     * <p>"Please type the letters below" field is present</p>
     * <p>Reload Captcha image is present</p>
     *
     * @test
     */
    public function enableCaptcha()
    {
        //Data
        $config = $this->loadDataSet('Captcha', 'enable_front_forgot_password_captcha');
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->frontend('forgot_customer_password');
        //Verification
        $this->assertTrue($this->controlIsVisible('field', 'captcha'));
        $this->assertTrue($this->controlIsVisible('pageelement', 'captcha'));
        $this->assertTrue($this->controlIsVisible('button', 'captcha_reload'));
    }

    /**
     * <p>Empty CAPTCHA in Forgot Password page</p>
     * <p>Preconditions:</p>
     * <p>1.Enable CAPTCHA on frontend option is set to Yes</p>
     * <p>2.Display mode is set to Always</p>
     * <p>3.Forms - Forgot Password is selected</p>
     * <p>Steps:</p>
     * <p>1.Open Forgot Password page</p>
     * <p>2.Fill "Email Address" field</p>
     * <p>2.Click "Submit" button</p>
     * <p>Expected result</p>
     * <p>Show validation message "This is a required field."</p>
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
        $this->assertMessagePresent('validation', 'empty_captcha');

    }

    /**
     * <p>Wrong CAPTCHA in Forgot Password page</p>
     * <p>Preconditions:</p>
     * <p>1.Enable CAPTCHA on frontend option is set to Yes</p>
     * <p>2.Display mode is set to Always</p>
     * <p>3.Forms - Forgot Password is selected</p>
     * <p>Steps:</p>
     * <p>1.Open Forgot Password page</p>
     * <p>2.Input correct Email Address</p>
     * <p>2.Input wrong Captcha in field</p>
     * <p>3.Click "Submit" button</p>
     * <p>Expected result</p>
     * <p>Show error message "Incorrect CAPTCHA."</p>
     *
     * @depends enableCaptcha
     * @test
     * @TestlinkId TL-MAGE-5673
     */
    public function wrongCaptcha()
    {
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
     * <p>Preconditions:</p>
     * <p>1.Enable CAPTCHA on frontend option is set to Yes</p>
     * <p>2.Display mode is set to Always</p>
     * <p>3.Forms - Forgot Password is selected</p>
     * <p>Steps:</p>
     * <p>1.Open Forgot Password page</p>
     * <p>2.Input correct Email Address</p>
     * <p>2.Input correct Captcha in field</p>
     * <p>3.Click "Submit" button</p>
     * <p>Expected result</p>
     * <p>Login page is open</p>
     * <p>Show message "If there is an account associated with email you will receive an email with a link to reset your password."</p>
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
     * <p>Preconditions:</p>
     * <p>1.Enable CAPTCHA on frontend option is set to Yes</p>
     * <p>2.Display mode is set to Always</p>
     * <p>3.Forms - Forgot Password is selected</p>
     * <p>Steps:</p>
     * <p>1.Open Forgot Password page</p>
     * <p>2.Click "Refresh" icon on Captcha image </p>
     * <p>Expected result</p>
     * <p>CAPTCHA image should be refreshed</p>
     *
     * @depends enableCaptcha
     * @test
     * @TestlinkId TL-MAGE-3791
     */
    public function refreshCaptcha()
    {
        //Steps
        $this->frontend('forgot_customer_password');
        $xpath = $this->_getControlXpath('pageelement', 'captcha') . '@src';
        $captchaUrl1 = $this->getAttribute($xpath);
        $this->clickControl('button', 'captcha_reload', false);
        $this->waitForAjax();
        $captchaUrl2 = $this->getAttribute($xpath);
        //Verification
        $this->assertNotEquals($captchaUrl1, $captchaUrl2, 'Captcha is not refreshed');
    }
}
