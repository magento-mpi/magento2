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
 * Bundle Dynamic product creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Product_Create_Bundle_DynamicTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Login to backend</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions</p>
     * <p>Navigate to Catalog->Manage Products</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is displayed');
        $this->addParameter('id', '0');
    }

    /**
     * <p>Creating product with required fields only</p>
     * <p>Steps:</p>
     * <p>1. Click "Add product" button;</p>
     * <p>2. Fill in "Attribute Set" and "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     */
    public function test_WithRequiredFieldsOnly()
    {
        //Data
        $productData = $this->loadData('bundle_dynamic_product_required', null,
                        array('general_name', 'general_sku'));
        //Steps.
        $this->productHelper()->createProduct($productData, 'bundle');

        $page = $this->getCurrentLocationUimapPage();
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        return $productData;
    }

    /**
     * <p>Creating product with existing SKU</p>
     * <p>Steps:</p>
     * <p>1. Click "Add product" button;</p>
     * <p>2. Fill in "Attribute Set" and "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields using exist SKU;</p>
     * <p>5. Click "Save" button;</p>
     * <p>6. Verify error message;</p>
     * <p>Expected result:</p>
     * <p>Error message appears;</p>
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithSkuThatAlreadyExists($productData)
    {
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertTrue($this->validationMessage('existing_sku'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

//    /**
//     * <p>Creating product with empty required fields</p>
//     * <p>Steps:</p>
//     * <p>1. Click "Add product" button;</p>
//     * <p>2. Fill in "Attribute Set" and "Product Type" fields;</p>
//     * <p>3. Click "Continue" button;</p>
//     * <p>4. Leave one required field empty and fill in the rest of fields;</p>
//     * <p>5. Click "Save" button;</p>
//     * <p>6. Verify error message;</p>
//     * <p>7. Repeat scenario for all required fields for both tabs;</p>
//     * <p>Expected result:</p>
//     * <p>Product is not created, error message appears;</p>
//     *
//     * @dataProvider data_EmptyField
//     * @depends test_WithRequiredFieldsOnly
//     */
//    public function test_WithRequiredFieldsEmpty($emptyField, $fieldType)
//        {
//        //Data
//        if ($emptyField == 'general_sku') {
//            $productData = $this->loadData('bundle_dynamic_product_required',
//                            array($emptyField => '%noValue%'));
//        } elseif ($emptyField == 'general_visibility') {
//            $productData = $this->loadData('bundle_dynamic_product_required',
//                            array($emptyField => '-- Please Select --'), 'general_sku');
//        } elseif ($emptyField == 'general_sku_type') {
//            $productData = $this->loadData('bundle_dynamic_product_required',
//                            array($emptyField => '-- Select --'), 'general_sku');
//        }  elseif ($emptyField == 'inventory_qty') {
//            $productData = $this->loadData('bundle_dynamic_product_required', array($emptyField => ''),
//                            'general_sku');
//        } else {
//            $productData = $this->loadData('bundle_dynamic_product_required',
//                            array($emptyField => '%noValue%'), 'general_sku');
//        }
//        //Steps
//        $this->productHelper()->createProduct($productData, 'bundle');
//        //Verifying
//        sleep(5);
//        $fieldXpath = $this->_getControlXpath($fieldType, $emptyField);
//        $this->addParameter('fieldXpath', $fieldXpath);
//        $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
//        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
//    }
//
//    public function data_EmptyField()
//    {
//        return array(
//            array('general_name', 'field'),
//            array('general_description', 'field'),
//            array('general_short_description', 'field'),
//            array('general_sku_type', 'dropdown'),
//            array('general_sku', 'field'),
////            array('general_weight_type', 'dropdown'),
////            array('general_weight', 'field'),
////            array('general_status', 'dropdown'),
////            array('general_visibility', 'dropdown'),
////            array('prices_price_type', 'dropdown'),
////            array('prices_price', 'field'),
////            array('prices_tax_class', 'dropdown'),
//        );
//    }
//
    /**
     * <p>Creating product with special characters into required fields</p>
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields with special symbols ("General" tab), rest - with normal data;
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product created, confirmation message appears</p>
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithSpecialCharacters()
    {
        //Data
        $productData = $this->loadData('bundle_dynamic_product_required',
                        array(
                    'general_name' => $this->generate('string', 32, ':punct:'),
                    'general_description' => $this->generate('string', 32, ':punct:'),
                    'general_short_description' => $this->generate('string', 32, ':punct:'),
                    'general_sku' => $this->generate('string', 32, ':punct:')
                ));
        $productSearch = $this->loadData('product_search',
                        array('general_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->assertTrue($this->verifyForm($productData, 'general'), $this->messages);
    }

    /**
     * <p>Creating product with long values from required fields</p>
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields with long values ("General" tab), rest - with normal data;
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product created, confirmation message appears</p>
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithLongValues()
    {
        //Data
        $productData = $this->loadData('bundle_dynamic_product_required',
                        array(
                    'general_name' => $this->generate('string', 255, ':alnum:'),
                    'general_description' => $this->generate('string', 255, ':alnum:'),
                    'general_short_description' => $this->generate('string', 255, ':alnum:'),
                    'general_sku' => $this->generate('string', 64, ':alnum:'),
                ));
        $productSearch = $this->loadData('product_search',
                        array('general_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->assertTrue($this->verifyForm($productData, 'general'), $this->messages);
    }

    /**
     * <p>Creating product with SKU length more than 64 characters.</p>
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields, use for sku string with length more than 64 characters</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithIncorrectSkuLength()
    {
        //Data
        $productData = $this->loadData('bundle_dynamic_product_required',
                        array('general_sku' => $this->generate('string', 65, ':alnum:')));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertTrue($this->validationMessage('incorrect_sku_length'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    /**
     * @TODO
     */
    public function test_WithInvalidValueForFields_InvalidWeight()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithInvalidValueForFields_InvalidPrice()
    {
        // @TODO
    }

    /**
     * <p>Creating product with invalid special price</p>
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in field "Special Price" with invalid data, the rest fields - with correct data;
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:<p>
     * <p>Product is not created, error message appears;</p>
     *
     * @dataProvider data_invalidData_NumericField
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithSpecialPrice_InvalidValue($invalidValue)
    {
        //Data
        $productData = $this->loadData('bundle_dynamic_product_required',
                        array('prices_special_price' => $invalidValue), 'general_sku');
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertTrue($this->validationMessage('invalid_special_price'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    /**
     * @TODO
     */
    public function test_WithInvalidValueForFields_InvalidQty()
    {
        // @TODO
    }

    /**
     * <p>Creating product with empty custom options</p>
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields with correct data;</p>
     * <p>5. Click "Custom Options" tab;</p>
     * <p>6. Click "Add New Option" button;</p>
     * <p>7. Leave fields empty;</p>
     * <p>8. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @dataProvider data_EmptyCustomFields
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithCustomOptions_EmptyFields($emptyCustomFields)
    {
        //Data
        $productData = $this->loadData('bundle_dynamic_product_required', null, 'general_sku');
        $productData['custom_options_data'][] = $this->loadData('custom_options_empty',
                        array($emptyCustomFields => "%noValue%"));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
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
     * <p>Creating product with invalid custom options</p>
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields with correct data;</p>
     * <p>5. Click "Custom Options" tab;</p>
     * <p>6. Click "Add New Option" button;</p>
     * <p>7. Fill in fields with incorrect data;</p>
     * <p>8. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @dataProvider data_invalidData_NumericField
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithCustomOptions_InvalidValues($invalidData)
    {
        //Data
        $productData = $this->loadData('bundle_dynamic_product_required', NULL, 'general_sku');
        $productData['custom_options_data'][] = $this->loadData('custom_options_field',
                        array('custom_options_price' => $invalidData));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertTrue($this->validationMessage('enter_valid_number'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    /**
     * @TODO
     */
    public function test_WithSpecialPrice_EmptyValue()
    {
        // @TODO
    }

    /**
     * <p>Creating product with empty tier price</p>
     * <p>Steps<p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields with correct data;</p>
     * <p>5. Click "Add Tier" button and leave fields in current fieldset empty;</p>
     * <p>6. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @dataProvider data_EmptyField_TierPrice
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithTierPriceFieldsEmpty($emptyTierPrice)
    {
        //Data
        $productData = $this->loadData('bundle_dynamic_product_required', null, 'general_sku');
        $productData['prices_tier_price_data'][] = $this->loadData('prices_tier_price_1',
                        array($emptyTierPrice => '%noValue%'));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $fieldXpath = $this->_getControlXpath('field', $emptyTierPrice);
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function data_EmptyField_TierPrice()
    {
        return array(
            array('prices_tier_price_qty'),
            array('prices_tier_price_price'),
        );
    }

    /**
     * <p>Creating product with invalid Tier Price Data</p>
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields with correct data;</p>
     * <p>5. Click "Add Tier" button and fill in fields in current fieldset with imcorrect data;</p>
     * <p>6. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @dataProvider data_invalidData_NumericField
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithTierPrice_InvalidValues($invalidTierData)
    {
        //Data
        $tierData = array(
            'prices_tier_price_qty' => $invalidTierData,
            'prices_tier_price_price' => $invalidTierData
        );
        $productData = $this->loadData('bundle_dynamic_product_required', null, 'general_sku');
        $productData['prices_tier_price_data'][] = $this->loadData('prices_tier_price_1', $tierData);
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        foreach ($tierData as $key => $value) {
            $fieldXpath = $this->_getControlXpath('field', $key);
            $this->addParameter('fieldXpath', $fieldXpath);
            $this->assertTrue($this->validationMessage('invalid_tier_price'), $this->messages);
        }
        $this->assertTrue($this->verifyMessagesCount(2), $this->messages);
    }

    /**
     * <p>Creating product with empty Bundle Items fields</p>
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields with correct data;</p>
     * <p>5. Click "Bundle Items" tab;</p>
     * <p>6. Click "Add New Option" button;</p>
     * <p>7. Leave fields empty;</p>
     * <p>8. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithBundleItems_EmptyFields()
    {
        //Data
        $productData = $this->loadData('bundle_dynamic_product_required', null, 'general_sku');
        $productData['bundle_items_data']['bundle_items_1'] = $this->loadData('bundle_items_1',
                        array('bundle_items_default_title' => "%noValue%"));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $xpath = $this->_getControlXpath('field', 'bundle_items_default_title');
        $this->addParameter('fieldXpath', $xpath);
        $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);

        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function data_invalidData_NumericField()
    {
        return array(
            array($this->generate('string', 9, ':punct:')),
            array($this->generate('string', 9, ':alpha:')),
            array('g3648GJHghj'),
            array('-128')
        );
    }

}
