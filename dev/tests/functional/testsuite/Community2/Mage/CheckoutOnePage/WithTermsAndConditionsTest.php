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
class Community2_Mage_CheckoutOnePage_WithTermsAndConditionsTest extends Mage_Selenium_TestCase
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
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->termsAndConditionsHelper()->deleteAllTerms();
        $this->paypalHelper()->paypalDeveloperLogin();
        $this->paypalHelper()->deleteAllAccounts();
    }

    /**
     * <p>Creating Simple product</p>
     *
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $termsData = $this->loadDataSet('TermsAndConditions', 'generic_terms_all', array('status' => 'Enabled'));
        //Steps and Verification
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_checkout_terms_and_conditions');
        $this->termsAndConditionsHelper()->createTermsAndConditions($termsData);
        $this->assertMessagePresent('success', 'condition_saved');
        $agreementId =
            $this->termsAndConditionsHelper()->getAgreementId(array('condition_name' => $termsData['condition_name']));

        $this->paypalHelper()->paypalDeveloperLogin();
        $accountInfo = $this->paypalHelper()->createPreconfiguredAccount('paypal_sandbox_new_pro_account');
        $api = $this->paypalHelper()->getApiCredentials($accountInfo['email']);
        $accounts = $this->paypalHelper()->createBuyerAccounts('visa');

        return array('sku' => $simple['general_name'], 'api' => $api, 'email' => $userData['email'],
            'visa'  => $accounts['visa']['credit_card'],
            'agreement' => array('agreement_id' => $agreementId,
            'agreement_content' => $termsData['content'],
            'agreement_checkbox_text' => $termsData['checkbox_text']));
    }

    /**
     * <p>Payment methods with Terms and Conditions without 3D secure.</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Proceed to Checkout".</p>
     * <p>4. Select Checkout Method with log in</p>
     * <p>4. Fill in Billing Information tab.</p>
     * <p>5. Select "Ship to this address" option.</p>
     * <p>6. Click 'Continue' button.</p>
     * <p>7. Select Shipping Method.</p>
     * <p>8. Click 'Continue' button.</p>
     * <p>9. Select Payment Method(by data provider).</p>
     * <p>10. Select Terms and Conditions.</p>
     * <p>11. Click 'Continue' button.</p>
     * <p>12. Verify information into "Order Review" tab</p>
     * <p>13. Place order.</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful.</p>
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
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'exist_flatrate_checkmoney',
            array('general_name' => $testData['sku'], 'email_address' => $testData['email'],
                'payment_data' => $this->loadDataSet('Payment', 'payment_' . $payment)));
        $checkoutData['agreement'] =
            $this->loadDataSet('TermsAndConditions', 'checkout_agreement', $testData['agreement']);
        $configName = ($payment !== 'checkmoney') ? $payment . '_without_3Dsecure' : $payment;
        $paymentConfig = $this->loadDataSet('PaymentMethod', $configName);
        if ($payment != 'payflowpro' && $payment != 'checkmoney') {
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
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
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