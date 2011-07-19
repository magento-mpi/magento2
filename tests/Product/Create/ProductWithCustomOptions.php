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
 * Product creation with custom options tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Product_CreateWithCustomOptions extends Mage_Selenium_TestCase
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
     * <p>Creating product with empty fields for "Select" type</p>
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields with correct data;</p>
     * <p>5. Click "Custom Options" tab;</p>
     * <p>6. Click "Add New Option" button;</p>
     * <p>7. Select "Multipleselect" (or any other from Select type) into "Input Type" field;</p>
     * <p>7. Leave fields empty;</p>
     * <p>8. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @dataProvider dataemptyFields
     * @param array $emptyField
     * @test
     */
    public function CustomOptionsEmptyFieldsSelectType($emptyField)
    {
        //Data
        $productData = $this->loadData('simple_product_required', null, 'general_sku');
        $productData['custom_options_data'][] = $this->loadData('custom_options_empty_select_type',
                        $emptyField);
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $fieldXpath = $this->_getControlXpath('field', 'custom_options_title');
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function dataemptyFields()
    {
        return array(
            array(array('custom_options_general_input_type' => 'Drop-down')),
            array(array('custom_options_general_input_type' => 'Radio Buttons')),
            array(array('custom_options_general_input_type' => 'Checkbox')),
            array(array('custom_options_general_input_type' => 'Multiple Select'))
        );
    }

    /**
     * <p>Creating product with invalid "Price" custom options</p>
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields with correct data;</p>
     * <p>5. Click "Custom Options" tab;</p>
     * <p>6. Click "Add New Option" button;</p>
     * <p>7. Select "Multipleselect" into "Input Type" field;</p>
     * <p>8. Fill in "Price" field with incorrect data;</p>
     * <p>9. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @dataProvider datainvalidDataNumericField
     * @test
     */
    public function invalidPriceForCustomOptions($invalidData)
    {
        //Data
        $productData = $this->loadData('simple_product_required', null, 'general_sku');
        $productData['custom_options_data'][] = $this->loadData('custom_options_multipleselect',
                        array('custom_options_price' => $invalidData));

        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $fieldXpath = $this->_getControlXpath('field', 'custom_options_price');
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertTrue($this->validationMessage('enter_valid_number_select_type'),
                $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    /**
     * <p>Creating product with invalid "Sort Order" into custom options</p>
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields with correct data;</p>
     * <p>5. Click "Custom Options" tab;</p>
     * <p>6. Click "Add New Option" button;</p>
     * <p>7. Select "Multipleselect" into "Input Type" field;</p>
     * <p>8. Fill in "Sort Order" field with incorrect data;</p>
     * <p>9. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @dataProvider datainvalidDataNumericField
     * @test
     */
    public function CustomOptionsInvalidSortOrder($invalidData)
    {
        //Data
        $productData = $this->loadData('simple_product_required', NULL, 'general_sku');
        $productData['custom_options_data'][] = $this->loadData('custom_options_field',
                        array('custom_options_general_sort_order' => $invalidData));
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $fieldXpath = $this->_getControlXpath('field', 'custom_options_general_sort_order');
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertTrue($this->validationMessage('enter_valid_sort_order'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
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
     * <p>7. Select "Field" or "Area" into "Input Type" field;</p>
     * <p>8. Fill in "Max Characters" field with incorrect data;</p>
     * <p>9. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @dataProvider datainvalidDataNumericField
     * @test
     */
    public function CustomOptionsInvalidMaxChar($invalidData)
    {
        //Data
        $productData = $this->loadData('simple_product_required', NULL, 'general_sku');
        $productData['custom_options_data'][] = $this->loadData('custom_options_field',
                        array('custom_options_max_characters' => $invalidData));
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $fieldXpath = $this->_getControlXpath('field', 'custom_options_max_characters');
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertTrue($this->validationMessage('enter_valid_sort_order'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function datainvalidDataNumericField()
    {
        return array(
            array($this->generate('string', 9, ':punct:')),
            array($this->generate('string', 9, ':alpha:')),
            array('g3648GJHghj')
        );
    }

}
