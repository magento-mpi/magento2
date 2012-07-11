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
 * Log in and Reset password actions with enable captcha
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_AdminUser_CaptchaTest extends Mage_Selenium_TestCase {

    public function setUpBeforeTests()
    {
        $this->admin('log_in_to_admin', false);
        if (!$this->controlIsPresent('field', 'captcha')) {
            $this->loginAdminUser();
            $this->navigate('system_configuration');
            $config = $this->loadDataSet('Captcha', 'enable_admin_captcha');
            try {
                $this->systemConfigurationHelper()->configure($config);
            } catch (Exception $e) { }
        }
    }

    protected function tearDownAfterTestClass()
    {
        $this->admin('log_in_to_admin', false);
        if ($this->controlIsPresent('field', 'captcha')) {
            $loginData = array('user_name' => $this->_configHelper->getDefaultLogin(),
                               'password'  => $this->_configHelper->getDefaultPassword(), 'captcha' => '1111');
            $disCaptcha = $this->loadDataSet('AdminUsers', 'disable_admin_captcha');
            //Steps
            $this->adminUserHelper()->loginAdmin($loginData);
            $this->navigate('system_configuration');
            $this->systemConfigurationHelper()->configure($disCaptcha);
            $this->logoutAdminUser();
        }
    }

    /**
     *  <p>Preconditions:</p>
     * <p>Navigate to Login Admin Page</p>
     */
    protected function assertPreconditions() {
        $logOutXpath = $this->_getControlXpath('link', 'log_out');
        $this->admin('log_in_to_admin', false);
        if ($this->_findCurrentPageFromUrl() != 'log_in_to_admin' && $this->isElementPresent($logOutXpath)) {
            $this->logoutAdminUser();
        }
        $this->validatePage('log_in_to_admin');
    }

    /**
     * <p>Login with empty "Captcha"</p>
     * <p>Steps</p>
     * <p>1. Enter valid data in the user and password fields </p>
     * <p>2.Leave captcha field empty;</p>
     * <p>3. Click "Login" button;</p>
     * <p>Expected result:</p>
     * <p>Error message appears - "This is a required field"</p>
     *
     * @test
     *
     * @TestlinkId TL-MAGE-5467
     */
    public function loginEmptyCaptcha()
    {
        //data
        $loginData = array('user_name' => $this->_configHelper->getDefaultLogin(),
                           'password'  => $this->_configHelper->getDefaultPassword());
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verifying
        $this->assertMessagePresent('validation', 'empty_captcha');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Login with wrong "Captcha"</p>
     * <p>Steps</p>
     * <p>1. Enter valid data in the user and password fields </p> 
     * <p>2. Enter "1112" in the  captcha field;</p>
     * <p>3. Click "Login" button;</p>
     * <p>Expected result:</p>
     * <p>Error message appears - "Invalid User Name or Password."</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5469
     */
    public function loginWrongCaptcha()
    {
        //data
        $loginData = array('user_name' => $this->_configHelper->getDefaultLogin(),
                           'password'  => $this->_configHelper->getDefaultPassword(), 'captcha' => '1112');
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verifying
        $this->assertMessagePresent('error', 'incorrect_captcha');
    }

    /**
     * <p>Login with valid "Captcha"</p>
     * <p>Steps</p>
     * <p>1. Enter valid data in the user and password fields </p>
     * <p>2.Enter "1111" in the  captcha field;</p>
     * <p>3. Click "Login" button;</p>
     * <p>Expected result:</p>
     * <p> Login to admin"</p>
     * @test
     * @return array
     * @TestlinkId TL-MAGE-5468
     */
    public function loginValidCaptcha()
    {
        //Data
        $loginData = array('user_name' => $this->_configHelper->getDefaultLogin(),
                           'password'  => $this->_configHelper->getDefaultPassword(), 'captcha' => '1111');
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verifying
        $this->assertTrue($this->checkCurrentPage('dashboard'), $this->getParsedMessages());
        $this->logoutAdminUser();

        return $loginData;
    }

    /**
     * <p>Valid e-mail used in "Forgot password" field</p>
     * <p>Steps</p>
     * <p>Pre-Conditions:</p>
     * <p>Admin User is created</p>
     * <p>1.Fill in "Forgot password" field with correct data;</p>
     * <p>2. Click "Retrieve password" button;</p>
     * <p>Expected result:</p>
     * <p>Error message "This is a required field" appears under 'captcha' field;</p>
     * * @test
     * @TestlinkId TL-MAGE-5471
     */
    public function forgotPasswordEmptyCaptcha()
    {
        //Data
        $userData = $this->loadDataSet('AdminUsers', 'generic_admin_user');
        $emailData = array('email' => $userData['email']);
        //Steps
        $loginData = array('user_name' => $this->_configHelper->getDefaultLogin(),
                           'password'  => $this->_configHelper->getDefaultPassword(), 'captcha' => '1111');
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_user');
        //Steps
        $this->logoutAdminUser();
        $this->adminUserHelper()->forgotPassword($emailData);
        //Verifying
        $this->addParameter('adminEmail', $emailData['email']);
        $this->assertMessagePresent('validation', 'empty_captcha');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Valid e-mail used in "Forgot password" field</p>
     * <p>Steps</p>
     * <p>Pre-Conditions:</p>
     * <p>Admin User is created</p>
     * <p>1.Fill in "Forgot password" field with correct data;</p>
     * <p>2. Fill in "Captcha" field with incorrect data;
     * <p>2. Click "Retrieve password" button;</p>
     * <p>Expected result:</p>
     * <p><p>Error message "Incorrect Captcha";</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5473
     */
    public function forgotPasswordWrongCaptcha()
    {
        //Data
        $emailData = array('email' => $this->generate('email', 15), 'captcha_field' => '1112');
        //Steps
        $this->adminUserHelper()->forgotPassword($emailData);
        $this->addParameter('captcha_field', $emailData['captcha_field']);
        //Verifying
        $this->addParameter('adminEmail', $emailData['email']);
        $this->assertMessagePresent('error', 'incorrect_captcha');
    }

    /**
     * <p>Valid e-mail used in "Forgot password" field</p>
     * <p>Steps</p>
     * <p>Pre-Conditions:</p>
     * <p>Admin User is created</p>
     * <p>1.Fill in "Forgot password" and "Captcha" fields with correct data;</p>
     * <p>2. Click "Retrieve password" button;</p>
     * <p>Expected result:</p>
     * <p>Success message "If there is an account associated.." appears.</p>
     * <p>Please check your email and click Back to Login."</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5476
     */
    public function forgotPasswordCorrectCaptcha()
    {
        //Data
        $emailData = array('email' => $this->generate('email', 15), 'captcha_field' => '1111');
        //Steps
        $this->adminUserHelper()->forgotPassword($emailData);
        $this->addParameter('captcha_field', $emailData['captcha_field']);
        //Verifying
        $this->addParameter('adminEmail', $emailData['email']);
        $this->assertMessagePresent('success', 'retrieve_password');
    }
}
