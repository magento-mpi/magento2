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
class Core_Mage_Order_AuthorizeNetDP_NewCustomerWithSimpleSmokeTest extends Mage_Selenium_TestCase
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

        return $simple['general_sku'];
    }

    /**
     * <p>Place order via Authorize.net Direct Post</p>
     *
     * @param string $simpleSku
     *
     * @return array
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6097
     */
    public function orderAuthorizeNetDPSmoke($simpleSku)
    {
        $this->markTestIncomplete('MAGETWO-9104');
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
     *
     * @param array $orderData
     * @param string $card
     *
     * @test
     * @dataProvider differentCardInAuthorizeNetDPDataProvider
     * @depends orderAuthorizeNetDPSmoke
     * @TestlinkId TL-MAGE-6098
     */
    public function differentCardInAuthorizeNetDP($card, $orderData)
    {
        $this->markTestIncomplete('MAGETWO-2856');
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
     *
     * @param string $captureType
     * @param array $orderData
     *
     * @test
     * @dataProvider captureTypeDataProvider
     * @depends orderAuthorizeNetDPSmoke
     * @TestlinkId TL-MAGE-6099
     */
    public function fullInvoiceWithDifferentTypesOfCapture($captureType, $orderData)
    {
        $this->markTestIncomplete('MAGETWO-2856');
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
     *
     * @param string $captureType
     * @param string $refundType
     * @param array $orderData
     *
     * @test
     * @dataProvider refundDataProvider
     * @depends orderAuthorizeNetDPSmoke
     * @TestlinkId TL-MAGE-6100
     */
    public function fullRefund($captureType, $refundType, $orderData)
    {
        $this->markTestIncomplete('MAGETWO-2856');
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
     *
     * @param string $captureType
     * @param string $refundType
     * @param array $orderData
     *
     * @test
     * @dataProvider refundDataProvider
     * @depends orderAuthorizeNetDPSmoke
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6101
     */
    public function partialRefund($captureType, $refundType, $orderData)
    {
        $this->markTestIncomplete('MAGETWO-2856');
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
     *
     * @param array $orderData
     *
     * @test
     * @depends orderAuthorizeNetDPSmoke
     * @TestlinkId TL-MAGE-6102
     */
    public function fullShipmentForOrderWithoutInvoice($orderData)
    {
        $this->markTestIncomplete('MAGETWO-2856');
        //Steps and Verifying
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty();
    }

    /**
     * <p>Holding and unholding order after creation.</p>
     *
     * @param array $orderData
     *
     * @test
     * @depends orderAuthorizeNetDPSmoke
     * @TestlinkId TL-MAGE-6103
     */
    public function holdAndUnholdPendingOrderViaOrderPage($orderData)
    {
        $this->markTestIncomplete('MAGETWO-2856');
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
     *
     * @param array $orderData
     *
     * @test
     * @depends orderAuthorizeNetDPSmoke
     * @TestlinkId TL-MAGE-6104
     */
    public function cancelPendingOrderFromOrderPage($orderData)
    {
        $this->markTestIncomplete('MAGETWO-2856');
        //Steps and Verifying
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');
    }

    /**
     * <p>Reorder Authorize.net Direct Post.</p>
     *
     * @param array $orderData
     *
     * @test
     * @depends orderAuthorizeNetDPSmoke
     * @TestlinkId TL-MAGE-6105
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
     *
     * @param array $orderData
     *
     * @test
     * @depends orderAuthorizeNetDPSmoke
     * @TestlinkId TL-MAGE-6106
     */
    public function voidPendingOrderFromOrderPage($orderData)
    {
        $this->markTestIncomplete('MAGETWO-2856');
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