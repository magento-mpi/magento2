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
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CheckoutOnePage_LoggedIn_PaymentMethodsTest extends Mage_Selenium_TestCase
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
//        $this->loginAdminUser();
//        $this->systemConfigurationHelper()->useHttps('frontend', 'no');
    }

    /**
     * <p>Creating Simple product</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->frontend();
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');

        $this->paypalHelper()->paypalDeveloperLogin();
        $accountInfo = $this->paypalHelper()->createPreconfiguredAccount('paypal_sandbox_new_pro_account');
        $api = $this->paypalHelper()->getApiCredentials($accountInfo['email']);
        $accounts = $this->paypalHelper()->createBuyerAccounts('visa');

        return array(
            'sku' => $simple['general_name'],
            'api' => $api,
            'visa' => $accounts['visa']['credit_card'],
            'user' => array('email' => $userData['email'], 'password' => $userData['password'])
        );
    }

    /**
     * <p>Payment methods without 3D secure.</p>
     *
     * @param string $payment
     * @param array $testData
     *
     * @test
     * @dataProvider differentPaymentMethodsWithout3DDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3201
     */
    public function differentPaymentMethodsWithout3D($payment, $testData)
    {
        if ($payment == 'paypaldirectuk') {
            $this->markTestIncomplete('BUG: There is no "Website Payments Pro Payflow Edition" fiedset');
        }
        //Data
        $paymentData = $this->loadDataSet('Payment', 'payment_' . $payment);
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'signedin_flatrate_checkmoney_usa', array(
            'general_name' => $testData['sku'],
            'payment_data' => $paymentData
        ));
        $configName = ($payment !== 'checkmoney') ? $payment . '_without_3Dsecure' : $payment;
        $paymentConfig = $this->loadDataSet('PaymentMethod', $configName);
        if ($payment != 'purchaseorder' && $payment != 'payflowpro' && isset($paymentData['payment_info'])) {
            $checkoutData = $this->overrideArrayData($testData['visa'], $checkoutData, 'byFieldKey');
        }
        if ($payment == 'paypaldirect') {
            $paymentConfig = $this->overrideArrayData($testData['api'], $paymentConfig, 'byFieldKey');
        }
        //Steps
        $this->navigate('system_configuration');
        if (preg_match('/^pay(pal)|(flow)/', $payment)) {
            $this->systemConfigurationHelper()->configurePaypal($paymentConfig);
        } else {
            $this->systemConfigurationHelper()->configure($paymentConfig);
        }
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    public function differentPaymentMethodsWithout3DDataProvider()
    {
        return array(
            array('purchaseorder'),
            array('banktransfer'),
            array('cashondelivery'),
            array('paypaldirect'),
            array('savedcc'),
            array('paypaldirectuk'),
            array('checkmoney'),
            array('payflowpro'),
            array('authorizenet'),
            array('authorizenetdp')
        );
    }

    /**
     * <p>Payment methods with 3D secure.</p>
     *
     * @param string $payment
     * @param array $testData
     *
     * @test
     * @dataProvider differentPaymentMethodsWith3DDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3200
     */
    public function differentPaymentMethodsWith3D($payment, $testData)
    {
        if ($payment == 'paypaldirectuk') {
            $this->markTestIncomplete('BUG: There is no "Website Payments Pro Payflow Edition" fiedset');
        }
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'signedin_flatrate_checkmoney_usa', array(
            'general_name' => $testData['sku'],
            'payment_data' => $this->loadDataSet('Payment', 'payment_' . $payment)
        ));
        $paymentConfig = $this->loadDataSet('PaymentMethod', $payment . '_with_3Dsecure');
        //Steps
        if ($payment == 'paypaldirect') {
//            $this->systemConfigurationHelper()->useHttps('frontend', 'yes');
            $paymentConfig = $this->loadDataSet('PaymentMethod', $payment . '_with_3Dsecure', $testData['api']);
        }
        $this->navigate('system_configuration');
        if (preg_match('/^pay(pal)|(flow)/', $payment)) {
            $this->systemConfigurationHelper()->configurePaypal($paymentConfig);
            $this->systemConfigurationHelper()->configure('PaymentMethod/enable_3d_secure');
        } else {
            $this->systemConfigurationHelper()->configure($paymentConfig);
        }
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    public function differentPaymentMethodsWith3DDataProvider()
    {
        return array(
            array('paypaldirect'),
            array('savedcc'),
            array('paypaldirectuk'),
            array('payflowpro'),
            array('authorizenet')
        );
    }
}