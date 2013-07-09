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
 * Create orders using Maestro/Switch/Solo credit cards for PayPal Direct
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Order_PayPalDirect_Authorization_MaestroSoloCreditCardsTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Currency/enable_usd');
        $this->paypalHelper()->paypalDeveloperLogin();
        $this->paypalHelper()->deleteAllAccounts();
    }

    /**
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Steps
        $this->paypalHelper()->paypalDeveloperLogin();
        $accountInfo = $this->paypalHelper()->createPreconfiguredAccount('paypal_sandbox_new_pro_account_gbp');
        $api = $this->paypalHelper()->getApiCredentials($accountInfo['email']);
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        $settings = $this->loadDataSet('PaymentMethod', 'paypaldirect_with_3Dsecure', $api);
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->systemConfigurationHelper()->useHttps('admin', 'yes');
        $this->systemConfigurationHelper()->configurePaypal($settings);
        $this->systemConfigurationHelper()->configure('Currency/enable_gbp');
        $this->systemConfigurationHelper()->configure('PaymentMethod/enable_3d_secure');

        return array('api' => $api,
                     'sku' => $productData['general_sku']);
    }

    /**
     * <p>Create Orders using Switch/Maestro card</p>
     *
     * @param array $testData
     *
     * @return array
     * @test
     * @depends preconditionsForTests
     */
    public function orderWithSwitchMaestroCard($testData)
    {
        //Data
        $paymentInfo = $this->loadDataSet('Payment', 'else_switch_maestro');
        $paymentData = $this->loadDataSet('Payment', 'payment_paypaldirect', array('payment_info' => $paymentInfo));
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
                                        array('filter_sku'  => $testData['sku'],
                                             'payment_data' => $paymentData));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');

        return $orderData;
    }

    /**
     * <p>Website payments pro. Full Invoice With different types of Capture</p>
     *
     * @param string $captureType
     * @param array $orderData
     *
     * @test
     * @dataProvider typesOfCaptureDataProvider
     * @depends orderWithSwitchMaestroCard
     * @TestlinkId TL-MAGE-5373
     */
    public function fullInvoiceWithDifferentTypesOfCapture($captureType, $orderData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty($captureType);
    }

    public function typesOfCaptureDataProvider()
    {
        return array(
            array('Capture Online'),
            array('Capture Offline'),
            array('Not Capture')
        );
    }

    /**
     * <p>Partial invoice with different types of capture</p>
     *
     * @param string $captureType
     * @param array $orderData
     * @param array $testData
     *
     * @test
     * @dataProvider typesOfCaptureDataProvider
     * @depends orderWithSwitchMaestroCard
     * @depends preconditionsForTests
     */
    public function partialInvoiceWithDifferentTypesOfCapture($captureType, $orderData, $testData)
    {
        //Data
        $orderData['products_to_add']['product_1']['product_qty'] = 10;
        $invoice = $this->loadDataSet('SalesOrder', 'products_to_invoice',
                                      array('invoice_product_sku' => $testData['sku']));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty($captureType, $invoice);
    }

    /**
     * <p>PayPal Direct. Full Refund</p>
     *
     * @param string $captureType
     * @param string $refundType
     * @param array $orderData
     *
     * @test
     * @dataProvider creditMemoDataProvider
     * @depends orderWithSwitchMaestroCard
     * @TestlinkId TL-MAGE-5374
     */
    public function fullCreditMemo($captureType, $refundType, $orderData)
    {
        //Steps and Verifying
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $orderId = $this->orderHelper()->defineOrderId();
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty($captureType);
        $this->navigate('manage_sales_invoices');
        $this->orderInvoiceHelper()->openInvoice(array('filter_order_id' => $orderId));
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty($refundType);
    }

    /**
     * <p>Partial Credit Memo</p>
     *
     * @param string $captureType
     * @param string $refundType
     * @param array $orderData
     * @param array $testData
     *
     * @test
     * @dataProvider creditMemoDataProvider
     * @depends orderWithSwitchMaestroCard
     * @depends preconditionsForTests
     */
    public function partialCreditMemo($captureType, $refundType, $orderData, $testData)
    {
        //Data
        $orderData['products_to_add']['product_1']['product_qty'] = 10;
        $creditMemo = $this->loadDataSet('SalesOrder', 'products_to_refund',
                                         array('return_filter_sku' => $testData['sku']));
        //Steps and Verifying
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $orderId = $this->orderHelper()->defineOrderId();
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty($captureType);
        $this->navigate('manage_sales_invoices');
        $this->orderInvoiceHelper()->openInvoice(array('filter_order_id' => $orderId));
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty($refundType, $creditMemo);
    }

    public function creditMemoDataProvider()
    {
        return array(
            array('Capture Online', 'refund'),
            array('Capture Online', 'refund_offline'),
            array('Capture Offline', 'refund_offline')
        );
    }

    /**
     * <p>Shipment for order</p>
     *
     * @param array $orderData
     *
     * @test
     * @depends orderWithSwitchMaestroCard
     * @TestlinkId TL-MAGE-5375
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
     *
     * @param array $orderData
     *
     * @test
     * @depends orderWithSwitchMaestroCard
     * @TestlinkId TL-MAGE-5377
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
     *
     * @param array $orderData
     *
     * @test
     * @depends orderWithSwitchMaestroCard
     */
    public function cancelPendingOrderFromOrderPage($orderData)
    {
        //Steps and Verifying
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->paypalHelper()->verifyMagentoPayPalErrors();
        $this->assertMessagePresent('success', 'success_canceled_order');
    }

    /**
     * <p>Reorder.</p>
     *
     * @param array $orderData
     *
     * @test
     * @depends orderWithSwitchMaestroCard
     * @TestlinkId TL-MAGE-5378
     */
    public function reorderPendingOrder($orderData)
    {
        //Data
        $cardData = $orderData['payment_data']['payment_info'];
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->clickButton('reorder');
        $this->orderHelper()->verifyIfCreditCardFieldsAreEmpty($cardData);
        $this->fillForm($cardData);
        $this->orderHelper()->validate3dSecure();
        $this->orderHelper()->submitOrder();
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Void order.</p>
     *
     * @param array $orderData
     *
     * @test
     * @depends orderWithSwitchMaestroCard
     * @TestlinkId TL-MAGE-5379
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
        $this->paypalHelper()->verifyMagentoPayPalErrors();
        $this->assertMessagePresent('success', 'success_voided_order');
    }
}