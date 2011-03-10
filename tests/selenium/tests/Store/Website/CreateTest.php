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
 * Test creation new website
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Store_Website_CreateTest extends Mage_Selenium_TestCase {

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
     * Create Website. Fill in only reqired fields.
     *
     * Steps:
     *
     * 1. Click 'Create Website' button.
     *
     * 2. Fill in reqired fields.
     *
     * 3. Click 'Save Website' button.
     *
     * Expected result:
     *
     * Website is created.
     *
     * Success Message is displayed
     */
    public function test_WithRequiredFieldsOnly()
    {
        $this->clickButton('create_website');
        $this->fillForm($this->loadData('generic_website', NULL, NULL));
        $this->clickButton('save_website');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->navigated('manage_stores'),
                'After successful creation Website should be redirected to Manage Stores page');
        $this->assertTrue($this->successMessage('success_saved_website'),
                'No success message is displayed');
    }

    /**
     * Create Website. Fill in all reqired fields except the field "Name" .
     *
     * Steps:
     *
     * 1. Click 'Create Website' button.
     *
     * 2. Fill in fields.
     *
     * 3. Click 'Save Website' button.
     *
     * Expected result:
     *
     * Website is not created.
     *
     * Error Message is displayed.
     */
    public function test_WithRequiredFieldsEmpty_EmptyName()
    {
        $this->clickButton('create_website');
        $this->fillForm($this->loadData('generic_website',
                        array('website_name' => null), 'website_code'));
        $this->clickButton('save_website');
        $this->assertTrue($this->errorMessage('empty_reqired_field'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    /**
     * Create Website. Fill in all reqired fields  except the field "Code".
     *
     * Steps:
     *
     * 1. Click 'Create Website' button.
     *
     * 2. Fill in fields.
     *
     * 3. Click 'Save Website' button.
     *
     * Expected result:
     *
     * Website is not created.
     *
     * Error Message is displayed.
     */
    public function test_WithRequiredFieldsEmpty_EmptyCode()
    {
        $this->clickButton('create_website');
        $this->fillForm($this->loadData('generic_website',
                        array('website_code' => null), NULL));
        $this->clickButton('save_website');
        $this->assertTrue($this->errorMessage('empty_reqired_field'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    /**
     * Create Website. Fill in only reqired fields. Use max long values for fields 'Name' and 'Code'
     *
     * Steps:
     *
     * 1. Click 'Create Website' button.
     *
     * 2. Fill in required fields by long value alpha-numeric data.
     *
     * 3. Click 'Save Website' button.
     *
     * Expected result:
     *
     * Website is created. Success Message is displayed.
     *
     * Length of field "Name" is 255 characters. Length of field "Code" is 32 characters.
     */
    public function test_WithLongValues()
    {
        $this->clickButton('create_website');
        $longValues = array(
            'website_name' => $this->generate('string', 255, ':alnum:'),
            'website_code' => $this->generate('string', 32, array(':lower:', ':digit:'))
        );
        $this->fillForm($this->loadData('generic_website', $longValues, NULL));
        $this->clickButton('save_website');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->navigated('manage_stores'),
                'After successful creation Website should be redirected to Manage Stores page');
        $this->assertTrue($this->successMessage('success_saved_website'),
                'No success message is displayed');
        // @TODO
        //$this->searchAndOpen($longValues);
        //$xpathName = $this->_getUimapData('');
        //$xpathCode = $this->_getUimapData('');
        //$this->assertEquals(strlen($this->getValue($xpathName)), 255);
        //$this->assertEquals(strlen($this->getValue($xpathCode)), 32);
    }

    /**
     * Create Website. Fill in field 'Name' by using special characters.
     *
     * Steps:
     *
     * 1. Click 'Create Website' button.
     *
     * 2. Fill in 'Name' field by special characters.
     *
     * 3. Fill other required fields by regular data.
     *
     * 4. Click 'Save Website' button.
     *
     * Expected result:
     *
     * Website is created.
     *
     * Success Message is displayed
     */
    public function test_WithSpecialCharacters_InName()
    {
        $this->clickButton('create_website');
        $this->fillForm($this->loadData('generic_website',
                        array('website_name' => $this->generate('string', 32, ':punct:')),
                        'website_code'));
        $this->clickButton('save_website');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage('success_saved_website'),
                'No success message is displayed');
        $this->assertTrue($this->navigated('manage_stores'),
                'After successful creation Website should be redirected to Manage Stores page');
    }

    /**
     * Create Website.  Fill in field 'Code' by using special characters.
     *
     * Steps:
     *
     * 1. Click 'Create Website' button.
     *
     * 2. Fill in 'Code' field by special characters.
     *
     * 3. Fill other required fields by regular data.
     *
     * 4. Click 'Save Website' button.
     *
     * Expected result:
     *
     * Website is not created.
     *
     * Error Message is displayed.
     */
    public function test_WithSpecialCharacters_InCode()
    {
        $this->clickButton('create_website');
        $this->fillForm($this->loadData('generic_website',
                        array('website_name' => $this->generate('string', 32, ':punct:')), NULL));
        $this->clickButton('save_website');
        $this->assertTrue($this->errorMessage('wrong_website_code'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    /**
     * Create Website.  Fill in field 'Code' by using wrong values.
     *
     * Steps:
     *
     * 1. Click 'Create Website' button.
     *
     * 2. Fill in 'Code' field by wrong value.
     *
     * 3. Fill other required fields by regular data.
     *
     * 4. Click 'Save Website' button.
     *
     * Expected result:
     *
     * Website is not created.
     *
     * Error Message is displayed.
     *
     * @dataProvider data_InvalidCode
     */
    public function test_WithInvalidCode($invalidCode)
    {
        $this->clickButton('create_website');
        $this->fillForm($this->loadData('generic_website', $invalidCode, null));
        $this->clickButton('save_website');
        $this->assertTrue($this->errorMessage('wrong_website_code'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    public function data_InvalidCode()
    {
        return array(
            array('website_code' => 'invalid code'),
            array('website_code' => 'Invalid_code2'),
            array('website_code' => '2invalid_code2')
        );
    }

    /**
     * Create Website.  Fill in field 'Code' by using code that already exist.
     *
     * Steps:
     *
     * 1. Click 'Create Website' button.
     *
     * 2. Fill in 'Code' field by using code that already exist.
     *
     * 3. Fill other required fields by regular data.
     *
     * 4. Click 'Save Website' button.
     *
     * Expected result:
     *
     * Website is not created.
     *
     * Error Message is displayed.
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithCodeThatAlreadyExists()
    {
        $this->clickButton('create_website');
        $this->fillForm($this->loadData('generic_website', NULL, NULL));
        $this->clickButton('save_website');
        $this->assertTrue($this->errorMessage('website_code_exist'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    /**
     * Create Website with several Stores assigned to one Root Category
     *
     * Steps:
     *
     * 1. Create website
     *
     * 2. Create first store
     *
     * 3. Create second store
     */
    public function test_WithSeveralStores_AssignedToOneRootCategory()
    {
        //create website
        $this->clickButton('create_website');
        $this->fillForm($this->loadData('generic_website',
                        array('website_name' => 'withSeveralStores'), 'website_code'));
        $this->clickButton('save_website');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->navigated('manage_stores'),
                'After successful creation Website should be redirected to Manage Stores page');
        $this->assertTrue($this->successMessage('success_saved_website'),
                'No success message is displayed');
        //create first store
        $this->clickButton('create_store');
        $this->fillForm($this->loadData('generic_store',
                        array('website' => 'withSeveralStores', 'store_name' => 'store_1'), NULL));
        $this->clickButton('save_store');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->navigated('manage_stores'),
                'After successful creation store should be redirected to Manage Stores page');
        $this->assertTrue($this->successMessage('success_saved_store'),
                'No success message is displayed');
        //create second store
        $this->clickButton('create_store');
        $this->fillForm($this->loadData('generic_store',
                        array('website' => 'withSeveralStores', 'store_name' => 'store_2'), NULL));
        $this->clickButton('save_store');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->navigated('manage_stores'),
                'After successful creation store should be redirected to Manage Stores page');
        $this->assertTrue($this->successMessage('success_saved_store'),
                'No success message is displayed');
    }

    /**
     * Create Website with several Store Views in one Store
     *
     * Steps:
     *
     * 1. Create website
     *
     * 2. Create store
     *
     * 3. Create first store view
     *
     * 4. Create second store view
     */
    public function test_WithSeveralStoresViewsInOneStore()
    {
        //create website
        $this->clickButton('create_website');
        $this->fillForm($this->loadData('generic_website',
                        array('website_name' => 'WithSeveralStoresViewsInOneStore'), 'website_code'));
        $this->clickButton('save_website');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->navigated('manage_stores'),
                'After successful creation Website should be redirected to Manage Stores page');
        $this->assertTrue($this->successMessage('success_saved_website'),
                'No success message is displayed');
        //create store
        $this->clickButton('create_store');
        $this->fillForm($this->loadData('generic_store',
                        array('website' => 'WithSeveralStoresViewsInOneStore', 'store_name' => 'test_store'), NULL));
        $this->clickButton('save_store');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->navigated('manage_stores'),
                'After successful creation store should be redirected to Manage Stores page');
        $this->assertTrue($this->successMessage('success_saved_store'),
                'No success message is displayed');
        //create two store view
        for ($i = 1; $i <= 2; $i++) {
            $this->clickButton('create_store_view');
            $this->fillForm($this->loadData(
                            'generic_store_view',
                            array('store_name' => 'test_store'),
                            array('store_view_name', 'store_view_code')
            ));
            $this->clickButton('save_store_view');
            $this->assertFalse($this->errorMessage(), $this->messages);
            $this->assertTrue($this->navigated('manage_stores'),
                    'After successful creation store view should be redirected to Manage Stores page');
            $this->assertTrue($this->successMessage('success_saved_store_view'),
                    'No success message is displayed');
            $i +=1;
        }
    }

}
