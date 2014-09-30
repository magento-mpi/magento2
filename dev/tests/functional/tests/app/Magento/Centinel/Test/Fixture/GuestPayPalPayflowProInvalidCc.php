<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Centinel\Test\Fixture;

/**
 * Guest checkout with invalid CC. PayPal Payflow Pro with 3D Secure payment method and free shipping method
 *
 */
class GuestPayPalPayflowProInvalidCc extends AbstractCreditCard
{
    /**
     * {@inheritdoc}
     */
    protected function _initData()
    {
        parent::_initData();
        $this->_data = [
            'product_type' => 'simple_required',
            'checkout_data' => [
                'billing_address' => 'address_US_1',
                'shipping_methods' => 'free_shipping',
                'payment_method' => 'paypal_payflow_pro',
                'credit_card' => 'visa_3d_secure_invalid',
            ],
            'configuration' => [
                'free_shipping',
                'paypal_disabled_all_methods',
                'paypal_payflow_pro_3d_secure',
                '3d_secure_credit_card_validation',
            ],
        ];
    }
}
