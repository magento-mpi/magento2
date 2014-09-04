<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Centinel\Test\Fixture;

/**
 * Guest checkout. PayPal Payments Pro with 3D Secure payment method and free shipping method
 *
 */
class GuestPayPalPaymentsProValidCc extends AbstractCreditCard
{
    /**
     * {@inheritdoc}
     */
    protected function _initData()
    {
        parent::_initData();
        $this->_data = [
            'totals' => [
                'grand_total' => '15',
                'comment_history' => 'Authorized amount of $15'
            ],
            'payment_info' => [
                'verification_result' => 'Successful',
                'cardholder_validation' => 'Enrolled',
                'electronic_commerce_indicator' => 'Card Issuer Liability',
            ],
            'product_type' => 'simple_with_new_category',
            'checkout_data' => [
                'billing_address' => 'address_US_1',
                'shipping_methods' => 'flat_rate',
                'payment_method' => 'paypal_direct',
                'credit_card' => 'visa_3d_secure_valid',
            ],
            'configuration' => [
                'flat_rate',
                'paypal_disabled_all_methods',
                'paypal_payments_pro_3d_secure',
                '3d_secure_credit_card_validation',
                'default_tax_config',
                'display_price',
                'display_shopping_cart'
            ],
        ];
    }
}
