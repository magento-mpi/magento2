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
 * Simple product creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Product_Create_SimpleTest extends Mage_Selenium_TestCase
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
     * Navigate to Catalog -> Manage Products
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->addParameter('id', '0');
    }

    /**
     * Steps:
     *
     * 1. Login to Admin Page;
     *
     * 2. Goto Catalog -> Manage Products;
     *
     * 3. Click "Add product" button;
     *
     * 4. Fill in "Attribute Set" and "Product Type" fields;
     *
     * 5. Click "Continue" button;
     *
     * 6. Fill in required fields;
     *
     * 7. Click "Save" button;
     *
     * Expected result:
     *
     * Product created, confirmation message appears;
     *
     */
    public function test_WithRequiredFieldsOnly()
    {
        //Data - Loading settings for Simple product
        $productSettings = $this->loadData('settings_simple');
        $productData = $this->loadData('simple_product_required', NULL,
                        array('general_name', 'general_sku'));
        //Steps
        $this->productHelper()->createProduct($productSettings, $productData);
        //Verifying - success message, switch to Manage Products page
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        return $productData;
    }

    /**
     * Steps:
     *
     * 1. Login to Admin Page;
     *
     * 2. Goto Catalog -> Manage Products;
     *
     * 3. Click "Add product" button;
     *
     * 4. Fill in "Attribute Set" and "Product Type" fields;
     *
     * 5. Click "Continue" button;
     *
     * 6. Fill in required fields on General tab - use existing SKU;
     *
     * 7. Fill in required fields on Prices tab;
     *
     * 8. Click "Save" button;
     *
     * 9. Verify error message;
     *
     * Expected result:
     *
     * Error message appears;
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithSkuThatAlreadyExists($productData)
    {
        //Data - Loading settings for Simple product
        $productSettings = $this->loadData('product_create_settings_simple');
        //Steps.
        $this->productHelper()->createSimpleProduct($productSettings, $productData);
        //Verifying - one error message appears,
        $this->assertTrue($this->validationMessage('existing_sku'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    /**
     * Steps:
     *
     * 1. Login to Admin Page;
     *
     * 2. Goto Catalog -> Manage Products;
     *
     * 3. Click "Add product" button;
     *
     * 4. Fill in "Attribute Set" and "Product Type" fields;
     *
     * 5. Click "Continue" button;
     *
     * 6. Leave one required field empty and fill in the rest of fields on General tab;
     *
     * 7. Fill in required fields on Prices tab;
     *
     * 8. Click "Save" button;
     *
     * 9. Verify error message;
     *
     * 10. Repeat scenario for all required fields for both tabs;
     *
     * Expected result:
     *
     * Product is not created, error message appears;
     *
     * @dataProvider data_EmptyField
     */
    public function test_WithRequiredFieldsEmpty($emptyField, $fieldType)
    {
        //Data - Loading settings for Simple product
        $productSettings = $this->loadData('product_create_settings_simple');
        if ($emptyField == 'product_sku') {
            $productData = $this->loadData('simple_product', array($emptyField => '%noValue%'));
        } elseif ($emptyField == 'product_visibility' or $emptyField == 'product_tax_class') {
            $productData = $this->loadData('simple_product',
                            array($emptyField => '-- Please Select --'), 'product_sku');
        } else {
            $productData = $this->loadData('simple_product', array($emptyField => '%noValue%'),
                            'product_sku');
        }
        //Steps
        $this->productHelper()->createSimpleProduct($productSettings, $productData);
        //Verifying - error message appears
        $fieldXpath = $this->_getControlXpath($fieldType, $emptyField);
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function data_EmptyField()
    {
        return array(
            array('product_name', 'field'),
            array('product_description', 'field'),
            array('product_short_description', 'field'),
            array('product_sku', 'field'),
            array('product_weight', 'field'),
            array('product_status', 'dropdown'),
            array('product_visibility', 'dropdown'),
            array('product_price', 'field'),
            array('product_tax_class', 'dropdown')
        );
    }

    /**
     * Steps
     *
     * 1. Login to Admin Page;
     *
     * 2. Goto Catalog -> Manage Products;
     *
     * 3. Click "Add Product" button;
     *
     * 4. Fill in "Attribute Set", "Product Type" fields;
     *
     * 5. Click "Continue" button;
     *
     * 6. Fill in required fields in "General" tab with special characters ("General tab only");
     *
     * 7. Fill in required fields in "Prices" tab with normal data;
     *
     * 8. Click "Save" button;
     *
     * Expected result:
     *
     * Product created, confirmation message appears
     *
     */
    public function test_WithSpecialCharacters()
    {
        //Data - Loading settings for Simple product
        $productSettings = $this->loadData('product_create_settings_simple');
        $productData = $this->loadData('simple_product',
                        array(
                    'product_name' => $this->generate('string', 32, ':punct:'),
                    'product_description' => $this->generate('string', 32, ':punct:'),
                    'product_short_description' => $this->generate('string', 32, ':punct:'),
                    'product_sku' => $this->generate('string', 32, ':punct:'),
                    'product_weight' => $this->generate('string', 32, ':punct:')
                        )
        );
        //Steps
        $this->productHelper()->createSimpleProduct($productSettings, $productData);
        //Verifying - success message appears
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
    }

    /**
     * Steps
     *
     * 1. Login to Admin Page;
     *
     * 2. Goto Catalog -> Manage Products;
     *
     * 3. Click "Add Product" button;
     *
     * 4. Fill in "Attribute Set", "Product Type" fields;
     *
     * 5. Click "Continue" button;
     *
     * 6. Fill in required fields in "General" tab with correct data;
     *
     * 7. Fill in "Weight" field with special characters;
     *
     * 7. Fill in required fields in "Prices" tab with normal data;
     *
     * 8. Click "Save" button;
     *
     * Expected result:
     *
     * Product created, confirmation message appears, Weight=0;
     *
     */
    public function test_WithInvalidValueForFields_InvalidWeight()
    {
        //Data - Loading settings for Simple product
        $productSettings = $this->loadData('product_create_settings_simple');
        $productData = $this->loadData('simple_product',
                        array('product_weight' => $this->generate('string', 9, ':punct:')),
                        'product_sku');
        $productSearch = $this->loadData('product_search',
                        array('sku' => $productData['product_sku'],
                    'name' => $productData['product_name']
                        )
        );
        // Steps
        $this->productHelper()->createSimpleProduct($productSettings, $productData);
        // Verifying - success message appears
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        // Reset filter
        $this->clickButton('reset_filter', FALSE);
        $this->pleaseWait();
        $this->assertTrue($this->searchAndOpen($productSearch), $this->messages);
        // Verifying - select created product
        $xpath = $this->_getControlXpath('field', 'product_weight');
        $weightValue = $this->getValue($xpath);
        $this->assertEquals(0.0000, $weightValue, 'The product weight should be 0.0000');
    }

    /**
     * Steps
     *
     * 1. Login to Admin Page;
     *
     * 2. Goto Catalog -> Manage Products;
     *
     * 3. Click "Add Product" button;
     *
     * 4. Fill in "Attribute Set", "Product Type" fields;
     *
     * 5. Click "Continue" button;
     *
     * 6. Fill in required fields in "General" tab with correct data;
     *
     * 7. Fill in required fields in "Prices" tab with normal data;
     *
     * 8. Fill in "Price" field with special characters;
     *
     * 9. Click "Save" button;
     *
     * Expected result:
     *
     * Product is not created, error message appears;
     *
     * @dataProvider data_InvalidPrice
     */
    public function test_WithInvalidValueForFields_InvalidPrice($InvalidPrice)
    {
        //Data - Loading settings for Simple product
        $productSettings = $this->loadData('product_create_settings_simple');
        $productData = $this->loadData('simple_product', $InvalidPrice, 'product_sku');
        // Steps
        $this->productHelper()->createSimpleProduct($productSettings, $productData);
        // Verifying - error message appears
        $this->assertTrue($this->validationMessage('invalid_price'), $this->messages);
    }

    public function data_InvalidPrice()
    {
        return array(
            array(array('product_price' => $this->generate('string', 9, ':punct:'))),
            array(array('product_price' => 'g3648GJHghj')),
            array(array('product_price' => $this->generate('string', 9, ':alpha:')))
        );
    }

    /**
     * Steps
     *
     * 1. Login to Admin Page;
     *
     * 2. Goto Catalog -> Manage Products;
     *
     * 3. Click "Add Product" button;
     *
     * 4. Fill in "Attribute Set", "Product Type" fields;
     *
     * 5. Click "Continue" button;
     *
     * 6. Fill in required fields in "General" tab with correct data;
     *
     * 7. Fill in required fields in "Prices" tab with correct data;
     *
     * 8. Fill in required fields in "Inventory" tab with correct data;
     *
     * 8. Fill in "Weight" field with special characters;
     *
     * 9. Click "Save" button;
     *
     * Expected result:
     *
     * Product is not created, error message appears;
     *
     * @dataProvider data_InvalidQty
     */
    public function test_WithInvalidValueForFields_InvalidQty($InvalidQty)
    {
        //Data - Loading settings for Simple product
        $productSettings = $this->loadData('product_create_settings_simple');
        $productData = $this->loadData('simple_product', $InvalidQty, 'product_sku');
        // Steps
        $this->productHelper()->createSimpleProduct($productSettings, $productData);
        // Verifying - error message appears
        $this->assertTrue($this->validationMessage('invalid_qty'), $this->messages);
    }

    public function data_InvalidQty()
    {
        return array(
            array(array('qty' => $this->generate('string', 9, ':punct:'))),
            array(array('qty' => 'g3648GJHghj')),
            array(array('qty' => $this->generate('string', 9, ':alpha:')))
        );
    }

    /**
     * Steps
     *
     * 1. Login to Admin Page;
     *
     * 2. Goto Catalog -> Manage Products;
     *
     * 3. Click "Add Product" button;
     *
     * 4. Fill in "Attribute Set", "Product Type" fields;
     *
     * 5. Click "Continue" button;
     *
     * 6. Fill in required fields in "General" tab with correct data;
     *
     * 7. Fill in required fields in "Prices" tab with correct data;
     *
     * 8. Fill in required fields in "Inventory" tab with correct data;
     *
     * 8. Goto "Custom Options" tab;
     *
     * 10. Click "Add New Option" button;
     *
     * 11. Leave fields empty;
     *
     * 12. Click "Save" button;
     *
     * Expected result:
     *
     * Product is not created, error message appears;
     *
     *
     */
    public function test_WithCustomOptions_EmptyFields()
    {
        //Loading Data
        $productSettings = $this->loadData('product_create_settings_simple');
        $productData = $this->loadData('simple_product', NULL, 'product_sku');

        $this->clickButton('add_new_product');
        $this->productHelper()->fillProductSettings($productSettings);
        // Fill form
        $this->fillForm($productData, 'general');
        $this->clickControl('tab', 'prices', FALSE);
        $this->fillForm($productData, 'prices');
        $this->clickControl('tab', 'custom_options', FALSE);
        $this->pleaseWait();

        $page = $this->getCurrentLocationUimapPage();
        $fieldSet = $page->findFieldset('custom_options');
        $fieldSetXpath = $fieldSet->getXpath();
        $optionCount = $this->getXpathCount($fieldSetXpath . "//*[@class='option-box']") + 1;
        $this->addParameter('optionNumber', $optionCount);
        $page->assignParams($this->_paramsHelper);

        $this->clickButton('add_new_option', FALSE);
        $this->saveForm('save');

        $xpath = $fieldSet->findField('custom_title');
        $this->addParameter('fieldXpath', $xpath);

        $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->validationMessage('select_type_of_option'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(2), $this->messages);
    }

    /**
     * Steps
     *
     * 1. Login to Admin Page;
     *
     * 2. Goto Catalog -> Manage Products;
     *
     * 3. Click "Add Product" button;
     *
     * 4. Fill in "Attribute Set", "Product Type" fields;
     *
     * 5. Click "Continue" button;
     *
     * 6. Fill in required fields in "General" tab with correct data;
     *
     * 7. Fill in required fields in "Prices" tab with correct data;
     *
     * 8. Fill in required fields in "Inventory" tab with correct data;
     *
     * 8. Goto "Custom Options" tab;
     *
     * 10. Click "Add New Option" button;
     *
     * 11. Fill in fields with incorrect data;
     *
     * 12. Click "Save" button;
     *
     * Expected result:
     *
     * Product is not created, error message appears;
     *
     * @dataProvider data_invalidData
     */
    public function test_WithCustomOptions_InvalidValues($invalidData)
    {
        //Loading Data
        $productSettings = $this->loadData('product_create_settings_simple');
        $productData = $this->loadData('simple_product', $invalidData, 'product_sku');
        $this->clickButton('add_new_product');
        $this->productHelper()->fillProductSettings($productSettings);
        // Fill form
        $this->fillForm($productData, 'general');
        $this->clickControl('tab', 'prices', FALSE);
        $this->fillForm($productData, 'prices');
        $this->clickControl('tab', 'inventory', FALSE);
        $this->fillForm($productData, 'inventory');
        $this->clickControl('tab', 'custom_options', FALSE);
        $this->pleaseWait();
        $page = $this->getCurrentLocationUimapPage();
        $fieldSet = $page->findFieldset('custom_options');
        $fieldSetXpath = $fieldSet->getXpath();
        $optionCount = $this->getXpathCount($fieldSetXpath . "//*[@class='option-box']") + 1;
        $this->addParameter('optionNumber', $optionCount);
        $page->assignParams($this->_paramsHelper);

        $this->clickButton('add_new_option', FALSE);
        $this->fillForm($productData, 'custom_options');
        $this->saveForm('save');
        $page = $this->getCurrentLocationUimapPage();
        $fieldSet = $page->findFieldSet('custom_options');
        $xpath = $fieldSet->findField('custom_price');
        $this->addParameter('fieldXpath', $xpath);
        $page->assignParams($this->_paramsHelper);

        //$this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->validationMessage('custom_option_invalid_number'), $this->messages);

        $this->assertTrue($this->verifyMessagesCount(1), $this->messages);
    }

    public function data_invalidData()
    {
        return array(
            array(array('custom_price' => $this->generate('string', 9, ':punct:'))),
            array(array('custom_price' => 'g3648GJHghj')),
            array(array('custom_price' => $this->generate('string', 9, ':alpha:')))
        );
    }

    /**
     * Steps
     *
     * 1. Login to Admin Page;
     *
     * 2. Goto Catalog -> Manage Products;
     *
     * 3. Click "Add Product" button;
     *
     * 4. Fill in "Attribute Set", "Product Type" fields;
     *
     * 5. Click "Continue" button;
     *
     * 6. Fill in required fields in "General" tab with correct data;
     *
     * 7. Fill in required fields in "Prices" tab with correct data;
     *
     * 8. Fill in field "Special Price" with invalid data;
     *
     * 9. Click "Save" button;
     *
     * Expected result:
     *
     * Product is not created, error message appears;
     *
     * @dataProvider data_InvalidValue
     */
    public function test_WithSpecialPrice_InvalidValue($InvalidValue)
    {
        //Data - Loading settings for Simple product
        $productSettings = $this->loadData('product_create_settings_simple');
        $productData = $this->loadData('simple_product', $InvalidValue, 'product_sku');
        // Steps
        $this->productHelper()->createSimpleProduct($productSettings, $productData);
        //Verifying - error message appears
        $this->assertTrue($this->validationMessage('invalid_special_price'), $this->messages);
    }

    public function data_InvalidValue()
    {
        return array(
            array(array('product_special_price' => $this->generate('string', 9, ':punct:'))),
            array(array('product_special_price' => 'g3648GJHghj')),
            array(array('product_special_price' => $this->generate('string', 9, ':alpha:')))
        );
    }

    /**
     * Steps
     *
     * 1. Login to Admin Page;
     *
     * 2. Goto Catalog -> Manage Products;
     *
     * 3. Click "Add Product" button;
     *
     * 4. Fill in "Attribute Set", "Product Type" fields;
     *
     * 5. Click "Continue" button;
     *
     * 6. Fill in required fields in "General" tab with correct data;
     *
     * 7. Fill in required fields in "Prices" tab with correct data;
     *
     * 8. Click "Add Tier" button and leave fields in current fieldset empty;
     *
     * 9. Fill in required fields in "Inventory" tab with correct data;
     *
     * 10. Click "Save" button;
     *
     * Expected result:
     *
     * Product is not created, error message appears;
     *
     * @dataProvider data_EmptyFieldTier
     */
    public function test_WithTierPriceFieldsEmpty($emptyFieldTier, $fieldType)
    {
        //Data - Loading settings for Simple product
        $productSettings = $this->loadData('product_create_settings_simple');
        if ($emptyFieldTier == 'product_sku') {
            $productData = $this->loadData('simple_product', array($emptyFieldTier => '%noValue%'));
        } elseif ($emptyFieldTier == 'product_visibility' or $emptyFieldTier == 'product_tax_class') {
            $productData = $this->loadData('simple_product',
                            array($emptyFieldTier => '-- Please Select --'), 'product_sku');
        } else {
            $productData = $this->loadData('simple_product', array($emptyFieldTier => '%noValue%'),
                            'product_sku');
        }
        //Steps
        $this->productHelper()->createSimpleProduct($productSettings, $productData);
        //Verifying - error message appears
        $fieldXpath = $this->_getControlXpath($fieldType, $emptyFieldTier);
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function data_EmptyFieldTier()
    {
        return array(
            array('product_tier_price_qty', 'field'),
            array('product_tier_price_price', 'field'),
        );
    }

    /**
     *
     * 1. Login to Admin Page;
     *
     * 2. Goto Catalog -> Manage Products;
     *
     * 3. Click "Add Product" button;
     *
     * 4. Fill in "Attribute Set", "Product Type" fields;
     *
     * 5. Click "Continue" button;
     *
     * 6. Fill in required fields in "General" tab with correct data;
     *
     * 7. Fill in required fields in "Prices" tab with correct data;
     *
     * 8. Click "Add Tier" button and fill in fields in current fieldset with imcorrect data;
     *
     * 9. Fill in required fields in "Inventory" tab with correct data;
     *
     * 10. Click "Save" button;
     *
     * Expected result:
     *
     * Product is not created, error message appears;
     *
     * @dataProvider data_invalidDataTier
     */
    public function test_WithTierPrice_InvalidValues($data_invalidDataTier)
    {
        //Data - Loading settings for Simple product
        $productSettings = $this->loadData('product_create_settings_simple');
        $productData = $this->loadData('simple_product', $data_invalidDataTier, 'product_sku');
        $this->productHelper()->createSimpleProduct($productSettings, $productData);
        // Steps
        foreach ($data_invalidDataTier as $key => $value) {
            $fieldXpath = $this->_getControlXpath('field', $key);
            $this->addParameter('fieldXpath', $fieldXpath);
            $this->assertTrue($this->validationMessage('invalid_tier_price'), $this->messages);
        }
        // Verifying - error message appears
        $this->assertTrue($this->verifyMessagesCount(2), $this->messages);
    }

    public function data_invalidDataTier()
    {
        return array(
            array(array('product_tier_price_qty' => $this->generate('string', 9, ':punct:'),
                    'product_tier_price_price' => $this->generate('string', 9, ':punct:'))),
            array(array('product_tier_price_qty' => 'g3648GJHghj',
                    'product_tier_price_price' => 'g3648GJHghj')),
            array(array('product_tier_price_qty' => $this->generate('string', 9, ':alpha:'),
                    'product_tier_price_price' => $this->generate('string', 9, ':alpha:'))),
        );
    }

    /**
     * @TODO
     */
    public function test_OnConfigurableProductPage_QuickCreate()
    {
        // @TODO
        $this->markTestIncomplete();
    }

    /**
     * @TODO
     */
    public function test_OnConfigurableProductPage_CreateEmpty()
    {
        // @TODO
        $this->markTestIncomplete();
    }

    /**
     * @TODO
     */
    public function test_OnConfigurableProductPage_CopyFromConfigurable()
    {
        // @TODO
        $this->markTestIncomplete();
    }

}
