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
 * Guest checkout with invalid CC. PayPal Payments Pro with 3D Secure payment method and free shipping method
 *
 * @package Magento\Centinel
 */
class GuestPayPalPaymentsProInvalidCc extends AbstractCreditCard
{
    /**
     * {@inheritdoc}
     */
    protected function _initData()
    {
        parent::_initData();
        $this->_data = array(
            'product_type' => 'simple_with_new_category',
            'checkout_data' => array(
                'billing_address' => 'address_US_1',
                'shipping_methods' => 'free_shipping',
                'payment_method' => 'paypal_direct',
                'credit_card' => 'visa_3d_secure_invalid',
            ),
            'configuration' => array(
                'free_shipping',
                'paypal_disabled_all_methods',
                'paypal_payments_pro_3d_secure',
                '3d_secure_credit_card_validation',
            ),
        );
    }
}
