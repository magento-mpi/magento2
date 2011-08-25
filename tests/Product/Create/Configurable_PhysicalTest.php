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
 * Configurable product creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Product_Create_Configurable_PhysicalTest extends Mage_Selenium_TestCase
{

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Manage Attributes.</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_attributes');
        $this->assertTrue($this->checkCurrentPage('manage_attributes'), 'Wrong page is opened');
        $this->addParameter('id', 0);
    }

    /**
     * <p>Create configurable product with required fields</p>
     * <p>Steps</p>
     * <p>1. Create attribute and attribute set for configurable product;</p>
     * <p>2. Navigate to "Manage Products" page;</p>
     * <p>3. Click "Add Product" button;</p>
     * <p>4. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>5. Click "Continue" button;</p>
     * <p>6. Fill in required fields with correct data;</p>
     * <p>7. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is created;</p>
     *
     */
    public function test_WithRequiredFieldsOnly()
    {
        //Preconditions
        $attrData = $this->loadData('product_attribute_dropdown_with_options',
                        array('admin_title' => $this->generate('string', 5, ':alnum:'),
                            'scope' => 'Global'
                        ),
                        'attribute_code');
        $this->productAttributeHelper()->createAttribute($attrData);
        $attrSet = $this->loadData('attribute_set',
                        array('attribute_1' => $attrData['attribute_code']
                        ),
                        array('name', 'folder'));
        $this->AttributeSetHelper()->createAttributeSet($attrSet);
        //Steps
        $productData = $this->loadData('configurable_product_required',
                        array(
                            'product_attribute_set' => $attrSet['name'],
                            'configurable_attribute_title' => $attrData['admin_title']
                        ),
                        'general_sku');
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->ProductHelper()->createProduct($productData, 'configurable');
        $this->waitForPageToLoad();
        //Verification
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                        'After successful product creation should be redirected to Manage Products page');
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        //Removing attribute set, attribute, product
        $this->AttributeSetHelper()->openAttributeSet(array('name'=>$attrSet['name']));
        $this->deleteElement('delete_attribute_set', 'confirmation_for_delete');
        $searchData = $this->loadData('attribute_search_data',
                        array(
                            'attribute_code'  => $attrData['attribute_code'],
                            'attribute_lable' => $attrData['admin_title']
                        ));
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->openAttribute($searchData);
        $this->deleteElement('delete_attribute', 'delete_confirm_message');
        $this->assertTrue($this->successMessage('success_deleted_attribute'), $this->messages);
    }

    /**
     * <p>Create configurable product with sku that already exists</p>
     * <p>Steps</p>
     * <p>1. Create attribute and attribute set for configurable product;</p>
     * <p>2. Navigate to "Manage Products" page;</p>
     * <p>3. Click "Add Product" button;</p>
     * <p>4. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>5. Click "Continue" button;</p>
     * <p>6. Fill in required fields with correct data;</p>
     * <p>7. Click "Save" button;</p>
     * <p>8. Click "Add Product" button;</p>
     * <p>9. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>10. Click "Continue" button;</p>
     * <p>11. Fill in required fields with correct data, but with sku that already exists;</p>
     * <p>12. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, 'sku fields should be unique' message appears;</p>
     *
     */
    public function test_WithSkuThatAlreadyExists()
    {
        //Preconditions
        $attrData = $this->loadData('product_attribute_dropdown_with_options',
                        array(
                            'admin_title' => $this->generate('string', 5, ':alnum:'),
                            'scope' => 'Global'
                        ),
                        'attribute_code');
        $this->productAttributeHelper()->createAttribute($attrData);
        $attrSet = $this->loadData('attribute_set',
                        array(
                            'attribute_1' => $attrData['attribute_code']
                        ),
                        array('name', 'folder'));
        $this->AttributeSetHelper()->createAttributeSet($attrSet);
        //Steps
        $productData = $this->loadData('configurable_product_required',
                        array(
                            'product_attribute_set' => $attrSet['name'],
                            'configurable_attribute_title' => $attrData['admin_title']
                        ),
                        'general_sku');
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->ProductHelper()->createProduct($productData, 'configurable');
        $this->waitForPageToLoad();
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->ProductHelper()->createProduct($productData, 'configurable');
        $this->pleaseWait(10,10);
        //Verification
        $this->assertTrue($this->validationMessage('existing_sku'), $this->messages);
        //Removing attribute set, attribute, product
        $this->AttributeSetHelper()->openAttributeSet(array('name'=>$attrSet['name']));
        $this->deleteElement('delete_attribute_set', 'confirmation_for_delete');
        $searchData = $this->loadData('attribute_search_data',
                        array(
                            'attribute_code'  => $attrData['attribute_code'],
                            'attribute_lable' => $attrData['admin_title']
                        )
        );
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->openAttribute($searchData);
        $this->deleteElement('delete_attribute', 'delete_confirm_message');
        $this->assertTrue($this->successMessage('success_deleted_attribute'), $this->messages);
    }

    /**
     * <p>Create configurable product with empty required fields</p>
     * <p>Steps</p>
     * <p>1. Create attribute and attribute set for configurable product;</p>
     * <p>2. Navigate to "Manage Products" page;</p>
     * <p>3. Click "Add Product" button;</p>
     * <p>4. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>5. Click "Continue" button;</p>
     * <p>6. Fill in required fields with correct data, but leave one field empty;</p>
     * <p>7. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, message appears with warning for each empty field;</p>
     *
     * @dataProvider dataEmptyField
     */
    public function test_WithRequiredFieldsEmpty($emptyField, $fieldType)
    {
        //Preconditions
        $attrData = $this->loadData('product_attribute_dropdown_with_options',
                        array(
                            'admin_title' => $this->generate('string', 5, ':alnum:'),
                            'scope' => 'Global'
                        ),
                        'attribute_code');
        $this->productAttributeHelper()->createAttribute($attrData);
        $attrSet = $this->loadData('attribute_set',
                        array(
                            'attribute_1' => $attrData['attribute_code']
                        ),
                        array('name', 'folder'));
        $this->AttributeSetHelper()->createAttributeSet($attrSet);

        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        if ($emptyField == 'general_sku') {
            $productData = $this->loadData('configurable_product_required',
                            array(
                                $emptyField => '%noValue%', 'product_attribute_set' => $attrSet['name'],
                                'configurable_attribute_title' => $attrData['admin_title'])
                            );
        } elseif ($emptyField == 'general_visibility') {
            $productData = $this->loadData('configurable_product_required',
                            array(
                                $emptyField => '-- Please Select --', 'product_attribute_set' => $attrSet['name'],
                                'configurable_attribute_title' => $attrData['admin_title']
                            ),
                            'general_sku');
        } elseif ($emptyField == 'inventory_qty') {
            $productData = $this->loadData('configurable_product_required',
                            array(
                                $emptyField => '', 'product_attribute_set' => $attrSet['name'],
                                'configurable_attribute_title' => $attrData['admin_title']
                            ),
                            'general_sku');
        } else {
            $productData = $this->loadData('configurable_product_required',
                            array(
                                $emptyField => '%noValue%', 'product_attribute_set' => $attrSet['name'],
                                'configurable_attribute_title' => $attrData['admin_title']
                            ),
                            'general_sku');
        }
        //Steps
        $this->ProductHelper()->createProduct($productData, 'configurable');
        $this->addFieldIdToMessage($fieldType, $emptyField);
        //Verification
        $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
        //Removing attribute set, attribute, product
        $this->AttributeSetHelper()->openAttributeSet(array('name'=>$attrSet['name']));
        $this->deleteElement('delete_attribute_set', 'confirmation_for_delete');
        $searchData = $this->loadData('attribute_search_data',
                        array(
                            'attribute_code'  => $attrData['attribute_code'],
                            'attribute_lable' => $attrData['admin_title']
                        )
        );
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->openAttribute($searchData);
        $this->deleteElement('delete_attribute', 'delete_confirm_message');
        $this->assertTrue($this->successMessage('success_deleted_attribute'), $this->messages);
    }

    /**
     * <p>Create configurable product with special chars in required fields</p>
     * <p>Steps</p>
     * <p>1. Create attribute and attribute set for configurable product;</p>
     * <p>2. Navigate to "Manage Products" page;</p>
     * <p>3. Click "Add Product" button;</p>
     * <p>4. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>5. Click "Continue" button;</p>
     * <p>6. Fill in required fields with correct data (special chars);</p>
     * <p>7. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is created;</p>
     *
     */
    public function test_WithSpecialCharacters()
    {
        //Preconditions
        $attrData = $this->loadData('product_attribute_dropdown_with_options',
                        array(
                            'admin_title' => $this->generate('string', 5, ':alnum:'),
                            'scope' => 'Global'
                        ),
                        'attribute_code');
        $this->productAttributeHelper()->createAttribute($attrData);
        $attrSet = $this->loadData('attribute_set',
                        array(
                            'attribute_1' => $attrData['attribute_code']
                        ),
                        array('name', 'folder'));
        $this->AttributeSetHelper()->createAttributeSet($attrSet);
        //Steps
        $productData = $this->loadData('configurable_product_required',
                        array(
                            'product_attribute_set' => $attrSet['name'],
                            'configurable_attribute_title' => $attrData['admin_title'],
                            'general_name'              => $this->generate('string', 32, ':punct:'),
                            'general_description'       => $this->generate('string', 32, ':punct:'),
                            'general_short_description' => $this->generate('string', 32, ':punct:'),
                            'general_sku'               => $this->generate('string', 32, ':punct:')
                        ));
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->ProductHelper()->createProduct($productData, 'configurable');
        $this->waitForPageToLoad();
        //Verification
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $productSearch = $this->loadData('product_search',
                        array(
                            'product_sku' => $productData['general_sku']
                        ));
        $this->productHelper()->openProduct($productSearch);
        $this->assertTrue($this->verifyForm($productData, 'general'), $this->messages);
        //Removing attribute set, attribute, product
        $this->AttributeSetHelper()->openAttributeSet(array('name'=>$attrSet['name']));
        $this->deleteElement('delete_attribute_set', 'confirmation_for_delete');
        $searchData = $this->loadData('attribute_search_data',
                        array(
                            'attribute_code'  => $attrData['attribute_code'],
                            'attribute_lable' => $attrData['admin_title']
                        ));
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->openAttribute($searchData);
        $this->deleteElement('delete_attribute', 'delete_confirm_message');
        $this->assertTrue($this->successMessage('success_deleted_attribute'), $this->messages);
    }

    /**
     * <p>Create configurable product with invalid price</p>
     * <p>Steps</p>
     * <p>1. Create attribute and attribute set for configurable product;</p>
     * <p>2. Navigate to "Manage Products" page;</p>
     * <p>3. Click "Add Product" button;</p>
     * <p>4. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>5. Click "Continue" button;</p>
     * <p>6. Fill in all fields with correct data, and set incorrect value to price;</p>
     * <p>7. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, warning message appears;</p>
     *
     * @dataProvider dataInvalidNumericField
     */
    public function test_WithInvalidValueForFields_InvalidPrice($invalidPrice)
    {
        //Preconditions
        $attrData = $this->loadData('product_attribute_dropdown_with_options',
                        array(
                            'admin_title' => $this->generate('string', 5, ':alnum:'),
                            'scope' => 'Global'
                        ),
                        'attribute_code');
        $this->productAttributeHelper()->createAttribute($attrData);
        $attrSet = $this->loadData('attribute_set',
                        array(
                            'attribute_1' => $attrData['attribute_code']
                        ),
                        array('name', 'folder'));
        $this->AttributeSetHelper()->createAttributeSet($attrSet);
        //Steps
        $productData = $this->loadData('configurable_product',
                        array(
                            'product_attribute_set' => $attrSet['name'],
                            'configurable_attribute_title' => $attrData['admin_title'],
                            'prices_price' => $invalidPrice
                        ),
                        'general_sku');
        unset($productData['associated_products_configurable_data']);
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->ProductHelper()->createProduct($productData, 'configurable');
        $this->pleaseWait(10,10);
        //Verification
        $this->addFieldIdToMessage('field', 'prices_price');
        $this->assertTrue($this->validationMessage('enter_zero_or_greater'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
        //Removing attribute set, attribute, product
        $this->AttributeSetHelper()->openAttributeSet(array('name'=>$attrSet['name']));
        $this->deleteElement('delete_attribute_set', 'confirmation_for_delete');
        $searchData = $this->loadData('attribute_search_data',
                        array(
                            'attribute_code'  => $attrData['attribute_code'],
                            'attribute_lable' => $attrData['admin_title']
                        ));
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->openAttribute($searchData);
        $this->deleteElement('delete_attribute', 'delete_confirm_message');
        $this->assertTrue($this->successMessage('success_deleted_attribute'), $this->messages);
    }

    /**
     * <p>Create configurable product with empty fields in custom options</p>
     * <p>Steps</p>
     * <p>1. Create attribute and attribute set for configurable product;</p>
     * <p>2. Navigate to "Manage Products" page;</p>
     * <p>3. Click "Add Product" button;</p>
     * <p>4. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>5. Click "Continue" button;</p>
     * <p>6. Fill in all fields with correct data, and leave empty field in custom options;</p>
     * <p>7. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, warning message appears;</p>
     *
     * @dataProvider dataEmptyGeneralFields
     */
    public function test_WithCustomOptions_EmptyFields($emptyCustomField)
    {
        //Preconditions
        $attrData = $this->loadData('product_attribute_dropdown_with_options',
                        array(
                            'admin_title' => $this->generate('string', 5, ':alnum:'),
                            'scope' => 'Global'
                        ),
                        'attribute_code');
        $this->productAttributeHelper()->createAttribute($attrData);
        $attrSet = $this->loadData('attribute_set',
                        array('attribute_1' => $attrData['attribute_code']), array('name', 'folder'));
        $this->AttributeSetHelper()->createAttributeSet($attrSet);
        //Steps
        $productData = $this->loadData('configurable_product',
                        array(
                            'product_attribute_set' => $attrSet['name'],
                            'configurable_attribute_title' => $attrData['admin_title']
                        ),
                        'general_sku');
        $productData['custom_options_data'][] = $this->loadData('custom_options_empty',
                        array($emptyCustomField => "%noValue%"));
        unset($productData['associated_products_configurable_data']);
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->ProductHelper()->createProduct($productData, 'configurable');
        $this->pleaseWait(10,10);
        //Verification
        if ($emptyCustomField == 'custom_options_general_title') {
            $this->addFieldIdToMessage('field', $emptyCustomField);
            $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        } else {
            $this->assertTrue($this->validationMessage('select_type_of_option'), $this->messages);
        }
        //Removing attribute set, attribute, product
        $this->AttributeSetHelper()->openAttributeSet(array('name'=>$attrSet['name']));
        $this->deleteElement('delete_attribute_set', 'confirmation_for_delete');
        $searchData = $this->loadData('attribute_search_data',
                        array(
                            'attribute_code'  => $attrData['attribute_code'],
                            'attribute_lable' => $attrData['admin_title']
                        )
        );
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->openAttribute($searchData);
        $this->deleteElement('delete_attribute', 'delete_confirm_message');
        $this->assertTrue($this->successMessage('success_deleted_attribute'), $this->messages);
    }
    /**
     * <p>Create configurable product with invalid value in special price</p>
     * <p>Steps</p>
     * <p>1. Create attribute and attribute set for configurable product;</p>
     * <p>2. Navigate to "Manage Products" page;</p>
     * <p>3. Click "Add Product" button;</p>
     * <p>4. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>5. Click "Continue" button;</p>
     * <p>6. Fill in all fields with correct data, but put to special price field incorrect value;</p>
     * <p>7. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, warning message appears;</p>
     *
     * @dataProvider dataInvalidNumericField
     */
    public function test_WithSpecialPrice_InvalidValue($invalidValue)
    {
        //Preconditions
        $attrData = $this->loadData('product_attribute_dropdown_with_options',
                        array(
                            'admin_title' => $this->generate('string', 5, ':alnum:'),
                            'scope' => 'Global'
                        ),
                        'attribute_code');
        $this->productAttributeHelper()->createAttribute($attrData);
        $attrSet = $this->loadData('attribute_set',
                        array('attribute_1' => $attrData['attribute_code']),
                        array('name', 'folder'));
        $this->AttributeSetHelper()->createAttributeSet($attrSet);
        //Steps
        $productData = $this->loadData('configurable_product',
                        array(
                            'product_attribute_set' => $attrSet['name'],
                            'configurable_attribute_title' => $attrData['admin_title'],
                            'prices_special_price' => $invalidValue
                        ),
                        'general_sku');
        unset($productData['associated_products_configurable_data']);
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->ProductHelper()->createProduct($productData, 'configurable');
        $this->pleaseWait(10,10);
        //Verification
        $this->addFieldIdToMessage('field', 'prices_special_price');
        $this->assertTrue($this->validationMessage('enter_zero_or_greater'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
        //Removing attribute set, attribute, product
        $this->AttributeSetHelper()->openAttributeSet(array('name'=>$attrSet['name']));
        $this->deleteElement('delete_attribute_set', 'confirmation_for_delete');
        $searchData = $this->loadData('attribute_search_data',
                        array(
                            'attribute_code'  => $attrData['attribute_code'],
                            'attribute_lable' => $attrData['admin_title']
                        ));
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->openAttribute($searchData);
        $this->deleteElement('delete_attribute', 'delete_confirm_message');
        $this->assertTrue($this->successMessage('success_deleted_attribute'), $this->messages);
    }

    /**
     * <p>Create configurable product with empty value in special price</p>
     * <p>Steps</p>
     * <p>1. Create attribute and attribute set for configurable product;</p>
     * <p>2. Navigate to "Manage Products" page;</p>
     * <p>3. Click "Add Product" button;</p>
     * <p>4. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>5. Click "Continue" button;</p>
     * <p>6. Fill in all fields with correct data, but leave special price field empty;</p>
     * <p>7. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is created;</p>
     */
    public function test_WithSpecialPrice_EmptyValue()
    {
        //Preconditions
        $attrData = $this->loadData('product_attribute_dropdown_with_options',
                        array(
                            'admin_title' => $this->generate('string', 5, ':alnum:'),
                            'scope' => 'Global'
                        ),
                        'attribute_code');
        $this->productAttributeHelper()->createAttribute($attrData);
        $attrSet = $this->loadData('attribute_set',
                        array('attribute_1' => $attrData['attribute_code']),
                        array('name', 'folder'));
        $this->AttributeSetHelper()->createAttributeSet($attrSet);
        //Steps
        $productData = $this->loadData('configurable_product',
                        array(
                            'product_attribute_set' => $attrSet['name'],
                            'configurable_attribute_title' => $attrData['admin_title'],
                            'prices_special_price' => '%noValue%'
                        ),
                        'general_sku');
        unset($productData['associated_products_configurable_data']);
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->ProductHelper()->createProduct($productData, 'configurable');
        $this->waitForPageToLoad();
        //Verification
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        //Removing attribute set, attribute, product
        $this->AttributeSetHelper()->openAttributeSet(array('name'=>$attrSet['name']));
        $this->deleteElement('delete_attribute_set', 'confirmation_for_delete');
        $searchData = $this->loadData('attribute_search_data',
                        array(
                            'attribute_code'  => $attrData['attribute_code'],
                            'attribute_lable' => $attrData['admin_title']
                        ));
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->openAttribute($searchData);
        $this->deleteElement('delete_attribute', 'delete_confirm_message');
        $this->assertTrue($this->successMessage('success_deleted_attribute'), $this->messages);
    }
    /**
     * <p>Create configurable product with empty value in tier price</p>
     * <p>Steps</p>
     * <p>1. Create attribute and attribute set for configurable product;</p>
     * <p>2. Navigate to "Manage Products" page;</p>
     * <p>3. Click "Add Product" button;</p>
     * <p>4. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>5. Click "Continue" button;</p>
     * <p>6. Fill in all fields with correct data, but leave tier price field empty;</p>
     * <p>7. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, appropriate message appears;</p>
     *
     * @dataProvider tierPriceFields
     */
    public function test_WithTierPrice_EmptyFields($emptyTierPrice)
    {
        //Preconditions
        $attrData = $this->loadData('product_attribute_dropdown_with_options',
                        array(
                            'admin_title' => $this->generate('string', 5, ':alnum:'),
                            'scope' => 'Global'
                        ),
                        'attribute_code');
        $this->productAttributeHelper()->createAttribute($attrData);
        $attrSet = $this->loadData('attribute_set',
                        array('attribute_1' => $attrData['attribute_code']),
                        array('name', 'folder'));
        $this->AttributeSetHelper()->createAttributeSet($attrSet);
        //Steps
        $productData = $this->loadData('configurable_product',
                        array(
                            'product_attribute_set' => $attrSet['name'],
                            'configurable_attribute_title' => $attrData['admin_title'],
                            $emptyTierPrice => '%noValue%'
                        ),
                        'general_sku');
        unset($productData['associated_products_configurable_data']);
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->ProductHelper()->createProduct($productData, 'configurable');
        $this->pleaseWait(10,10);
        //Verification
        $this->addFieldIdToMessage('field', $emptyTierPrice);
        $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        //Removing attribute set, attribute, product
        $this->AttributeSetHelper()->openAttributeSet(array('name'=>$attrSet['name']));
        $this->deleteElement('delete_attribute_set', 'confirmation_for_delete');
        $searchData = $this->loadData('attribute_search_data',
                        array(
                            'attribute_code'  => $attrData['attribute_code'],
                            'attribute_lable' => $attrData['admin_title']
                        ));
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->openAttribute($searchData);
        $this->deleteElement('delete_attribute', 'delete_confirm_message');
        $this->assertTrue($this->successMessage('success_deleted_attribute'), $this->messages);
    }

    /**
     * <p>Create configurable product with invalid value in tier price</p>
     * <p>Steps</p>
     * <p>1. Create attribute and attribute set for configurable product;</p>
     * <p>2. Navigate to "Manage Products" page;</p>
     * <p>3. Click "Add Product" button;</p>
     * <p>4. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>5. Click "Continue" button;</p>
     * <p>6. Fill in all fields with correct data, but put invalid value to tier price field;</p>
     * <p>7. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, appropriate message appears;</p>
     *
     * @dataProvider dataInvalidNumericField
     */
    public function test_WithTierPrice_InvalidValues($invalidTierData)
    {
        //Preconditions
        $attrData = $this->loadData('product_attribute_dropdown_with_options',
                        array(
                            'admin_title' => $this->generate('string', 5, ':alnum:'),
                            'scope' => 'Global'
                        ),
                        'attribute_code');
        $this->productAttributeHelper()->createAttribute($attrData);
        $attrSet = $this->loadData('attribute_set',
                        array('attribute_1' => $attrData['attribute_code']),
                        array('name', 'folder'));
        $this->AttributeSetHelper()->createAttributeSet($attrSet);
        //Steps
        $productData = $this->loadData('configurable_product',
                        array(
                            'product_attribute_set' => $attrSet['name'],
                            'configurable_attribute_title' => $attrData['admin_title']
                        ),
                        'general_sku');
        unset($productData['associated_products_configurable_data']);
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $tierData = array(
                        'prices_tier_price_qty' => $invalidTierData,
                        'prices_tier_price_price' => $invalidTierData
        );
        $productData['prices_tier_price_data'][] = $this->loadData('prices_tier_price_1', $tierData);

        $this->ProductHelper()->createProduct($productData, 'configurable');
        $this->pleaseWait(10,10);
        //Verification
        foreach ($tierData as $key => $value) {
            $this->addFieldIdToMessage('field', $key);
            $this->assertTrue($this->validationMessage('enter_greater_than_zero'), $this->messages);
        }
        //Removing attribute set, attribute, product
        $this->AttributeSetHelper()->openAttributeSet(array('name'=>$attrSet['name']));
        $this->deleteElement('delete_attribute_set', 'confirmation_for_delete');
        $searchData = $this->loadData('attribute_search_data',
                        array(
                            'attribute_code'  => $attrData['attribute_code'],
                            'attribute_lable' => $attrData['admin_title']
                        ));
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->openAttribute($searchData);
        $this->deleteElement('delete_attribute', 'delete_confirm_message');
        $this->assertTrue($this->successMessage('success_deleted_attribute'), $this->messages);
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

    public function tierPriceFields()
    {
        return array(
            array('prices_tier_price_qty'),
            array('prices_tier_price_price'),
        );
    }

    public function dataEmptyGeneralFields()
    {
        return array(
            array('custom_options_general_title'),
            array('custom_options_general_input_type')
        );
    }

    public function dataEmptyField()
    {
        return array(
            array('general_name', 'field'),
            array('general_description', 'field'),
            array('general_short_description', 'field'),
            array('general_sku', 'field'),
            array('general_status', 'dropdown'),
            array('general_visibility', 'dropdown'),
            array('prices_price', 'field'),
            array('prices_tax_class', 'dropdown')
        );
    }
}
