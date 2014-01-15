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

/**
 * Guest checkout. PayPal Payflow Pro with 3D Secure payment method and free shipping method
 *
 * @package Magento\Centinel
 */
class GuestPayPalPayflowProValidCc extends AbstractCreditCard
{
    /**
     * {@inheritdoc}
     */
    protected function _initData()
    {
        parent::_initData();
        $this->_data = array(
            'totals' => array(
                'grand_total' => '$10',
                'comment_history' => 'Authorized amount of $10'
            ),
            'payment_info' => array(
                'verification_result' => 'Successful',
                'cardholder_validation' => 'Enrolled',
                'electronic_commerce_indicator' => 'Card Issuer Liability',
            ),
            'product_type' => 'simple_required',
            'checkout_data' => array(
                'billing_address' => 'address_US_1',
                'shipping_methods' => 'free_shipping',
                'payment_method' => 'paypal_payflow_pro',
                'credit_card' => 'visa_3d_secure_valid',
            ),
            'configuration' => array(
                'free_shipping',
                'paypal_disabled_all_methods',
                'paypal_payflow_pro_3d_secure',
                '3d_secure_credit_card_validation',
            ),
        );
    }
}
