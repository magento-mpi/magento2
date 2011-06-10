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
class Product_Create_VirtualTest extends Mage_Selenium_TestCase
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
     * 6. Fill in required fields on General tab;
     *
     * 7. Fill in required fields on Prices tab;
     *
     * 8. Click "Save" button;
     *
     * 9. Verify confirmation message;
     *
     * Expected result:
     *
     * Product created, confirmation message appears;
     *
     */
    public function test_WithRequiredFieldsOnly()
    {
        //Data
        $productSettings = $this->loadData('product_create_settings_virtual');
        $productData = $this->loadData('virtual_product', NULL, 'product_sku');

        //Steps. Open 'Manage Products' page, click 'Add New Product' button, fill in form.
        $this->clickButton('add_new_product');
        $this->productHelper()->fillProductSettings($productSettings);
        $this->fillForm($productData, 'general');
        $this->clickControl('tab', 'prices', FALSE);
        $this->fillForm($productData, 'prices');
        $this->saveForm('save');
        //Verifying
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
        //Data
        $productSettings = $this->loadData('product_create_settings_virtual');
        //Steps. Open 'Manage Products' page, click 'Add New Product' button, fill in form.
        $this->clickButton('add_new_product');
        $this->productHelper()->fillProductSettings($productSettings);
        $this->fillForm($productData, 'general');
        $this->clickControl('tab', 'prices', FALSE);
        $this->fillForm($productData, 'prices');
        $this->saveForm('save');
        //Verifying
        $this->assertTrue($this->validationMessage('existing_sku'), $this->messages);
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
    public function test_WithRequiredFieldsEmpty($emptyField, $tabWithError)
    {
        //Data
        $productSettings = $this->loadData('product_create_settings_virtual');
        if (array_key_exists('product_sku', $emptyField)) {
            $productData = $this->loadData('virtual_product', $emptyField);
        } else {
            $productData = $this->loadData('virtual_product', $emptyField, 'product_sku');
        }
        //Steps
        $this->clickButton('add_new_product');
        $this->productHelper()->fillProductSettings($productSettings);
        $this->fillForm($productData, 'general');
        $this->clickControl('tab', 'prices', FALSE);
        $this->fillForm($productData, 'prices');
        $this->saveForm('save');
        //Verifying
        $page = $this->getUimapPage('admin', 'new_product');
        $fieldSet = $page->findFieldset($tabWithError);
        foreach ($emptyField as $key => $value) {
            if ($fieldSet->findField($key) != Null) {
                $fieldXpath = $fieldSet->findField($key);
            } else {
                $fieldXpath = $fieldSet->findDropdown($key);
            }
        }
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function data_EmptyField()
    {
        return array(
            array(array('product_name' => ''), 'general'),
            array(array('product_description' => ''), 'general'),
            array(array('product_short_description' => ''), 'general'),
            array(array('product_sku' => ''), 'general'),
            array(array('product_status' => '%noValue%'), 'general'),
            array(array('product_visibility' => '-- Please Select --'), 'general'),
            array(array('product_price' => '%noValue%'), 'prices'),
            array(array('product_tax_class' => '%noValue%'), 'prices'),
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
        //Loading Data
        $productSettings = $this->loadData('product_create_settings_virtual');
        $productData = $this->loadData('virtual_product',
                        array(
                    'product_name' => $this->generate('string', 32, ':punct:'),
                    'product_description' => $this->generate('string', 32, ':punct:'),
                    'product_short_description' => $this->generate('string', 32, ':punct:'),
                    'product_sku' => $this->generate('string', 32, ':punct:')
                        )
        );

        $this->clickButton('add_new_product');
        $this->productHelper()->fillProductSettings($productSettings);
        $this->fillForm($productData, 'general');
        $this->clickControl('tab', 'prices', FALSE);
        $this->fillForm($productData, 'prices');
        $this->saveForm('save');

        //Verifying

        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
    }

    /**
     * @TODO
     */
    public function test_WithInvalidValueForFields_InvalidWeight()
    {
        // @TODO
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
     */
    public function test_WithInvalidValueForFields_InvalidPrice()
    {
        //Loading Data
        $productSettings = $this->loadData('product_create_settings_virtual');
        $productData = $this->loadData('virtual_product',
                        array('product_price' => $this->generate('string', 9, ':punct:')),
                        'product_sku');
        // Steps
        $this->clickButton('add_new_product');
        $this->productHelper()->fillProductSettings($productSettings);
        // Fill form
        $this->fillForm($productData, 'general');
        $this->clickControl('tab', 'prices', FALSE);
        $this->fillForm($productData, 'prices');
        $this->saveForm('save');
        $this->assertTrue($this->validationMessage('invalid_price'), $this->messages);
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
        //Loading Data
        $productSettings = $this->loadData('product_create_settings_virtual');
        $productData = $this->loadData('virtual_product', $InvalidQty, 'product_sku');
        $this->clickButton('add_new_product');
        $this->productHelper()->fillProductSettings($productSettings);
        // Fill form
        $this->fillForm($productData, 'general');
        $this->clickControl('tab', 'prices', FALSE);
        $this->fillForm($productData, 'prices');
        $this->clickControl('tab', 'inventory', FALSE);
        $this->fillForm($productData, 'inventory');
        $this->saveForm('save');
        //
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
        $productSettings = $this->loadData('product_create_settings_virtual');
        $productData = $this->loadData('virtual_product', NULL, 'product_sku');

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
        $productSettings = $this->loadData('product_create_settings_virtual');
        $productData = $this->loadData('virtual_product', $invalidData, 'product_sku');
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
     * @TODO
     */
    public function test_WithSpecialPrice_EmptyValue()
    {
        // @TODO
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
     * 11. Fill in required fields with incorrect data;
     *
     * 12. Click "Save" button;
     *
     * Expected result:
     *
     * Product is not created, error message appears;
     */
    public function test_WithSpecialPrice_InvalidValue()
    {
        //Loading Data
        $productSettings = $this->loadData('product_create_settings_virtual');
        $productData = $this->loadData('virtual_product',
                        array('product_special_price' => $this->generate('string', 9, ':punct:')),
                        'product_sku');

        $this->clickButton('add_new_product');
        $this->productHelper()->fillProductSettings($productSettings);
        // Fill form
        $this->fillForm($productData, 'general');
        $this->clickControl('tab', 'prices', FALSE);
        $this->fillForm($productData, 'prices');
        $this->saveForm('save');
        $this->assertTrue($this->validationMessage('invalid_special_price'), $this->messages);
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
     *
     */
    public function test_WithTierPrice_EmptyFields()
    {
        //Loading Data
        $productSettings = $this->loadData('product_create_settings_virtual');
        $productData = $this->loadData('virtual_product', NULL, 'product_sku');

        $this->clickButton('add_new_product');
        $this->productHelper()->fillProductSettings($productSettings);
        // Fill form

        $this->fillForm($productData, 'general');
        $this->clickControl('tab', 'prices', FALSE);

        $page = $this->getCurrentLocationUimapPage();
        $fieldSet = $page->findFieldset('prices');
        $fieldSetXpath = $fieldSet->getXpath();
        $rowNumber = $this->getXpathCount($fieldSetXpath . "//*[@id='tier_price_container']/tr");
        $this->addParameter('rowNumber', $rowNumber);
        $page->assignParams($this->_paramsHelper);

        $this->clickButton('add_tier_price', FALSE);
        $this->fillForm($productData, 'prices');
        $this->saveForm('save');
        $page = $this->getCurrentLocationUimapPage();
        $fieldSet = $page->findFieldSet('prices');

        $emptyFields = array('product_tier_price_qty', 'product_tier_price_price');
        foreach ($emptyFields as $value) {
            $fieldXpath = $fieldSet->findField($value);
            $this->addParameter('fieldXpath', $fieldXpath);
            $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        }
        $this->assertTrue($this->verifyMessagesCount(2), $this->messages);
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
        //Loading Data
        $productSettings = $this->loadData('product_create_settings_virtual');
        $productData = $this->loadData('virtual_product', $data_invalidDataTier, 'product_sku');

        $this->clickButton('add_new_product');
        $this->productHelper()->fillProductSettings($productSettings);
        // Fill form

        $this->fillForm($productData, 'general');
        $this->clickControl('tab', 'prices', FALSE);

        $page = $this->getCurrentLocationUimapPage();
        $fieldSet = $page->findFieldset('prices');
        $fieldSetXpath = $fieldSet->getXpath();
        $rowNumber = $this->getXpathCount($fieldSetXpath . "//*[@id='tier_price_container']/tr");
        $this->addParameter('rowNumber', $rowNumber);
        $page->assignParams($this->_paramsHelper);

        $this->clickButton('add_tier_price', FALSE);
        $this->fillForm($productData, 'prices');
        $this->saveForm('save');

        $page = $this->getCurrentLocationUimapPage();
        $fieldSet = $page->findFieldSet('prices');

        $fields = array('product_tier_price_qty', 'product_tier_price_price');
        foreach ($fields as $value) {
            $fieldXpath = $fieldSet->findField($value);
            $this->addParameter('fieldXpath', $fieldXpath);
            $this->assertTrue($this->validationMessage('invalid_tier_price_qty'), $this->messages);
        }

        $this->assertTrue($this->verifyMessagesCount(2), $this->messages);
    }

    public function data_invalidDataTier()
    {
        return array(
            array(array('product_tier_price_qty' => $this->generate('string', 9, ':punct:'), 'product_tier_price_price' => $this->generate('string',
                            9, ':punct:'))),
            array(array('product_tier_price_qty' => 'g3648GJHghj', 'product_tier_price_price' => 'g3648GJHghj')),
            array(array('product_tier_price_qty' => $this->generate('string', 9, ':alpha:'), 'product_tier_price_price' => $this->generate('string',
                            9, ':alpha:'))),
        );
    }

}
