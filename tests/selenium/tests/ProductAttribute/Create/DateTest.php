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
 * Create new product attribute. Type: Date
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductAttribute_Create_DateTest extends Mage_Selenium_TestCase {
    /*
     * Preconditions:
     * Admin user should be logged in.
     * Should stays on the Admin Dashboard page after login.
     * Navigate to System -> Manage Customers.
     */

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->assertTrue($this->checkCurrentPage('dashboard'),
                'Wrong page is opened');
        $this->navigate('manage_attributes');
        $this->assertTrue($this->checkCurrentPage('manage_attributes'),
                'Wrong page is opened');
    }

    public function test_Navigation()
    {
        $this->assertTrue($this->clickButton('add_new_attribute'),
                'There is no "Add New Attribute" button on the page');
        $this->assertTrue($this->checkCurrentPage('new_product_attribute'),
                'Wrong page is opened');
        $this->assertTrue($this->controlIsPresent('button', 'back'),
                'There is no "Back" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'reset'),
                'There is no "Reset" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'save_attribute'),
                'There is no "Save" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'save_and_continue_edit'),
                'There is no "Save and Continue Edit" button on the page');
    }

    /**
     * Create "Date" type Product Attribute (required fields only)
     *
     * Steps:
     * 1.Click on "Add New Attribute" button
     * 2.Choose "Date" in 'Catalog Input Type for Store Owner' dropdown
     * 3.Fill all required fields
     * 4.Click on "Save Attribute" button
     *
     * Expected result:
     * New attribute ["Date" type] successfully created.
     * Success message: 'The product attribute has been saved.' is displayed.
     *
     * @depends test_Navigation
     */
    public function test_WithRequiredFieldsOnly()
    {
        //Data
        $attrData = $this->loadData('product_attribute_date', null, array('attribute_code', 'admin_title'));
        //Steps
        $this->createAttribute($attrData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_attribute'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_attributes'),
                'After successful customer creation should be redirected to Manage Attributes page');

        return $attrData;
    }

    /**
     * Checking of verification for duplicate of Product Attributes with similar code
     * Creation of new attribute with existing code.
     *
     * Steps:
     * 1.Click on "Add New Attribute" button
     * 2.Choose "Date" in 'Catalog Input Type for Store Owner' dropdown
     * 3.Fill 'Attribute Code' field by code used in test before.
     * 4.Fill other required fields by regular data.
     * 5.Click on "Save Attribute" button
     *
     * Expected result:
     * New attribute ["Date" type] shouldn't be created.
     * Error message: 'Attribute with the same code already exists' is displayed.
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithAttributeCodeThatAlreadyExists(array $attrData)
    {
        //Steps
        $this->createAttribute($attrData);
        //Verifying
        $this->assertTrue($this->errorMessage('exists_attribute_code'), $this->messages);
    }

    /**
     * Checking validation for required fields are EMPTY
     *
     * Steps:
     * 1.Click on "Add New Attribute" button
     * 2.Choose "Date" in 'Catalog Input Type for Store Owner' dropdown
     * 3.Skip filling of one field required and fill other required fields.
     * 4.Click on "Save Attribute" button
     *
     * Expected result:
     * New attribute ["Date" type] shouldn't be created.
     * Error JS message: 'This is a required field.' is displayed.
     *
     * @dataProvider data_EmptyField
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithRequiredFieldsEmpty($emptyField)
    {
        //Data
        $attrData = $this->loadData('product_attribute_date', $emptyField);
        //Steps
        $this->createAttribute($attrData);
        //Verifying
        $page = $this->getUimapPage('admin', 'new_product_attribute');
        foreach ($emptyField as $fieldName => $fieldXpath) {
            switch ($fieldName) {
                case 'attribute_code':
                    $fieldSet = $page->findFieldSet('attribute_properties');
                    break;
                case 'admin_title':
                    $fieldSet = $page->findFieldSet('manage_titles');
                    break;
            }
            $xpath = $fieldSet->findField($fieldName);
            $this->addParameter('fieldXpath', $xpath);
        }
        $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function data_EmptyField()
    {
        return array(
            array(array('attribute_code' => '')),
            array(array('admin_title' => '')),
        );
    }

    /**
     * Checking validation for valid data in the 'Attribute Code' field
     *
     * Steps:
     * 1.Click on "Add New Attribute" button
     * 2.Choose "Date" in 'Catalog Input Type for Store Owner' dropdown
     * 3.Fill 'Attribute Code' field by invalid data [Examples: '0xxx'/'_xxx'/'111']
     * 4.Fill other required fields by regular data.
     * 5.Click on "Save Attribute" button
     *
     * Expected result:
     * New attribute ["Date" type] shouldn't be created.
     * Error JS message: 'Please use only letters (a-z), numbers (0-9) or underscore(_) in
     * this field, first character should be a letter.' is displayed.
     *
     * @dataProvider data_WrongCode
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithInvalidAttributeCode($wrongAttributeCode)
    {
        //Data
        $attrData = $this->loadData('product_attribute_date', $wrongAttributeCode);
        //Steps
        $this->createAttribute($attrData);
        //Verifying
        $this->assertTrue($this->errorMessage('invalid_attribute_code'), $this->messages);
    }

    public function data_WrongCode()
    {
        return array(
            array(array('attribute_code' => '11code_wrong')),
            array(array('attribute_code' => 'CODE_wrong')),
            array(array('attribute_code' => 'wrong code')),
            array(array('attribute_code' => $this->generate('string', 11, ':punct:'))),
        );
    }

    /**
     * Checking of correct validate of submitting form by using special
     * characters for all fields exclude 'Attribute Code' field.
     *
     * Steps:
     * 1.Click on "Add New Attribute" button
     * 2.Choose "Date" in 'Catalog Input Type for Store Owner' dropdown
     * 3.Fill 'Attribute Code' field by regular data.
     * 4.Fill other required fields by special characters.
     * 5.Click on "Save Attribute" button
     *
     * Expected result:
     * New attribute ["Date" type] successfully created.
     * Success message: 'The product attribute has been saved.' is displayed.
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithSpecialCharacters_InTitle()
    {
        //Data
        $attrData = $this->loadData('product_attribute_date',
                        array('admin_title' => $this->generate('string', 32, ':punct:'),), 'attribute_code');
        //Steps
        $this->createAttribute($attrData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_attribute'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_attributes'),
                'After successful customer creation should be redirected to Manage Attributes page');
    }

    /**
     * Checking of correct work of submitting form by using long values for fields filling
     *
     * Steps:
     * 1.Click on "Add New Attribute" button
     * 2.Choose "Date" in 'Catalog Input Type for Store Owner' dropdown
     * 3.Fill all required fields by long value alpha-numeric data.
     * 4.Click on "Save Attribute" button
     *
     * Expected result:
     * New attribute ["Date" type] successfully created.
     * Success message: 'The product attribute has been saved.' is displayed.
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithLongValues()
    {
        //Data
        $attrData = $this->loadData('product_attribute_date',
                        array(
                            'attribute_code' => $this->generate('string', 255, ':lower:'),
                            'admin_title' => $this->generate('string', 255, ':alnum:'),
                ));
        $searchData = $this->loadData('attribute_search_data',
                        array(
                            'attribute_code' => $attrData['attribute_code'],
                            'attribute_lable' => $attrData['admin_title'],
                ));
        //Steps
        $this->createAttribute($attrData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_attribute'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_attributes'),
                'After successful customer creation should be redirected to Manage Attributes page');
        //Steps
        $this->clickButton('reset_filter');
        $this->navigate('manage_attributes');
        $this->assertTrue($this->searchAndOpen($searchData), 'Attribute is not found');
        //Verifying
        $this->assertTrue($this->verifyForm($attrData, 'properties'));
        $this->clickControl('tab', 'manage_lables_options');
        $this->assertTrue($this->verifyForm($attrData, 'manage_lables_options'));
    }

    /**
     * Checking of attributes creation functionality during product createion process
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Products
     * 2.Click on "Add Product" button
     * 3.Specify settings for product creation
     * 3.1.Select "Attribute Set"
     * 3.2.Select "Product Type"
     * 4.Click on "Continue" button
     * 5.Click on "Create New Attribute" button in the top of "General" fieldset under "General" tab
     * 6.Choose "Date" in 'Catalog Input Type for Store Owner' dropdown
     * 7.Fill all required fields.
     * 8.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Date" type] successfully created.
     *                  Success message: 'The product attribute has been saved.' is displayed.
     *                  Pop-up window is closed automatically
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_OnProductPage_WithRequiredFieldsOnly()
    {
        //Data
        $productSettings = $this->loadData('product_create_settings_simple');
        $attrData = $this->loadData('product_attribute_date', null, 'attribute_code');
        //Steps
        $this->navigate('manage_products');
        $this->clickButton('add_new_product');
        $this->assertTrue($this->checkCurrentPage('new_product_settings'),
                'Wrong page is displayed'
        );
        $this->fillForm($productSettings);
        // Defining and adding %attributeSetID% and %productType% for 'new_product' Uimap
        $page = $this->getCurrentUimapPage();
        $fieldSet = $page->findFieldset('product_settings');
        foreach ($productSettings as $fieldsName => $fieldValue) {
            $xpath = '//' . $fieldSet->findDropdown($fieldsName);
            switch ($fieldsName) {
                case 'attribute_set':
                    $attributeSetID = $this->getValue($xpath . "/option[text()='$fieldValue']");
                    break;
                case 'product_type':
                    $productType = $this->getValue($xpath . "/option[text()='$fieldValue']");
                    break;
                default:
                    break;
            }
        }
        $this->addParameter('attributeSetID', $attributeSetID);
        $this->addParameter('productType', $productType);
        $this->clickButton('continue_button');
        $this->clickButton('create_new_attribute', FALSE);
        $this->waitForPopUp('new_attribute', '30000');
        $this->selectWindow("name=new_attribute");
//        @TODO
//        $this->creteAttribute($attrData);
//        $this->clickButton('save_attribute', true);
//        $this->assertFalse($this->successMessage('success_saved_attribute'), $this->messages);
    }

    /**
     * *********************************************
     * *         HELPER FUNCTIONS                  *
     * *********************************************
     */

    /**
     * Action_helper method for Create Attribute action
     *
     * @param array $attrData Array which contains DataSet for filling of the current form
     */
    public function createAttribute($attrData)
    {
        $this->clickButton('add_new_attribute');
        $this->fillForm($attrData, 'properties');
        $this->clickControl('tab', 'manage_lables_options', false);
        $this->fillForm($attrData, 'manage_lables_options');
        $this->saveForm('save_attribute');
    }

}