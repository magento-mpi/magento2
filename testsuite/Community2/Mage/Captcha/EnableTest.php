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
 * Enable captcha in the Login and Forgot Password forms
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_Captcha_EnableTest extends Mage_Selenium_TestCase
{
    public function tearDownAfterTest()
    {
        $config = $this->loadDataSet('Captcha', 'disable_admin_captcha');
        $this->admin('log_in_to_admin');
        if ($this->controlIsPresent('field', 'captcha')) {
            $loginData = array('user_name' => $this->_configHelper->getDefaultLogin(),
                               'password'  => $this->_configHelper->getDefaultPassword(), 'captcha' => '1111');
            //Steps
            $this->fillFieldset($loginData, 'log_in');
            $this->clickButton('login');
            $this->navigate('system_configuration');
            $this->systemConfigurationHelper()->configure($config);
        }
    }

    /**
     * <p>Enabled - works for Login, Forgot Password in one time</p>
     * <p>Steps</p>
     * <p>1. Configure CAPTCHA for all forms </p>
     * <p>2.Log out;</p>
     * <p>Expected result:</p>
     * <p>CAPTCHA is present on the Login page"</p>
     * <p>CAPTCHA is present on the Forgot Password page"</p>
     *
     * @test
     *
     * @TestlinkId TL-MAGE-2619
     */
    public function forAllForms()
    {
        $config = $this->loadDataSet('Captcha', 'enable_admin_captcha');
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->logoutAdminUser();
        $this->assertTrue($this->controlIsVisible('field', 'captcha'), 'There is no "Captcha" field on the page');
        $this->assertTrue($this->controlIsVisible('pageelement', 'captcha'),
            'There is no "Captcha" pageelement on the page');
        $this->assertTrue($this->controlIsVisible('button', 'captcha_reload'),
            'There is no "Captcha_reload" button on the page');
        $this->clickControl('link', 'forgot_password');
        $this->assertTrue($this->controlIsVisible('field', 'captcha_field'), 'There is no "Captcha" field on the page');
        $this->assertTrue($this->controlIsVisible('pageelement', 'captcha'),
            'There is no "Captcha" pageelement on the page');
        $this->assertTrue($this->controlIsVisible('button', 'captcha_reload'),
            'There is no "Captcha_reload" button on the page');
    }

    /**
     * <p>Enabled for Admin Login form</p>
     * <p>Steps</p>
     * <p>1. Configure CAPTCHA for Admin Login form only</p>
     * <p>2.Log out</p>
     * <p>Expected result:</p>
     * <p>CAPTCHA is present on the Login to Admin page"</p>
     * <p>CAPTCHA is not present on the Forgot Password page"</p>
     *
     * @test
     *
     * @TestlinkId TL-MAGE-2614, TL-MAGE-2616
     *
     */
    public function forAdminLoginForm()
    {
        $config = $this->loadDataSet('Captcha', 'choose_login_form');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->logoutAdminUser();
        $this->admin('log_in_to_admin');
        $this->assertTrue($this->controlIsVisible('field', 'captcha'), 'There is no "Captcha" field form on the page');
        $this->clickControl('link', 'forgot_password');
        $this->assertFalse($this->controlisVisible('field', 'captcha_field'), 'There is "Captcha" field on the page');
    }

    /**
     * <p>Enabled  for Admin Forgot Password </p>
     * <p>Steps</p>
     * <p>1. Configure CAPTCHA for Admin Forgot Password form only</p>
     * <p>2.Log out</p>
     * <p>Expected result:</p>
     * <p>CAPTCHA is present on the Login to Admin page"</p>
     * <p>CAPTCHA is not present on the Forgot Password page"</p>
     * @test
     *
     * @TestlinkId TL-MAGE-2614, TL-MAGE-2616
     *
     */
    public function forForgotPasswordForm()
    {
        $config = $this->loadDataSet('Captcha', 'choose_admin_forgot_password');
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->logoutAdminUser();
        $this->assertFalse($this->controlisVisible('field', 'captcha'), 'There is "Captcha" field on the page');
        $this->clickControl('link', 'forgot_password');
        $this->assertTrue($this->controlIsVisible('field', 'captcha_field'), 'There is no "Captcha" field on the page');
    }
}
