<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Order
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Create order with AuthorizeNet payment method
 *
 * @package     selenium
 * @subpackage  functional_tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_Order_ZeroSubtotal_NewCustomerWithSimpleSmokeTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_zerosubtotal_enable');
        $this->systemConfigurationHelper()->configure('PaymentMethod/zerosubtotal_enable');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('PaymentMethod/zerosubtotal_disable');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_zerosubtotal_disable');
    }

    /**
     * <p>Creating Simple product</p>
     *
     * @return string
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_zero_price');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        return $simple['general_name'];
    }

    /**
     * <p>Place order via ZeroSubtotal Checkout</p>
     * <p>Preconditions:</p>
     * <p>Payment method is enabled and product is created</p>
     * <p>Steps:</p>
     * <p>1. Go to admin</p>
     * <p>2. Sales > Orders > Created New Order</p>
     * <p>3. Click 'Create New Customer'</p>
     * <p>4. Add product ot 'Items Ordered'</p>
     * <p>5. Fill Billing Address with new customer data</p>
     * <p>6. 'No payment information required' text is present</p>
     * <p>7. Select Shipping method</p>
     * <p>8. Click 'Submit Order'</p>
     * <p>Expected result:</p>
     * <p>Order is successfully placed</p>
     *
     * @param string $simpleSku
     *
     * @return array
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6149
     */
    public function orderZeroSubtotalCheckoutSmoke($simpleSku)
    {
        //Data
        $paymentData = $this->loadDataSet('Payment', 'payment_zerosubtotal');
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku'  => $simpleSku,
                  'payment_data' => $paymentData));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        return $orderData;
    }

    /**
     * <p>Zero Subtotal Checkout with full invoice</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7. Add product with zero price.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Verify 'No payment method is required' text is present</p>
     * <p>10. Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>13.Create Invoice.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer. Invoice is created.</p>
     *
     * @param array $orderData
     *
     * @test
     * @depends orderZeroSubtotalCheckoutSmoke
     * @TestlinkId TL-MAGE-6150
     */
    public function fullInvoiceZeroSubtotalCheckoutSmoke($orderData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
    }

    /**
     * <p>Shipment for Zero Subtotal Checkout order</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
     * <p>5.Fill all required fields;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add product;</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Verify 'No payment information required' text is present;</p>
     * <p>10.Choose any from 'Get shipping methods and rates';</p>
     * <p>11. Submit order;</p>
     * <p>12. Ship order;</p>
     * <p>Expected result:</p>
     * <p>New customer successfully created. Order is created for the new customer;</p>
     * <p>Message "The order has been created." is displayed.</p>
     * <p>Order is shipped successfully</p>
     *
     * @param array $orderData
     *
     * @test
     * @depends orderZeroSubtotalCheckoutSmoke
     * @TestlinkId TL-MAGE-6151
     */
    public function fullShipmentZeroSubtotalWithoutInvoice($orderData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty();
    }

    /**
     * <p>Holding and unholding order after creation.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Hold order;</p>
     * <p>Expected result:</p>
     * <p>Order is hold;</p>
     * <p>4. Unhold order;</p>
     * <p>Expected result:</p>
     * <p>Order is unhold;</p>
     *
     * @param array $orderData
     *
     * @test
     * @depends orderZeroSubtotalCheckoutSmoke
     * @TestlinkId TL-MAGE-6152
     */
    public function holdAndUnholdPendingOrderViaOrderPage($orderData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->clickButton('hold');
        //Verifying
        $this->assertMessagePresent('success', 'success_hold_order');
        //Steps
        $this->clickButton('unhold');
        //Verifying
        $this->assertMessagePresent('success', 'success_unhold_order');
    }

    /**
     * <p>Cancel Pending Order From Order Page</p>
     * <p>1. Create order via Zero Subtotal Checkout</p>
     * <p>2. Create new customer during order placement</p>
     * <p>3. Open created order and click'Cancel'</p>
     * <p>Expected result:</p>
     * <p>Order is cancelled</p>
     *
     * @param array $orderData
     *
     * @test
     * @depends orderZeroSubtotalCheckoutSmoke
     * @TestlinkId TL-MAGE-6153
     */
    public function cancelPendingOrderFromOrderPage($orderData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        //Verifying
        $this->assertMessagePresent('success', 'success_canceled_order');
    }

    /**
     * <p>Reorder Zero Subtotal Checkout.</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
     * <p>5.Fill all required fields;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add products;</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Check Authorize.net Direct post payment method;</p>
     * <p>10.Choose any from 'Get shipping methods and rates';</p>
     * <p>11. Submit order;</p>
     * <p>12. Edit order (add products and change billing address);</p>
     * <p>13. Submit order;</p>
     * <p>Expected results:</p>
     * <p>New customer successfully created. Order is created for the new customer;</p>
     * <p>Message "The order has been created." is displayed.</p>
     * <p>New order during reorder is created.</p>
     * <p>Message "The order has been created." is displayed.</p>
     * <p>Bug MAGE-5802</p>
     *
     * @param array $orderData
     *
     * @test
     * @depends orderZeroSubtotalCheckoutSmoke
     * @TestlinkId TL-MAGE-6154
     * @group skip_due_to_bug
     */
    public function reorderPendingOrder($orderData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->clickButton('reorder');
        $data = $orderData['payment_data']['payment_info'];
        $this->orderHelper()->verifyIfCreditCardFieldsAreEmpty($data);
        $this->fillFieldSet($data, 'order_payment_method');
        $this->orderHelper()->submitOrder();
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Place order via ZeroSubtotal Checkout</p>
     * <p>Preconditions:</p>
     * <p>Payment method is enabled and product is created</p>
     * <p>Shipping is set to Zero</p>
     * <p>"New Order Status" set to "Processing"</p>
     * <p>"Automatic Invoice all Items" = "Yes"</p>
     * <p>Steps:</p>
     * <p>1. Go to admin</p>
     * <p>2. Sales > Orders > Created New Order</p>
     * <p>3. Click 'Create New Customer'</p>
     * <p>4. Add product ot 'Items Ordered'</p>
     * <p>5. Fill Billing Address with new customer data</p>
     * <p>6. 'No payment information required' text is present</p>
     * <p>7. Select Shipping method</p>
     * <p>8. Click 'Submit Order'</p>
     * <p>Expected result:</p>
     * <p>Order is successfully placed</p>
     *
     * @param string $simpleSku
     *
     * @return array
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6172
     */
    public function orderZeroSubtotalCheckoutCapture($simpleSku)
    {
        //Data
        $paymentData = $this->loadDataSet('Payment', 'payment_zerosubtotal');
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku, 'payment_data' => $paymentData));
        $paymentConfig = $this->loadDataSet('PaymentMethod', 'zerosubtotal_enable',
            array('zsc_new_order_status' => 'Processing', 'zsc_automatically_invoice_all_items' => 'Yes'));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($paymentConfig);
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        return $orderData;
    }
}