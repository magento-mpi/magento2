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
class Store_Website_CreateTest extends Mage_Selenium_TestCase
{

    /**
     * Log in to Backend.
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * Preconditions:
     * Navigate to System -> Manage Stores
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_stores');
        $this->assertTrue($this->checkCurrentPage('manage_stores'), 'Wrong page is opened');
    }

    /**
     * Test navigation.
     *
     * Steps:
     *
     * 1. Verify that 'Create Website' button is present and click her.
     *
     * 2. Verify that the create website page is opened.
     *
     * 3. Verify that 'Back' button is present.
     *
     * 4. Verify that 'Save Website' button is present.
     *
     * 5. Verify that 'Reset' button is present.
     */
    public function test_Navigation()
    {
        $this->assertTrue($this->clickButton('create_website'),
                'There is no "Create Website" button on the page');
        $this->assertTrue($this->checkCurrentPage('new_website'), 'Wrong page is opened');
        $this->assertTrue($this->controlIsPresent('button', 'back'),
                'There is no "Back" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'save_website'),
                'There is no "Save" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'reset'),
                'There is no "Reset" button on the page');
    }

    /**
     * Create Website. Fill in only required fields.
     *
     * Steps:
     *
     * 1. Click 'Create Website' button.
     *
     * 2. Fill in required fields.
     *
     * 3. Click 'Save Website' button.
     *
     * Expected result:
     *
     * Website is created.
     *
     * Success Message is displayed
     *
     * @depends test_Navigation
     */
    public function test_WithRequiredFieldsOnly()
    {
        //Data
        $websiteData = $this->loadData('generic_website', NULL,
                        array('website_name', 'website_code'));
        //Steps
        $this->storeHelper()->createWebsite($websiteData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_website'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_stores'),
                'After successful creation store view should be redirected to Manage Stores page');

        return $websiteData;
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
    public function test_WithCodeThatAlreadyExists(array $websiteData)
    {
        //Steps
        $this->storeHelper()->createWebsite($websiteData);
        //Verifying
        $this->assertTrue($this->errorMessage('website_code_exist'), $this->messages);
    }

    /**
     * Create Website. Fill in all required fields except one field.
     *
     * Steps:
     *
     * 1. Click 'Create Website' button.
     *
     * 2. Fill in required fields except one field.
     *
     * 3. Click 'Save Website' button.
     *
     * Expected result:
     *
     * Website is not created.
     *
     * Error Message is displayed.
     *
     * @depends test_WithRequiredFieldsOnly
     * @dataProvider data_EmptyField
     */
    public function test_WithRequiredFieldsEmpty($emptyField)
    {
        //Data
        if ($emptyField == 'website_code') {
            $websiteData = $this->loadData('generic_website', array($emptyField => '%noValue%'));
        } else {
            $websiteData = $this->loadData('generic_website', array($emptyField => '%noValue%'),
                            'website_code');
        }
        //Steps
        $this->storeHelper()->createWebsite($websiteData);
        //Verifying
        $xpath = $this->_getControlXpath('field', $emptyField);
        $this->addParameter('fieldXpath', $xpath);
        $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function data_EmptyField()
    {
        return array(
            array('website_name'),
            array('website_code'),
        );
    }

    /**
     * Create Website. Fill in only required fields. Use max long values for fields 'Name' and 'Code'
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
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithLongValues()
    {
        //Data
        $longValues = array(
            'website_name' => $this->generate('string', 255, ':alnum:'),
            'website_code' => $this->generate('string', 32, ':lower:')
        );
        $websiteData = $this->loadData('generic_website', $longValues);
        //Steps
        $this->storeHelper()->createWebsite($websiteData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_website'), $this->messages);
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
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithSpecialCharacters_InName()
    {
        //Data
        $websiteData = $this->loadData('generic_website',
                        array('website_name' => $this->generate('string', 32, ':punct:')),
                        'website_code');
        //Steps
        $this->storeHelper()->createWebsite($websiteData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_website'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_stores'),
                'After successful creation store view should be redirected to Manage Stores page');
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
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithInvalidCode($invalidCode)
    {
        //Data
        $websiteData = $this->loadData('generic_website', array('website_code' => $invalidCode));
        //Steps
        $this->storeHelper()->createWebsite($websiteData);
        //Verifying
        $this->assertTrue($this->errorMessage('wrong_website_code'), $this->messages);
    }

    public function data_InvalidCode()
    {
        return array(
            array('invalid code'),
            array('Invalid_code2'),
            array('2invalid_code2'),
            array($this->generate('string', 32, ':punct:'))
        );
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
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithSeveralStores_AssignedToOneRootCategory()
    {
        //1.1.Create website
        //Data
        $websiteData = $this->loadData('generic_website', NULL,
                        array('website_name', 'website_code'));
        //Steps
        $this->storeHelper()->createWebsite($websiteData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_website'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_stores'),
                'After successful creation store view should be redirected to Manage Stores page');
        //1.2.Create two stores
        for ($i = 1; $i <= 2; $i++) {
            //Data
            $storeData = $this->loadData('generic_store',
                            array('website' => $websiteData['website_name']), 'store_name');
            //Steps
            $this->storeHelper()->createStore($storeData);
            //Verifying
            $this->assertTrue($this->successMessage('success_saved_store'), $this->messages);
            $this->assertTrue($this->checkCurrentPage('manage_stores'),
                    'After successful creation store view should be redirected to Manage Stores page');
        }
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
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithSeveralStoresViewsInOneStore()
    {
        //1.1.Create website
        //Data
        $websiteData = $this->loadData('generic_website', Null,
                        array('website_code', 'website_name'));
        //Steps
        $this->storeHelper()->createWebsite($websiteData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_website'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_stores'),
                'After successful creation store view should be redirected to Manage Stores page');
        //1.2.Create store
        //Data
        $storeData = $this->loadData('generic_store',
                        array('website' => $websiteData['website_name']), 'store_name');
        //Steps
        $this->storeHelper()->createStore($storeData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_store'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_stores'),
                'After successful creation store view should be redirected to Manage Stores page');
        //1.3.Create two store view
        for ($i = 1; $i <= 2; $i++) {
            //Data
            $storeViewData = $this->loadData('generic_store_view',
                            array('store_name' => $storeData['store_name']),
                            array('store_view_name', 'store_view_code'));
            //Steps
            $this->storeHelper()->createStoreView($storeViewData);
            //Verifying
            $this->assertTrue($this->successMessage('success_saved_store_view'), $this->messages);
            $this->assertTrue($this->checkCurrentPage('manage_stores'),
                    'After successful creation store view should be redirected to Manage Stores page');
        }
    }

}
