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
     * Action_helper method for Create Attribute action
     *
     * @param array $attrData Array which contains DataSet for filling of the current form
     *
     */
    public function creteAttribute($attrData)
    {
        $this->clickButton('add_new_attribute');
        $this->fillForm($attrData, 'properties');
        $this->clickControl('tab', 'manage_lables_options',false);
        $this->fillForm($attrData, 'manage_lables_options');
    }

    /*
     * Preconditions
     * Admin user should be logged in.
     * Should stays on the Admin Dashboard page after login
     */
    protected function assertPreConditions()
    {
        $this->assertTrue($this->loginAdminUser());
        $this->assertTrue($this->admin('dashboard'));
    }

    public function test_Navigation()
    {
        $this->assertTrue($this->navigate('manage_attributes'));
        $this->assertTrue($this->clickButton('add_new_attribute'),
                'There is no "Add New Attribute" button on the page');
        $this->assertTrue($this->navigated('new_product_attribute'),
                'Wrong page is displayed');
        $this->assertTrue($this->navigate('new_product_attribute'),
                'Wrong page is displayed when accessing direct URL');
        $this->assertTrue($this->controlIsPresent('field','attribute_code'),
                'There is no "Attribute Code" field on the page');
        $this->assertTrue($this->controlIsPresent('field','apply_to'),
                'There is no "Apply To" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('field','admin_title'),
                'There is no "Admin Title" field on the page');
    }

    /**
     * Create "Dropdown" type Product Attribute (required fields only)
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Attributes
     * 2.Click on "Add New Attribute" button
     * 3.Choose "Dropdown" in 'Catalog Input Type for Store Owner' dropdown
     * 4.Fill all required fields
     * 5.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Dropdown" type] successfully created.
     *                  Success message: 'The product attribute has been saved.' is displayed.
     */
    public function test_WithRequiredFieldsOnly()
    {
        $this->assertTrue($this->navigate('manage_attributes'),'Wrong page is displayed');
        $attrData = $this->loadData('product_attribute_dropdown', null, null);
        $this->creteAttribute($attrData);
        $this->clickButton('save_attribute',true);
        $this->assertFalse($this->successMessage('success_saved_attribute'), $this->messages);
    }

    /**
     * Checking validation for 'Attribute Code' field is EMPTY
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Attributes
     * 2.Click on "Add New Attribute" button
     * 3.Choose "Dropdown" in 'Catalog Input Type for Store Owner' dropdown
     * 4.Skip filling of 'Attribute Code' field and fill other required fields.
     * 5.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Dropdown" type] shouldn't be created.
     *                  Error JS message: 'Please use only letters (a-z), numbers (0-9) or underscore(_) in
     *                                            this field, first character should be a letter.' is displayed.
     */
    public function test_WithRequiredFieldsEmpty_EmptyAttributeCode()
    {
        $this->assertTrue($this->navigate('manage_attributes'),'Wrong page is displayed');
        $attrData = $this->loadData('product_attribute_dropdown', array('attribute_code' => ''));
        $this->creteAttribute($attrData);
        $this->clickButton('save_attribute', false);
        $this->assertTrue($this->errorMessage('error_empty_attribute_code'), $this->messages);
    }

    /**
     * Checking validation for 'Admin title field is EMPTY'
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Attributes
     * 2.Click on "Add New Attribute" button
     * 3.Choose "Dropdown" in 'Catalog Input Type for Store Owner' dropdown
     * 4.Skip filling of 'Admin' field in the 'Manage Titles' fields set under the 'Manage Label / Options' tab
     *      and fill other required fields.
     * 5.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Dropdown" type] shouldn't be created.
     *                  Error JS message: 'Failed' is displayed below 'Admin' field.
     */
    public function test_WithRequiredFieldsEmpty_EmptyAdminTitle()
    {
        $this->assertTrue($this->navigate('manage_attributes'),'Wrong page is displayed');
        $attrData = $this->loadData('product_attribute_dropdown', array('admin_title' => ''));
        $this->creteAttribute($attrData);
        $this->clickButton('save_attribute', false);
        $this->assertTrue($this->errorMessage('error_empty_attribute_title'), $this->messages);
    }

    /**
     * Checking validation for 'Admin Option title field is EMPTY'
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Attributes
     * 2.Click on "Add New Attribute" button
     * 3.Choose "Dropdown" in 'Catalog Input Type for Store Owner' dropdown
     * 4.Skip filling of 'Admin' field in the 'Manage Options' fields set under the 'Manage Label / Options' tab
     *      and fill other required fields.
     * 5.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Dropdown" type] shouldn't be created.
     *                  Error JS message: 'Failed' is displayed below 'Admin' field.
     */
    public function test_WithRequiredFieldsEmpty_EmptyAdminOptionTitle()
    {
        $this->assertTrue($this->navigate('manage_attributes'),'Wrong page is displayed');
        $attrData = $this->loadData('product_attribute_dropdown', array('admin_option' => ''));
        $this->creteAttribute($attrData);
        $this->clickButton('save_attribute', false);
        $this->assertTrue($this->errorMessage('error_invalid_attribute_code'), $this->messages);
    }

     /**
     * Checking validation for valid data in the 'Attribute Code' field
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Attributes
     * 2.Click on "Add New Attribute" button
     * 3.Choose "Dropdown" in 'Catalog Input Type for Store Owner' dropdown
     * 4.Fill 'Attribute Code' field by invalid data [Examples: '0xxx'/'_xxx'/'111']
     * 5.Fill other required fields by regular data.
     * 6.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Dropdown" type] shouldn't be created.
     *                  Error JS message: 'Please use only letters (a-z), numbers (0-9) or underscore(_) in
     *                                            this field, first character should be a letter.' is displayed.
     */
    public function test_WithInvalidAttributeCode()
    {
        $this->assertTrue($this->navigate('manage_attributes'),'Wrong page is displayed');
        $attrData = $this->loadData('product_attribute_dropdown', array('attribute_code' => '111'));
        $this->creteAttribute($attrData);
        $this->clickButton('save_attribute', false);
        $this->assertTrue($this->errorMessage('error_invalid_attribute_code'), $this->messages);
    }

    /**
     * Checking of verification for duplicate of Product Attributes with similar code
     * Creation of new attribute with existing code.
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Attributes
     * 2.Click on "Add New Attribute" button
     * 3.Choose "Dropdown" in 'Catalog Input Type for Store Owner' dropdown
     * 4.Fill 'Attribute Code' field by code used in test before.
     * 5.Fill other required fields by regular data.
     * 6.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Dropdown" type] shouldn't be created.
     *                  Error message: 'Attribute with the same code already exists' is displayed.
     */
    public function test_WithAttributeCodeThatAlreadyExists()
    {
        $this->assertTrue($this->navigate('manage_attributes'),'Wrong page is displayed');
        $attrData = $this->loadData('product_attribute_dropdown', null, null);
        $this->creteAttribute($attrData);
        $this->clickButton('save_attribute', false);
        $this->assertTrue($this->errorMessage('error_exists_attribute_code'), $this->messages);
    }

    /**
     * Checking of correct validate of submitting form by using special characters for 'Attribute Code' field filling.
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Attributes
     * 2.Click on "Add New Attribute" button
     * 3.Choose "Dropdown" in 'Catalog Input Type for Store Owner' dropdown
     * 4.Fill 'Attribute Code' field by special characters.
     * 5.Fill other required fields by regular data.
     * 6.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Dropdown" type] shouldn't be created.
     *                  Error JS message: 'Please use only letters (a-z), numbers (0-9) or underscore(_) in
     *                                            this field, first character should be a letter.' is displayed.
     */
    public function test_WithSpecialCharacters()
    {
        $this->assertTrue($this->navigate('manage_attributes'),'Wrong page is displayed');
        $attrData = $this->loadData('product_attribute_dropdown', array(
            'attribute_code' => $this->generate('string', 11, ':punct:'),
            'admin_title'  => $this->generate('string', 11, ':punct:'),
            'storeview_title'  => $this->generate('string', 11, ':punct:'),
            'admin_option'  => $this->generate('string', 11, ':punct:'),
            'storeview_option'  => $this->generate('string', 11, ':punct:')));
        $this->creteAttribute($attrData);
        $this->clickButton('save_attribute', false);
        $this->assertTrue($this->errorMessage('error_invalid_attribute_code'), $this->messages);
        // TODO -> add assertTrue() for all validation massages
    }

     /**
     * Checking of correct validate of submitting form by using special characters for all fields
     *          exclude 'Attribute Code' field.
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Attributes
     * 2.Click on "Add New Attribute" button
     * 3.Choose "Dropdown" in 'Catalog Input Type for Store Owner' dropdown
     * 4.Fill 'Attribute Code' field by regular data.
     * 5.Fill other required fields by special characters.
     * 6.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Dropdown" type] shouldn't be created.
     *                  Error JS message: 'Please use only letters (a-z), numbers (0-9) or underscore(_) in
     *                                            this field, first character should be a letter.' is displayed.
     */
    public function test_WithSpecialCharactersExclAttributeCode()
    {
        $this->assertTrue($this->navigate('manage_attributes'),'Wrong page is displayed');
        $attrData = $this->loadData('product_attribute_dropdown', array(
            'attribute_code' => $this->generate('string', 10, ':alnum:'),
            'admin_title'  => $this->generate('string', 12, ':punct:'),
            'storeview_title'  => $this->generate('string', 12, ':punct:'),
            'admin_option'  => $this->generate('string', 12, ':punct:'),
            'storeview_option'  => $this->generate('string', 12, ':punct:')));
        $this->creteAttribute($attrData);
        $this->clickButton('save_attribute', false);
        $this->assertTrue($this->errorMessage('error_invalid_attribute_code'), $this->messages);
        // TODO -> add assertTrue() for all validation massages
    }

    /**
     * Checking of correct work of submitting form by using long values for fields filling
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Attributes
     * 2.Click on "Add New Attribute" button
     * 3.Choose "Dropdown" in 'Catalog Input Type for Store Owner' dropdown
     * 4.Fill all required fields by long value alpha-numeric data.
     * 5.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Dropdown" type] successfully created.
     *                  Success message: 'The product attribute has been saved.' is displayed.
     */
    public function test_WithLongValues()
    {
        $this->assertTrue($this->navigate('manage_attributes'),'Wrong page is displayed');
        $attrData = $this->loadData('product_attribute_dropdown', array(
            'attribute_code' => $this->generate('string', 260, ':alnum:'),
            'admin_title'  => $this->generate('string', 260, ':alnum:'),
            'storeview_title'  => $this->generate('string', 260, ':alnum:'),
            'admin_option'  => $this->generate('string', 260, ':alnum:'),
            'storeview_option'  => $this->generate('string', 260, ':alnum:')));
        $this->creteAttribute($attrData);
        $this->clickButton('save_attribute', true);
        $this->assertFalse($this->successMessage('success_saved_attribute'), $this->messages);
    }

    /**
     * Checking validation for data in the 'Position' field
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Attributes
     * 2.Click on "Add New Attribute" button
     * 3.Choose "Dropdown" in 'Catalog Input Type for Store Owner' dropdown
     * 4.Fill 'Position' field by invalid data [Examples: 'aaaa'/'_xxx'/'q1a2z3']
     * 5.Fill other required fields by regular data.
     * 6.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Dropdown" type] shouldn't be created.
     *                  Error JS message: 'Please use only letters (a-z), numbers (0-9) or underscore(_) in
     *                                            this field, first character should be a letter.' is displayed.
     */
    public function test_WithInvalidPosition()
    {
        $this->assertTrue($this->navigate('manage_attributes'),'Wrong page is displayed');
        $attrData = $this->loadData('product_attribute_dropdown', array('position' => 'abcdefg'));
        $this->creteAttribute($attrData);
        $this->clickButton('save_attribute', false);
        $this->assertTrue($this->errorMessage('error_invalid_position'), $this->messages);
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
     * 6.Choose "Dropdown" in 'Catalog Input Type for Store Owner' dropdown
     * 7.Fill all required fields.
     * 8.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Dropdown" type] successfully created.
     *                  Success message: 'The product attribute has been saved.' is displayed.
     *                  Pop-up window is closed automatically
     */
    public function test_OnProductPage_WithRequiredFieldsOnly()
    {
        $this->assertTrue(
                $this->navigate('manage_products')->clickButton('add_new_product')->navigated('new_product_settings'),
                'Wrong page is displayed'
        );
        $this->fillForm('product_create_settings_simple',null,null);
        $this->clickButton('continue_button');
        $this->clickButton('fieldset_general/create_new_attribute_button');
        $this->waitForPopUp('new_attribute','30000');
        $attrData = $this->loadData('product_attribute_dropdown', null, 'attribute_code');
        $this->creteAttribute($attrData);
        $this->clickButton('save_attribute', true);
        $this->assertFalse($this->successMessage('success_saved_attribute'), $this->messages);
    }

    /**
     * @TODO : Waiting a tests for Configurable products
     */
    public function test_OnProductPage_WithOptions()
    {
        // @TODO : Waiting a tests for Configurable products
    }
}
