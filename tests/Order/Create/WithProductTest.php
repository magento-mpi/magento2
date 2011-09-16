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
 * Order creation with product
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Order_Create_WithProductTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Preconditions:</p>
     *
     * <p>Log in to Backend.</p>
     * <p>Navigate to 'Manage Products' page</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    protected function assertPreConditions()
    {
        $this->navigate('system_configuration');
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
        $this->addParameter('tabName', 'edit/section/payment/');
        $this->clickControl('tab', 'sales_payment_methods');
        $payment = $this->loadData('saved_cc_wo3d_enable');
        $this->fillForm($payment, 'sales_payment_methods');
        $this->saveForm('save_config');
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $this->addParameter('id', '0');
    }

//    /**
//     * Test Realizing precondition for creating configurable product.
//     *
//     * @test
//     */
//    public function createConfigurableAttribute()
//    {
//        //Data
//        $attrData = $this->loadData('product_attribute_dropdown_with_options', null,
//                array('admin_title', 'attribute_code'));
//        $associatedAttributes = $this->loadData('associated_attributes',
//                array('General' => $attrData['attribute_code']));
//        //Steps
//        $this->navigate('manage_attributes');
//        $this->productAttributeHelper()->createAttribute($attrData);
//        //Verifying
//        $this->assertTrue($this->successMessage('success_saved_attribute'), $this->messages);
//        //Steps
//        $this->navigate('manage_attribute_sets');
//        $this->attributeSetHelper()->openAttributeSet();
//        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
//        $this->saveForm('save_attribute_set');
//        //Verifying
//        $this->assertTrue($this->successMessage('success_attribute_set_saved'), $this->messages);
//
//        return $attrData;
//    }

    /**
     * <p>Creating order with simple products</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select simple product and add it to the order;</p>
     * <p>4. Fill in all required information</p>
     * <p>5. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @test
     * depends createConfigurableAttribute
     */
    public function withSimpleProduct(/* $attrData */)
    {
        //Data
        $simple = $this->loadData('simple_product_for_order', null, array('general_name', 'general_sku'));
//        $attrCode = $attrData['attribute_code'];
//        $simple['general_user_attr']['dropdown'][$attrCode] = $attrData['option_1']['admin_option_name'];
        $orderData = $this->loadData('order_template',
                array('filter_sku' => $simple['general_sku'],
            'customer_email' => $this->generate('email', 32, 'valid')));
        //Steps
        $this->productHelper()->createProduct($simple);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
//        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
//        //Steps
//        $this->navigate('manage_sales_orders');
//        $orderId = $this->orderHelper()->createOrder($orderData);
//        //Verifying
//        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
        $simple = array('general_sku' => $simple['general_sku'], 'general_name' => $simple['general_name']);
        return $simple;
    }

    /**
     * <p>Creating order with virtual products</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select virtual product and add it to the order. Fill any required information to configure product;</p>
     * <p>4. Fill in all required information. Shipping address fill and shipping methods should be disabled;</p>
     * <p>5. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * depends createConfigurableAttribute
     * @test
     */
    public function withVirtualProduct(/* $attrData */)
    {
        //Data
        $virtual = $this->loadData('virtual_product_for_order', null, array('general_name', 'general_sku'));
//        $attrCode = $attrData['attribute_code'];
//        $virtual['general_user_attr']['dropdown'][$attrCode] = $attrData['option_1']['admin_option_name'];
        $orderData = $this->loadData('order_template_virtual',
                array('filter_sku' => $virtual['general_sku'],
            'customer_email' => $this->generate('email', 32, 'valid')));
        //Steps
        $this->productHelper()->createProduct($virtual, 'virtual');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
//        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
//        //Steps
//        $this->navigate('manage_sales_orders');
//        $orderId = $this->orderHelper()->createOrder($orderData);
//        //Verifying
//        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
        $virtual = array('general_sku' => $virtual['general_sku'], 'general_name' => $virtual['general_name']);
        return $virtual;
    }

