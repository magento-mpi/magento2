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
 * Creating Order with specific shipment
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Order_Create_ShippingMethodsTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     *
     * <p>Log in to Backend.</p>
     * <p>Navigate to 'System Configuration' page</p>
     * <p>Enable all shipping methods</p>
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
     * @test
     */
    public function createProducts()
    {
        $productData = $this->loadData('simple_product_for_order', null, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        return $productData;
    }

    /**
     * <p>Creating order with Flat Rate shipment method</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select simple product and add it to the order;</p>
     * <p>4. Fill in all required information;</p>
     * <p>5. Choose Flat Rate (Fixed) for shipping method;</p>
     * <p>6. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @depends createProducts
     * @test
     */
    public function shippingMethodsFlatRate($productData)
    {
        $this->navigate('system_configuration');
        $this->addParameter('tabName', 'edit/section/carriers/');
        $this->clickControl('tab', 'sales_shipping_methods', TRUE);
        $shipment = $this->loadData('flat_rate');
        $this->fillForm($shipment, 'sales_shipping_methods');
        $this->saveForm('save_config');
        $this->navigate('manage_sales_orders');
        $billingAddress = $this->orderHelper()->customerAddressGenerator(
                ':alpha:', $addrType = 'billing', $symNum = 32, TRUE);
        $shippingAddress = $this->orderHelper()->customerAddressGenerator(
                ':alpha:', $addrType = 'shipping', $symNum = 32, TRUE);
        $shippingAddress['shipping_same_as_billing_address'] = 'no';
        $orderData = $this->loadData('order_req_1', array(
                'billing_addr_data' => $billingAddress,
                'shipping_addr_data' => $shippingAddress,
                'customer_email' => $this->generate('email', 32, 'valid')));
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->addParameter('order_id', $orderId);
        $this->addParameter('id', $this->defineIdFromUrl());
        $this->clickButton('invoice', TRUE);
        $this->clickButton('submit_invoice', TRUE);
        $this->assertTrue($this->successMessage('success_creating_invoice'), $this->messages);
        $this->clickButton('ship', TRUE);
        $this->clickButton('submit_shipment', TRUE);
        $this->assertTrue($this->successMessage('success_creating_shipment'), $this->messages);
    }

    /**
     * <p>Creating order with Free shipment method</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select simple product and add it to the order;</p>
     * <p>4. Fill in all required information;</p>
     * <p>5. Choose Free shipping method;</p>
     * <p>6. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @depends createProducts
     * @test
     */
    public function shippingMethodsFreeShipping($productData)
    {
        $this->navigate('system_configuration');
        $this->addParameter('tabName', 'edit/section/carriers/');
        $this->clickControl('tab', 'sales_shipping_methods', TRUE);
        $shipment = $this->loadData('free_shipping');
        $this->fillForm($shipment, 'sales_shipping_methods');
        $this->saveForm('save_config');
        $this->navigate('manage_sales_orders');
        $billingAddress = $this->orderHelper()->customerAddressGenerator(
                ':alpha:', $addrType = 'billing', $symNum = 32, TRUE);
        $shippingAddress = $this->orderHelper()->customerAddressGenerator(
                ':alpha:', $addrType = 'shipping', $symNum = 32, TRUE);
        $shippingAddress['shipping_same_as_billing_address'] = 'no';
        $orderData = $this->loadData('order_ship_free', array(
                'billing_addr_data' => $billingAddress,
                'shipping_addr_data' => $shippingAddress,
                'customer_email' => $this->generate('email', 32, 'valid')));
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->addParameter('order_id', $orderId);
        $this->addParameter('id', $this->defineIdFromUrl());
        $this->clickButton('invoice', TRUE);
        $this->clickButton('submit_invoice', TRUE);
        $this->assertTrue($this->successMessage('success_creating_invoice'), $this->messages);
        $this->clickButton('ship', TRUE);
        $this->clickButton('submit_shipment', TRUE);
        $this->assertTrue($this->successMessage('success_creating_shipment'), $this->messages);
    }

    /**
     * <p>Creating order with USPS shipment method</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select simple product and add it to the order;</p>
     * <p>4. Fill in all required information;</p>
     * <p>5. Choose USPS shipping method;</p>
     * <p>6. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @depends createProducts
     * @test
     */
    public function shippingMethodsUSPS($productData)
    {
        $this->navigate('system_configuration');
        $this->addParameter('tabName', 'edit/section/carriers/');
        $this->clickControl('tab', 'sales_shipping_methods', TRUE);
        $shipment = $this->loadData('usps');
        $this->fillForm($shipment, 'sales_shipping_methods');
        $this->saveForm('save_config');
        $this->navigate('manage_sales_orders');
        $orderData = $this->loadData('order_ship_usps', array(
                'shipping_same_as_billing_address' => 'no',
                'customer_email' => $this->generate('email', 32, 'valid')));
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->addParameter('order_id', $orderId);
        $this->addParameter('id', $this->defineIdFromUrl());
        $this->clickButton('invoice', TRUE);
        $this->clickButton('submit_invoice', TRUE);
        $this->assertTrue($this->successMessage('success_creating_invoice'), $this->messages);
        $this->clickButton('ship', TRUE);
        $this->clickButton('submit_shipment', TRUE);
        $this->assertTrue($this->successMessage('success_creating_shipment'), $this->messages);
    }

    /**
     * <p>Creating order with UPS (xml is set in system configuration) shipment method</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select simple product and add it to the order;</p>
     * <p>4. Fill in all required information;</p>
     * <p>5. Choose UPS shipping method;</p>
     * <p>6. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @depends createProducts
     * @test
     */
    public function shippingMethodsUPSXML($productData)
    {
        $this->navigate('system_configuration');
        $this->addParameter('tabName', 'edit/section/carriers/');
        $this->clickControl('tab', 'sales_shipping_methods', TRUE);
        $shipment = $this->loadData('ups');
        $this->fillForm($shipment, 'sales_shipping_methods');
        $this->saveForm('save_config');
        $this->navigate('manage_sales_orders');
        $orderData = $this->loadData('order_ship_ups', array(
                'shipping_same_as_billing_address' => 'no',
                'customer_email' => $this->generate('email', 32, 'valid')));
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->addParameter('order_id', $orderId);
        $this->addParameter('id', $this->defineIdFromUrl());
        $this->clickButton('invoice', TRUE);
        $this->clickButton('submit_invoice', TRUE);
        $this->assertTrue($this->successMessage('success_creating_invoice'), $this->messages);
        $this->clickButton('ship', TRUE);
        $this->clickButton('submit_shipment', TRUE);
        $this->assertTrue($this->successMessage('success_creating_shipment'), $this->messages);
    }

//    /**
//     * <p>Creating order with FedEx shipment method</p>
//     * <p>Steps:</p>
//     * <p>1. Navigate to "Manage Orders" page;</p>
//     * <p>2. Create new order for new customer;</p>
//     * <p>3. Select simple product and add it to the order;</p>
//     * <p>4. Fill in all required information;</p>
//     * <p>5. Choose FedEx shipping method;</p>
//     * <p>6. Click "Submit Order" button;</p>
//     * <p>Expected result:</p>
//     * <p>Order is created;</p>
//     *
//     * @depends createProducts
//     * @test
//     */
//    public function shippingMethodsFedEx($productData)
//    {
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/carriers/');
//        $this->clickControl('tab', 'sales_shipping_methods', TRUE);
//        $shipment = $this->loadData('fedex');
//        $this->fillForm($shipment, 'sales_shipping_methods');
//        $this->saveForm('save_config');
//        //rework parameter
//        $this->navigate('manage_sales_orders');
//        $orderData = $this->loadData('order_ship_ups', array(
//                'shipping_same_as_billing_address' => 'no',
//                'customer_email' => $this->generate('email', 32, 'valid')));
//        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
//        $orderId = $this->orderHelper()->createOrder($orderData);
//        $this->addParameter('order_id', $orderId);
//        $this->addParameter('id', $this->defineIdFromUrl());
//        $this->clickButton('invoice', TRUE);
//        $this->clickButton('submit_invoice', TRUE);
//        $this->assertTrue($this->successMessage('success_creating_invoice'), $this->messages);
//        $this->clickButton('ship', TRUE);
//        $this->clickButton('submit_shipment', TRUE);
//        $this->assertTrue($this->successMessage('success_creating_shipment'), $this->messages);
//    }
//
//    /**
//     * <p>Creating order with DHL shipment method</p>
//     * <p>Steps:</p>
//     * <p>1. Navigate to "Manage Orders" page;</p>
//     * <p>2. Create new order for new customer;</p>
//     * <p>3. Select simple product and add it to the order;</p>
//     * <p>4. Fill in all required information;</p>
//     * <p>5. Choose DHL shipping method;</p>
//     * <p>6. Click "Submit Order" button;</p>
//     * <p>Expected result:</p>
//     * <p>Order is created;</p>
//     *
//     * @depends createProducts
//     * @test
//     */
//    public function shippingMethodsDHL($productData)
//    {
//        $this->navigate('system_configuration');
//        $this->addParameter('tabName', 'edit/section/carriers/');
//        $this->clickControl('tab', 'sales_shipping_methods', TRUE);
//        $shipment = $this->loadData('dhl');
//        $this->fillForm($shipment, 'sales_shipping_methods');
//        $this->saveForm('save_config');
//        //rework parameter
//        $this->navigate('manage_sales_orders');
//        $orderData = $this->loadData('order_ship_dhl', array(
//                'shipping_same_as_billing_address' => 'no',
//                'customer_email' => $this->generate('email', 32, 'valid')));
//        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
//        $orderId = $this->orderHelper()->createOrder($orderData);
//        $this->addParameter('order_id', $orderId);
//        $this->addParameter('id', $this->defineIdFromUrl());
//        $this->clickButton('invoice', TRUE);
//        $this->clickButton('submit_invoice', TRUE);
//        $this->assertTrue($this->successMessage('success_creating_invoice'), $this->messages);
//        $this->clickButton('ship', TRUE);
//        $this->clickButton('submit_shipment', TRUE);
//        $this->assertTrue($this->successMessage('success_creating_shipment'), $this->messages);
//    }
}
