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
class Product_Create_Bundle extends Mage_Selenium_TestCase
{

    /**
     * <p>Login to backend</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog -> Manage Products</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->addParameter('id', '0');
    }


    /**
     * <p>Creating product with required fields only</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @test
     */
    public function requiredFieldsForDynamicSmoke()
    {
        //Data
        $productData = $this->loadData('dynamic_bundle_required', null,
                        array('general_name', 'general_sku'));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        return $productData;
    }

    /**
     * <p>Creating product with required fields only</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @test
     */
    public function requiredFieldsForFixedSmoke()
    {
        //Data
        $productData = $this->loadData('fixed_bundle_required', null,
                        array('general_name', 'general_sku'));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
    }

    /**
     * <p>Creating product with existing SKU</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields using exist SKU;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>6. Verify error message;</p>
     * <p>Expected result:</p>
     * <p>Error message appears;</p>
     *
     * @depends requiredFieldsForDynamicSmoke
     * @test
     */
    public function skuThatAlreadyExists($productData)
    {
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertTrue($this->validationMessage('existing_sku'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    /**
     * <p>Creating product with empty required fields</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Leave one required field empty and fill in the rest of fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>6. Verify error message;</p>
     * <p>7. Repeat scenario for all required fields for both tabs;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @dataProvider dataEmptyField
     * @depends requiredFieldsForDynamicSmoke
     * @test
     */
    public function emptyRequiredFields($emptyField, $fieldType)
    {
        //Data
        $field = key($emptyField);
        if ($field != 'general_sku') {
            $productData = $this->loadData('fixed_bundle_required', $emptyField, 'general_sku');
        } else {
            $productData = $this->loadData('fixed_bundle_required', $emptyField);
        }
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $fieldXpath = $this->_getControlXpath($fieldType, $field);
        $fieldId = $this->getAttribute($fieldXpath . '/@id');
        $this->addParameter('fieldId', $fieldId);

        $this->assertTrue($this->validationMessage('empty_required_field_by_id'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function dataEmptyField()
    {
        return array(
            array(array('general_name' => '%noValue%'), 'field'),
            array(array('general_description' => '%noValue%'), 'field'),
            array(array('general_short_description' => '%noValue%'), 'field'),
            array(array('general_sku_type' => '-- Select --'), 'dropdown'),
            array(array('general_sku' => '%noValue%'), 'field'),
            array(array('general_weight_type' => '-- Select --'), 'dropdown'),
            array(array('general_weight' => '%noValue%'), 'field'),
            array(array('general_status' => '%noValue%'), 'dropdown'),
            array(array('general_visibility' => '-- Please Select --'), 'dropdown'),
            array(array('prices_price_type' => '-- Select --'), 'dropdown'),
            array(array('prices_price' => '%noValue%'), 'field'),
            array(array('prices_tax_class' => '-- Please Select --'), 'dropdown')
        );
    }

    /**
     * <p>Creating product with special characters into required fields</p>
     * <p>Steps</p>
     * <p>1. Click 'Add Product' button;</p>
     * <p>2. Fill in 'Attribute Set', 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields with special symbols ('General' tab), rest - with normal data;
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product created, confirmation message appears</p>
     *
     * @depends requiredFieldsForDynamicSmoke
     * @test
     */
    public function specialCharactersInRequiredFields()
    {
        //Data
        $productData = $this->loadData('dynamic_bundle_required',
                        array(
                            'general_name'              => $this->generate('string', 32, ':punct:'),
                            'general_description'       => $this->generate('string', 32, ':punct:'),
                            'general_short_description' => $this->generate('string', 32, ':punct:'),
                            'general_sku'               => $this->generate('string', 32, ':punct:')
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
     * <p>1. Click 'Add Product' button;</p>
     * <p>2. Fill in 'Attribute Set', 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields with long values ('General' tab), rest - with normal data;
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product created, confirmation message appears</p>
     *
     * @depends requiredFieldsForDynamicSmoke
     * @test
     */
    public function longValuesInRequiredFields()
    {
        //Data
        $productData = $this->loadData('fixed_bundle_required',
                        array(
                            'general_name'              => $this->generate('string', 255, ':alnum:'),
                            'general_description'       => $this->generate('string', 255, ':alnum:'),
                            'general_short_description' => $this->generate('string', 255, ':alnum:'),
                            'general_sku'               => $this->generate('string', 64, ':alnum:'),
                            'general_weight'            => 99999999.9999
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
     * <p>1. Click 'Add Product' button;</p>
     * <p>2. Fill in 'Attribute Set', 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields, use for sku string with length more than 64 characters</p>
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @depends requiredFieldsForDynamicSmoke
     * @test
     */
    public function incorrectSkuLength()
    {
        //Data
        $productData = $this->loadData('dynamic_bundle_required',
                        array('general_sku' => $this->generate('string', 65, ':alnum:')));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertTrue($this->validationMessage('incorrect_sku_length'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    /**
     * <p>Creating product with invalid weight</p>
     * <p>Steps</p>
     * <p>1. Click 'Add Product' button;</p>
     * <p>2. Fill in 'Attribute Set', 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in 'Weight' field with special characters, the rest - with normal data;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product created, confirmation message appears, Weight=0;</p>
     *
     * @depends requiredFieldsForDynamicSmoke
     * @test
     */
    public function invalidValueForWeight()
    {
        //Data
        $productData = $this->loadData('fixed_bundle_required',
                        array('general_weight' => $this->generate('string', 9, ':punct:')),
                        array('general_name', 'general_sku'));
        $productSearch = $this->loadData('product_search',
                        array(
                            'general_sku'  => $productData['general_sku'],
                            'general_name' => $productData['general_name']
                ));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $xpath = $this->_getControlXpath('field', 'general_weight');
        $weightValue = $this->getValue($xpath);
        $this->assertEquals(0.0000, $weightValue, 'The product weight should be 0.0000');
    }

    /**
     * <p>Creating product with invalid price</p>
     * <p>Steps</p>
     * <p>1. Click 'Add Product' button;</p>
     * <p>2. Fill in 'Attribute Set', 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in 'Price' field with special characters, the rest fields - with normal data;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @dataProvider dataInvalidNumericField
     * @depends requiredFieldsForDynamicSmoke
     * @test
     */
    public function invalidValueForPrice($invalidPrice)
    {
        //Data
        $productData = $this->loadData('dynamic_bundle_required',
                        array('prices_price' => $invalidPrice, 'prices_price_type' => 'Fixed'),
                        'general_sku');
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertTrue($this->validationMessage('invalid_price'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    /**
     * <p>Creating product with invalid special price</p>
     * <p>Steps</p>
     * <p>1. Click 'Add Product' button;</p>
     * <p>2. Fill in 'Attribute Set', 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in field 'Special Price' with invalid data, the rest fields - with correct data;
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:<p>
     * <p>Product is not created, error message appears;</p>
     *
     * @dataProvider dataInvalidNumericField
     * @depends requiredFieldsForDynamicSmoke
     * @test
     */
    public function invalidValueForSpecialPrice($invalidValue)
    {
        //Data
        $productData = $this->loadData('dynamic_bundle_required',
                        array('prices_special_price' => $invalidValue), 'general_sku');
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertTrue($this->validationMessage('invalid_special_price'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    /**
     * <p>Creating product with empty tier price</p>
     * <p>Steps<p>
     * <p>1. Click 'Add Product' button;</p>
     * <p>2. Fill in 'Attribute Set', 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields with correct data;</p>
     * <p>5. Click 'Add Tier' button and leave fields in current fieldset empty;</p>
     * <p>6. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @dataProvider dataEmptyFieldTierPrice
     * @depends requiredFieldsForDynamicSmoke
     * @test
     */
    public function emptyFieldsTierPriceFor($emptyTierPrice)
    {
        //Data
        $productData = $this->loadData('dynamic_bundle_required', null, 'general_sku');
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

    public function dataEmptyFieldTierPrice()
    {
        return array(
            array('prices_tier_price_qty'),
            array('prices_tier_price_price'),
        );
    }

    /**
     * <p>Creating product with invalid Tier Price Data</p>
     * <p>Steps</p>
     * <p>1. Click 'Add Product' button;</p>
     * <p>2. Fill in 'Attribute Set', 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields with correct data;</p>
     * <p>5. Click 'Add Tier' button and fill in fields in current fieldset with imcorrect data;</p>
     * <p>6. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @dataProvider dataInvalidNumericField
     * @depends requiredFieldsForDynamicSmoke
     * @test
     */
    public function invalidValuesFoeTierPrice($invalidTierData)
    {
        //Data
        $tierData = array(
            'prices_tier_price_qty'   => $invalidTierData,
            'prices_tier_price_price' => $invalidTierData
        );
        $productData = $this->loadData('dynamic_bundle_required', null, 'general_sku');
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
     * <p>Creating product with empty Bundle Items Default Title</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Add Bundle Items Option;</p>
     * <p>6. Leave Default Title field empty and fill in the rest of fields;</p>
     * <p>7. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @depends requiredFieldsForDynamicSmoke
     * @test
     */
    public function emptyBundleItemsTitle()
    {
        //Data
        $productData = $this->loadData('dynamic_bundle_required', null,
                        array('general_name', 'general_sku'));
        $productData['bundle_items_data'][] = $this->loadData('bundle_items_1',
                        array('bundle_items_default_title' => '%noValue%'));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $xpath = $this->_getControlXpath('field', 'bundle_items_default_title');
        $this->addParameter('fieldXpath', $xpath);
        $this->assertTrue($this->successMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    /**
     * @test
     * @dataProvider dataInvalidNumericField
     * @depends requiredFieldsForDynamicSmoke
     */
    public function invalidPositionForBundleItems($invalidPosition)
    {
        //Data
        $productData = $this->loadData('dynamic_bundle_required', null,
                        array('general_name', 'general_sku'));
        $productData['bundle_items_data'][] = $this->loadData('bundle_items_1',
                        array('bundle_items_position' => $invalidPosition));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $fieldXpath = $this->_getControlXpath('field', 'bundle_items_position');
        $fieldId = $this->getAttribute($fieldXpath . '/@name');
        $this->addParameter('fieldId', $fieldId);
        $this->assertTrue($this->successMessage('invalid_numeric_value'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function dataInvalidNumericField()
    {
        return array(
            array($this->generate('string', 9, ':punct:')),
            array($this->generate('string', 9, ':alpha:')),
            array('g3648GJHghj'),
            array('-128')
        );
    }

}
