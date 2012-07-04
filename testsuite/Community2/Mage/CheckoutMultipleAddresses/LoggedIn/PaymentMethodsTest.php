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
class Community2_Mage_CheckoutMultipleAddresses_LoggedIn_PaymentMethodsTest extends Mage_Selenium_TestCase
{
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
        $this->systemConfigurationHelper()->useHttps('frontend', 'no');
    }

    /**
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps and Verification
        $simple1 = $this->productHelper()->createSimpleProduct();
        $simple2 = $this->productHelper()->createSimpleProduct();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        return array('products' => array('product_1' => $simple1['simple']['product_name'],
                                         'product_2' => $simple2['simple']['product_name']),
                     'user'     => array('email' => $userData['email'],
                     'password' => $userData['password']));

    }

    /**
     * <p>Payment methods without 3D secure.</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>2.Customer without address is registered.</p>
     * <p>3.Customer signed in at the frontend.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Fill in Select Addresses page.</p>
     * <p>5. Click 'Continue to Shipping Information' button.</p>
     * <p>6. Fill in Shipping Information page</p>
     * <p>7. Click 'Continue to Billing Information' button.</p>
     * <p>8. Select Payment Method(by data provider).</p>
     * <p>9. Click 'Continue to Review Your Order' button.</p>
     * <p>10. Verify information into "Place Order" page</p>
     * <p>11. Place order.</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful.</p>
     *
     * @param string $payment
     * @param array $testData
     *
     * @test
     * @dataProvider paymentsWithout3dDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5280
     */
    public function paymentsWithout3d($payment, $testData)
    {
        //Data
        $paymentData = $this->loadDataSet('Payment', 'payment_' . $payment);
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_signed_in',
            array('payment' => $paymentData), $testData['products']);
        $paymentConfig = $this->loadDataSet('PaymentMethod', $payment);
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($paymentConfig);
        $this->customerHelper()->frontLoginCustomer($testData['user']);
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