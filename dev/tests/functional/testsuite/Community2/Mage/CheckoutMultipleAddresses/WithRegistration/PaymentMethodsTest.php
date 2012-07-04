<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CheckoutMultipleAddresses
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for payment methods. Frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_CheckoutMultipleAddresses_WithRegistration_PaymentMethodsTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
    }

    /**
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        $simple1 = $this->productHelper()->createSimpleProduct();
        $simple2 = $this->productHelper()->createSimpleProduct();

        return array('products' => array('product_1' => $simple1['simple']['product_name'],
                                         'product_2' => $simple2['simple']['product_name']));
    }

    /**
     * <p>Payment methods without 3D secure.</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with Registering</p>
     * <p>5. Fill in Select Addresses page.</p>
     * <p>6. Click 'Continue to Shipping Information' button.</p>
     * <p>7. Fill in Shipping Information page</p>
     * <p>8. Click 'Continue to Billing Information' button.</p>
     * <p>9. Select Payment Method(by data provider).</p>
     * <p>10. Click 'Continue to Review Your Order' button.</p>
     * <p>11. Verify information into "Place Order" page</p>
     * <p>12. Place order.</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful.</p>
     *
     * @param string $payment
     * @param array $testData
     *
     * @test
     * @dataProvider paymentsWithout3dDataProvider
     * @depends preconditionsForTests
     */
    public function paymentsWithout3d($payment, $testData)
    {
        //Data
        $paymentData = $this->loadDataSet('Payment', 'payment_' . $payment);
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_register',
            array('payment' => $paymentData), $testData['products']);
        $paymentConfig = $this->loadDataSet('PaymentMethod', $payment);
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($paymentConfig);
        $this->frontend();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    public function paymentsWithout3dDataProvider()
    {
        return array(
            array('purchaseorder'),
            array('banktransfer'),
            array('cashondelivery'),
        );
    }
}