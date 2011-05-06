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
 * Test creation new store view
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Store_StoreView_CreateTest extends Mage_Selenium_TestCase {

    /**
     * Preconditions:
     *
     * Log in to Backend.
     *
     * Navigate to System -> Manage Stores
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->assertTrue($this->checkCurrentPage('dashboard'),
                'Wrong page is opened');
        $this->navigate('manage_stores');
        $this->assertTrue($this->checkCurrentPage('manage_stores'),
                'Wrong page is opened');
    }

    /**
     * Test navigation.
     *
     * Steps:
     *
     * 1. Verify that 'Create Store View' button is present and click her.
     *
     * 2. Verify that the create store view page is opened.
     *
     * 3. Verify that 'Back' button is present.
     *
     * 4. Verify that 'Save Store View' button is present.
     *
     * 5. Verify that 'Reset' button is present.
     */
    public function test_Navigation()
    {
        $this->assertTrue($this->clickButton('create_store_view'),
                'There is no "Create Store View" button on the page');
        $this->assertTrue($this->checkCurrentPage('new_store_view'),
                'Wrong page is opened');
        $this->assertTrue($this->controlIsPresent('button', 'back'),
                'There is no "Back" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'save_store_view'),
                'There is no "Save" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'reset'),
                'There is no "Reset" button on the page');
    }

    /**
     * Create Store View. Fill in only required fields.
     *
     * Steps:
     *
     * 1. Click 'Create Store View' button.
     *
     * 2. Fill in required fields.
     *
     * 3. Click 'Save Store View' button.
     *
     * Expected result:
     *
     * Store View is created.
     *
     * Success Message is displayed
     *
     * @depends test_Navigation
     */
    public function test_WithRequiredFieldsOnly()
    {
        //Data
        $storeViewData = $this->loadData('generic_store_view',
                        NULL, array('store_view_name', 'store_view_code'));
        //Steps
        $this->clickButton('create_store_view');
        $this->fillForm($storeViewData);
        $this->saveForm('save_store_view');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_store_view'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_stores'),
                'After successful creation store view should be redirected to Manage Stores page');

        return $storeViewData;
    }

    /**
     * Create Store View.  Fill in field 'Code' by using code that already exist.
     *
     * Steps:
     *
     * 1. Click 'Create Store View' button.
     *
     * 2. Fill in 'Code' field by using code that already exist.
     *
     * 3. Fill other required fields by regular data.
     *
     * 4. Click 'Save Store View' button.
     *
     * Expected result:
     *
     * Store View is not created.
     *
     * Error Message is displayed.
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithCodeThatAlreadyExists(array $storeViewData)
    {
        //Steps
        $this->clickButton('create_store_view');
        $this->fillForm($storeViewData);
        $this->saveForm('save_store_view');
        //Verifying
        $this->assertTrue($this->errorMessage('store_view_code_exist'), $this->messages);
    }

    /**
     * Create Store View. Fill in  required fields except one field.
     *
     * Steps:
     *
     * 1. Click 'Create Store View' button.
     *
     * 2. Fill in required fields except one field.
     *
     * 3. Click 'Save Store View' button.
     *
     * Expected result:
     *
     * Store View is not created.
     *
     * Error Message is displayed.
     *
     * @depends test_WithRequiredFieldsOnly
     * @dataProvider data_EmptyField
     */
    public function test_WithRequiredFieldsEmpty($emptyField)
    {
        //Data
        $storeViewData = $this->loadData('generic_store_view', $emptyField);
        //Steps
        $this->clickButton('create_store_view');
        $this->fillForm($storeViewData);
        $this->saveForm('save_store_view');
        //Verifying
        $page = $this->getUimapPage('admin', 'new_store_view');
        foreach ($emptyField as $key => $value) {
            $xpath = $page->findField($key);
            $this->addParameter('fieldXpath', $xpath);
        }
        $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function data_EmptyField()
    {
        return array(
            array(array('store_view_name' => '')),
            array(array('store_view_code' => '')),
        );
    }

    /**
     * Create Store View. Fill in only required fields. Use max long values for fields 'Name' and 'Code'
     *
     * Steps:
     *
     * 1. Click 'Create Store View' button.
     *
     * 2. Fill in required fields by long value alpha-numeric data.
     *
     * 3. Click 'Save Store View' button.
     *
     * Expected result:
     *
     * Store View is created. Success Message is displayed.
     *
     * Length of field "Name" is 255 characters. Length of field "Code" is 32 characters.
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithLongValues()
    {
        //Data
        $longValues = array(
            'store_view_name' => $this->generate('string', 255, ':alnum:'),
            'store_view_code' => $this->generate('string', 32, ':lower:')
        );
        $storeViewData = $this->loadData('generic_store_view', $longValues);
        //Steps
        $this->clickButton('create_store_view');
        $this->fillForm($storeViewData);
        $this->saveForm('save_store_view');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_store_view'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_stores'),
                'After successful creation store view should be redirected to Manage Stores page');
//        @TODO
//        $this->searchAndOpen($longValues);
//        $page = $this->getCurrentLocationUimapPage();
//        foreach ($longValues as $key => $value) {
//            $xpath = $page->findField($key);
//            $this->assertEquals($longValues[$key], $this->getValue('//' . $xpath),
//                    "The stored value for '$key' field is not equal to specified");
//        }
    }

    /**
     * Create Store View. Fill in field 'Name' by using special characters.
     *
     * Steps:
     *
     * 1. Click 'Create Store View' button.
     *
     * 2. Fill in 'Name' field by special characters.
     *
     * 3. Fill other required fields by regular data.
     *
     * 4. Click 'Save Store View' button.
     *
     * Expected result:
     *
     * Store View is created.
     *
     * Success Message is displayed
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithSpecialCharacters_InName()
    {
        //Data
        $storeViewData = $this->loadData(
                        'generic_store_view',
                        array('store_view_name' => $this->generate('string', 32, ':punct:')),
                        'store_view_code'
        );
        //Steps
        $this->clickButton('create_store_view');
        $this->fillForm($storeViewData);
        $this->saveForm('save_store_view');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_store_view'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_stores'),
                'After successful creation store view should be redirected to Manage Stores page');
    }

    /**
     * Create Store View.  Fill in field 'Code' by using special characters.
     *
     * Steps:
     *
     * 1. Click 'Create Store View' button.
     *
     * 2. Fill in 'Code' field by special characters.
     *
     * 3. Fill other required fields by regular data.
     *
     * 4. Click 'Save Store View' button.
     *
     * Expected result:
     *
     * Store View is not created.
     *
     * Error Message is displayed.
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithSpecialCharacters_InCode()
    {
        //Data
        $storeViewData = $this->loadData('generic_store_view',
                        array('store_view_code' => $this->generate('string', 32, ':punct:')));
        //Steps
        $this->clickButton('create_store_view');
        $this->fillForm($storeViewData);
        $this->saveForm('save_store_view');
        //Verifying
        $this->assertTrue($this->errorMessage('wrong_store_view_code'), $this->messages);
    }

    /**
     * Create Store View.  Fill in field 'Code' by using wrong values.
     *
     * Steps:
     *
     * 1. Click 'Create Store View' button.
     *
     * 2. Fill in 'Code' field by wrong value.
     *
     * 3. Fill other required fields by regular data.
     *
     * 4. Click 'Save Store View' button.
     *
     * Expected result:
     *
     * Store View is not created.
     *
     * Error Message is displayed.
     *
     * @dataProvider data_InvalidCode
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithInvalidCode($invalidCode)
    {
        //Data
        $storeViewData = $this->loadData('generic_store_view', $invalidCode);
        //Steps
        $this->clickButton('create_store_view');
        $this->fillForm($storeViewData);
        $this->saveForm('save_store_view');
        //Verifying
        $this->assertTrue($this->errorMessage('wrong_store_view_code'), $this->messages);
    }

    public function data_InvalidCode()
    {
        return array(
            array(array('store_view_code' => 'invalid code')),
            array(array('store_view_code' => 'Invalid_code2')),
            array(array('store_view_code' => '2invalid_code2'))
        );
    }

}