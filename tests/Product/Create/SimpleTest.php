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
        $productSettings = $this->loadData('settings_simple');
        //Steps.
        $this->productHelper()->createProduct($productSettings, $productData);
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
        $productSettings = $this->loadData('settings_simple');
        if ($emptyField == 'general_sku') {
            $productData = $this->loadData('simple_product_required',
                            array($emptyField => '%noValue%'));
        } elseif ($emptyField == 'general_visibility' or $emptyField == 'prices_tax_class') {
            $productData = $this->loadData('simple_product_required',
                            array($emptyField => '-- Please Select --'), 'general_sku');
        } elseif ($emptyField == 'inventory_qty') {
            $productData = $this->loadData('simple_product_required', array($emptyField => ''),
                            'general_sku');
        } else {
            $productData = $this->loadData('simple_product_required',
                            array($emptyField => '%noValue%'), 'general_sku');
        }
        //Steps
        $this->productHelper()->createProduct($productSettings, $productData);
        //Verifying - error message appears
        $fieldXpath = $this->_getControlXpath($fieldType, $emptyField);
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function data_EmptyField()
    {
        return array(
            array('general_name', 'field'),
            array('general_description', 'field'),
            array('general_short_description', 'field'),
            array('general_sku', 'field'),
            array('general_weight', 'field'),
            array('general_status', 'dropdown'),
            array('general_visibility', 'dropdown'),
            array('prices_price', 'field'),
            array('prices_tax_class', 'dropdown'),
            array('inventory_qty', 'field')
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
        $productSettings = $this->loadData('settings_simple');
        $productData = $this->loadData('simple_product_required',
                        array(
                            'general_name'              => $this->generate('string', 32, ':punct:'),
                            'general_description'       => $this->generate('string', 32, ':punct:'),
                            'general_short_description' => $this->generate('string', 32, ':punct:'),
                            'general_sku'               => $this->generate('string', 32, ':punct:'),
                            'general_weight'            => $this->generate('string', 32, ':punct:')
                        )
        );
        //Steps
        $this->productHelper()->createProduct($productSettings, $productData);
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
        $productSettings = $this->loadData('settings_simple');
        $productData = $this->loadData('simple_product_required',
                        array(
                    'general_weight' => $this->generate('string', 9, ':punct:')
                        ), 'general_sku');
        $productSearch = $this->loadData('product_search',
                        array(
                    'general_sku' => $productData['general_sku'],
                    'general_name' => $productData['general_name']
                        ));
        // Steps
        $this->productHelper()->createProduct($productSettings, $productData);
        // Verifying - success message appears
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        // Reset filter
        $this->clickButton('reset_filter', FALSE);
        $this->pleaseWait();
        $this->assertTrue($this->searchAndOpen($productSearch));
        // Verifying - select created product
        $xpath = $this->_getControlXpath('field', 'general_weight');
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
        $productSettings = $this->loadData('settings_simple');
        $productData = $this->loadData('simple_product_required', $InvalidPrice, 'general_sku');
        // Steps
        $this->productHelper()->createProduct($productSettings, $productData);
        // Verifying - error message appears
        $this->assertTrue($this->validationMessage('invalid_price'), $this->messages);
    }

    public function data_InvalidPrice()
    {
        return array(
            array(array('prices_price' => $this->generate('string', 9, ':punct:'))),
            array(array('prices_price' => 'g3648GJHghj')),
            array(array('prices_price' => $this->generate('string', 9, ':alpha:')))
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
        $productSettings = $this->loadData('settings_simple');
        $productData = $this->loadData('simple_product_required', $InvalidQty, 'general_sku');
        // Steps
        $this->productHelper()->createProduct($productSettings, $productData);
        // Verifying - error message appears
        $this->assertTrue($this->validationMessage('invalid_qty'), $this->messages);
    }

    public function data_InvalidQty()
    {
        return array(
            array(array('inventory_qty' => $this->generate('string', 9, ':punct:'))),
            array(array('inventory_qty' => 'g3648GJHghj')),
            array(array('inventory_qty' => $this->generate('string', 9, ':alpha:')))
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
     * @dataProvider data_EmptyCustomFields
     */
    public function test_WithCustomOptions_EmptyFields($emptyCustomFields)
    {
        //Loading Data
        $productSettings = $this->loadData('settings_simple');
        $productData = $this->loadData('simple_product_required', null, 'general_sku');
        $productData['custom_options'] = $this->loadData('custom_options_empty',
                        array($emptyCustomFields => "%noValue%"));
        // Fill form
        $this->productHelper()->createProduct($productSettings, $productData);
        // Verifying - error messages appears
        if ($emptyCustomFields == 'custom_options_general_title') {
            $xpath = $this->_getControlXpath('field', $emptyCustomFields);
            $this->addParameter('fieldXpath', $xpath);
            $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        } else {
            $this->assertTrue($this->validationMessage('select_type_of_option'), $this->messages);
        }
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function data_EmptyCustomFields()
    {
        return array(
            array('custom_options_general_title'),
            array('custom_options_general_input_type')
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
        // Loading Data
        $productSettings = $this->loadData('settings_simple');
        $productData = $this->loadData('simple_product_required', NULL, 'general_sku');
        $productData['custom_options'] = $this->loadData('custom_options_field', $invalidData);
        // Steps
        $this->productHelper()->createProduct($productSettings, $productData);
        // Verifying  - error message appears
        $this->assertTrue($this->validationMessage('enter_valid_number'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function data_invalidData()
    {
        return array(
            array(array('custom_options_price' => $this->generate('string', 9, ':punct:'))),
            array(array('custom_options_price' => 'g3648GJHghj')),
            array(array('custom_options_price' => $this->generate('string', 9, ':alpha:')))
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
        $productSettings = $this->loadData('settings_simple');
        $productData = $this->loadData('simple_product_required', $InvalidValue, 'general_sku');
        // Steps
        $this->productHelper()->createProduct($productSettings, $productData);
        //Verifying - error message appears
        $this->assertTrue($this->validationMessage('invalid_special_price'), $this->messages);
    }

    public function data_InvalidValue()
    {
        return array(
            array(array('prices_special_price' => $this->generate('string', 9, ':punct:'))),
            array(array('prices_special_price' => 'g3648GJHghj')),
            array(array('prices_special_price' => $this->generate('string', 9, ':alpha:')))
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
        $productSettings = $this->loadData('settings_simple');
        $productData = $this->loadData('simple_product_required', array($emptyFieldTier => ''),
                            'general_sku');
        $productData['prices_tier_price'] = $this->loadData('prices_tier_price_1', $emptyFieldTier);
        //Steps
        $this->productHelper()->createProduct($productSettings, $productData);
        //Verifying - error message appears
        $fieldXpath = $this->_getControlXpath($fieldType, $emptyFieldTier);
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function data_EmptyFieldTier()
    {
        return array(
            array('prices_tier_price_qty', 'field'),
            array('prices_tier_price_price', 'field'),
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
        $productSettings = $this->loadData('settings_simple');
        $productData = $this->loadData('simple_product_required', $data_invalidDataTier, 'general_sku');
        $productData['prices_tier_price'] = $this->loadData('prices_tier_price_1', $data_invalidDataTier);
        // Steps
        $this->productHelper()->createProduct($productSettings, $productData);
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
            array(array('prices_tier_price_qty' => $this->generate('string', 9, ':punct:'),
                    'prices_tier_price_price' => $this->generate('string', 9, ':punct:'))),
            array(array('prices_tier_price_qty' => 'g3648GJHghj',
                    'prices_tier_price_price' => 'g3648GJHghj')),
            array(array('prices_tier_price_qty' => $this->generate('string', 9, ':alpha:'),
                    'prices_tier_price_price' => $this->generate('string', 9, ':alpha:'))),
        );
    }
//
//    /**
//     * @TODO
//     */
//    public function test_OnConfigurableProductPage_QuickCreate()
//    {
//        // @TODO
//        $this->markTestIncomplete();
//    }
//
//    /**
//     * @TODO
//     */
//    public function test_OnConfigurableProductPage_CreateEmpty()
//    {
//        // @TODO
//        $this->markTestIncomplete();
//    }
//
//    /**
//     * @TODO
//     */
//    public function test_OnConfigurableProductPage_CopyFromConfigurable()
//    {
//        // @TODO
//        $this->markTestIncomplete();
//    }
}
