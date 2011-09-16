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
 * Creating order for new customer
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class NewCustomerComplete_Test extends Mage_Selenium_TestCase
{
   /**
    * <p>Preconditions:</p>
    * <p>Log in to Backend.</p>
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
    }
    /**
     * @test
     */
    public function createProducts()
    {
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $this->addParameter('id', '0');
        $productData = $this->loadData('simple_product_for_order', NULL, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        return $productData;
    }

    /**
     * <p>Create customer via 'Create order' form (all fields are filled with special chars).</p>
     * <p>Create order(all fields are filled).</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
     * <p>5.Fill all fields with special characters;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add first two products;</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Check payment method 'Check / Money order';</p>
     * <p>10.Choose first from 'Get shipping methods and rates';</p>
     * <p>11.Submit order;</p>
     * <p>12.Invoice order;</p>
     * <p>13. Ship order;</p>
     * <p>Expected result:</p>
     * <p>New customer successfully created. Order is created for the new customer, invoiced and shipped.</p>
     *
     * @depends createProducts
     * @test
     */
    public function orderCompleteSpecialCharacters($productData)
    {
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $billingAddress = $this->orderHelper()->customerAddressGenerator(
                ':punct:', $addrType = 'billing', $symNum = 32, TRUE);
        $shippingAddress = $this->orderHelper()->customerAddressGenerator(
                ':punct:', $addrType = 'shipping', $symNum = 32, TRUE);
        $shippingAddress['shipping_same_as_billing_address'] = 'no';
        $shippingAddress['shipping_state'] = 'California';
        $billingAddress['billing_state'] = 'California';
        $orderData = $this->loadData('order_req_1', array(
                'billing_addr_data' => $billingAddress,
                'shipping_addr_data' => $shippingAddress,
                'customer_email' => $this->generate('email', 32, 'valid')));
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->addParameter('order_id', $orderId);
        $this->clickButton('invoice');
        $this->clickButton('submit_invoice');
        $this->assertTrue($this->successMessage('success_creating_invoice'), $this->messages);
        $this->clickButton('ship');
        $this->clickButton('submit_shipment');
        $this->assertTrue($this->successMessage('success_creating_shipment'), $this->messages);
    }
    /**
     * <p>Create customer via 'Create order' form (all fields are filled).</p>
     * <p>Create order(all fields are filled).</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
     * <p>5.Fill all required fields;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add first two products;</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Check payment method 'Check / Money order';</p>
     * <p>10.Choose first from 'Get shipping methods and rates';</p>
     * <p>11. Submit order;</p>
     * <p>Expected result:</p>
     * <p>New customer successfully created. Order is created for the new customer;</p>
     * <p>Message "The order has been created." is displayed.</p>
     *
     * @depends createProducts
     * @test
     */
    public function orderCompleteAllFields($productData)
    {
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $billingAddress = $this->orderHelper()->customerAddressGenerator(
                ':alpha:', $addrType = 'billing', $symNum = 32, FALSE);
        $shippingAddress = $this->orderHelper()->customerAddressGenerator(
                ':alpha:', $addrType = 'shipping', $symNum = 32, FALSE);
        $shippingAddress['shipping_same_as_billing_address'] = 'no';
        $shippingAddress['shipping_state'] = 'California';
        $billingAddress['billing_state'] = 'California';
        $orderData = $this->loadData('order_req_1', array(
                'billing_addr_data' => $billingAddress,
                'shipping_addr_data' => $shippingAddress,
                'customer_email' => $this->generate('email', 32, 'valid')));
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->addParameter('order_id', $orderId);
        $this->clickButton('invoice');
        $this->clickButton('submit_invoice');
        $this->assertTrue($this->successMessage('success_creating_invoice'), $this->messages);
        $this->clickButton('ship');
        $this->clickButton('submit_shipment');
        $this->assertTrue($this->successMessage('success_creating_shipment'), $this->messages);
    }
    /**
     * <p>Create customer via 'Create order' form (required fields are filled).</p>
     * <p>Create order(required fields are filled).</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons);</p>
     * <p>5.Fill all required fields;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add first two products (select third options for second product);</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Check payment method 'Check / Money order';</p>
     * <p>10.Choose first from 'Get shipping methods and rates';</p>
     * <p>11. Submit order;</p>
     * <p>Expected result:</p>
     * <p>New customer successfully created. Order is created for the new customer;</p>
     * <p>Message "The order has been created." is displayed.</p>
     *
     * @depends createProducts
     * @test
     */
    public function orderCompleteReqFields($productData)
    {
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $billingAddress = $this->orderHelper()->customerAddressGenerator(
                ':alpha:', $addrType = 'billing', $symNum = 32, TRUE);
        $shippingAddress = $this->orderHelper()->customerAddressGenerator(
                ':alpha:', $addrType = 'shipping', $symNum = 32, TRUE);
        $shippingAddress['shipping_same_as_billing_address'] = 'no';
        $shippingAddress['shipping_state'] = 'California';
        $billingAddress['billing_state'] = 'California';
        $orderData = $this->loadData('order_req_1', array(
                'billing_addr_data' => $billingAddress,
                'shipping_addr_data' => $shippingAddress,
                'customer_email' => $this->generate('email', 32, 'valid')));
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->addParameter('order_id', $orderId);
        $this->clickButton('invoice');
        $this->clickButton('submit_invoice');
        $this->assertTrue($this->successMessage('success_creating_invoice'), $this->messages);
        $this->clickButton('ship');
        $this->clickButton('submit_shipment');
        $this->assertTrue($this->successMessage('success_creating_shipment'), $this->messages);
    }
}
