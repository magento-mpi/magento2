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
 * Duplicate product tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Product_DuplicateTest extends Mage_Selenium_TestCase
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
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $this->addParameter('id', '0');
    }

    /**
     * Test Realizing precondition for creating configurable product.
     *
     * @test
     */
    public function createConfigurableAttribute()
    {
        //Data
        $attrData = $this->loadData('product_attribute_dropdown_with_options', null,
                array('admin_title', 'attribute_code'));
        $associatedAttributes = $this->loadData('associated_attributes',
                array('General' => $attrData['attribute_code']));
        //Steps
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_attribute'), $this->messages);
        //Steps
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        //Verifying
        $this->assertTrue($this->successMessage('success_attribute_set_saved'), $this->messages);

        return $attrData;
    }

    /**
     * <p>Creating duplicated simple product</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>6. Open created product;</p>
     * <p>7. Click "Duplicate" button;</p>
     * <p>8. Verify required fields has the same data except SKU (field empty)</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @depends createConfigurableAttribute
     * @test
     */
    public function duplicateSimple($attrData)
    {
        //Data
        $productData = $this->loadData('duplicate_simple', null, array('general_name', 'general_sku'));
        $productData['general_user_attr']['dropdown'][$attrData['attribute_code']] =
                $attrData['option_1']['admin_option_name'];
        $productSearch = $this->loadData('product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->clickButton('duplicate');
        $this->assertTrue($this->successMessage('success_duplicated_product'), $this->messages);
        $this->productHelper()->verifyProductInfo($productData, array('general_sku', 'general_status'));

        return $productData['general_sku'];
    }

    /**
     * <p>Creating duplicated virtual product</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>6. Open created product;</p>
     * <p>7. Click "Duplicate" button;</p>
     * <p>8. Verify required fields has the same data except SKU (field empty)</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @depends createConfigurableAttribute
     * @test
     */
    public function duplicateVirtual($attrData)
    {
        //Data
        $productData = $this->loadData('duplicate_virtual', null, array('general_name', 'general_sku'));
        $productData['general_user_attr']['dropdown'][$attrData['attribute_code']] =
                $attrData['option_2']['admin_option_name'];
        $productSearch = $this->loadData('product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'virtual');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->clickButton('duplicate');
        $this->assertTrue($this->successMessage('success_duplicated_product'), $this->messages);
        $this->productHelper()->verifyProductInfo($productData, array('general_sku', 'general_status'));

        return $productData['general_sku'];
    }

    /**
     * <p>Creating duplicated downloadable product</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>6. Open created product;</p>
     * <p>7. Click "Duplicate" button;</p>
     * <p>8. Verify required fields has the same data except SKU (field empty)</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @depends createConfigurableAttribute
     * @test
     */
    public function duplicateDownloadable($attrData)
    {
        //Data
        $productData = $this->loadData('duplicate_downloadable', null, array('general_name', 'general_sku'));
        $productData['general_user_attr']['dropdown'][$attrData['attribute_code']] =
                $attrData['option_3']['admin_option_name'];
        $productSearch = $this->loadData('product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'downloadable');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->clickButton('duplicate');
        $this->assertTrue($this->successMessage('success_duplicated_product'), $this->messages);
        $this->productHelper()->verifyProductInfo($productData, array('general_sku', 'general_status'));

        return $productData['general_sku'];
    }

    /**
     * <p>Creating grouped product with assosiated products</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>6. Open created product;</p>
     * <p>7. Click "Duplicate" button;</p>
     * <p>8. Verify required fields has the same data except SKU (field empty)</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @depends duplicateSimple
     * @depends duplicateVirtual
     * @depends duplicateDownloadable
     *
     * @test
     */
    public function duplicateGrouped($simple, $virtual, $downloadable)
    {
        //Data
        $productData = $this->loadData('duplicate_grouped',
                array(
                    'related_search_sku'           => $simple,
                    'related_product_position'     => 10,
                    'up_sells_search_sku'          => $virtual,
                    'up_sells_product_position'    => 20,
                    'cross_sells_search_sku'       => $downloadable,
                    'cross_sells_product_position' => 30
                ), array('general_name', 'general_sku'));

        $productData['associated_grouped_data']['associated_grouped_1']['associated_search_sku'] = $simple;
        $productData['associated_grouped_data']['associated_grouped_2']['associated_search_sku'] = $virtual;
        $productData['associated_grouped_data']['associated_grouped_3']['associated_search_sku'] = $downloadable;
        $productSearch = $this->loadData('product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'grouped');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->clickButton('duplicate');
        $this->assertTrue($this->successMessage('success_duplicated_product'), $this->messages);
        $this->productHelper()->verifyProductInfo($productData, array('general_sku', 'general_status'));
    }

    /**
     * <p>Creating duplicated Bundle Product</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>6. Open created product;</p>
     * <p>7. Click "Duplicate" button;</p>
     * <p>8. Verify required fields has the same data except SKU (field empty)</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @depends duplicateSimple
     * @depends duplicateVirtual
     * @dataProvider dataBundle
     * @test
     */
    public function duplicateBundle($data, $simple, $virtual)
    {
        //Data
        $productData = $this->loadData($data,
                array(
                    'related_search_sku'           => $simple,
                    'related_product_position'     => 10,
                    'up_sells_search_sku'          => $virtual,
                    'up_sells_product_position'    => 20,
                    'cross_sells_search_sku'       => $virtual,
                    'cross_sells_product_position' => 30
                ), array('general_name', 'general_sku'));

        $productData['bundle_items_data']['bundle_items_1']['bundle_items_add_product_1']['bundle_items_search_sku'] =
                $simple;
        $productData['bundle_items_data']['bundle_items_2']['bundle_items_add_product_1']['bundle_items_search_sku'] =
                $virtual;
        $productSearch = $this->loadData('product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->clickButton('duplicate');
        $this->assertTrue($this->successMessage('success_duplicated_product'), $this->messages);
        $this->productHelper()->verifyProductInfo($productData, array('general_sku', 'general_status'));
    }

    public function dataBundle()
    {
        return array(
            array('duplicate_fixed_bundle'),
            array('duplicate_dynamic_bundle'),
        );
    }

    /**
     * <p>Duplicate Configurable product with assosiated products</p>
     * <p>Preconditions</p>
     * <p>Attribute Set created</p>
     * <p>Virtual product created</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in all required fields;</p>
     * <p>5. Goto "Associated products" tab;</p>
     * <p>6. Select created Virtual product;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>6. Open created product;</p>
     * <p>7. Click "Duplicate" button;</p>
     * <p>8. Verify required fields has the same data except SKU (field empty)</p>
     *
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @test
     * @depends createConfigurableAttribute
     * @depends duplicateSimple
     * @depends duplicateVirtual
     * @depends duplicateDownloadable
     */
    public function duplicateConfigurable($attrData, $simple, $virtual, $downloadable)
    {
        $this->markTestIncomplete('Need clarification from CORE TEAM');
        //Data
        $productData = $this->loadData('duplicate_configurable',
                array(
                    'configurable_attribute_title' => $attrData['admin_title'],
                    'related_search_sku'           => $simple,
                    'related_product_position'     => 10,
                    'up_sells_search_sku'          => $virtual,
                    'up_sells_product_position'    => 20,
                    'cross_sells_search_sku'       => $downloadable,
                    'cross_sells_product_position' => 30
                ), array('general_name', 'general_sku'));

        $productData['associated_configurable_data']['associated_configurable_1']['associated_search_sku'] = $simple;
        $productData['associated_configurable_data']['associated_configurable_2']['associated_search_sku'] = $virtual;
        $productData['associated_configurable_data']['associated_configurable_3']['associated_search_sku'] =
                $downloadable;
        $productSearch = $this->loadData('product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->clickButton('duplicate');
        //Verifying
        $this->assertTrue($this->successMessage('success_duplicated_product'), $this->messages);
        $this->productHelper()->fillConfigurableSettings($productData);
        //Verifying
//        unset ($productData['associated_configurable_data']);
        $this->productHelper()->verifyProductInfo($productData,
                array('general_sku', 'general_status', 'configurable_attribute_title'));
    }

}
