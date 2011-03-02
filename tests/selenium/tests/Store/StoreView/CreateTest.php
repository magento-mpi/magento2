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

/**
 * Test Customer Registration
 */
class CreateTest extends Mage_Selenium_TestCase {

    public static function setUpBeforeClass() {
        $this->assertTrue($this->loginAdminUser());
        $this->assertTrue($this->admin('dashboard'));
        $this->assertTrue($this->navigate('manage_stores'));
    }

    protected function assertPreConditions() {
        $this->assertTrue($this->navigated('manage_stores'));
    }

    public function testNavigation() {
        $this->assertTrue($this->clickButton('create_store_view'), 'There is no "Create Store View" button on the page');
        $this->assertTrue($this->navigated('new_store_view'), 'Wrong page is displayed');
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_store_view'), 'There is no "Save" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
    }

    /**
     * @depends testNavigation
     */
    public function testStoreViewCreate_WithRequiredFieldsOnly() {
        $this->clickButton('create_store_view');
        $this->fillForm($this->data('generic_store_view', null, 'store_view_code'));
        $this->clickButton('save_store_view');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
        $this->assertTrue($this->navigated('manage_stores'), 'After successful creation store view should be redirected to Manage Stores page');
    }

    /**
     * @depends testNavigation
     */
    public function testStoreViewCreate_WithLongValues() {
        $this->clickButton('create_store_view');
        $this->fillForm($this->data('generic_store_view', array(
                    'store_view_name' => $this->generate('string', 255, ':alnum:'),
                    'store_view_code' => $this->generate('string', 32, ':alnum:'),
                        ), 'store_view_code'));
        $this->clickButton('save_store_view');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
        $this->assertTrue($this->navigated('manage_stores'), 'After successful creation store view should be redirected to Manage Stores page');
    }

}