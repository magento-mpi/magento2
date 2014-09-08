<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Centinel\Test\Fixture;

/**
 * Registered checkout. Authorize.Net with 3D Secure payment method and free shipping method
 *
 */
class RegisteredAuthorizenetValidCc extends AbstractCreditCard
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
            'customer' => [
                'name' => 'customer_US_1',
                'is_registered' => true,
            ],
            'checkout_data' => [
                'billing_address' => null,
                'shipping_methods' => 'flat_rate',
                'payment_method' => 'authorizenet',
                'credit_card' => 'visa_3d_secure_valid',
            ],
            'configuration' => [
                'flat_rate',
                'authorizenet_3d_secure',
                '3d_secure_credit_card_validation',
            ],
        ];
    }
}
