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
 * Create new product attribute. Type: Text Field
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductAttribute_Create_TextFieldTest extends Mage_Selenium_TestCase
{

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
     * Create "Text Field" type Product Attribute (required fields only)
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Attributes
     * 2.Click on "Add New Attribute" button
     * 3.Choose "Text Field" in 'Catalog Input Type for Store Owner' dropdown
     * 4.Fill all required fields
     * 5.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Text Field" type] successfully created.
     *                  Success message: 'The product attribute has been saved.' is displayed.
     */
    public function test_WithRequiredFieldsOnly()
    {
        $this->assertTrue(
                $this->navigate('manage_attributes')->clickButton('add_new_attribute')->navigated('new_product_attribute'),
                'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('product_attribute_textfield', null, null));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Checking validation for 'Attribute Code' field is EMPTY
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Attributes
     * 2.Click on "Add New Attribute" button
     * 3.Choose "Text Field" in 'Catalog Input Type for Store Owner' dropdown
     * 4.Skip filling of 'Attribute Code' field and fill other required fields.
     * 5.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Text Field" type] shouldn't be created.
     *                  Error JS message: 'Please use only letters (a-z), numbers (0-9) or underscore(_) in
     *                                            this field, first character should be a letter.' is displayed.
     */
    public function test_WithRequiredFieldsEmpty_EmptyAttributeCode()
    {
        $this->assertTrue(
                $this->navigate('manage_attributes')->clickButton('add_new_attribute')->navigated('new_product_attribute'),
                'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('product_attribute_textfield', array(
            'attribute_code' => '')));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Checking validation for 'Admin title field is EMPTY'
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Attributes
     * 2.Click on "Add New Attribute" button
     * 3.Choose "Text Field" in 'Catalog Input Type for Store Owner' dropdown
     * 4.Skip filling of 'Admin' field in the 'Manage Titles' fields set under the 'Manage Label / Options' tab
     *      and fill other required fields.
     * 5.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Text Field" type] shouldn't be created.
     *                  Error JS message: 'Failed' is displayed below 'Admin' field.
     */
    public function test_WithRequiredFieldsEmpty_EmptyAdminTitle()
    {
        $this->assertTrue(
                $this->navigate('manage_attributes')->clickButton('add_new_attribute')->navigated('new_product_attribute'),
                'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('product_attribute_textfield', array(
            'admin_title' => '')));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Checking validation for valid data in the 'Attribute Code' field
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Attributes
     * 2.Click on "Add New Attribute" button
     * 3.Choose "Text Field" in 'Catalog Input Type for Store Owner' dropdown
     * 4.Fill 'Attribute Code' field by invalid data [Examples: '0xxx'/'_xxx'/'111']
     * 5.Fill other required fields by regular data.
     * 6.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Text Field" type] shouldn't be created.
     *                  Error JS message: 'Please use only letters (a-z), numbers (0-9) or underscore(_) in
     *                                            this field, first character should be a letter.' is displayed.
     */
    public function test_WithInvalidAttributeCode()
    {
        $this->assertTrue(
                $this->navigate('manage_attributes')->clickButton('add_new_attribute')->navigated('new_product_attribute'),
                'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('product_attribute_textfield', array(
            'attribute_code' => '111')));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Checking of verification for duplicate of Product Attributes with similar code
     * Creation of new attribute with existing code.
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Attributes
     * 2.Click on "Add New Attribute" button
     * 3.Choose "Text Field" in 'Catalog Input Type for Store Owner' dropdown
     * 4.Fill 'Attribute Code' field by code used in test before.
     * 5.Fill other required fields by regular data.
     * 6.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Text Field" type] shouldn't be created.
     *                  Error message: 'Attribute with the same code already exists' is displayed.
     */
    public function test_WithAttributeCodeThatAlreadyExists()
    {
        $this->assertTrue(
                $this->navigate('manage_attributes')->clickButton('add_new_attribute')->navigated('new_product_attribute'),
                'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('product_attribute_textfield', null, null));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Checking of correct validate of submitting form by using special characters for 'Attribute Code' field filling.
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Attributes
     * 2.Click on "Add New Attribute" button
     * 3.Choose "Text Field" in 'Catalog Input Type for Store Owner' dropdown
     * 4.Fill 'Attribute Code' field by special characters.
     * 5.Fill other required fields by regular data.
     * 6.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Text Field" type] shouldn't be created.
     *                  Error JS message: 'Please use only letters (a-z), numbers (0-9) or underscore(_) in
     *                                            this field, first character should be a letter.' is displayed.
     */
    public function test_WithSpecialCharacters()
    {
        $this->assertTrue(
                $this->navigate('manage_attributes')->clickButton('add_new_attribute')->navigated('new_product_attribute'),
                'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('product_attribute_textfield', array(
            'attribute_code' => $this->generate('string', 12, ':punct:'),
            'default_value'  => $this->generate('string', 15, ':punct:'),
            'admin_title'  => $this->generate('string', 12, ':punct:'),
            'storeview_title'  => $this->generate('string', 12, ':punct:'))));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Checking of correct validate of submitting form by using special characters for all fields
     *          exclude 'Attribute Code' field.
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Attributes
     * 2.Click on "Add New Attribute" button
     * 3.Choose "Text Field" in 'Catalog Input Type for Store Owner' dropdown
     * 4.Fill 'Attribute Code' field by regular data.
     * 5.Fill other required fields by special characters.
     * 6.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Text Field" type] shouldn't be created.
     *                  Error JS message: 'Please use only letters (a-z), numbers (0-9) or underscore(_) in
     *                                            this field, first character should be a letter.' is displayed.
     */
    public function test_WithSpecialCharactersExclAttributeCode()
    {
        $this->assertTrue(
                $this->navigate('manage_attributes')->clickButton('add_new_attribute')->navigated('new_product_attribute'),
                'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('product_attribute_textfield', array(
            'attribute_code' => $this->generate('string', 11, ':alnum:'),
            'default_value'  => $this->generate('string', 15, ':punct:'),
            'admin_title'  => $this->generate('string', 11, ':punct:'),
            'storeview_title'  => $this->generate('string', 11, ':punct:'))));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Checking of correct validate of submitting form by using special characters for all fields
     *          exclude 'Attribute Code' & 'Default Value' field.
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Attributes
     * 2.Click on "Add New Attribute" button
     * 3.Choose "Text Field" in 'Catalog Input Type for Store Owner' dropdown
     * 4.Fill 'Attribute Code' & 'Default Value' field by regular data.
     * 5.Fill other required fields by special characters.
     * 6.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Text Field" type] shouldn't be created.
     *                  Error JS message: 'Please use only letters (a-z), numbers (0-9) or underscore(_) in
     *                                            this field, first character should be a letter.' is displayed.
     */
    public function test_WithSpecialCharactersExclDefaultValue()
    {
        $this->assertTrue(
                $this->navigate('manage_attributes')->clickButton('add_new_attribute')->navigated('new_product_attribute'),
                'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('product_attribute_textfield', array(
            'attribute_code' => $this->generate('string', 12, ':alnum:'),
            'default_value'  => $this->generate('string', 15, ':digit:'),
            'admin_title'  => $this->generate('string', 12, ':punct:'),
            'storeview_title'  => $this->generate('string', 12, ':punct:'))));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Checking of correct work of submitting form by using long values for fields filling
     *
     * Steps:
     * 1.Go to Catalog->Attributes->Manage Attributes
     * 2.Click on "Add New Attribute" button
     * 3.Choose "Text Field" in 'Catalog Input Type for Store Owner' dropdown
     * 4.Fill all required fields by long value alpha-numeric data.
     * 5.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Text Field" type] successfully created.
     *                  Success message: 'The product attribute has been saved.' is displayed.
     */
    public function test_WithLongValues()
    {
        $this->assertTrue(
                $this->navigate('manage_attributes')->clickButton('add_new_attribute')->navigated('new_product_attribute'),
                'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('product_attribute_textfield', array(
            'attribute_code' => $this->generate('string', 260, ':alnum:'),
            'default_value'  => $this->generate('string', 300, ':alnum:'),
            'admin_title'  => $this->generate('string', 260, ':alnum:'),
            'storeview_title'  => $this->generate('string', 260, ':alnum:'))));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
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
     * 6.Choose "Text Field" in 'Catalog Input Type for Store Owner' dropdown
     * 7.Fill all required fields.
     * 8.Click on "Save Attribute" button
     *
     * Expected result: new attribute ["Text Field" type] successfully created.
     *                  Success message: 'The product attribute has been saved.' is displayed.
     *                  Pop-up window is closed automatically
     */
    public function test_OnProductPage_WithRequiredFieldsOnly()
    {
        $this->assertTrue(
                $this->navigate('manage_products')->clickButton('add_new_product')->navigated('new_product'),
                'Wrong page is displayed'
        );
        $this->fillForm('product_create_settings_simple',null,null);
        $this->clickButton('continue_button');
        $this->clickButton('fieldset_general/create_new_attribute_button');
        $this->waitForPopUp('new_attribute','30000');
        $this->fillForm($this->loadData('product_attribute_textfield', null, 'attribute_code'));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }
}
