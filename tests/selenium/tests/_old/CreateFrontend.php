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
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class CreateCustomer extends PHPUnit_Extensions_SeleniumTestCase {

    protected static $_data;
    protected static $_uimap;
    protected $captureScreenshotOnFailure = TRUE;
    protected $screenshotPath = 'C:\wamp\www\screenshots';
    protected $screenshotUrl = 'http://localhost/screenshots';

    protected function setUp()
    {
        self::$_data = array(
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'test@magento.com',
            'password' => '123123q',
            'confirm_password' => '123123q',
            'else_password' => 'qa123123',
            'long_text' => 'ThisIsVeryLongTextWithLength30ThisIsVeryLongTextWithLength60ThisIsVeryLongTextWithLength90ThisAVeryLongTextWithLength120ThisAVeryLongTextWithLength150ThisAVeryLongTextWithLength180ThisAVeryLongTextWithLength210ThisAVeryLongTextWithLength240ThisIsVeryLo255',
            'long_email' => 'ThisIsVeryLongTextWithLength30ThisIsVeryLongTextWithLengthThis64@ThisIsVeryLongTextWithLength30ThisIsVeryLongTextWithLength60mag.com.org.ua.ru.com.org.ua.ru.com.org.ua.ru.com.org.ua.ru.com.org.ua.ru.com.org.ua.ru.com.org.ua.ru.com.org.ua.ru.com.org.ua.net'
        );
        self::$_uimap = array(
            'frontend_logo' => "//img[@alt='Magento Commerce']",
            'wrong_page' => "//h3[text()='We are sorry, but the page you are looking for cannot be found.']",
            'my_account' => "link=My Account",
            'register' => "//button[contains(span,'Register')]",
            'first_name' => "//*[@id='firstname']",
            'last_name' => "//*[@id='lastname']",
            'email' => "//*[@id='email_address']",
            'password' => "//*[@id='password']",
            'confirm_password' => "//*[@id='confirmation']",
            'submit' => "//button[contains(span,'Submit')]",
            'empty_req_field' => "//*[normalize-space(@class)='validation-advice' and not(normalize-space(@style)='display: none;')]",
            'success_message' => "//li[normalize-space(@class)='success-msg']",
            'error_message' => "//li[normalize-space(@class)='error-msg']"
        );
        $this->setBrowser("*chrome");
        $this->setBrowserUrl("http://192.168.3.175/nightly/index.php/");
    }

    protected function assertPreConditions()
    {
        $this->start();
        $this->open('');
        $this->waitForPageToLoad("60000");
        $this->assertTrue($this->isElementPresent(self::$_uimap['frontend_logo']));
        $this->assertFalse($this->isElementPresent(self::$_uimap['wrong_page']));
    }

    /**
     * @dataProvider data_Positive
     */
    public function testCreateCustomer_Positive($fName, $lName, $email)
    {
        $this->clickAndWait(self::$_uimap['my_account']);
        $this->clickAndWait(self::$_uimap['register']);
        $this->type(self::$_uimap['first_name'], $fName);
        $this->type(self::$_uimap['last_name'], $lName);
        $this->type(self::$_uimap['email'], $email);
        $this->type(self::$_uimap['password'], self::$_data['password']);
        $this->type(self::$_uimap['confirm_password'], self::$_data['confirm_password']);
        $this->click(self::$_uimap['submit']);
        if (!$this->isElementPresent(self::$_uimap['empty_req_field'])) {
            $this->waitForPageToLoad("40000");
        }
        $this->assertTrue($this->isElementPresent(self::$_uimap['success_message']));
        echo "\r\n" . 'Test is passed' . "\r\n";
    }

    public function data_Positive()
    {
        $this->setUp();
        return array(
            array(
                self::$_data['long_text'],
                self::$_data['long_text'],
                self::$_data['long_email']
            ),
            array(
                self::$_data['first_name'],
                self::$_data['last_name'],
                self::$_data['email']
            ),
        );
    }

    /**
     * @dataProvider data_EmptyFields
     */
    public function testCreateCustomer_EmptyFields($fName, $lName, $email, $pass, $confirmPass)
    {
        $this->clickAndWait(self::$_uimap['my_account']);
        $this->clickAndWait(self::$_uimap['register']);
        $this->type(self::$_uimap['first_name'], $fName);
        $this->type(self::$_uimap['last_name'], $lName);
        $this->type(self::$_uimap['email'], $email);
        $this->type(self::$_uimap['password'], $pass);
        $this->type(self::$_uimap['confirm_password'], $confirmPass);
        $this->click(self::$_uimap['submit']);
        if (!$this->isElementPresent(self::$_uimap['empty_req_field'])) {
            $this->waitForPageToLoad("40000");
        }
        $qtyErrors = $this->getXpathCount(self::$_uimap['empty_req_field']);
        $this->assertTrue($this->isElementPresent(self::$_uimap['empty_req_field']));
        if ($pass == NULL) {
            $this->assertEquals(2, $qtyErrors);
        } else {
            $this->assertEquals(1, $qtyErrors);
        }
        echo "\r\n" . 'Test is passed' . "\r\n";
    }

    public function data_EmptyFields()
    {
        $this->setUp();
        return array(
            array(
                NULL,
                self::$_data['last_name'],
                self::$_data['email'],
                self::$_data['password'],
                self::$_data['confirm_password']
            ),
            array(
                self::$_data['first_name'],
                NULL,
                self::$_data['email'],
                self::$_data['password'],
                self::$_data['confirm_password']
            ),
            array(
                self::$_data['first_name'],
                self::$_data['last_name'],
                NULL,
                self::$_data['password'],
                self::$_data['confirm_password']
            ),
            array(
                self::$_data['first_name'],
                self::$_data['last_name'],
                self::$_data['email'],
                NULL,
                self::$_data['confirm_password']
            ),
            array(
                self::$_data['first_name'],
                self::$_data['last_name'],
                self::$_data['email'],
                self::$_data['password'],
                NULL,
            ),
        );
    }

}
