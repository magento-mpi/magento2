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
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
require_once 'PHPUnit/Framework/TestCase.php';

class CreateFrontend extends PHPUnit_Extensions_SeleniumTestCase {//PHPUnit_Framework_TestCase {

    protected static $_data;
    protected static $_uimap;
    protected $captureScreenshotOnFailure = TRUE;
    protected $screenshotPath = 'C:\wamp\www\screenshots';
    protected $screenshotUrl = 'http://localhost/screenshots';

    protected function setUp()
    {
        self::$_data = array(
            'first_name'        => 'first name',
            'last_name'         => 'last name',
            'email'             => 'test@magento.com',
            'password'          => '123123q',
            'confirm_password'  => '123123q',
            'long_text'         => 'ThisIsVeryLongTextWithLength30ThisIsVeryLongTextWithLength60ThisIsVeryLongTextWithLength90ThisAVeryLongTextWithLength120ThisAVeryLongTextWithLength150ThisAVeryLongTextWithLength180ThisAVeryLongTextWithLength210ThisAVeryLongTextWithLength240ThisIsVeryLo255',
            'long_email'        => 'ThisIsVeryLongTextWithLength30ThisIsVeryLongTextWithLengthThis64@ThisIsVeryLongTextWithLength30ThisIsVeryLongTextWithLength60mag.com.org.ua.ru.com.org.ua.ru.com.org.ua.ru.com.org.ua.ru.com.org.ua.ru.com.org.ua.ru.com.org.ua.ru.com.org.ua.ru.com.org.ua.net'
        );
        self::$_uimap = array(
            'frontend_logo'     => "//img[@alt='Magento Commerce']",
            'wrong_page'        => "//h3[text()='We are sorry, but the page you are looking for cannot be found.']",
            'my_account'        => 'link=My Account',
            'register'          => '//button[contains(span,"Register")]',
            'first_name'        => "//*[@id='firstname']",
            'last_name'         => "//*[@id='lastname']",
            'email'             => "//*[@id='email_address']",
            'password'          => "//*[@id='password']",
            'confirm_password'  => "//*[@id='confirmation']",
            'submit'            => '//button[contains(span,"Submit")]',
            'success_message'   => "//li[normalize-space(@class)='success-msg']",
            'error_message'     => "//li[normalize-space(@class)='error-msg']"
        );
        $this->setBrowser("*chrome");
        $this->setBrowserUrl("http://kd.varien.com/dev/alexandr.malyshenko/1.9.x/index.php/");
        $this->start();
    }

    protected function assertPreConditions()
    {
        $this->open('');
        $this->waitForPageToLoad("60000");
        $this->assertTrue($this->isElementPresent(self::$_uimap['frontend_logo']));
        $this->assertFalse($this->isElementPresent(self::$_uimap['wrong_page']));
    }

    /**
     * dataProvider testData_Positive
     */
    public function testCreateStoreView_Positive()
    {
        $this->clickAndWait(self::$_uimap['my_account']);
        $this->clickAndWait(self::$_uimap['register']);
        $this->type(self::$_uimap['first_name'], self::$_data['first_name']);
        $this->type(self::$_uimap['last_name'], self::$_data['last_name']);
        $this->type(self::$_uimap['email'], self::$_data['email']);
        $this->type(self::$_uimap['password'], self::$_data['password']);
        $this->type(self::$_uimap['confirm_password'], self::$_data['confirm_password']);
        $this->clickAndWait(self::$_uimap['submit']);
        $this->assertTrue($this->isElementPresent(self::$_uimap['success_message']));
        echo "\nTest is passed\n";
    }

    /*public function testData_Positive()
    {
        return array(
            array(self::$_data['long_text'], self::$_data['long_text'], self::$_data['long_email']),
            array(self::$_data['first_name'], self::$_data['last_name'], self::$_data['email']),
        );
    }*/

}
