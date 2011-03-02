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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @TODO
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Store_StoreView_CreateTest extends Mage_Selenium_TestCase
{

    /**
     * Navigate to System -> Manage Stores
     */
    protected function assertPreConditions()
    {
        $this->assertTrue($this->navigated('manage_stores'));
    }

    /**
     * @TODO
     */
    public function test_WithRequiredFieldsOnly()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithRequiredFieldsEmpty_EmptyName()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithRequiredFieldsEmpty_EmptyCode()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithLongValues()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithSpecialCharacters()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithInvalidCode()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithCodeThatAlreadyExists()
    {
        // @TODO
    }
    
/**
    public function setUpBeforeClass() {
        $this->assertTrue($this->adminLogin());
        $this->assertTrue($this->admin('dashboard'));
        $this->assertTrue($this->navigate('manage_stores'));
    }
*/

    public function testNavigation() {
        $this->assertTrue($this->clickButton('create_store_view'),
                'There is no "Create Store View" button on the page');
        $this->assertTrue($this->navigated('new_store_view'),
                'Wrong page is displayed');
        $this->assertTrue($this->controlIsPresent('button', 'back'),
                'There is no "Back" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'save_store_view'),
                'There is no "Save" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'reset'),
                'There is no "Reset" button on the page');
    }

    /**
     * @depends testNavigation
     */
    public function testStoreViewCreate_WithRequiredFieldsOnly() {
        $this->clickButton('create_store_view');
        $this->fillForm($this->loadData('generic_store_view',
                        array('store_view_sort_order' => null), 'store_view_code'));
        $this->clickButton('save_store_view');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
        $this->assertTrue($this->navigated('manage_stores'),
                'After successful creation store view should be redirected to Manage Stores page');
    }

    /**
     * @depends testNavigation
     * @depends testStoreViewCreate_WithRequiredFieldsOnly
     */
    public function testStoreViewCreate_WithCodeThatAlreadyExists() {
        $this->clickButton('create_store_view');
        $this->fillForm($this->loadData('generic_store_view', NULL, NULL));
        $this->clickButton('save_store_view');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
        $this->assertTrue($this->navigated('manage_stores'),
                'After successful creation store view should be redirected to Manage Stores page');
    }

    /**
     * @depends testNavigation
     * @dataProvider data_EmptyFields
     */
    public function testStoreViewCreate_WithRequiredFieldsEmpty($emptyField) {
        $this->clickButton('create_store_view');
        $this->fillForm($this->loadData('generic_store_view', $emptyField, 'store_view_code'));
        $this->clickButton('save_store_view');
        $this->assertTrue($this->errorMessage(), 'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
        $this->assertFalse($this->navigated('manage_stores'),
                "After unsuccessful creation store view doesn't have to be redirected to Manage Stores page");
    }

    public function data_EmptyFields() {
        return array(
            array('store_view_name' => null),
            array('store_view_code' => null)
        );
    }

    /**
     * @depends testNavigation
     */
    public function testStoreViewCreate_WithLongValuesMax() {
        $this->clickButton('create_store_view');
        $this->fillForm($this->loadData('generic_store_view',
                        array(
                            'store_view_name' => $this->generate('string', 255, ':alnum:'),
                            'store_view_code' => $this->generate('string', 32, ':alnum:')
                        ), 'store_view_code'));
        $this->clickButton('save_store_view');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
        $this->assertTrue($this->navigated('manage_stores'),
                'After successful creation store view should be redirected to Manage Stores page');
    }

    /**
     * @depends testNavigation
     */
    public function testStoreViewCreate_WithLongValuesMoreThanMax() {
        $this->clickButton('create_store_view');
        $longValues = array(
            'store_view_name' => $this->generate('string', 256, ':alnum:'),
            'store_view_code' => $this->generate('string', 33, ':alnum:'),
        );
        $this->fillForm($this->loadData('generic_store_view', $longValues, 'store_view_code'));
        $this->clickButton('save_store_view');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
        $this->assertTrue($this->navigated('manage_stores'),
                'After successful creation store view should be redirected to Manage Stores page');
        $this->searchAndOpen($longValues);
        $this->assertEquals(strlen($this->getValue('store_view_name')), 255);
        $this->assertEquals(strlen($this->getValue('store_view_code')), 32);
    }

    /**
     * @depends testNavigation
     * @dataProvider data_InvalidCode
     */
    public function testStoreViewCreate_WithInvalidCode($invalidCode) {
        $this->clickButton('create_store_view');
        $this->fillForm($this->loadData('generic_store_view', $invalidCode, 'store_view_code'));
        $this->clickButton('save_store_view');
        $this->assertTrue($this->errorMessage(), 'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
        $this->assertFalse($this->navigated('manage_stores'),
                "After unsuccessful creation store view doesn't have to be redirected to Manage Stores page");
    }

    public function data_InvalidCode() {
        return array(
            array('store_view_code' => 'invalid code'),
            array('store_view_code' => 'Invalid_code2'),
            array('store_view_code' => '2invalid_code2')
        );
    }
    
}