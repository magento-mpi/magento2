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
 * Create order on the backend using PayflowProVerisign
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Order_PayFlowProVerisign_Authorization_NewCustomerWithSimpleTest extends Mage_Selenium_TestCase
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
        $this->systemConfigurationHelper()->configurePaypal('PaymentMethod/payflowpro_without_3Dsecure');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Create Simple Product for tests</p>
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
     * <p>Smoke test for order without 3D secure</p>
     *
     * @param string $simpleSku
     *
     * @return array
     * @test
     * @depends preconditionsForTests
     */
    public function orderWithout3DSecureSmoke($simpleSku)
    {
        //Data
        $paymentData = $this->loadDataSet('Payment', 'payment_payflowpro');
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku, 'payment_data' => $paymentData));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');

        return $orderData;
    }

    /**
     * <p>Create order with PayFlowPro Verisign using all types of credit card</p>
     *
     * @param string $card
     * @param array $orderData
     *
     * @test
     * @dataProvider cardPayFlowProVerisignDataProvider
     * @depends orderWithout3DSecureSmoke
     */
    public function orderWithDifferentCreditCard($card, $orderData)
    {
        //Data
        $orderData['payment_data']['payment_info'] = $this->loadDataSet('Payment', $card);
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
    }

    public function cardPayFlowProVerisignDataProvider()
    {
        return array(
            array('else_american_express'),
            array('else_visa'),
            array('payflowpro_mastercard'),
            array('else_discover'),
            array('else_jcb')
        );
    }

    /**
     * <p>Verisign. Full Invoice With different types of Capture</p>
     *
     * @param string $captureType
     * @param array $orderData
     *
     * @test
     * @dataProvider captureTypeDataProvider
     * @depends orderWithout3DSecureSmoke
     * @TestlinkId TL-MAGE-3289
     */
    public function fullInvoiceWithDifferentTypesOfCapture($captureType, $orderData)
    {
        //Steps and Verifying
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty($captureType);
    }

    public function captureTypeDataProvider()
    {
        return array(
            array('Capture Online'),
            array('Capture Offline'),
            array('Not Capture')
        );
    }

    /**
     * <p>Verisign. Refund</p>
     *
     * @param string $captureType
     * @param string $refundType
     * @param array $orderData
     *
     * @test
     * @dataProvider creditMemoDataProvider
     * @depends orderWithout3DSecureSmoke
     * @TestlinkId TL-MAGE-3288
     */
    public function fullCreditMemo($captureType, $refundType, $orderData)
    {
        if ($captureType != 'Capture Offline') {
            $this->markTestIncomplete('BUG: Fatal error on page');
        }
        //Steps and Verifying
        $this->navigate('manage_sales_orders');
        $orderId = $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->addParameter('elementTitle', '#' . $orderId);
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty($captureType);
        $this->navigate('manage_sales_invoices');
        $this->orderInvoiceHelper()->openInvoice(array('filter_order_id' => $orderId));
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty($refundType);
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
     * @depends orderWithout3DSecureSmoke
     * @TestlinkId TL-MAGE-3290
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
     * @depends orderWithout3DSecureSmoke
     * @TestlinkId TL-MAGE-3291
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
     * Cancel Pending Order From Order Page
     *
     * @param array $orderData
     *
     * @test
     * @depends orderWithout3DSecureSmoke
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
     * @depends orderWithout3DSecureSmoke
     * @TestlinkId TL-MAGE-3292
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
        $this->fillForm($data);
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
     * @depends orderWithout3DSecureSmoke
     * @TestlinkId TL-MAGE-3293
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

    /**
     * <p>Create Orders using different payment methods with 3DSecure</p>
     *
     * @param string $card
     * @param bool $needSetUp
     * @param array $orderData
     *
     * @test
     * @dataProvider createOrderWith3DSecureDataProvider
     * @depends orderWithout3DSecureSmoke
     * @TestlinkId TL-MAGE-3287
     */
    public function createOrderWith3DSecure($card, $needSetUp, $orderData)
    {
        //Data
        $orderData['payment_data']['payment_info'] = $this->loadDataSet('Payment', $card);
        //Steps
        if ($needSetUp) {
            $this->systemConfigurationHelper()->useHttps('admin', 'yes');
            $this->systemConfigurationHelper()->configurePaypal('PaymentMethod/payflowpro_with_3Dsecure');
            $this->systemConfigurationHelper()->configure('PaymentMethod/enable_3d_secure');
        }
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
    }

    public function createOrderWith3DSecureDataProvider()
    {
        return array(
            array('3dsecure_jcb', true),
            array('payflowpro_mastercard', false),
            array('else_visa', false)
        );
    }
}