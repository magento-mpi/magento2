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

class CreateStoreView extends PHPUnit_Extensions_SeleniumTestCase {

    protected static $_data;
    protected static $_uimap;
    protected $captureScreenshotOnFailure = TRUE;
    protected $screenshotPath = 'C:\wamp\www\screenshots';
    protected $screenshotUrl = 'http://localhost/screenshots';

    protected function setUp()
    {
        self::$_data = array(
            'admin_name'                        => 'admin',
            'admin_password'                    => '123123q',
            'admin_path'                        => 'System/Manage Stores',
            //etalon values
            'store_name'                        => 'Main Website Store',
            'store_view_name'                   => 'Test Store View Name',
            'store_view_code'                   => 'store_view_code',
            'store_view_status'                 => 'Enabled',
            'store_view_sort_order'             => 1,
            //Max value
            'store_view_name_max_lenght'        => 'ThisIsVeryLongTextWithLength30ThisIsVeryLongTextWithLength60ThisIsVeryLongTextWithLength90ThisAVeryLongTextWithLength120ThisAVeryLongTextWithLength150ThisAVeryLongTextWithLength180ThisAVeryLongTextWithLength210ThisAVeryLongTextWithLength240ThisIsVeryLo255',
            'store_view_code_max_lenght'        => 'this_is_very_long_code_lenght_32',
            'store_view_sort_order_max_value'   => 65535,
            //Invalid value for field 'Code'
            'store_view_code_invalid_value_1'   => 'a!@#$%^&*()_+"№%?*-=:;.,/\|{}[]',
            'store_view_code_invalid_value_2'   => 'store view code',
            'store_view_code_invalid_value_3'   => 'Store_View_Code',
            'store_view_code_invalid_value_4'   => '1_store_view_code',
        );
        self::$_uimap = array(
            //Login Page
            'admin_name'                => "//*[@id='username']",
            'admin_password'            => "//*[@id='login']",
            'login_button'              => "//input[@title='Login']",
            //Dashboard Page
            'admin_logo'                => "//img[@class='logo']",
            //Manage Stores Page
            'search_store_view_name'    => "//*[@id='filter_store_title']",
            'add_store_view'            => '//button[span="Create Store View"]',
            'search_button'             => '//button[span="Search"]',
            //Store View page
            'store_name'                => "//*[@id='store_group_id']",
            'store_view_name'           => "//*[@id='store_name']",
            'store_view_code'           => "//*[@id='store_code']",
            'store_view_status'         => "//*[@id='store_is_active']",
            'store_view_sort_order'     => "//*[@id='store_sort_order']",
            'save_store_view'           => '//button[span="Save Store View"]',
            //Global
            'success_message'           => "//li[normalize-space(@class)='success-msg']",
            'error_message'             => "//li[normalize-space(@class)='error-msg']",
        );
        $this->setBrowser("*chrome");
        $this->setBrowserUrl("http://192.168.3.175/nightly/index.php/");
    }

    protected function assertPreConditions()
    {
        $this->start();
        $this->open('admin/');
        $this->waitForPageToLoad("60000");
        $this->type(self::$_uimap['admin_name'], self::$_data['admin_name']);
        $this->type(self::$_uimap['admin_password'], self::$_data['admin_password']);
        $this->click(self::$_uimap['login_button']);
        $this->waitForPageToLoad("40000");
        $this->assertFalse($this->isTextPresent('Invalid Username or Password.'));
        $this->assertTrue($this->isElementPresent(self::$_uimap['admin_logo']));
        $result = false;
        $link = '//ul[@id="nav"]';
        $nodes = explode('/', self::$_data['admin_path']);
        $i = 0;
        foreach ($nodes as $node) {
            $link .= '//li[a/span="' . $node . '" and  contains(@class,"level' . $i . '")]';
            $i++;
        }
        $link .= '/a';
        $this->assertTrue($this->isElementPresent($link));
        $this->clickAndWait($link);
        $pageName = end($nodes);
        $openedPageName = $this->getText("//*[@id='page:main-container']//h3");
        $this->assertEquals($openedPageName, $pageName);
    }

    /**
     * @dataProvider data_Positive
     */
    public function testCreateStoreView_Positive($storeViewName, $storeViewCode, $storeSortOrder)
    {
        $this->clickAndWait(self::$_uimap['add_store_view']);
        $this->select(self::$_uimap['store_name'], self::$_data['store_name']);
        $this->type(self::$_uimap['store_view_name'], $storeViewName);
        $this->type(self::$_uimap['store_view_code'], $storeViewCode);
        $this->select(self::$_uimap['store_view_status'], self::$_data['store_view_status']);
        $this->type(self::$_uimap['store_view_sort_order'], $storeSortOrder);
        $this->click(self::$_uimap['save_store_view']);
        $this->waitForPageToLoad("40000");
        $this->assertTrue($this->isElementPresent(self::$_uimap['success_message']));
        echo 'Test is passed' . "\r\n";
    }

    public function data_Positive()
    {
        $this->setUp();
        $stamp = 1;
        return array(
            array(
                self::$_data['store_view_name'],
                self::$_data['store_view_code'],
                self::$_data['store_view_sort_order']
            ),
            array(
                self::$_data['store_view_name_max_lenght'],
                self::$_data['store_view_code_max_lenght'],
                self::$_data['store_view_sort_order_max_value']
            ),
            array(
                self::$_data['store_view_code_invalid_value_1'],
                self::$_data['store_view_code'] . $stamp,
                self::$_data['store_view_sort_order']
            ),
        );
    }

    /**
     * @depends testCreateStoreView_Positive
     * @dataProvider data_invalidCode
     */
    public function testCreateStoreView_invalidCode($storeViewCode)
    {
        $this->clickAndWait(self::$_uimap['add_store_view']);
        $this->select(self::$_uimap['store_name'], self::$_data['store_name']);
        $this->type(self::$_uimap['store_view_name'], self::$_data['store_view_name']);
        $this->type(self::$_uimap['store_view_code'], $storeViewCode);
        $this->select(self::$_uimap['store_view_status'], self::$_data['store_view_status']);
        $this->type(self::$_uimap['store_view_sort_order'], self::$_data['store_view_sort_order']);
        $this->click(self::$_uimap['save_store_view']);
        $this->waitForPageToLoad("40000");
        $this->assertTrue($this->isElementPresent(self::$_uimap['error_message']));
        echo 'Test is passed' . "\r\n";
    }

    public function data_invalidCode()
    {
        $this->setUp();
        $invalidValue5 = self::$_data['store_view_code_max_lenght'] . 'abc';
        return array(
            array(self::$_data['store_view_code_invalid_value_1']),
            array(self::$_data['store_view_code_invalid_value_2']),
            array(self::$_data['store_view_code_invalid_value_3']),
            array(self::$_data['store_view_code_invalid_value_4']),
            array($invalidValue5)
        );
    }

}
