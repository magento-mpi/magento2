<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Centinel\Test\Fixture;

/**
 * Guest checkout with invalid CC. Authorize.Net with 3D Secure payment method and free shipping method
 *
 */
class GuestAuthorizenetInvalidCc extends AbstractCreditCard
{
    /**
     * {@inheritdoc}
     */
    protected function _initData()
    {
        parent::_initData();
        $this->_data = [
            'product_type' => 'simple_with_new_category',
            'checkout_data' => [
                'billing_address' => 'address_US_1',
                'shipping_methods' => 'flat_rate',
                'payment_method' => 'authorizenet',
                'credit_card' => 'visa_3d_secure_invalid',
            ],
            'configuration' => [
                'flat_rate',
                'authorizenet_3d_secure',
                '3d_secure_credit_card_validation',
            ],
        ];
    }
}
