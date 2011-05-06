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
        $this->loginAdminUser();
        $this->assertTrue($this->admin());
        $this->navigate('manage_stores');
        $this->assertTrue($this->checkCurrentPage('manage_stores'),
                'Wrong page is opened');
    }

    /**
     * Test navigation.
     *
     * Steps:
     *
     * 1. Verify that 'Create Store' button is present and click her.
     *
     * 2. Verify that the create store page is opened.
     *
     * 3. Verify that 'Back' button is present.
     *
     * 4. Verify that 'Save Store' button is present.
     *
     * 5. Verify that 'Reset' button is present.
     */
    public function test_Navigation()
    {
        $this->assertTrue($this->clickButton('create_store'),
                'There is no "Create Store" button on the page');
        $this->assertTrue($this->checkCurrentPage('new_store'),
                'Wrong page is opened');
        $this->assertTrue($this->controlIsPresent('button', 'back'),
                'There is no "Back" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'save_store'),
                'There is no "Save" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'reset'),
                'There is no "Reset" button on the page');
    }

    /**
     * Create Store. Fill in only required fields.
     *
     * Steps:
     *
     * 1. Click 'Create Store' button.
     *
     * 2. Fill in required fields.
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
        //Data
        $storeData = $this->loadData('generic_store', Null, 'store_name');
        //Steps
        $this->clickButton('create_store');
        $this->fillForm($storeData);
        $this->saveForm('save_store');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_store'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_stores'),
                'After successful creation store should be redirected to Manage Stores page');
    }

    /**
     * Create Store. Fill in required fields except one field.
     *
     * Steps:
     *
     * 1. Click 'Create Store' button.
     *
     * 2. Fill in fields except one required field.
     *
     * 3. Click 'Save Store' button.
     *
     * Expected result:
     *
     * Store is not created.
     *
     * Error Message is displayed.
     *
     * @dataProvider data_EmptyField
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithRequiredFieldsEmpty($emptyField)
    {
        //Data
        $storeData = $this->loadData('generic_store', $emptyField);
        //Steps
        $this->clickButton('create_store');
        $this->fillForm($storeData);
        $this->saveForm('save_store');
        //Verifying
        $page = $this->getCurrentLocationUimapPage();
        foreach ($emptyField as $key => $value) {
            $xpath = ($page->findField($key) == NULL) ? ($page->findDropdown($key)) : ($page->findField($key));
            $this->addParameter('fieldXpath', $xpath);
        }
        $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    /**
     * Data for test_WithRequiredFieldsEmpty
     * @return array
     */
    public function data_EmptyField()
    {
        return array(
            array(array('store_name' => '')),
            array(array('root_category' => '')),
        );
    }

    /**
     * Create Store. Fill in only required fields. Use max long values for field 'Name'
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
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithLongValues()
    {
        //Data
        $longValues = array('store_name' => $this->generate('string', 255, ':alnum:'));
        $storeData = $this->loadData('generic_store', $longValues);
        //Steps
        $this->clickButton('create_store');
        $this->fillForm($storeData);
        $this->saveForm('save_store');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_store'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_stores'),
                'After successful creation store should be redirected to Manage Stores page');
//        @TODO
//        //Steps
//        $this->searchAndOpen($longValues);
//        //Verifying
//        $page = $this->getCurrentLocationUimapPage();
//        $xpathName = $page->findField('store_name');
//        $this->assertEquals($longValues['store_name'], $this->getValue('//' . $xpathName),
//                "The stored value for '$key' field is not equal to specified");
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
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithSpecialCharacters_InName()
    {
        //Data
        $storeData = $this->loadData('generic_store',
                        array('store_name' => $this->generate('string', 32, ':punct:')));
        //Steps
        $this->clickButton('create_store');
        $this->fillForm($storeData);
        $this->saveForm('save_store');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_store'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_stores'),
                'After successful creation store should be redirected to Manage Stores page');
    }

}
