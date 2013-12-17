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
 * Guest checkout. PayPal Payments Pro with 3D Secure payment method and free shipping method
 *
 * @package Magento\Centinel
 */
class GuestPayPalPaymentsProValidCc extends AbstractCreditCard
{
    /**
     * {@inheritdoc}
     */
    protected function _initData()
    {
        parent::_initData();
        $this->_data = array(
            'totals' => array(
                'grand_total' => '$15',
                'comment_history' => 'Authorized amount of $15'
            ),
            'payment_info' => array(
                'verification_result' => 'Successful',
                'cardholder_validation' => 'Enrolled',
                'electronic_commerce_indicator' => 'Card Issuer Liability',
            ),
            'product_type' => 'simple_with_new_category',
            'checkout_data' => array(
                'billing_address' => 'address_US_1',
                'shipping_methods' => 'flat_rate',
                'payment_method' => 'paypal_direct',
                'credit_card' => 'visa_3d_secure_valid',
            ),
            'configuration' => array(
                'flat_rate',
                'paypal_disabled_all_methods',
                'paypal_payments_pro_3d_secure',
                '3d_secure_credit_card_validation',
                'default_tax_config',
                'display_price',
                'display_shopping_cart'
            ),
        );
    }
}