//    /**
//     * <p>Creating order with downloadable products</p>
//     * <p>Steps:</p>
//     * <p>1. Navigate to "Manage Orders" page;</p>
//     * <p>2. Create new order for new customer;</p>
//     * <p>3. Select downloadable product and add it to the order.Fill any required information to configure product;</p>
//     * <p>4. Fill in all required information; Shipping methods and address should be disabled;</p>
//     * <p>5. Click "Submit Order" button;</p>
//     * <p>Expected result:</p>
//     * <p>Order is created;</p>
//     *
//     * @test
//     */
//    public function withDownloadableConfigProduct()
//    {
//        //Data
//        $downloadable = $this->loadData('downloadable_product_for_order', null, array('general_name', 'general_sku'));
//        $orderData = $this->loadData('order_template_virtual',
//                array(
//            'filter_sku' => $downloadable['general_sku'],
//            'configurable_options' => $this->loadData('config_option_download'),
//            'customer_email' => $this->generate('email', 32, 'valid')
//                ));
//        //Steps
//        $this->productHelper()->createProduct($downloadable, 'downloadable');
//        //Verifying
//        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
//        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
//        //Steps
//        $this->navigate('manage_sales_orders');
//        $orderId = $this->orderHelper()->createOrder($orderData);
//        //Verifying
//        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
//    }
//    /**
//     * <p>Creating order with downloadable products</p>
//     * <p>Steps:</p>
//     * <p>1. Navigate to "Manage Orders" page;</p>
//     * <p>2. Create new order for new customer;</p>
//     * <p>3. Select downloadable product and add it to the order.Fill any required information to configure product;</p>
//     * <p>4. Fill in all required information; Shipping methods and address should be disabled;</p>
//     * <p>5. Click "Submit Order" button;</p>
//     * <p>Expected result:</p>
//     * <p>Order is created;</p>
//     *
//     * depends createConfigurableAttribute
//     * @test
//     */
//    public function withDownloadableNotConfigProduct(/* $attrData */)
//    {
//        //Data
//        $downloadable = $this->loadData('downloadable_product_for_order',
//                array('downloadable_links_purchased_separately' => 'No'), array('general_name', 'general_sku'));
////        $attrCode = $attrData['attribute_code'];
////        $downloadable['general_user_attr']['dropdown'][$attrCode] = $attrData['option_1']['admin_option_name'];
//        $orderData = $this->loadData('order_template_virtual',
//                array('filter_sku' => $downloadable['general_sku'],
//            'customer_email' => $this->generate('email', 32, 'valid')));
//        //Steps
//        $this->productHelper()->createProduct($downloadable, 'downloadable');
//        //Verifying
//        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
////        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
////        //Steps
////        $this->navigate('manage_sales_orders');
////        $orderId = $this->orderHelper()->createOrder($orderData);
////        //Verifying
////        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
//
//        return $downloadable['general_sku'];
//    }
//    /**
//     * <p>Creating order with grouped products</p>
//     * <p>Steps:</p>
//     * <p>1. Navigate to "Manage Orders" page;</p>
//     * <p>2. Create new order for new customer;</p>
//     * <p>3. Select group product and add it to the order. Fill any required information to configure product;</p>
//     * <p>4. Fill in all required information</p>
//     * <p>5. Click "Submit Order" button;</p>
//     * <p>Expected result:</p>
//     * <p>Order is created;</p>
//     *
//     * @depends withSimpleProduct
//     * @depends withVirtualProduct
//     * @depends withDownloadableNotConfigProduct
//     * @test
//     */
//    public function groupedWithAllTypesOfProducts($simple, $virtual, $download)
//    {
//        //Data
//
//        $grouped = $this->loadData('grouped_product_for_order', array('associated_search_sku' => $simple),
//                array('general_name', 'general_sku'));
//        $grouped['associated_grouped_data']['associated_grouped_2'] = $this->loadData('associated_grouped',
//                array('associated_search_sku' => $virtual));
//        $grouped['associated_grouped_data']['associated_grouped_3'] = $this->loadData('associated_grouped',
//                array('associated_search_sku' => $download));
//        $orderData = $this->loadData('order_template',
//                array(
//            'filter_sku' => $grouped['general_sku'],
//            'configurable_options' => $this->loadData('config_option_grouped',
//                    array('value_1' => $simple, 'value_2' => $virtual, 'value_3' => $download)),
//            'customer_email' => $this->generate('email', 32, 'valid')));
//        //Steps
//        $this->productHelper()->createProduct($grouped, 'grouped');
//        //Verifying
//        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
//        //Steps
//        $this->navigate('manage_sales_orders');
//        $orderId = $this->orderHelper()->createOrder($orderData);
//        //Verifying
//        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
//
//        return $grouped['general_sku'];
//    }
//
//    /**
//     * <p>Creating order with grouped products</p>
//     * <p>Steps:</p>
//     * <p>1. Navigate to "Manage Orders" page;</p>
//     * <p>2. Create new order for new customer;</p>
//     * <p>3. Select group product and add it to the order. Fill any required information to configure product;</p>
//     * <p>4. Fill in all required information</p>
//     * <p>5. Click "Submit Order" button;</p>
//     * <p>Expected result:</p>
//     * <p>Order is created;</p>
//     *
//     * @depends withVirtualProduct
//     * @depends withDownloadableNotConfigProduct
//     * @depends groupedWithAllTypesOfProducts
//     * @test
//     */
//    public function groupedWithVirtualTypesOfProducts($virtual, $download, $grouped)
//    {
//        //Data
//        $orderData = $this->loadData('order_template_virtual',
//                array(
//            'filter_sku' => $grouped,
//            'configurable_options' => $this->loadData('config_option_grouped',
//                    array('value_1' => $download, 'value_2' => $virtual)),
//            'customer_email' => $this->generate('email', 32, 'valid')));
//        //Steps
//        $this->navigate('manage_sales_orders');
//        $orderId = $this->orderHelper()->createOrder($orderData);
//        //Verifying
//        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
//
//        return $orderData;
//    }

    /**
     * <p>Creating order with bundled products</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select bundled product and add it to the order. Fill any required information to configure product;</p>
     * <p>4. Fill in all required information</p>
     * <p>5. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @depends withSimpleProduct
     * @depends withVirtualProduct
     * @test
     */
    public function bundleWithSimple($simple, $virtual)
    {
        //Data
        $bundle = $this->loadData('fixed_bundle_for_order', array('bundle_items_search_sku' => $simple['general_sku']),
                array('general_name', 'general_sku'));
        for ($i = 1; $i < 5; $i++) {
            $bundle['bundle_items_data']['item_' . $i]['add_product_2'] = $this->loadData('bundle_item_1/add_product_1',
                    array('bundle_items_search_sku' => $virtual['general_sku']));
        }
        $orderData = $this->loadData('config_option_bundle',
                array(
            'filter_sku' => $bundle['general_sku'],
            'multiple_choose' => $virtual['general_name'],
            'dropdown_choose' => $simple['general_name'],
            'customer_email' => $this->generate('email', 32, 'valid')));
        $orderData['products_to_add']['product_1']['configurable_options']
            ['optionRadio']['value_1'] = $simple['general_name'];
        $orderData['products_to_add']['product_1']['configurable_options']
            ['optionCheck']['value_1'] = $virtual['general_name'];
        $orderData['products_to_add']['product_1']['configurable_options']
            ['optionCheck']['value_2'] = $simple['general_name'];
        //Steps
        $this->productHelper()->createProduct($bundle, 'bundle');
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        //Steps
        $this->navigate('manage_sales_orders');
        $orderId = $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
    }

