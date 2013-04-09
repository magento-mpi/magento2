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
 * Creating Order with promoted product
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Order_Create_WithProductWithWarningTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Log in to Backend and configure preconditions.</p>
     *
     * <p>Log in to Backend.</p>
     *
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $this->systemConfigurationHelper()->configure('PaymentMethod/savedcc_without_3Dsecure');
    }

    /**
     * <p>Log in to Backend.</p>
     */
    public function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Order creation with product that contains validation message</p>
     *
     * @param string $productData
     * @param string $message
     * @param integer $productQty
     *
     * @test
     * @dataProvider orderWithProductWithValidationMessageDataProvider
     * @TestlinkId	TL-MAGE-3286
     */
    public function orderWithProductWithValidationMessage($productData, $message, $productQty)
    {
        //Data
        $simple = $this->loadDataSet('SalesOrder', $productData);

        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
                                        array('filter_sku'  => $simple['general_sku'],
                                              'product_qty' => $productQty));
        $billingAddress = $orderData['billing_addr_data'];
        $shippingAddress = $orderData['shipping_addr_data'];
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        $this->orderHelper()->addProductToOrder($orderData['products_to_add']['product_1']);
        $this->addParameter('sku', $simple['general_sku']);
        if ($message === 'requested_quantity_not_available') {
            $this->addParameter('productTitle', $simple['general_name']);
        } else {
            $this->addParameter('qty', 10);
        }
        $this->assertMessagePresent('validation', $message);
        $this->orderHelper()->fillOrderAddress($billingAddress, $billingAddress['address_choice'], 'billing');
        $this->orderHelper()->fillOrderAddress($shippingAddress, $shippingAddress['address_choice'], 'shipping');
        $this->clickControl('link', 'get_shipping_methods_and_rates', false);
        $this->pleaseWait();
        $this->orderHelper()->selectShippingMethod($orderData['shipping_data']);
        $this->orderHelper()->selectPaymentMethod($orderData['payment_data']);
        $this->orderHelper()->submitOrder();
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty();
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
    }

    public function orderWithProductWithValidationMessageDataProvider()
    {
        return array(
            array('simple_low_qty', 'requested_quantity_not_available', 5),
            array('simple_out_of_stock', 'out_of_stock_product', 1),
            array('simple_min_allowed_qty', 'min_allowed_quantity_error', 5),
            array('simple_max_allowed_qty', 'max_allowed_quantity_error', 11),
            array('simple_with_increments', 'wrong_increments_qty', 5)
        );
    }
}