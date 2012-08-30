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
 * @package     selenium
 * @subpackage  functional_tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_CheckoutOnePage_Existing_PaymentMethodDirectPostTest extends Mage_Selenium_TestCase
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
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('PaymentMethod/authorizenetdp_disable');
    }

    /**
     * <p>Creating Simple product</p>
     *
     * @return string
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps and Verification
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        return array('sku' => $simple['general_name'], 'email' => $userData['email']);
    }

    /**
     * <p>Payment method Authorize.net Direct post.</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Proceed to Checkout".</p>
     * <p>4. Select Authorize.net Direct post with log in</p>
     * <p>4. Fill in Billing Information tab.</p>
     * <p>5. Select "Ship to this address" option.</p>
     * <p>6. Click 'Continue' button.</p>
     * <p>7. Select Shipping Method.</p>
     * <p>8. Click 'Continue' button.</p>
     * <p>9. Select Authorize.net Direct Post Payment Method(by data provider).</p>
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
     * @TestlinkId TL-MAGE-6075
     */
    public function authorizeDirectPost($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'exist_flatrate_checkmoney',
            array('general_name' => $testData['sku'], 'email_address' => $testData['email'],
                'payment_data' => $this->loadDataSet('Payment', 'payment_authorizenetdp')));
        $paymentConfig = $this->loadDataSet('PaymentMethod', 'authorizenetdp_enable');
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($paymentConfig);
        $this->frontend();
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verifying
        $this->assertMessagePresent('success', 'success_checkout');
    }
}