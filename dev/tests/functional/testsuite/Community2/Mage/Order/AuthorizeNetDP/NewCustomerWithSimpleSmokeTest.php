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
class Community2_Mage_Order_AuthorizeNetDP_NewCustomerWithSimpleSmokeTest extends Mage_Selenium_TestCase
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
        $this->systemConfigurationHelper()->configure('PaymentMethod/authorizenetdp_enable');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('PaymentMethod/authorizenetdp_disable');
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
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        return $simple['general_name'];
    }

    /**
     * <p>Place order via Authorize.net Direct Post</p>
     * <p>Preconditions:</p>
     * <p>Payment method is enabled and product is created</p>
     * <p>Steps:</p>
     * <p>1. Go to admin</p>
     * <p>2. Sales > Orders > Created New Order</p>
     * <p>3. Click 'Create New Customer'</p>
     * <p>4. Add product ot 'Items Ordered'</p>
     * <p>5. Fill Billing Address with new customer data</p>
     * <p>6. Select Authorize.net Direct Post and put card data</p>
     * <p>7. Select Shipping method</p>
     * <p>8. Click 'Submit Order'</p>
     * <p>Expected result:</p>
     * <p>Order is successfully placed</p>
     * <p>Bug MAGETWO-2856</p>
     *
     * @param string $simpleSku
     *
     * @return array
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6097
     * @group skip_due_to_bug
     */
    public function orderAuthorizeNetDPSmoke($simpleSku)
    {
        //Data
        $paymentData = $this->loadDataSet('Payment', 'payment_authorizenetdp');
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
     * <p>Create order with AuthorizeNet Direct Post using all types of credit cards</p>
     * <p>Preconditions:</p>
     * <p>Payment method is enabled and product is created</p>
     * <p>Steps:</p>
     * <p>1. Go to admin</p>
     * <p>2. Sales > Orders > Created New Order</p>
     * <p>3. Click 'Create New Customer'</p>
     * <p>4. Add product ot 'Items Ordered'</p>
     * <p>5. Fill Billing Address with new customer data</p>
     * <p>6. Select Authorize.net Direct Post and put card data</p>
     * <p>7. Select Shipping method</p>
     * <p>8. Click 'Submit Order'</p>
     * <p>Expected result:</p>
     * <p>Order is successfully placed</p>
     * <p>Bug MAGETWO-2856</p>
     *
     * @param array $orderData
     * @param string $card
     *
     * @test
     * @dataProvider differentCardInAuthorizeNetDPDataProvider
     * @depends orderAuthorizeNetDPSmoke
     * @TestlinkId TL-MAGE-6098
     * @group skip_due_to_bug
     */
    public function differentCardInAuthorizeNetDP($card, $orderData)
    {
        //Data
        $orderData['payment_data']['payment_info'] = $this->loadDataSet('Payment', $card);
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
    }

    public function differentCardInAuthorizeNetDPDataProvider()
    {
        return array(
            array('else_american_express'),
            array('else_visa'),
            array('else_mastercard'),
            array('else_discover'),
            array('else_other')
        );
    }

    /**
     * <p>Authorize.Net Direct Post Full Invoice With different types of Capture</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add first two products.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'AuthorizeNet - Visa'</p>
     * <p>10. Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>13.Create Invoice.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer. Invoice is created.</p>
     * <p>Bug MAGETWO-2856</p>
     *
     * @param string $captureType
     * @param array $orderData
     *
     * @test
     * @dataProvider captureTypeDataProvider
     * @depends orderAuthorizeNetDPSmoke
     * @TestlinkId TL-MAGE-6099
     * @group skip_due_to_bug
     */
    public function fullInvoiceWithDifferentTypesOfCapture($captureType, $orderData)
    {
        //Steps and Verifying
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty($captureType);
    }

    /**
     * <p>Data provider for fullInvoiceWithDifferentTypesOfCapture test</p>
     *
     * @return array
     */
    public function captureTypeDataProvider()
    {
        return array(
            array('Capture Online'),
            array('Capture Offline'),
            array('Not Capture')
        );
    }

    /**
     * <p>Authorize.Net Direct Post Full refund/p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add product.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'Credit Card - Visa'</p>
     * <p>10. Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>13.Invoice order.</p>
     * <p>14.Make refund online.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer. Credit memo is(isn't) created</p>
     * <p>Bug MAGETWO-2856</p>
     *
     * @param string $captureType
     * @param string $refundType
     * @param array $orderData
     *
     * @test
     * @dataProvider refundDataProvider
     * @depends orderAuthorizeNetDPSmoke
     * @TestlinkId TL-MAGE-6100
     * @group skip_due_to_bug
     */
    public function fullRefund($captureType, $refundType, $orderData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $orderId = $this->orderHelper()->defineOrderId();
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty($captureType);
        $this->navigate('manage_sales_invoices');
        $this->orderInvoiceHelper()->openInvoice(array('filter_order_id' => $orderId));
        $this->addParameter('invoice_id', $this->getParameter('id'));
        $this->clickButton('credit_memo');
        $this->clickButton($refundType);
        //Verifying
        if ($refundType != 'refund') {
            $this->assertMessagePresent('success', 'success_creating_creditmemo');
        } else {
            $this->assertMessagePresent('error', 'failed_authorizedp_online_refund');
        }
    }

    /**
     * <p>Partial refund Authorize.net Direct post</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add product.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'Credit Card - Visa'</p>
     * <p>10. Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>13.Invoice order.</p>
     * <p>14.Make partial refund online(update items quantity).</p>
     * <p>15.Execute partial refund.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer. Credit memo is(isn't) created</p>
     * <p>Bug MAGETWO-2856</p>
     *
     * @param string $captureType
     * @param string $refundType
     * @param array $orderData
     * @param string $sku
     *
     * @test
     * @dataProvider refundDataProvider
     * @depends orderAuthorizeNetDPSmoke
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6101
     * @group skip_due_to_bug
     */
    public function partialRefund($captureType, $refundType, $orderData, $sku)
    {
        //Data
        $orderData['products_to_add']['product_1']['product_qty'] = 10;
        $creditMemo = $this->loadDataSet('SalesOrder', 'products_to_refund');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $orderId = $this->orderHelper()->defineOrderId();
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty($captureType);
        $this->navigate('manage_sales_invoices');
        $this->orderInvoiceHelper()->openInvoice(array('filter_order_id' => $orderId));
        //Verifying
        if ($refundType != 'refund') {
            $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty($refundType, $creditMemo);
        } else {
            $this->addParameter('invoice_id', $this->getParameter('id'));
            $this->clickButton('credit_memo');
            $this->fillFieldSet($creditMemo['product_1'], 'items_to_refund');
            $this->clickButton('update_qty', false);
            $this->pleaseWait();
            $this->clickButton($refundType);
            $error = $this->errorMessage('failed_authorizedp_online_refund');
            if (!$error['success']) {
                $this->skipTestWithScreenshot(self::messagesToString($this->getMessagesOnPage()));
            }
        }
    }

    public function refundDataProvider()
    {
        return array(
            array('Capture Online', 'refund'),
            array('Capture Online', 'refund_offline'),
            array('Capture Offline', 'refund_offline')
        );
    }

    /**
     * <p>Shipment for Authorize.net Direct Post order</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
     * <p>5.Fill all required fields;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add product;</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Check payment method 'Authorize.net Direct Post';</p>
     * <p>10.Choose any from 'Get shipping methods and rates';</p>
     * <p>11. Submit order;</p>
     * <p>12. Ship order;</p>
     * <p>Expected result:</p>
     * <p>New customer successfully created. Order is created for the new customer;</p>
     * <p>Message "The order has been created." is displayed.</p>
     * <p>Order is invoiced and shipped successfully</p>
     * <p>Bug MAGETWO-2856</p>
     *
     * @param array $orderData
     *
     * @test
     * @depends orderAuthorizeNetDPSmoke
     * @TestlinkId TL-MAGE-6102
     * @group skip_due_to_bug
     */
    public function fullShipmentForOrderWithoutInvoice($orderData)
    {
        //Steps and Verifying
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
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
     * <p>Bug MAGETWO-2856</p>
     *
     * @param array $orderData
     *
     * @test
     * @depends orderAuthorizeNetDPSmoke
     * @TestlinkId TL-MAGE-6103
     * @group skip_due_to_bug
     */
    public function holdAndUnholdPendingOrderViaOrderPage($orderData)
    {
        //Steps and Verifying
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButton('hold');
        $this->assertMessagePresent('success', 'success_hold_order');
        $this->clickButton('unhold');
        $this->assertMessagePresent('success', 'success_unhold_order');
    }

    /**
     * <p>Cancel Pending Order From Order Page</p>
     * <p>1. Create order via Authorize.net Direct Post</p>
     * <p>2. Create new customer during order placement</p>
     * <p>3. Open created order and click'Cancel'</p>
     * <p>Expected result:</p>
     * <p>Order is cancelled</p>
     * <p>Bug MAGETWO-2856</p>
     *
     * @param array $orderData
     *
     * @test
     * @depends orderAuthorizeNetDPSmoke
     * @TestlinkId TL-MAGE-6104
     * @group skip_due_to_bug
     */
    public function cancelPendingOrderFromOrderPage($orderData)
    {
        //Steps and Verifying
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');
    }

    /**
     * <p>Reorder Authorize.net Direct Post.</p>
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
     * <p>Bug MAGETWO-2856</p>
     *
     * @param array $orderData
     *
     * @test
     * @depends orderAuthorizeNetDPSmoke
     * @TestlinkId TL-MAGE-6105
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
     * <p>Void order Authorize.net Direct Post.</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders.</p>
     * <p>2.Press "Create New Order" button.</p>
     * <p>3.Press "Create New Customer" button.</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists.</p>
     * <p>5.Fill all fields.</p>
     * <p>6.Press 'Add Products' button.</p>
     * <p>7.Add product.</p>
     * <p>8.Choose shipping address the same as billing.</p>
     * <p>9.Check payment method 'Authorize.net Direct Post - Visa'</p>
     * <p>10. Fill in all required fields.</p>
     * <p>11.Choose first from 'Get shipping methods and rates'.</p>
     * <p>12.Submit order.</p>
     * <p>13.Void Order.</p>
     * <p>Expected result:</p>
     * <p>New customer is created. Order is created for the new customer. Void successful</p>
     * <p>Bug MAGETWO-2856</p>
     *
     * @param array $orderData
     *
     * @test
     * @depends orderAuthorizeNetDPSmoke
     * @TestlinkId TL-MAGE-6106
     * @group skip_due_to_bug
     */
    public function voidPendingOrderFromOrderPage($orderData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->clickButtonAndConfirm('void', 'confirmation_to_void');
        //Verifying
        $this->assertMessagePresent('success', 'success_voided_order');
    }
}