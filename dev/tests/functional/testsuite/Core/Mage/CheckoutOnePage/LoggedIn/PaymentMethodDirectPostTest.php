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
class Core_Mage_CheckoutOnePage_LoggedIn_PaymentMethodDirectPostTest extends Mage_Selenium_TestCase
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
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        return array('sku' => $simple['general_name']);
    }

    /**
     * <p>Payment method Authorize.net Direct post.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6077
     */
    public function authorizeDirectPost($testData)
    {
        $this->markTestIncomplete('MAGETWO-9104');
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'signedin_flatrate_checkmoney_usa', array(
            'general_name' => $testData['sku'],
            'payment_data' => $this->loadDataSet('Payment', 'payment_authorizenetdp')
        ));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('PaymentMethod/authorizenetdp_enable');
        $this->logoutCustomer();
        $this->navigate('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_registration');
        //Steps
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }
}