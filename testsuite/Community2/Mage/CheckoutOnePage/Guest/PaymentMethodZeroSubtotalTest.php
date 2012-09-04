<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CheckoutOnePage
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for payment methods. Frontend - OnePageCheckout
 *
 * @package     selenium
 * @subpackage  functional_tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_CheckoutOnePage_Guest_PaymentMethodZeroSubtotalTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_zerosubtotal_enable');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->frontend();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
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
        return array('sku' => $simple['general_name']);

    }

    /**
     * <p>Payment method Zero Subtotal Checkout.</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Proceed to Checkout".</p>
     * <p>4. Fill in Billing Information tab.</p>
     * <p>5. Select "Ship to this address" option.</p>
     * <p>6. Click 'Continue' button.</p>
     * <p>7. Select Shipping Method.</p>
     * <p>8. Click 'Continue' button.</p>
     * <p>9. Check that 'No Payment Information required' is present.</p>
     * <p>10. Click 'Continue' button.</p>
     * <p>11. Verify information into "Order Review" tab</p>
     * <p>12. Place order.</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6141
     */
    public function zeroSubtotalCheckout($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'guest_flatrate_checkmoney',
            array('general_name' => $testData['sku'],
                  'payment_data' => $this->loadDataSet('Payment', 'payment_zerosubtotal')));
        $paymentConfig = $this->loadDataSet('PaymentMethod', 'zerosubtotal_enable');
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($paymentConfig);
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verifying
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Payment method Zero Subtotal Checkout.</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>2.Shipping is set to Zero.</p>
     * <p>3. "New Order Status" set to "Processing".</p>
     * <p>3. "Automatic Invoice all Items" = "Yes".</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Proceed to Checkout".</p>
     * <p>4. Fill in Billing Information tab.</p>
     * <p>5. Select "Ship to this address" option.</p>
     * <p>6. Click 'Continue' button.</p>
     * <p>7. Select Shipping Method.</p>
     * <p>8. Click 'Continue' button.</p>
     * <p>9. Check that 'No Payment Information required' is present.</p>
     * <p>10. Click 'Continue' button.</p>
     * <p>11. Verify information into "Order Review" tab</p>
     * <p>12. Place order.</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6163
     */
    public function zeroSubtotalCheckoutCapture($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'guest_flatrate_checkmoney',
            array('general_name' => $testData['sku'],
                  'payment_data' => $this->loadDataSet('Payment', 'payment_zerosubtotal')));
        $paymentConfig = $this->loadDataSet('PaymentMethod', 'zerosubtotal_enable',
            array('zsc_new_order_status' => 'Processing', 'zsc_automatically_invoice_all_items' => 'Yes'));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($paymentConfig);
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verifying
        $this->assertMessagePresent('success', 'success_checkout');
    }
}