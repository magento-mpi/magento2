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
 * Test creation new Store.
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Store_Store_CreateTest extends Mage_Selenium_TestCase {

    /**
     * Preconditions:
     *
     * Log in to Backend.
     *
     * Navigate to System -> Manage Stores
     */
    protected function assertPreConditions()
    {
        $this->assertTrue($this->loginAdminUser());
        $this->assertTrue($this->admin());
        $this->assertTrue($this->navigate('manage_stores'));
    }

    /**
     * Create Store. Fill in only reqired fields.
     *
     * Steps:
     *
     * 1. Click 'Create Store' button.
     *
     * 2. Fill in reqired fields.
     *
     * 3. Click 'Save Store' button.
     *
     * Expected result:
     *
     * Store is created.
     *
     * Success Message is displayed
     */
    public function test_WithRequiredFieldsOnly()
    {
        $this->clickButton('create_store');
        $this->fillForm($this->loadData('generic_store', NULL, NULL));
        $this->clickButton('save_store');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->navigated('manage_stores'),
                'After successful creation store should be redirected to Manage Stores page');
        $this->assertTrue($this->successMessage('success_saved_store'),
                'No success message is displayed');
    }

    /**
     * Create Store. Fill in all reqired fields except the field "Name" .
     *
     * Steps:
     *
     * 1. Click 'Create Store' button.
     *
     * 2. Fill in fields except the field "Name"
     *
     * 3. Click 'Save Store' button.
     *
     * Expected result:
     *
     * Store is not created.
     *
     * Error Message is displayed for field "Name".
     */
    public function test_WithRequiredFieldsEmpty_EmptyName()
    {
        $this->clickButton('create_store');
        $this->fillForm($this->loadData('generic_store',
                        array('store_name' => null), NULL));
        $this->clickButton('save_store');
        $this->assertTrue($this->errorMessage('empty_reqired_field'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    /**
     * Create Store. Fill in all reqired fields except the field "Root Category".
     *
     * Steps:
     *
     * 1. Click 'Create Store' button.
     *
     * 2. Fill in fields except the field "Root Category"
     *
     * 3. Click 'Save Store' button.
     *
     * Expected result:
     *
     * Store is not created.
     *
     * Error Message is displayed for field "Root Category".
     */
    public function test_WithRequiredFieldsEmpty_EmptyRootCategory()
    {
        $this->clickButton('create_store');
        $this->fillForm($this->loadData('generic_store',
                        array('root_category' => null), NULL));
        $this->clickButton('save_store');
        $this->assertTrue($this->errorMessage('empty_reqired_field'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    /**
     * Create Store. Fill in only reqired fields. Use max long values for field 'Name'
     *
     * Steps:
     *
     * 1. Click 'Create Store' button.
     *
     * 2. Fill in required fields by long value alpha-numeric data.
     *
     * 3. Click 'Save Store' button.
     *
     * Expected result:
     *
     * Store is created. Success Message is displayed.
     *
     * Length of field "Name" is 255 characters.
     */
    public function test_WithLongValues()
    {
        $this->clickButton('create_store');
        $longValues = array(
            'store_name' => $this->generate('string', 255, ':alnum:'),
        );
        $this->fillForm($this->loadData('generic_store', $longValues, NULL));
        $this->clickButton('save_store');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->navigated('manage_stores'),
                'After successful creation store should be redirected to Manage Stores page');
        $this->assertTrue($this->successMessage('success_saved_store'),
                'No success message is displayed');
        // @TODO
        //$this->searchAndOpen($longValues);
        //$xpathName = $this->_getUimapData('');
        //$this->assertEquals(strlen($this->getValue($xpathName)), 255);
    }

    /**
     * Create Store. Fill in field 'Name' by using special characters.
     *
     * Steps:
     *
     * 1. Click 'Create Store' button.
     *
     * 2. Fill in 'Name' field by special characters.
     *
     * 3. Fill other required fields by regular data.
     *
     * 4. Click 'Save Store' button.
     *
     * Expected result:
     *
     * Store is created.
     *
     * Success Message is displayed
     */
    public function test_WithSpecialCharacters_InName()
    {
        $this->clickButton('create_store');
        $this->fillForm($this->loadData('generic_store',
                        array('store_name' => $this->generate('string', 32, ':punct:')), null));
        $this->clickButton('save_store');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage('success_saved_store'),
                'No success message is displayed');
        $this->assertTrue($this->navigated('manage_stores'),
                'After successful creation store should be redirected to Manage Stores page');
    }

}
