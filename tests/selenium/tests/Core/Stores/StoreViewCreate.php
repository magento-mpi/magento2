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

class StoreViewCreate extends PHPUnit_Extensions_SeleniumTestCase {//PHPUnit_Framework_TestCase {

    protected static $_data;
    protected static $_uimap;
    protected $captureScreenshotOnFailure = TRUE;
    protected $screenshotPath = 'C:\wamp\www\screenshots';
    protected $screenshotUrl = 'http://localhost/screenshots';

    protected function setUp()
    {
        self::$_data = array(
            'admin_name'            => 'admin',
            'admin_password'        => '123123q',
            'admin_path'            => 'System/Manage Stores',
            'store_view_name'       => 'Test Store View Name',
            'store_name'            => 'Main Website Store',
            'store_view_status'     => 'Enabled',
            'store_view_sort_order' => 1,
            'success_message'       => 'The store view has been saved',
            'error_message'         => 'Store with the same code already exists.',
        );
        self::$_uimap = array(
            'admin_name'            => "//*[@id='username']",
            'admin_password'        => "//*[@id='login']",
            'login_button'          => "//input[@title='Login']",
            'admin_logo'            => "//img[@class='logo']",
            'add_store_view'        => '//button[span="Create Store View"]',
            'save_store_view'       => '//button[span="Save Store View"]',
            'store_name'            => "//*[@id='store_group_id']",
            'store_view_name'       => "//*[@id='store_name']",
            'store_view_code'       => "//*[@id='store_code']",
            'store_view_status'     => "//*[@id='store_is_active']",
            'store_view_sort_order' => "//*[@id='store_sort_order']",
            'success_message'       => "//li[normalize-space(@class)='success-msg']",
            'error_message'         => "//li[normalize-space(@class)='error-msg']"
        );
        $this->setBrowser("*chrome");
        $this->setBrowserUrl("http://kd.varien.com/dev/alexandr.malyshenko/1.9.x/index.php/");
        $this->start();
    }

    protected function assertPreConditions()
    {
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
        if ($openedPageName === $pageName) {
            $result = true;
        }
        $this->assertTrue($result);
    }

    /**
     * @dataProvider testData_Positive
     */
    public function testCreateStoreView_Positive($storeViewName, $storeViewCode)
    {
        $this->clickAndWait(self::$_uimap['add_store_view']);
        $this->select(self::$_uimap['store_name'], self::$_data['store_name']);
        $this->type(self::$_uimap['store_view_name'], $storeViewName);
        $this->type(self::$_uimap['store_view_code'], $storeViewCode);
        $this->select(self::$_uimap['store_view_status'], self::$_data['store_view_status']);
        $this->type(self::$_uimap['store_view_sort_order'], self::$_data['store_view_sort_order']);
        $this->click(self::$_uimap['save_store_view']);
        $this->waitForPageToLoad("40000");
        $this->assertTrue($this->isElementPresent(self::$_uimap['success_message']));
        $this->assertTrue($this->isTextPresent(self::$_data['success_message']));
        echo "\nTest is passed\n";
    }

    public function testData_Positive()
    {
        return array(
            array('Test Store View Name', 'store_view_code'),
            array('ThisIsVeryLongTextWithLength30ThisIsVeryLongTextWithLength60ThisIsVeryLongTextWithLength90ThisAVeryLongTextWithLength120ThisAVeryLongTextWithLength150ThisAVeryLongTextWithLength180ThisAVeryLongTextWithLength210ThisAVeryLongTextWithLength240ThisIsVeryLo255',
                'this_is_very_long_code_lenght_32'),
            array('1234567890!@#$%^&*()_"№;%:?*-.,/|', 'store_view_code_1'),
            array('n', 'm'),
        );
    }

    /**
     * @depends testCreateStoreView_Positive
     * @dataProvider testData_Negative
     */
    public function testCreateStoreView_Negative($storeViewCode)
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
        echo "\nTest is passed\n";
    }

    public function testData_Negative()
    {
        return array(
            array('store_View_code'),
            array('this_is_very_long_code_lenght_32_more'),
            array('store view code'),
            array('1store_view_code'),
            array('!@#$%^&*()_"№;%:?*-.,/|')
        );
    }

}
