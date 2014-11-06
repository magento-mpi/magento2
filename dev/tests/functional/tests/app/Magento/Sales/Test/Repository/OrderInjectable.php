<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Repository for order.
 */
class OrderInjectable extends AbstractRepository
{
    /**
     * @constructor
     * @param array $defaultConfig
     * @param array $defaultData
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(array $defaultConfig = [], array $defaultData = [])
    {
        $this->_data['default'] = [
            'customer_id' => ['dataSet' => 'default'],
            'base_currency_code' => false,
            'store_id' => ['dataSet' => 'default_store_view'],
            'order_currency_code' => 'USD',
            'shipping_method' => 'flatrate_flatrate',
            'payment_auth_expiration' => ['method' => 'checkmo'],
            'payment_authorization_amount' => ['method' => 'free'],
            'billing_address_id' => ['dataSet' => 'US_address'],
            'entity_id' => ['products' => 'catalogProductSimple::default']
        ];

        $this->_data['two_simple_product'] = [
            'customer_id' => ['dataSet' => 'default'],
            'base_currency_code' => false,
            'store_id' => ['dataSet' => 'default_store_view'],
            'order_currency_code' => 'USD',
            'shipping_method' => 'flatrate_flatrate',
            'payment_auth_expiration' => ['method' => 'checkmo'],
            'payment_authorization_amount' => ['method' => 'free'],
            'billing_address_id' => ['dataSet' => 'US_address'],
            'entity_id' => ['products' => 'catalogProductSimple::default,catalogProductSimple::default']
        ];

        $this->_data['virtual_product'] = [
            'customer_id' => ['dataSet' => 'default'],
            'base_currency_code' => false,
            'store_id' => ['dataSet' => 'default_store_view'],
            'order_currency_code' => 'USD',
            'shipping_method' => 'flatrate_flatrate',
            'payment_auth_expiration' => ['method' => 'checkmo'],
            'payment_authorization_amount' => ['method' => 'free'],
            'billing_address_id' => ['dataSet' => 'US_address'],
            'entity_id' => ['products' => 'catalogProductVirtual::default']
        ];

        $this->_data['downloadable_product'] = [
            'customer_id' => ['dataSet' => 'default'],
            'base_currency_code' => false,
            'store_id' => ['dataSet' => 'default_store_view'],
            'order_currency_code' => 'USD',
            'shipping_method' => '',
            'payment_auth_expiration' => ['method' => 'checkmo'],
            'payment_authorization_amount' => ['method' => 'free'],
            'billing_address_id' => ['dataSet' => 'US_address'],
            'entity_id' => ['products' => 'downloadableProductInjectable::with_two_bought_links']
        ];

        $this->_data['two_downloadable_product'] = [
            'customer_id' => ['dataSet' => 'default'],
            'base_currency_code' => false,
            'store_id' => ['dataSet' => 'default_store_view'],
            'order_currency_code' => 'USD',
            'shipping_method' => '',
            'payment_auth_expiration' => ['method' => 'checkmo'],
            'payment_authorization_amount' => ['method' => 'free'],
            'billing_address_id' => ['dataSet' => 'US_address'],
            'entity_id' => [
                'products' =>
                    'downloadableProductInjectable::with_two_bought_links,'
                    . 'downloadableProductInjectable::with_two_bought_links'
            ]
        ];

        $this->_data['with_coupon'] = [
            'customer_id' => ['dataSet' => 'default'],
            'base_currency_code' => false,
            'store_id' => ['dataSet' => 'default_store_view'],
            'order_currency_code' => 'USD',
            'shipping_method' => 'flatrate_flatrate',
            'payment_auth_expiration' => ['method' => 'checkmo'],
            'payment_authorization_amount' => ['method' => 'free'],
            'billing_address_id' => ['dataSet' => 'US_address'],
            'entity_id' => ['products' => 'catalogProductSimple::default'],
            'coupon_code' => ['dataSet' => 'active_sales_rule_for_all_groups'],
            'price' => ['preset' => 'default_with_discount']
        ];

        $this->_data['with_unique_customer'] = [
            'customer_id' => ['dataSet' => 'johndoe_unique'],
            'base_currency_code' => false,
            'store_id' => ['dataSet' => 'default_store_view'],
            'order_currency_code' => 'USD',
            'shipping_method' => 'flatrate_flatrate',
            'payment_auth_expiration' => ['method' => 'checkmo'],
            'payment_authorization_amount' => ['method' => 'free'],
            'billing_address_id' => ['dataSet' => 'US_address'],
            'entity_id' => ['products' => 'catalogProductSimple::default']
        ];

        $this->_data['simple_big_qty'] = [
            'customer_id' => ['dataSet' => 'default'],
            'base_currency_code' => false,
            'store_id' => ['dataSet' => 'default_store_view'],
            'order_currency_code' => 'USD',
            'shipping_method' => 'flatrate_flatrate',
            'payment_auth_expiration' => ['method' => 'checkmo'],
            'payment_authorization_amount' => ['method' => 'free'],
            'billing_address_id' => ['dataSet' => 'US_address'],
            'entity_id' => ['products' => 'catalogProductSimple::simple_big_qty']
        ];

        $this->_data['virtual_big_qty'] = [
            'customer_id' => ['dataSet' => 'default'],
            'base_currency_code' => false,
            'store_id' => ['dataSet' => 'default_store_view'],
            'order_currency_code' => 'USD',
            'shipping_method' => 'flatrate_flatrate',
            'payment_auth_expiration' => ['method' => 'checkmo'],
            'payment_authorization_amount' => ['method' => 'free'],
            'billing_address_id' => ['dataSet' => 'US_address'],
            'entity_id' => ['products' => 'catalogProductVirtual::virtual_big_qty']
        ];
    }
}
