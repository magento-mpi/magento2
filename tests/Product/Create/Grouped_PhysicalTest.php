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
 * @TODO
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Product_Create_Grouped_PhysicalTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Log in to Backend.</p>
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
     * <p>1. Click "Add product" button;</p>
     * <p>2. Fill in "Attribute Set" and "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @test
     */
    public function onlyRequiredFieldsInGrouped()
    {
        //Data
        $productData = $this->loadData('grouped_product_required', null,
                array('general_name', 'general_sku'));
        //Steps
        $this->productHelper()->createProduct($productData, 'grouped');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        return $productData;
    }

    /**
     * <p>Creating product with all fields</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in all fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @test
     */
    public function allFieldsForGrouped()
    {

        //Data
        $productDataSimple = $this->loadData('simple_product_required', null,
                array('general_name', 'general_sku'));
        $productDataVirtual = $this->loadData('virtual_product_required', null,
                array('general_name', 'general_sku'));

        $productData = $this->loadData('grouped_product', null,
                array('general_name', 'general_sku'));

        //Creating Simple Product
        $this->productHelper()->createProduct($productDataSimple);

        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        //Creating Virtual Product
        $this->productHelper()->createProduct($productDataVirtual, 'virtual');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        //Creating Grouped Product with phisical Simple
        $productData['associated_products_grouped_data']['associated_products_grouped_1']
                ['associated_products_sku'] = $productDataSimple['general_sku'];
        unset($productData['associated_products_grouped_data']['associated_products_grouped_2']);

        $this->productHelper()->createProduct($productData, 'grouped');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        //Creating Grouped Product with phisical Simple
        $productData = $this->loadData('grouped_product', null,
                array('general_name', 'general_sku'));
        unset($productData['associated_products_grouped_data']['associated_products_grouped_1']);
        $productData['associated_products_grouped_data']['associated_products_grouped_2']
                ['associated_products_sku'] = $productDataVirtual['general_sku'];
        $this->productHelper()->createProduct($productData, 'grouped');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        //Creating Grouped Product with phisical Simple
        $productData = $this->loadData('grouped_product', null,
                array('general_name', 'general_sku'));
        $productData['associated_products_grouped_data']['associated_products_grouped_1']
                ['associated_products_sku'] = $productDataSimple['general_sku'];
        $productData['associated_products_grouped_data']['associated_products_grouped_2']
                ['associated_products_sku'] = $productDataVirtual['general_sku'];
        $this->productHelper()->createProduct($productData, 'grouped');
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
     * @depends onlyRequiredFieldsInGrouped
     * @test
     */
    public function existSkuInGrouped($productData)
    {
        //Steps
        $this->productHelper()->createProduct($productData, 'grouped');
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
     * @test
     */
    public function emptyRequiredFieldInGrouped($emptyField, $fieldType)
    {
        //Data
        $field = key($emptyField);
        if ($field != 'general_sku') {
            $productData = $this->loadData('grouped_product_required', $emptyField, 'general_sku');
        } else {
            $productData = $this->loadData('grouped_product_required', $emptyField);
        }
        //Steps
        $this->productHelper()->createProduct($productData, 'grouped');
        //Verifying
        $this->addFieldIdToMessage($fieldType, $field);
        $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function dataEmptyField()
    {
        return array(
            array(array('general_name' => '%noValue%'), 'field'),
            array(array('general_description' => '%noValue%'), 'field'),
            array(array('general_short_description' => '%noValue%'), 'field'),
            array(array('general_sku' => '%noValue%'), 'field'),
            array(array('general_status' => '%noValue%'), 'dropdown'),
            array(array('general_visibility' => '-- Please Select --'), 'dropdown'),
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
     * @test
     */
    public function specialCharactersInRequiredFields()
    {
        //Data
        $productData = $this->loadData('grouped_product_required',
                array(
            'general_name' => $this->generate('string', 32, ':punct:'),
            'general_description' => $this->generate('string', 32, ':punct:'),
            'general_short_description' => $this->generate('string', 32, ':punct:'),
            'general_sku' => $this->generate('string', 32, ':punct:')
                ));
        $productSearch = $this->loadData('product_search',
                array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'grouped');
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
     * @test
     */
    public function longValuesInRequiredFields()
    {
        //Data
        $productData = $this->loadData('grouped_product_required',
                array(
            'general_name' => $this->generate('string', 255, ':alnum:'),
            'general_description' => $this->generate('string', 255, ':alnum:'),
            'general_short_description' => $this->generate('string', 255, ':alnum:'),
            'general_sku' => $this->generate('string', 64, ':alnum:')
                ));
        $productSearch = $this->loadData('product_search',
                array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'grouped');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->assertTrue($this->verifyForm($productData, 'general'), $this->messages);
    }
}
