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
class Core_Mage_CheckoutMultipleAddresses_WithRegistration_PaymentMethodsTest extends Mage_Selenium_TestCase
{
    private static $_paypalAccount;

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->systemConfigurationHelper()->useHttps('frontend', 'no');
        if (isset(self::$_paypalAccount)) {
            $this->paypalHelper()->paypalDeveloperLogin();
            $this->paypalHelper()->deleteAccount(self::$_paypalAccount);
        }
    }

    /**
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        $simple1 = $this->productHelper()->createSimpleProduct();
        $simple2 = $this->productHelper()->createSimpleProduct();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');

        $this->paypalHelper()->paypalDeveloperLogin();
        $accountInfo = $this->paypalHelper()->createPreconfiguredAccount('paypal_sandbox_new_pro_account');
        $api = $this->paypalHelper()->getApiCredentials($accountInfo['email']);
        $accounts = $this->paypalHelper()->createBuyerAccounts('visa');
        self::$_paypalAccount = $accountInfo['email'];

        return array(
            'products' => array(
                'product_1' => $simple1['simple']['product_name'],
                'product_2' => $simple2['simple']['product_name']
            ),
            'api' => $api,
            'visa' => $accounts['visa']['credit_card']
        );
    }

    /**
     * <p>Payment methods without 3D secure.</p>
     *
     * @param string $payment
     * @param array $testData
     *
     * @test
     * @dataProvider paymentsWithout3dDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3183
     */
    public function paymentsWithout3d($payment, $testData)
    {
        //Data
        $paymentData = $this->loadDataSet('Payment', 'payment_' . $payment);
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_register',
            array('payment' => $paymentData), $testData['products']);
        $configName = ($payment !== 'checkmoney') ? $payment . '_without_3Dsecure' : $payment;
        $paymentConfig = $this->loadDataSet('PaymentMethod', $configName);
        if ($payment != 'payflowpro' && isset($paymentData['payment_info']['card_number'])) {
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
            array('paypaldirect'),
            array('savedcc'),
            array('paypaldirectuk'),
            array('checkmoney'),
            array('payflowpro'),
            array('authorizenet')
        );
    }

    /**
     * <p>Payment methods with 3D secure.</p>
     *
     * @param string $payment
     * @param array $testData
     *
     * @test
     * @dataProvider paymentsWith3dDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3182
     */
    public function paymentsWith3d($payment, $testData)
    {
        //Data
        $paymentData = $this->loadDataSet('Payment', 'payment_' . $payment);
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_register',
            array('payment' => $paymentData), $testData['products']);
        $paymentConfig = $this->loadDataSet('PaymentMethod', $payment . '_with_3Dsecure');
        //Steps
        if ($payment == 'paypaldirect') {
            $this->systemConfigurationHelper()->useHttps('frontend', 'yes');
            $paymentConfig = $this->overrideArrayData($testData['api'], $paymentConfig, 'byFieldKey');
        }
        $this->navigate('system_configuration');
        if (preg_match('/^pay(pal)|(flow)/', $payment)) {
            $this->systemConfigurationHelper()->configurePaypal($paymentConfig);
            $this->systemConfigurationHelper()->configure('PaymentMethod/enable_3d_secure');
        } else {
            $this->systemConfigurationHelper()->configure($paymentConfig);
        }
        $this->frontend();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    public function paymentsWith3dDataProvider()
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