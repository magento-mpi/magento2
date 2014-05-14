<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for payment methods. Frontend - OnePageCheckout
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CheckoutOnePage_WithTermsAndConditionsTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('TermsAndConditions/terms_and_conditions_frontend_allow');
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
        $this->systemConfigurationHelper()->configure('TermsAndConditions/terms_and_conditions_frontend_disable');
        $this->navigate('manage_sales_checkout_terms_conditions');
        $this->termsAndConditionsHelper()->deleteAllTerms();
    }

    /**
     * <p>Creating Simple product</p>
     *
     * @return array
     *
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $termsData = $this->loadDataSet('TermsAndConditions', 'generic_terms_all', array('status' => 'Enabled'));
        //Steps and Verification
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');

        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');

        $this->navigate('manage_sales_checkout_terms_conditions');
        $this->termsAndConditionsHelper()->createTermsAndConditions($termsData);
        $this->assertMessagePresent('success', 'condition_saved');

        $agreementId = $this->termsAndConditionsHelper()->getAgreementId(
            array('condition_name' => $termsData['condition_name'])
        );

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
            'email' => $userData['email'],
            'visa' => $accounts['visa']['credit_card'],
            'agreement' => array(
                'agreement_id' => $agreementId,
                'agreement_content' => $termsData['content'],
                'agreement_checkbox_text' => $termsData['checkbox_text']
            )
        );
    }

    /**
     * <p>Payment methods with Terms and Conditions without 3D secure.</p>
     *
     * @param string $payment
     * @param array $testData
     *
     * @test
     * @dataProvider withDifferentPaymentMethodsDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2328
     */
    public function withDifferentPaymentMethods($payment, $testData)
    {
        if ($payment == 'paypaldirectuk') {
            $this->markTestIncomplete('BUG: There is no "Website Payments Pro Payflow Edition" fiedset');
        }
        //Data
        $paymentData = $this->loadDataSet('Payment', 'payment_' . $payment);
        $checkout = $this->loadDataSet('OnePageCheckout', 'exist_flatrate_checkmoney_usa', array(
            'general_name' => $testData['sku'],
            'email_address' => $testData['email'],
            'payment_data' => $paymentData
        ));
        $checkout['agreement'] = $this->loadDataSet('TermsAndConditions', 'checkout_agreement', $testData['agreement']);
        $configName = ($payment !== 'checkmoney') ? $payment . '_without_3Dsecure' : $payment;
        $paymentConfig = $this->loadDataSet('PaymentMethod', $configName);
        if ($payment != 'payflowpro' && isset($paymentData['payment_info'])) {
            $checkout = $this->overrideArrayData($testData['visa'], $checkout, 'byFieldKey');
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
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkout);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    public function withDifferentPaymentMethodsDataProvider()
    {
        return array(
            array('authorizenet'),
            array('paypaldirect'),
            array('savedcc'),
            array('paypaldirectuk'),
            array('checkmoney'),
            array('payflowpro'),
        );
    }
}