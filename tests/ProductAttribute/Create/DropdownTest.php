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
 * Create new product attribute. Type: Dropdown
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductAttribute_Create_DropdownTest extends Mage_Selenium_TestCase
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
     * Navigate to System -> Manage Attributes.
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_attributes');
        $this->assertTrue($this->checkCurrentPage('manage_attributes'), 'Wrong page is opened');
        $this->addParameter('id', 0);
    }

    public function test_Navigation()
    {
        $this->assertTrue($this->clickButton('add_new_attribute'),
                'There is no "Add New Attribute" button on the page');
        $this->assertTrue($this->checkCurrentPage('new_product_attribute'), 'Wrong page is opened');
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_attribute'),
                'There is no "Save" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_and_continue_edit'),
                'There is no "Save and Continue Edit" button on the page');
    }

    /**
     * Create "Dropdown" type Product Attribute (required fields only)
     *
     * Steps:
     * 1.Click on "Add New Attribute" button
     * 2.Choose "Dropdown" in 'Catalog Input Type for Store Owner' dropdown
     * 3.Fill all required fields
     * 4.Click on "Save Attribute" button
     *
     * Expected result:
     * New attribute ["Dropdown" type] successfully created.
     * Success message: 'The product attribute has been saved.' is displayed.
     *
     * @depends test_Navigation
     */
    public function test_WithRequiredFieldsOnly()
    {
        //Data
        $attrData = $this->loadData('product_attribute_dropdown', null,
                        array('attribute_code', 'admin_title'));
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_attribute'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_attributes'),
                'After successful attribute creation should be redirected to Manage Attributes page');

        return $attrData;
    }

    /**
     * Checking of verification for duplicate of Product Attributes with similar code
     * Creation of new attribute with existing code.
     *
     * Steps:
     * 1.Click on "Add New Attribute" button
     * 2.Choose "Dropdown" in 'Catalog Input Type for Store Owner' dropdown
     * 3.Fill 'Attribute Code' field by code used in test before.
     * 4.Fill other required fields by regular data.
     * 5.Click on "Save Attribute" button
     *
     * Expected result:
     * New attribute ["Dropdown" type] shouldn't be created.
     * Error message: 'Attribute with the same code already exists' is displayed.
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithAttributeCodeThatAlreadyExists(array $attrData)
    {
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertTrue($this->errorMessage('exists_attribute_code'), $this->messages);
    }

    /**
     * Checking validation for required fields are EMPTY
     *
     * Steps:
     * 1.Click on "Add New Attribute" button
     * 2.Choose "Dropdown" in 'Catalog Input Type for Store Owner' dropdown
     * 3.Skip filling of one field required and fill other required fields.
     * 4.Click on "Save Attribute" button
     *
     * Expected result:
     * New attribute ["Dropdown" type] shouldn't be created.
     * Error JS message: 'This is a required field.' is displayed.
     *
     * @dataProvider data_EmptyField
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithRequiredFieldsEmpty($emptyField)
    {
        //Data
        if ($emptyField == 'attribute_code') {
            $attrData = $this->loadData('product_attribute_dropdown',
                            array($emptyField => '%noValue%'));
        } elseif ($emptyField == 'apply_to') {
            $attrData = $this->loadData('product_attribute_dropdown',
                            array($emptyField => 'Selected Product Types'), 'attribute_code');
        } elseif ($emptyField == 'admin_title') {
            $attrData = $this->loadData('product_attribute_dropdown',
                            array($emptyField => '%noValue%'), 'attribute_code');
        }
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        if ($emptyField != 'apply_to') {
            $fieldXpath = $this->_getControlXpath('field', $emptyField);
        } else {
            $fieldXpath = $this->_getControlXpath('multiselect', 'apply_product_types');
        }
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function data_EmptyField()
    {
        return array(
            array('attribute_code'),
            array('admin_title'),
            array('apply_to')
        );
    }

    /**
     * Checking validation for valid data in the 'Attribute Code' field
     *
     * Steps:
     * 1.Click on "Add New Attribute" button
     * 2.Choose "Dropdown" in 'Catalog Input Type for Store Owner' dropdown
     * 3.Fill 'Attribute Code' field by invalid data [Examples: '0xxx'/'_xxx'/'111']
     * 4.Fill other required fields by regular data.
     * 5.Click on "Save Attribute" button
     *
     * Expected result:
     * New attribute ["Dropdown" type] shouldn't be created.
     * Error JS message: 'Please use only letters (a-z), numbers (0-9) or underscore(_) in
     * this field, first character should be a letter.' is displayed.
     *
     * @dataProvider data_WrongCode
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithInvalidAttributeCode($wrongAttributeCode, $validationMessage)
    {
        //Data
        $attrData = $this->loadData('product_attribute_dropdown',
                        array('attribute_code' => $wrongAttributeCode));
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertTrue($this->validationMessage($validationMessage), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function data_WrongCode()
    {
        return array(
            array('11code_wrong', 'invalid_attribute_code'),
            array('CODE_wrong', 'invalid_attribute_code'),
            array('wrong code', 'invalid_attribute_code'),
            array($this->generate('string', 11, ':punct:'), 'invalid_attribute_code'),
            array($this->generate('string', 33, ':lower:'), 'wrong_length_attribute_code')
        );
    }

    /**
     * Checking validation for notvalid data in the 'Position' field
     *
     * Steps:
     * 1.Click on "Add New Attribute" button.
     * 2.Choose "Dropdown" in 'Catalog Input Type for Store Owner' dropdown.
     * 3.Fill 'Position' field by invalid data.
     * 4.Fill other required fields by regular data.
     * 5.Click on "Save Attribute" button
     *
     * Expected result:
     * New attribute ["Dropdown" type] shouldn't be created.
     * Error JS message: 'Please use numbers only in this field.
     * Please avoid spaces or other characters such as dots or commas.' is displayed.
     *
     * @dataProvider data_InvalidPosition
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithInvalidPosition($invalidPosition)
    {
        //Data
        $attrData = $this->loadData('product_attribute_dropdown',
                        array('position' => $invalidPosition), 'attribute_code');
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertTrue($this->validationMessage('error_invalid_position'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function data_InvalidPosition()
    {
        return array(
            array('11code'),
            array('CODE11'),
            array('11 11'),
            array('11.11'),
            array('11,11'),
            array($this->generate('string', 10, ':punct:'))
        );
    }

    /**
     * Checking of correct validate of submitting form by using special
     * characters for all fields exclude 'Attribute Code' field.
     *
     * Steps:
     * 1.Click on "Add New Attribute" button
     * 2.Choose "Dropdown" in 'Catalog Input Type for Store Owner' dropdown
     * 3.Fill 'Attribute Code' field by regular data.
     * 4.Fill other required fields by special characters.
     * 5.Click on "Save Attribute" button
     *
     * Expected result:
     * New attribute ["Dropdown" type] successfully created.
     * Success message: 'The product attribute has been saved.' is displayed.
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithSpecialCharacters_InTitle()
    {
        //Data
        $attrData = $this->loadData('product_attribute_dropdown',
                        array('admin_title' => $this->generate('string', 32, ':punct:')),
                        'attribute_code');
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_attribute'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_attributes'),
                'After successful attribute creation should be redirected to Manage Attributes page');
    }

    /**
     * Checking of correct work of submitting form by using long values for fields filling
     *
     * Steps:
     * 1.Click on "Add New Attribute" button
     * 2.Choose "Dropdown" in 'Catalog Input Type for Store Owner' dropdown
     * 3.Fill all required fields by long value alpha-numeric data.
     * 4.Click on "Save Attribute" button
     *
     * Expected result:
     * New attribute ["Dropdown" type] successfully created.
     * Success message: 'The product attribute has been saved.' is displayed.
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithLongValues()
    {
        //Data
        $attrData = $this->loadData('product_attribute_dropdown_with_options',
                        array(
                            'attribute_code' => $this->generate('string', 30, ':lower:'),
                            'admin_title' => $this->generate('string', 255, ':alnum:'),
                            'position' => 2147483647
                        )
        );
        $searchData = $this->loadData('attribute_search_data',
                        array(
                            'attribute_lable' => $attrData['admin_title'],
                            'attribute_code' => $attrData['attribute_code']
                        )
        );
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_attribute'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_attributes'),
                'After successful attribute creation should be redirected to Manage Attributes page');
        //Steps
        $this->productAttributeHelper()->openAttribute($searchData);
        //Verifying
        $this->productAttributeHelper()->verifyAttribute($attrData);
    }

}
