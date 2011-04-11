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
//        @TODO func 'navigated'
//        $this->assertTrue($this->navigated('new_store'),
//                'Wrong page is displayed');
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
        $storeData = $this->loadData('generic_store');
        //Steps
        $this->clickButton('create_store1');
        $this->fillForm($storeData);
        $this->clickButton('save_store');
        //Verifying
        $this->assertFalse($this->errorMessage(), $this->messages);
//        @TODO func 'navigated'
//        $this->assertTrue($this->navigated('manage_stores'),
//                'After successful creation store should be redirected to Manage Stores page');
        $this->assertTrue($this->successMessage('success_saved_store'),
                'No success message is displayed');
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
     */
    public function test_WithRequiredFieldsEmpty($emptyField)
    {
        //Data
        $storeData = $this->loadData('generic_store', $emptyField);
        //Steps
        $this->clickButton('create_store');
        $this->fillForm($storeData);
        $this->clickButton('save_store');
        //Verifying
        foreach ($emptyField as $key => $value) {
            $xpath = ($this->getCurrentLocationUimapPage()->getMainForm()->findField($key) == NULL) ?
                    ($this->getCurrentLocationUimapPage()->getMainForm()->findDropdown($key)) :
                    ($this->getCurrentLocationUimapPage()->getMainForm()->findField($key));
            $this->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('fieldXpath' => $xpath)));
        }
        $this->assertTrue($this->errorMessage('empty_required_field'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    /**
     * Data for test_WithRequiredFieldsEmpty
     * @return array
     */
    public function data_EmptyField()
    {
        return array(
            array(array('store_name' => NULL)),
            array(array('root_category' => NULL)),
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
     */
    public function test_WithLongValues()
    {
        //Data
        $longValues = array('store_name' => $this->generate('string', 255, ':alnum:'));
        $storeData = $this->loadData('generic_store', $longValues);
        //Steps
        $this->clickButton('create_store');
        $this->fillForm($storeData);
        $this->clickButton('save_store');
        //Verifying
        $this->assertFalse($this->errorMessage(), $this->messages);
//        @TODO func 'navigated'
//        $this->assertTrue($this->navigated('manage_stores'),
//                'After successful creation store should be redirected to Manage Stores page');
        $this->assertTrue($this->successMessage('success_saved_store'),
                'No success message is displayed');
//        @TODO
//        $this->searchAndOpen($searchData);
//        $xpathName = $this->getCurrentLocationUimapPage()->getMainForm()->findField('store_name');
//        $this->assertEquals(strlen($this->getValue($xpathName)), 255);
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
        //Data
        $storeData = $this->loadData('generic_store',
                        array('store_name' => $this->generate('string', 32, ':punct:')));
        //Steps
        $this->clickButton('create_store');
        $this->fillForm($storeData);
        $this->clickButton('save_store');
        //Verifying
        $this->assertFalse($this->errorMessage(), $this->messages);
//        @TODO func 'navigated'
//        $this->assertTrue($this->navigated('manage_stores'),
//                'After successful creation store should be redirected to Manage Stores page');
        $this->assertTrue($this->successMessage('success_saved_store'),
                'No success message is displayed');
    }

}
