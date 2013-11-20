<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Centinel\Test\Fixture;

use Mtf\Factory\Factory;
use Magento\Checkout\Test\Fixture\Checkout;

/**
 * Guest checkout. PayPal Payflow Pro with 3D Secure payment method and free shipping method
 *
 * @package Magento\Centinel
 */
class GuestPayPalPayflowProValidCc extends Checkout
{
    /**
     * Init validation data
     */
    protected function _initData()
    {
        $this->_data = array(
            'totals' => array(
                'grand_total' => '$10',
                'comment_history' => 'Authorized amount of $10'
            ),
            'payment_info' => array(
                'verification_result' => 'Successful',
                'cardholder_validation' => 'Enrolled',
                'electronic_commerce_indicator' => 'Card Issuer Liability',
            )
        );
    }

    /**
     * Create required data
     */
    public function persist()
    {
        //Configuration
        $this->_persistConfiguration(array(
            'free_shipping',
            'paypal_disabled_all_methods',
            '3d_secure_credit_card_validation',
            'paypal_payflow_pro_3d_secure'
        ));

        //Tax
        Factory::getApp()->magentoTaxRemoveTaxRule();

        //Products
        $simple = Factory::getFixtureFactory()->getMagentoCatalogProduct();
        $simple->switchData('simple_required');
        $simple->persist();

        $this->products = array(
            $simple
        );

        //Checkout data
        $this->billingAddress = Factory::getFixtureFactory()->getMagentoCustomerAddress();
        $this->billingAddress->switchData('address_US_1');

        $this->shippingMethods = Factory::getFixtureFactory()->getMagentoShippingMethod();
        $this->shippingMethods->switchData('free_shipping');

        $this->paymentMethod = Factory::getFixtureFactory()->getMagentoPaymentMethod();
        $this->paymentMethod->switchData('paypal_payflow_pro');

        $this->creditCard = Factory::getFixtureFactory()->getMagentoPaymentCc();
        $this->creditCard->switchData('visa_payflowpro_3d_secure');
    }

    /**
     * Get Payment Information - 3D Secure Verification Result
     *
     * @return string
     */
    public function getVerificationResult()
    {
        return $this->getData('payment_info/verification_result');
    }

    /**
     * Get Payment Information - 3D Secure Cardholder Validation
     *
     * @return string
     */
    public function getCardholderValidation()
    {
        return $this->getData('payment_info/cardholder_validation');
    }

    /**
     * Get Payment Information - 3D Secure Electronic Commerce Indicator
     *
     * @return string
     */
    public function getEcommerceIndicator()
    {
        return $this->getData('payment_info/electronic_commerce_indicator');
    }
}