//    /**
//     * <p>Creating order with configurable products</p>
//     * <p>Steps:</p>
//     * <p>1. Navigate to "Manage Orders" page;</p>
//     * <p>2. Create new order for new customer;</p>
//     * <p>3. Select configurable product and add it to the order.Fill any required information to configure product;</p>
//     * <p>4. Fill in all required information</p>
//     * <p>5. Click "Submit Order" button;</p>
//     * <p>Expected result:</p>
//     * <p>Order is created;</p>
//     *
//     * @test
//     */
//    public function configurablePrd()
//    {
//        $attrData = $this->loadData('product_attribute_dropdown_with_options', null,
//                array('admin_title', 'attribute_code'));
//        $associatedAttributes = $this->loadData('associated_attributes',
//                array('General' => $attrData['attribute_code']));
//        $this->navigate('manage_attributes');
//        $this->productAttributeHelper()->createAttribute($attrData);
//        $this->assertTrue($this->successMessage('success_saved_attribute'), $this->messages);
//        $this->navigate('manage_attribute_sets');
//        $this->attributeSetHelper()->openAttributeSet();
//        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
//        $this->saveForm('save_attribute_set');
//        $this->assertTrue($this->successMessage('success_attribute_set_saved'), $this->messages);
//        $this->navigate('manage_products');
//        $simple = $this->loadData('simple_product_for_configurable_for_order', null,
//                array('general_name', 'general_sku'));
//        $simple['general_user_attr']['dropdown'][$attrData['attribute_code']] =
//                $attrData['option_1']['admin_option_name'];
//        $configurable = $this->loadData('configurable_product_for_order',
//                array('configurable_attribute_title' => $attrData['admin_title']),
//                array('general_name', 'general_sku'));
//        $configurable['associated_configurable_data'] = $this->loadData('associated_configurable_data',
//                array('associated_search_sku' => $simple['general_sku']));
//        $this->productHelper()->createProduct($simple);
//        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
//        $this->assertTrue($this->checkCurrentPage('manage_products'),
//                'After successful product creation should be redirected to Manage Products page');
//        $this->productHelper()->createProduct($configurable, 'configurable');
//        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
//        $this->assertTrue($this->checkCurrentPage('manage_products'),
//                'After successful product creation should be redirected to Manage Products page');
//        $orderData = $this->loadData('order_req_configurable_product',
//                array('filter_sku' => $configurable['general_sku']));
//        $orderData['products_to_add']['product_1']['configurable_options']['optionDropdown']['value_1'] =
//                $attrData['admin_title'];
//        $orderData['account_data']['customer_email'] = $this->generate('email', 32, 'valid');
//        $this->navigate('manage_sales_orders');
//        $orderId = $this->orderHelper()->createOrder($orderData);
//    }
}
