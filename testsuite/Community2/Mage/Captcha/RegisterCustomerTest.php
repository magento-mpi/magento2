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
class Community2_Mage_Captcha_RegisterCustomerTest extends Mage_Selenium_TestCase
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
     * <p>Enable Captcha on Register Customer page</p>
     * <p>Steps:</p>
     * <p>1.Enable CAPTCHA on frontend option is set to Yes</p>
     * <p>2.Display mode is set to Always</p>
     * <p>3.Forms - "Create user" is selected</p>
     * <p>4.Open Register Customer page</p>
     * <p>Expected result</p>
     * <p>CAPTCHA image is present</p>
     * <p>"Please type the letters below" field is present</p>
     * <p>Reload Captcha image is present</p>
     *
     * @test
     * @TestlinkId TL-MAGE-2622
     */
    public function enableCaptcha()
    {
        //Data
        $config = $this->loadDataSet('Captcha', 'enable_front_register_captcha');
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->frontend('register_account');
        //Validations
        $this->assertTrue($this->controlIsVisible('field', 'captcha'));
        $this->assertTrue($this->controlIsVisible('pageelement', 'captcha'));
        $this->assertTrue($this->controlIsVisible('button', 'captcha_reload'));
    }

    /**
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
     * <p>Open Customer Account page</p>
     *
     * @depends enableCaptcha
     * @test
     * @TestlinkId TL-MAGE-5668
     */
    public function correctCaptcha()
    {
        //Data
        $user = $this->loadDataSet('Customers', 'customer_account_register');
        $user['captcha'] = '1111';
        //Steps
        $this->frontend('register_account');
        $this->fillFieldset($user, 'account_info');
        $this->clickButton('submit');
        //Verification
        $this->validatePage('customer_account');
    }

    /**
     * <p>Wrong CAPTCHA in Register Customer page</p>
     * <p>Preconditions:</p>
     * <p>1.Enable CAPTCHA on frontend option is set to Yes</p>
     * <p>2.Display mode is set to Always</p>
     * <p>3.Forms - Create user is selected</p>
     * <p>Steps:</p>
     * <p>1.Open Register Customer page</p>
     * <p>2.Input all requirement field and WRONG captcha</p>
     * <p>3.Click "Submit" button</p>
     * <p>Expected result</p>
     * <p>Show message "Incorrect CAPTCHA."</p>
     *
     * @depends enableCaptcha
     * @test
     * @TestlinkId TL-MAGE-5669
     */
    public function wrongCaptcha()
    {
        //Data
        $user = $this->loadDataSet('Customers', 'customer_account_register');
        $user['captcha'] = '1234';
        //Steps
        $this->frontend('register_account');
        $this->fillFieldset($user, 'account_info');
        $this->clickButton('submit');
        //Verification
        $this->assertMessagePresent('error', 'incorrect_captcha');
    }

    /**
     * <p>Empty CAPTCHA in Register Customer page</p>
     * <p>Preconditions:</p>
     * <p>1.Enable CAPTCHA on frontend option is set to Yes</p>
     * <p>2.Display mode is set to Always</p>
     * <p>3.Forms - "Create user" is selected</p>
     * <p>Steps:</p>
     * <p>1.Open Register Customer page</p>
     * <p>2.Fill all requirement fields except Captcha </p>
     * <p>3.Click "Submit" button</p>
     * <p>Expected result</p>
     * <p>Show validation message "This is a required field."</p>
     *
     * @depends enableCaptcha
     * @test
     * @TestlinkId TL-MAGE-5670
     */
    public function emptyCaptcha()
    {
        //Data
        $user = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps
        $this->frontend('register_account');
        $this->fillFieldset($user, 'account_info');
        $this->clickButton('submit', false);
        //Verification
        $this->assertMessagePresent('validation', 'empty_captcha');
    }

    /**
     * <p>Refreshing CAPTCHA image in Register Customer page</p>
     * <p>Preconditions:</p>
     * <p>1.Enable CAPTCHA on frontend option is set to Yes</p>
     * <p>2.Display mode is set to Always</p>
     * <p>3.Forms - "Create user" is selected</p>
     * <p>Steps:</p>
     * <p>1.Open Forgot Register Customer page</p>
     * <p>2.Click "Refresh" icon on Captcha image </p>
     * <p>Expected result</p>
     * <p>CAPTCHA image should be refreshed</p>
     *
     * @depends enableCaptcha
     * @test
     * @TestlinkId TL-MAGE-3789
     */
    public function refreshCaptcha()
    {
        //Steps
        $this->frontend('register_account');
        $xpath = $this->_getControlXpath('pageelement', 'captcha') . '@src';
        $captchaUrl1 = $this->getAttribute($xpath);
        $this->clickControl('button', 'captcha_reload', false);
        $this->waitForAjax();
        $captchaUrl2 = $this->getAttribute($xpath);
        //Verification
        $this->assertNotEquals($captchaUrl1, $captchaUrl2, 'Captcha is not refreshed');
    }
}
