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
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->addParameter('id', '0');
    }

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
     */
    public function test_SimplePrd()
    {
        $productData = $this->loadData('simple_product_for_order', null, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        $billingAddress = $this->loadData('new_customer_order_billing_address_reqfields',
                array(
                    $this->OrderHelper()->customerAddressGenerator(
                    ':alnum:', $addrType = 'billing', $symNum = 32, TRUE),
                    'billing_save_in_address_book' => 'yes' ));
        $billingAddress['email'] = $this->generate('email', 32, 'valid');
        $shippingAddress = array(
                'shipping_first_name'           => $billingAddress['billing_first_name'],
                'shipping_last_name'            => $billingAddress['billing_last_name'],
                'shipping_street_address_1'     => $billingAddress['billing_street_address_1'],
                'shipping_city'                 => $billingAddress['billing_city'],
                'shipping_zip_code'             => $billingAddress['billing_zip_code'],
                'shipping_telephone'            => $billingAddress['billing_telephone'],
                'shipping_save_in_address_book' => 'yes');
        $products = $this->loadData('simple_products_to_add');
        $products['product_1']['general_sku'] = $productData['general_sku'];
        $this->navigate('manage_sales_orders');
        $orderId = $this->OrderHelper()->createOrderForNewCustomer(false, 'Default Store View',
                $products, $billingAddress['email'],
                $billingAddress, $shippingAddress, 'visa','Fixed');
        $this->OrderHelper()->coverUpTraces($orderId, array('email' => $billingAddress['email']));
    }

    /**
     * <p>Creating order with grouped products</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select group product and add it to the order. Fill any required information to configure product;</p>
     * <p>4. Fill in all required information</p>
     * <p>5. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @test
     */
    public function test_GroupedPrd()
    {
        $simpleData = $this->loadData('simple_product_for_order', null, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($simpleData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        $groupedData = $this->loadData('grouped_product_for_order',
                array('associated_products_sku' => $simpleData['general_sku']), array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($groupedData, 'grouped');
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        $billingAddress = $this->loadData('new_customer_order_billing_address_reqfields',
                array(
                    $this->OrderHelper()->customerAddressGenerator(
                    ':alnum:', $addrType = 'billing', $symNum = 32, TRUE),
                    'billing_save_in_address_book' => 'yes' ));
        $billingAddress['email'] = $this->generate('email', 32, 'valid');
        $shippingAddress = array(
                'shipping_first_name'           => $billingAddress['billing_first_name'],
                'shipping_last_name'            => $billingAddress['billing_last_name'],
                'shipping_street_address_1'     => $billingAddress['billing_street_address_1'],
                'shipping_city'                 => $billingAddress['billing_city'],
                'shipping_zip_code'             => $billingAddress['billing_zip_code'],
                'shipping_telephone'            => $billingAddress['billing_telephone'],
                'shipping_save_in_address_book' => 'yes');
        $products = $this->loadData('grouped_products_to_add');
        $products['product_1']['general_sku'] = $groupedData['general_sku'];
        $products['product_1']['options']['optionText']['value_1'] = $simpleData['general_sku'];
        $this->navigate('manage_sales_orders');
        $orderId = $this->orderHelper()->createOrderForNewCustomer(false, 'Default Store View',
                $products, $billingAddress['email'],
                $billingAddress, $shippingAddress, 'visa','Fixed');
        $this->orderHelper()->coverUpTraces($orderId, array('email' => $billingAddress['email']));
    }

    /**
     * <p>Creating order with configurable products</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select configurable product and add it to the order. Fill any required information to configure product;</p>
     * <p>4. Fill in all required information</p>
     * <p>5. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @test
     */
    public function test_ConfigurablePrd()
    {
        $attrData = $this->loadData('product_attribute_dropdown_with_options', null,
                array('admin_title', 'attribute_code'));
        $associatedAttributes = $this->loadData('associated_attributes',
                array('General' => $attrData['attribute_code']));
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertTrue($this->successMessage('success_saved_attribute'), $this->messages);
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        $this->assertTrue($this->successMessage('success_attribute_set_saved'), $this->messages);
        $this->navigate('manage_products');
        $simple = $this->loadData('simple_product_for_configurable_for_order',
                null, array('general_name', 'general_sku'));
        $simple['general_user_attr']['dropdown'][$attrData['attribute_code']] =
                $attrData['option_1']['admin_option_name'];
        $configurable = $this->loadData('configurable_product_for_order',
                array('configurable_attribute_title' => $attrData['admin_title']),
                array('general_name', 'general_sku'));
        $configurable['associated_products_configurable_data'] =
                $this->loadData('associated_products_configurable_data',
                        array('associated_products_sku' => $simple['general_sku']));
        $this->productHelper()->createProduct($simple);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        $this->productHelper()->createProduct($configurable, 'configurable');
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        $billingAddress = $this->loadData('new_customer_order_billing_address_reqfields',
                array(
                    $this->OrderHelper()->customerAddressGenerator(
                   ':alnum:', $addrType = 'billing', $symNum = 32, TRUE),
                    'billing_save_in_address_book' => 'yes' ));
        $billingAddress['email'] = $this->generate('email', 32, 'valid');
        $shippingAddress = array(
                'shipping_first_name'           => $billingAddress['billing_first_name'],
                'shipping_last_name'            => $billingAddress['billing_last_name'],
                'shipping_street_address_1'     => $billingAddress['billing_street_address_1'],
                'shipping_city'                 => $billingAddress['billing_city'],
                'shipping_zip_code'             => $billingAddress['billing_zip_code'],
                'shipping_telephone'            => $billingAddress['billing_telephone'],
                'shipping_save_in_address_book' => 'yes');
        $products = $this->loadData('configurable_products_to_add');
        $products['product_1']['general_sku'] = $configurable['general_sku'];
        $products['product_1']['options']['optionDropdown']['value_1'] = $attrData['admin_title'];
        $this->navigate('manage_sales_orders');
        $orderId = $this->OrderHelper()->createOrderForNewCustomer(false, 'Default Store View',
                $products, $billingAddress['email'],
                $billingAddress, $shippingAddress, 'visa', 'Fixed');
        $this->orderHelper()->coverUpTraces($orderId, array('email' => $billingAddress['email']));
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
     * @test
     */
    public function virtualPrd()
    {
        //Precondtions
        $productData = $this->loadData('virtual_product_for_order', null, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData, 'virtual');
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        //Steps
        $billingAddress = $this->loadData('new_customer_order_billing_address_reqfields',
                array(
                    $this->OrderHelper()->customerAddressGenerator(
                    ':alnum:', $addrType = 'billing', $symNum = 32, TRUE),
                    'billing_save_in_address_book' => 'yes' ));
        $billingAddress['email'] = $this->generate('email', 32, 'valid');
        $products = $this->loadData('virtual_products_to_add');
        $products['product_1']['general_sku'] = $productData['general_sku'];
        $this->navigate('manage_sales_orders');
        $orderId = $this->OrderHelper()->createOrderForNewCustomer(false, 'Default Store View',
                array('general_sku' => $productData['general_sku']), $billingAddress['email'],
                $billingAddress, null, 'visa', null);
        $this->orderHelper()->coverUpTraces($orderId, array('email' => $billingAddress['email']));
    }

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
     * @test
     */
    public function bundlePrd()
    {
        $simpleData = $this->loadData('simple_product_for_bundle_for_order',
                null, array('general_name', 'general_sku'));
        $bundleData = $this->loadData('fixed_bundle_for_order', null, array('general_name', 'general_sku'));
        $bundleData['bundle_items_data']['bundle_items_1'] = $this->loadData('bundle_items_1',
                array('bundle_items_sku' => $simpleData['general_sku']));
        $this->productHelper()->createProduct($simpleData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        $this->productHelper()->createProduct($bundleData, 'bundle');
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        $billingAddress = $this->loadData('new_customer_order_billing_address_reqfields',
                array(
                    $this->OrderHelper()->customerAddressGenerator(
                   ':alnum:', $addrType = 'billing', $symNum = 32, TRUE),
                    'billing_save_in_address_book' => 'yes' ));
        $billingAddress['email'] = $this->generate('email', 32, 'valid');
        $shippingAddress = array(
                'shipping_first_name'           => $billingAddress['billing_first_name'],
                'shipping_last_name'            => $billingAddress['billing_last_name'],
                'shipping_street_address_1'     => $billingAddress['billing_street_address_1'],
                'shipping_city'                 => $billingAddress['billing_city'],
                'shipping_zip_code'             => $billingAddress['billing_zip_code'],
                'shipping_telephone'            => $billingAddress['billing_telephone'],
                'shipping_save_in_address_book' => 'yes');
        $products = $this->loadData('bundle_products_to_add');
        $products['product_1']['general_sku'] = $bundleData['general_sku'];
        $this->navigate('manage_sales_orders');
        $orderId = $this->OrderHelper()->createOrderForNewCustomer(false, 'Default Store View',
                $products, $billingAddress['email'],
                $billingAddress, $shippingAddress, 'visa', 'Fixed');
    }

    /**
     * <p>Creating order with downloadable products</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select downloadable product and add it to the order. Fill any required information to configure product;</p>
     * <p>4. Fill in all required information; Shipping methods and address should be disabled;</p>
     * <p>5. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @test
     */
    public function downloadablePrd()
    {
        $productData = $this->loadData('downloadable_product_for_order', null, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData, 'downloadable');
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        $billingAddress = $this->loadData('new_customer_order_billing_address_reqfields',
                array(
                    $this->OrderHelper()->customerAddressGenerator(
                    ':alnum:', $addrType = 'billing', $symNum = 32, TRUE),
                    'billing_save_in_address_book' => 'yes' ));
        $billingAddress['email'] = $this->generate('email', 32, 'valid');
        $products = $this->loadData('downloadable_products_to_add');
        $products['product_1']['general_sku'] = $productData['general_sku'];
        $this->navigate('manage_sales_orders');
        $orderId = $this->OrderHelper()->createOrderForNewCustomer(false, 'Default Store View',
                $products, $billingAddress['email'],
                $billingAddress, null, 'visa', null);
        $this->orderHelper()->coverUpTraces($orderId, array('email' => $billingAddress['email']));
    }
}
