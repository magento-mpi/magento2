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
    {}
    /**
     * @test
     */
    public function createProducts()
    {
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $this->addParameter('id', '0');
        $productData = $this->loadData('simple_product_for_order', null, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
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
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
        $this->addParameter('tabName', 'edit/section/carriers/');
        $this->clickControl('tab', 'sales_shipping_methods', TRUE);
        $shipment = $this->loadData('flat_rate');
        $this->fillForm($shipment, 'sales_shipping_methods');
        $this->saveForm('save_config');
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $orderData = $this->loadData('order_req_1', array(
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
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
        $this->addParameter('tabName', 'edit/section/carriers/');
        $this->clickControl('tab', 'sales_shipping_methods', TRUE);
        $shipment = $this->loadData('free_shipping');
        $this->fillForm($shipment, 'sales_shipping_methods');
        $this->saveForm('save_config');
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $orderData = $this->loadData('order_ship_free', array(
                'customer_email' => $this->generate('email', 32, 'valid')));
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->addParameter('order_id', $orderId);
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
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
        $this->addParameter('tabName', 'edit/section/carriers/');
        $this->clickControl('tab', 'sales_shipping_methods', TRUE);
        $shipment = $this->loadData('usps');
        $this->fillForm($shipment, 'sales_shipping_methods');
        $this->saveForm('save_config');
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $orderData = $this->loadData('order_ship_usps', array(
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
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
        $this->addParameter('tabName', 'edit/section/carriers/');
        $this->clickControl('tab', 'sales_shipping_methods', TRUE);
        $shipment = $this->loadData('ups');
        $this->fillForm($shipment, 'sales_shipping_methods');
        $this->saveForm('save_config');
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $orderData = $this->loadData('order_ship_ups', array(
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
     * <p>Creating order with DHL shipment method</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select simple product and add it to the order;</p>
     * <p>4. Fill in all required information;</p>
     * <p>5. Choose DHL shipping method;</p>
     * <p>6. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @depends createProducts
     * @test
     */
    public function shippingMethodsDHL($productData)
    {
        $this->navigate('system_configuration');
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
        $this->addParameter('tabName', 'edit/section/carriers/');
        $this->clickControl('tab', 'sales_shipping_methods', TRUE);
        $shipment = $this->loadData('dhl');
        $this->fillForm($shipment, 'sales_shipping_methods');
        $this->saveForm('save_config');
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $orderData = $this->loadData('order_ship_dhl', array(
                'billing_zip_code' => '85258',
                'shipping_zip_code' => '85258',
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
     * <p>Creating order with FedEx shipment method</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Select simple product and add it to the order;</p>
     * <p>4. Fill in all required information;</p>
     * <p>5. Choose FedEx shipping method;</p>
     * <p>6. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created;</p>
     *
     * @depends createProducts
     * @test
     */
    public function shippingMethodsFedEx($productData)
    {
        $this->navigate('system_configuration');
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
        $this->addParameter('tabName', 'edit/section/carriers/');
        $this->clickControl('tab', 'sales_shipping_methods', TRUE);
        $shipment = $this->loadData('fedex');
        $this->fillForm($shipment, 'sales_shipping_methods');
        $this->saveForm('save_config');
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $orderData = $this->loadData('order_ship_ups', array(
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
}
