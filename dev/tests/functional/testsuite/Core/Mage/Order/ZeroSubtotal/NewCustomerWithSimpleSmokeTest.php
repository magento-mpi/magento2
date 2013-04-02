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
class Core_Mage_Order_ZeroSubtotal_NewCustomerWithSimpleSmokeTest extends Mage_Selenium_TestCase
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

        return $simple['general_sku'];
    }

    /**
     * <p>Place order via ZeroSubtotal Checkout</p>
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
        $orderData = $this->loadDataSet(
            'SalesOrder',
            'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku'  => $simpleSku, 'payment_data' => $paymentData)
        );
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        return $orderData;
    }

    /**
     * <p>Zero Subtotal Checkout with full invoice</p>
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
     *
     * @param array $orderData
     *
     * @test
     * @depends orderZeroSubtotalCheckoutSmoke
     * @TestlinkId TL-MAGE-6154
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
        $this->orderHelper()->submitOrder();
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
    }

    /**
     * <p>Place order via ZeroSubtotal Checkout</p>
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
        $orderData = $this->loadDataSet(
            'SalesOrder',
            'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku, 'payment_data' => $paymentData)
        );
        $paymentConfig = $this->loadDataSet(
            'PaymentMethod',
            'zerosubtotal_enable',
            array('zsc_new_order_status' => 'Processing', 'zsc_automatically_invoice_all_items' => 'Yes')
        );
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
