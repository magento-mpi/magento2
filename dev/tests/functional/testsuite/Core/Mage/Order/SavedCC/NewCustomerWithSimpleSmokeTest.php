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
 * Test admin order workflow with SavedCC payment method
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Order_SavedCC_NewCustomerWithSimpleSmokeTest extends Mage_Selenium_TestCase
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
        $this->systemConfigurationHelper()->configure('PaymentMethod/savedcc_without_3Dsecure');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
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
        $this->markTestIncomplete('MAGETWO-9242');
        //Data
        $paymentData = $this->loadDataSet('Payment', 'payment_savedcc');
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
     * <p>Create order with Saved CC using all types of credit card</p>
     *
     * @param string $card
     * @param array $orderData
     *
     * @test
     * @dataProvider cardSavedCCDataProvider
     * @depends orderWithout3DSecureSmoke
     */
    public function differentCardInSavedCC($card, $orderData)
    {
        //Data
        $orderData['payment_data']['payment_info'] = $this->loadDataSet('Payment', $card);
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
    }

    public function cardSavedCCDataProvider()
    {
        return array(
            array('saved_american_express'),
            array('saved_visa'),
            array('saved_mastercard'),
            array('saved_jcb'),
            array('saved_discover'),
            array('saved_solo'),
            array('saved_other')
        );
    }

    /**
     * <p>Invoice for full order</p>
     *
     * @param array $orderData
     *
     * @test
     * @depends orderWithout3DSecureSmoke
     * @TestlinkId TL-MAGE-3573
     */
    public function fullInvoiceWithSavedCC($orderData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
    }

    /**
     * <p>Invoice for part of order</p>
     *
     * @param array $orderData
     * @param string $sku
     *
     * @test
     * @depends orderWithout3DSecureSmoke
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3577
     */
    public function partialInvoiceWithCreditCard($orderData, $sku)
    {
        //Data
        $orderData['products_to_add']['product_1']['product_qty'] = 10;
        $invoice = $this->loadDataSet('SalesOrder', 'products_to_refund', array('return_filter_sku' => $sku));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty(null, $invoice);
    }

    /**
     * <p>Credit Memo for whole invoice<p>
     *
     * @param array $orderData
     *
     * @test
     * @depends orderWithout3DSecureSmoke
     * @TestlinkId TL-MAGE-3576
     */
    public function fullCreditMemoWithCreditCard($orderData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
    }

    /**
     * <p>Credit Memo for part of invoice</p>
     *
     * @param array $orderData
     * @param string $sku
     *
     * @test
     * @depends orderWithout3DSecureSmoke
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3576
     */
    public function partialCreditMemoWithCreditCard($orderData, $sku)
    {
        //Data
        $orderData['products_to_add']['product_1']['product_qty'] = 10;
        $creditMemo = $this->loadDataSet('SalesOrder', 'products_to_refund', array('return_filter_sku' => $sku));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline', $creditMemo);
    }

    /**
     * <p>Shipment for order</p>
     *
     * @param array $orderData
     *
     * @test
     * @depends orderWithout3DSecureSmoke
     * @TestlinkId TL-MAGE-3574
     */
    public function fullShipmentForOrderWithoutInvoice($orderData)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty();
    }

    /**
     * <p>Shipment for part of order</p>
     *
     * @param array $orderData
     * @param string $sku
     *
     * @test
     * @depends orderWithout3DSecureSmoke
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3578
     */
    public function partialShipmentForOrderWithoutInvoice($orderData, $sku)
    {
        //Data
        $orderData['products_to_add']['product_1']['product_qty'] = 10;
        $shipment = $this->loadDataSet('SalesOrder', 'products_to_ship', array('ship_product_sku' => $sku));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty($shipment);
    }

    /**
     * <p>Holding and unholding order after creation.</p>
     *
     * @param array $orderData
     *
     * @test
     * @depends orderWithout3DSecureSmoke
     * @TestlinkId TL-MAGE-3575
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
     * @depends orderWithout3DSecureSmoke
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
     * <p>Reorder.</p>
     *
     * @param array $orderData
     *
     * @test
     * @depends orderWithout3DSecureSmoke
     * @TestlinkId TL-MAGE-3579
     */
    public function reorderPendingOrder($orderData)
    {
        $this->markTestIncomplete('There is a problem with CVV field, but SavedCC payment method '
            . 'is going to be removed, so no need to fix it for now. See MAGETWO-2705 for details.');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->clickButton('reorder');
        $this->orderHelper()->submitOrder();
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
    }

    /**
     * <p>Create Orders using Saved CC payment method with 3DSecure</p>
     *
     * @param string $card
     * @param bool $needSetUp
     * @param array $orderData
     *
     * @test
     * @dataProvider createOrderWith3DSecureDataProvider
     * @depends orderWithout3DSecureSmoke
     * @TestlinkId TL-MAGE-3572
     */
    public function createOrderWith3DSecure($card, $needSetUp, $orderData)
    {
        //Data
        $orderData['payment_data']['payment_info'] = $this->loadDataSet('Payment', $card);
        //Steps
        if ($needSetUp) {
            $this->systemConfigurationHelper()->useHttps('admin', 'yes');
            $this->systemConfigurationHelper()->configure('PaymentMethod/savedcc_with_3Dsecure');
        }
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
    }

    public function createOrderWith3DSecureDataProvider()
    {
        return array(
            array('saved_jcb', true),
            array('saved_visa', false),
            array('saved_mastercard', false)
        );
    }
}