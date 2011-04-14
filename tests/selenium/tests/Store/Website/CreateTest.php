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
//        @TODO func 'navigated'
//        $this->assertTrue($this->navigated('new_website'),
//                'Wrong page is displayed');
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
     */
    public function test_WithRequiredFieldsOnly()
    {
        //Data
        $websiteData = $this->loadData('generic_website');
        //Steps
        $this->clickButton('create_website');
        $this->fillForm($websiteData);
        $this->clickButton('save_website');
        //Verifying
        $this->assertFalse($this->errorMessage(), $this->messages);
//        @TODO func 'navigated'
//        $this->assertTrue($this->navigated('manage_stores'),
//                'After successful creation website should be redirected to Manage Stores page');
        $this->assertTrue($this->successMessage('success_saved_website'),
                'No success message is displayed');
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
        //Data
        $websiteData = $this->loadData('generic_website');
        //Steps
        $this->clickButton('create_website');
        $this->fillForm($websiteData);
        $this->clickButton('save_website');
        //Verifying
        $this->assertTrue($this->errorMessage('website_code_exist'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    /**
     * Create Website. Fill in all required fields except the field "Name".
     *
     * Steps:
     *
     * 1. Click 'Create Website' button.
     *
     * 2. Fill in required fields except the field "Name".
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
        //Data
        $websiteData = $this->loadData('generic_website',
                        array('website_name' => NULL), 'website_code');
        //Steps
        $this->clickButton('create_website');
        $this->fillForm($websiteData);
        $this->clickButton('save_website');
        //Verifying
        $xpath = $this->getCurrentLocationUimapPage()->getMainForm()->findField('website_name');
        $this->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('fieldXpath' => $xpath)));
        $this->assertTrue($this->errorMessage('empty_required_field'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    /**
     * Create Website. Fill in all required fields except the field "Code".
     *
     * Steps:
     *
     * 1. Click 'Create Website' button.
     *
     * 2. Fill in required fields except the field "Code".
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
        //Data
        $websiteData = $this->loadData('generic_website', array('website_code' => NULL));
        //Steps
        $this->clickButton('create_website');
        $this->fillForm($websiteData);
        $this->clickButton('save_website');
        //Verifying
        $xpath = $this->getCurrentLocationUimapPage()->getMainForm()->findField('website_code');
        $this->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('fieldXpath' => $xpath)));
        $this->assertTrue($this->errorMessage('empty_required_field'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
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
     */
    public function test_WithLongValues()
    {
        //Data
        $longValues = array(
            'website_name' => $this->generate('string', 255, ':alnum:'),
            'website_code' => $this->generate('string', 32, array(':lower:'))
        );
        $websiteData = $this->loadData('generic_website', $longValues);
        //Steps
        $this->clickButton('create_website');
        $this->fillForm($websiteData);
        $this->clickButton('save_website');
        //Verifying
        $this->assertFalse($this->errorMessage(), $this->messages);
//        @TODO func 'navigated'
//        $this->assertTrue($this->navigated('manage_stores'),
//                'After successful creation website should be redirected to Manage Stores page');
        $this->assertTrue($this->successMessage('success_saved_website'),
                'No success message is displayed');
//        @TODO
//        $this->searchAndOpen($searchData);
//        $xpathName = $this->getCurrentLocationUimapPage()->getMainForm()->findField('website_name');
//        $this->assertEquals(strlen($this->getValue($xpathName)), 255);
//        $xpathName = $this->getCurrentLocationUimapPage()->getMainForm()->findField('website_code');
//        $this->assertEquals(strlen($this->getValue($xpathName)), 32);
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
        //Data
        $websiteData = $this->loadData('generic_website',
                        array('website_name' => $this->generate('string', 32, ':punct:')),
                        'website_code');
        //Steps
        $this->clickButton('create_website');
        $this->fillForm($websiteData);
        $this->clickButton('save_website');
        //Verifying
        $this->assertFalse($this->errorMessage(), $this->messages);
//        @TODO func 'navigated'
//        $this->assertTrue($this->navigated('manage_stores'),
//                'After successful creation website should be redirected to Manage Stores page');
        $this->assertTrue($this->successMessage('success_saved_website'),
                'No success message is displayed');
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
        //Data
        $websiteData = $this->loadData('generic_website',
                        array('website_code' => $this->generate('string', 32, ':punct:')));
        //Steps
        $this->clickButton('create_website');
        $this->fillForm($websiteData);
        $this->clickButton('save_website');
        //Verifying
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
        //Data
        $websiteData = $this->loadData('generic_website', $invalidCode);
        //Steps
        $this->clickButton('create_website');
        $this->fillForm($websiteData);
        $this->clickButton('save_website');
        //Verifying
        $this->assertTrue($this->errorMessage('wrong_website_code'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    public function data_InvalidCode()
    {
        return array(
            array(array('website_code' => 'invalid code')),
            array(array('website_code' => 'Invalid_code2')),
            array(array('website_code' => '2invalid_code2'))
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
     */
    public function test_WithSeveralStores_AssignedToOneRootCategory()
    {
        //1.1.Create website
        //Data
        $websiteData = $this->loadData('generic_website', NULL,
                        array('website_name', 'website_code'));
        //Steps
        $this->clickButton('create_website');
        $this->fillForm($websiteData);
        $this->clickButton('save_website');
        //Verifying
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage('success_saved_website'),
                'No success message is displayed');
        //1.2.Create two stores
        for ($i = 1; $i <= 2; $i++) {
            //Data
            $storeData = $this->loadData('generic_store',
                            array('website' => $websiteData['website_name']), 'store_name');
            //Steps
            $this->clickButton('create_store');
            $this->fillForm($storeData);
            $this->clickButton('save_store');
            //Verifying
            $this->assertFalse($this->errorMessage(), $this->messages);
            $this->assertTrue($this->successMessage('success_saved_store'),
                    'No success message is displayed');
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
     */
    public function test_WithSeveralStoresViewsInOneStore()
    {
        //1.1.Create website
        //Data
        $websiteData = $this->loadData('generic_website',
                        Null, array('website_code', 'website_name'));
        //Steps
        $this->clickButton('create_website');
        $this->fillForm($websiteData);
        $this->clickButton('save_website');
        //Verifying
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage('success_saved_website'),
                'No success message is displayed');
        //1.2.Create store
        //Data
        $storeData = $this->loadData('generic_store',
                        array('website' => $websiteData['website_name']), 'store_name');
        //Steps
        $this->clickButton('create_store');
        $this->fillForm($storeData);
        $this->clickButton('save_store');
        //Verifying
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage('success_saved_store'),
                'No success message is displayed');
        //1.3.Create two store view
        for ($i = 1; $i <= 2; $i++) {
            //Data
            $storeViewData = $this->loadData('generic_store_view',
                            array('store_name' => $storeData['store_name']),
                            array('store_view_name', 'store_view_code'));
            //Steps
            $this->clickButton('create_store_view');
            $this->fillForm($storeViewData);
            $this->clickButton('save_store_view');
            //Verifying
            $this->assertFalse($this->errorMessage(), $this->messages);
            $this->assertTrue($this->successMessage('success_saved_store_view'),
                    'No success message is displayed');
        }
    }

}
