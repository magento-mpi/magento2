<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class OrderInjectable
 * Fixture for Order
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class OrderInjectable extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Sales\Test\Repository\OrderInjectable';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Sales\Test\Handler\OrderInjectable\OrderInjectableInterface';

    protected $defaultDataSet = [
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

    protected $entity_id = [
        'attribute_code' => 'entity_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
        'source' => 'Magento\Sales\Test\Fixture\OrderInjectable\EntityId',
        'group' => null
    ];

    protected $state = [
        'attribute_code' => 'state',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $status = [
        'attribute_code' => 'status',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $coupon_code = [
        'attribute_code' => 'coupon_code',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'source' => 'Magento\Sales\Test\Fixture\OrderInjectable\CouponCode',
        'group' => null
    ];

    protected $protect_code = [
        'attribute_code' => 'protect_code',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $shipping_description = [
        'attribute_code' => 'shipping_description',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $is_virtual = [
        'attribute_code' => 'is_virtual',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $store_id = [
        'attribute_code' => 'store_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'source' => 'Magento\Sales\Test\Fixture\OrderInjectable\StoreId',
        'group' => null
    ];

    protected $customer_id = [
        'attribute_code' => 'customer_id',
        'backend_type' => 'virtual',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
        'source' => 'Magento\Sales\Test\Fixture\OrderInjectable\CustomerId',
        'group' => null
    ];

    protected $base_discount_amount = [
        'attribute_code' => 'base_discount_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_discount_canceled = [
        'attribute_code' => 'base_discount_canceled',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_discount_invoiced = [
        'attribute_code' => 'base_discount_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_discount_refunded = [
        'attribute_code' => 'base_discount_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_grand_total = [
        'attribute_code' => 'base_grand_total',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_shipping_amount = [
        'attribute_code' => 'base_shipping_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_shipping_canceled = [
        'attribute_code' => 'base_shipping_canceled',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_shipping_invoiced = [
        'attribute_code' => 'base_shipping_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_shipping_refunded = [
        'attribute_code' => 'base_shipping_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_shipping_tax_amount = [
        'attribute_code' => 'base_shipping_tax_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_shipping_tax_refunded = [
        'attribute_code' => 'base_shipping_tax_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_subtotal = [
        'attribute_code' => 'base_subtotal',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_subtotal_canceled = [
        'attribute_code' => 'base_subtotal_canceled',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_subtotal_invoiced = [
        'attribute_code' => 'base_subtotal_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_subtotal_refunded = [
        'attribute_code' => 'base_subtotal_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_tax_amount = [
        'attribute_code' => 'base_tax_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_tax_canceled = [
        'attribute_code' => 'base_tax_canceled',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_tax_invoiced = [
        'attribute_code' => 'base_tax_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_tax_refunded = [
        'attribute_code' => 'base_tax_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_to_global_rate = [
        'attribute_code' => 'base_to_global_rate',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_to_order_rate = [
        'attribute_code' => 'base_to_order_rate',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_total_canceled = [
        'attribute_code' => 'base_total_canceled',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_total_invoiced = [
        'attribute_code' => 'base_total_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_total_invoiced_cost = [
        'attribute_code' => 'base_total_invoiced_cost',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_total_offline_refunded = [
        'attribute_code' => 'base_total_offline_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_total_online_refunded = [
        'attribute_code' => 'base_total_online_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_total_paid = [
        'attribute_code' => 'base_total_paid',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_total_qty_ordered = [
        'attribute_code' => 'base_total_qty_ordered',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_total_refunded = [
        'attribute_code' => 'base_total_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $discount_amount = [
        'attribute_code' => 'discount_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $discount_canceled = [
        'attribute_code' => 'discount_canceled',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $discount_invoiced = [
        'attribute_code' => 'discount_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $discount_refunded = [
        'attribute_code' => 'discount_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $grand_total = [
        'attribute_code' => 'grand_total',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $shipping_amount = [
        'attribute_code' => 'shipping_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $shipping_canceled = [
        'attribute_code' => 'shipping_canceled',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $shipping_invoiced = [
        'attribute_code' => 'shipping_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $shipping_refunded = [
        'attribute_code' => 'shipping_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $shipping_tax_amount = [
        'attribute_code' => 'shipping_tax_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $shipping_tax_refunded = [
        'attribute_code' => 'shipping_tax_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $store_to_base_rate = [
        'attribute_code' => 'store_to_base_rate',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $store_to_order_rate = [
        'attribute_code' => 'store_to_order_rate',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $subtotal = [
        'attribute_code' => 'subtotal',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $subtotal_canceled = [
        'attribute_code' => 'subtotal_canceled',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $subtotal_invoiced = [
        'attribute_code' => 'subtotal_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $subtotal_refunded = [
        'attribute_code' => 'subtotal_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $tax_amount = [
        'attribute_code' => 'tax_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $tax_canceled = [
        'attribute_code' => 'tax_canceled',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $tax_invoiced = [
        'attribute_code' => 'tax_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $tax_refunded = [
        'attribute_code' => 'tax_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $total_canceled = [
        'attribute_code' => 'total_canceled',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $total_invoiced = [
        'attribute_code' => 'total_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $total_offline_refunded = [
        'attribute_code' => 'total_offline_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $total_online_refunded = [
        'attribute_code' => 'total_online_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $total_paid = [
        'attribute_code' => 'total_paid',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $total_qty_ordered = [
        'attribute_code' => 'total_qty_ordered',
        'backend_type' => 'array',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $total_refunded = [
        'attribute_code' => 'total_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $can_ship_partially = [
        'attribute_code' => 'can_ship_partially',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $can_ship_partially_item = [
        'attribute_code' => 'can_ship_partially_item',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $customer_is_guest = [
        'attribute_code' => 'customer_is_guest',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $customer_note_notify = [
        'attribute_code' => 'customer_note_notify',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $billing_address_id = [
        'attribute_code' => 'billing_address_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
        'source' => 'Magento\Sales\Test\Fixture\OrderInjectable\BillingAddressId'
    ];

    protected $customer_group_id = [
        'attribute_code' => 'customer_group_id',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $edit_increment = [
        'attribute_code' => 'edit_increment',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $email_sent = [
        'attribute_code' => 'email_sent',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $forced_shipment_with_invoice = [
        'attribute_code' => 'forced_shipment_with_invoice',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $payment_auth_expiration = [
        'attribute_code' => 'payment_auth_expiration',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $quote_address_id = [
        'attribute_code' => 'quote_address_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $quote_id = [
        'attribute_code' => 'quote_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $shipping_address_id = [
        'attribute_code' => 'shipping_address_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $adjustment_negative = [
        'attribute_code' => 'adjustment_negative',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $adjustment_positive = [
        'attribute_code' => 'adjustment_positive',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_adjustment_negative = [
        'attribute_code' => 'base_adjustment_negative',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_adjustment_positive = [
        'attribute_code' => 'base_adjustment_positive',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_shipping_discount_amount = [
        'attribute_code' => 'base_shipping_discount_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_subtotal_incl_tax = [
        'attribute_code' => 'base_subtotal_incl_tax',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_total_due = [
        'attribute_code' => 'base_total_due',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $payment_authorization_amount = [
        'attribute_code' => 'payment_authorization_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $shipping_discount_amount = [
        'attribute_code' => 'shipping_discount_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $subtotal_incl_tax = [
        'attribute_code' => 'subtotal_incl_tax',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $total_due = [
        'attribute_code' => 'total_due',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $weight = [
        'attribute_code' => 'weight',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $customer_dob = [
        'attribute_code' => 'customer_dob',
        'backend_type' => 'datetime',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $increment_id = [
        'attribute_code' => 'increment_id',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $applied_rule_ids = [
        'attribute_code' => 'applied_rule_ids',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_currency_code = [
        'attribute_code' => 'base_currency_code',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $customer_email = [
        'attribute_code' => 'customer_email',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $customer_firstname = [
        'attribute_code' => 'customer_firstname',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $customer_lastname = [
        'attribute_code' => 'customer_lastname',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $customer_middlename = [
        'attribute_code' => 'customer_middlename',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $customer_prefix = [
        'attribute_code' => 'customer_prefix',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $customer_suffix = [
        'attribute_code' => 'customer_suffix',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $customer_taxvat = [
        'attribute_code' => 'customer_taxvat',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $discount_description = [
        'attribute_code' => 'discount_description',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $ext_customer_id = [
        'attribute_code' => 'ext_customer_id',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $ext_order_id = [
        'attribute_code' => 'ext_order_id',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $global_currency_code = [
        'attribute_code' => 'global_currency_code',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $hold_before_state = [
        'attribute_code' => 'hold_before_state',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $hold_before_status = [
        'attribute_code' => 'hold_before_status',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $order_currency_code = [
        'attribute_code' => 'order_currency_code',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $original_increment_id = [
        'attribute_code' => 'original_increment_id',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $relation_child_id = [
        'attribute_code' => 'relation_child_id',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $relation_child_real_id = [
        'attribute_code' => 'relation_child_real_id',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $relation_parent_id = [
        'attribute_code' => 'relation_parent_id',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $relation_parent_real_id = [
        'attribute_code' => 'relation_parent_real_id',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $remote_ip = [
        'attribute_code' => 'remote_ip',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $shipping_method = [
        'attribute_code' => 'shipping_method',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $store_currency_code = [
        'attribute_code' => 'store_currency_code',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $store_name = [
        'attribute_code' => 'store_name',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $x_forwarded_for = [
        'attribute_code' => 'x_forwarded_for',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $customer_note = [
        'attribute_code' => 'customer_note',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $created_at = [
        'attribute_code' => 'created_at',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $updated_at = [
        'attribute_code' => 'updated_at',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $total_item_count = [
        'attribute_code' => 'total_item_count',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $customer_gender = [
        'attribute_code' => 'customer_gender',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $hidden_tax_amount = [
        'attribute_code' => 'hidden_tax_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_hidden_tax_amount = [
        'attribute_code' => 'base_hidden_tax_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $shipping_hidden_tax_amount = [
        'attribute_code' => 'shipping_hidden_tax_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_shipping_hidden_tax_amnt = [
        'attribute_code' => 'base_shipping_hidden_tax_amnt',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $hidden_tax_invoiced = [
        'attribute_code' => 'hidden_tax_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_hidden_tax_invoiced = [
        'attribute_code' => 'base_hidden_tax_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $hidden_tax_refunded = [
        'attribute_code' => 'hidden_tax_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_hidden_tax_refunded = [
        'attribute_code' => 'base_hidden_tax_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $shipping_incl_tax = [
        'attribute_code' => 'shipping_incl_tax',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_shipping_incl_tax = [
        'attribute_code' => 'base_shipping_incl_tax',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $coupon_rule_name = [
        'attribute_code' => 'coupon_rule_name',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_customer_balance_amount = [
        'attribute_code' => 'base_customer_balance_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $customer_balance_amount = [
        'attribute_code' => 'customer_balance_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_customer_balance_invoiced = [
        'attribute_code' => 'base_customer_balance_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $customer_balance_invoiced = [
        'attribute_code' => 'customer_balance_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_customer_balance_refunded = [
        'attribute_code' => 'base_customer_balance_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $customer_balance_refunded = [
        'attribute_code' => 'customer_balance_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $bs_customer_bal_total_refunded = [
        'attribute_code' => 'bs_customer_bal_total_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $customer_bal_total_refunded = [
        'attribute_code' => 'customer_bal_total_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gift_cards = [
        'attribute_code' => 'gift_cards',
        'backend_type' => 'text',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_gift_cards_amount = [
        'attribute_code' => 'base_gift_cards_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gift_cards_amount = [
        'attribute_code' => 'gift_cards_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_gift_cards_invoiced = [
        'attribute_code' => 'base_gift_cards_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gift_cards_invoiced = [
        'attribute_code' => 'gift_cards_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_gift_cards_refunded = [
        'attribute_code' => 'base_gift_cards_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gift_cards_refunded = [
        'attribute_code' => 'gift_cards_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gift_message_id = [
        'attribute_code' => 'gift_message_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_id = [
        'attribute_code' => 'gw_id',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_allow_gift_receipt = [
        'attribute_code' => 'gw_allow_gift_receipt',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_add_card = [
        'attribute_code' => 'gw_add_card',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_base_price = [
        'attribute_code' => 'gw_base_price',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_price = [
        'attribute_code' => 'gw_price',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_items_base_price = [
        'attribute_code' => 'gw_items_base_price',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_items_price = [
        'attribute_code' => 'gw_items_price',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_card_base_price = [
        'attribute_code' => 'gw_card_base_price',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_card_price = [
        'attribute_code' => 'gw_card_price',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_base_tax_amount = [
        'attribute_code' => 'gw_base_tax_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_tax_amount = [
        'attribute_code' => 'gw_tax_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_items_base_tax_amount = [
        'attribute_code' => 'gw_items_base_tax_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_items_tax_amount = [
        'attribute_code' => 'gw_items_tax_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_card_base_tax_amount = [
        'attribute_code' => 'gw_card_base_tax_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_card_tax_amount = [
        'attribute_code' => 'gw_card_tax_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_base_price_invoiced = [
        'attribute_code' => 'gw_base_price_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_price_invoiced = [
        'attribute_code' => 'gw_price_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_items_base_price_invoiced = [
        'attribute_code' => 'gw_items_base_price_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_items_price_invoiced = [
        'attribute_code' => 'gw_items_price_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_card_base_price_invoiced = [
        'attribute_code' => 'gw_card_base_price_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_card_price_invoiced = [
        'attribute_code' => 'gw_card_price_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_base_tax_amount_invoiced = [
        'attribute_code' => 'gw_base_tax_amount_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_tax_amount_invoiced = [
        'attribute_code' => 'gw_tax_amount_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_items_base_tax_invoiced = [
        'attribute_code' => 'gw_items_base_tax_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_items_tax_invoiced = [
        'attribute_code' => 'gw_items_tax_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_card_base_tax_invoiced = [
        'attribute_code' => 'gw_card_base_tax_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_card_tax_invoiced = [
        'attribute_code' => 'gw_card_tax_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_base_price_refunded = [
        'attribute_code' => 'gw_base_price_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_price_refunded = [
        'attribute_code' => 'gw_price_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_items_base_price_refunded = [
        'attribute_code' => 'gw_items_base_price_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_items_price_refunded = [
        'attribute_code' => 'gw_items_price_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_card_base_price_refunded = [
        'attribute_code' => 'gw_card_base_price_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_card_price_refunded = [
        'attribute_code' => 'gw_card_price_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_base_tax_amount_refunded = [
        'attribute_code' => 'gw_base_tax_amount_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_tax_amount_refunded = [
        'attribute_code' => 'gw_tax_amount_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_items_base_tax_refunded = [
        'attribute_code' => 'gw_items_base_tax_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_items_tax_refunded = [
        'attribute_code' => 'gw_items_tax_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_card_base_tax_refunded = [
        'attribute_code' => 'gw_card_base_tax_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $gw_card_tax_refunded = [
        'attribute_code' => 'gw_card_tax_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $paypal_ipn_customer_notified = [
        'attribute_code' => 'paypal_ipn_customer_notified',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '0',
        'input' => '',
    ];

    protected $reward_points_balance = [
        'attribute_code' => 'reward_points_balance',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_reward_currency_amount = [
        'attribute_code' => 'base_reward_currency_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $reward_currency_amount = [
        'attribute_code' => 'reward_currency_amount',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_rwrd_crrncy_amt_invoiced = [
        'attribute_code' => 'base_rwrd_crrncy_amt_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $rwrd_currency_amount_invoiced = [
        'attribute_code' => 'rwrd_currency_amount_invoiced',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_rwrd_crrncy_amnt_refnded = [
        'attribute_code' => 'base_rwrd_crrncy_amnt_refnded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $rwrd_crrncy_amnt_refunded = [
        'attribute_code' => 'rwrd_crrncy_amnt_refunded',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $reward_points_balance_refund = [
        'attribute_code' => 'reward_points_balance_refund',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $reward_points_balance_refunded = [
        'attribute_code' => 'reward_points_balance_refunded',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $reward_salesrule_points = [
        'attribute_code' => 'reward_salesrule_points',
        'backend_type' => 'int',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $id = [
        'attribute_code' => 'id',
        'backend_type' => 'virtual',
    ];

    protected $price = [
        'attribute_code' => 'price',
        'backend_type' => 'virtual',
        'is_required' => '1',
        'group' => null,
        'source' => 'Magento\Sales\Test\Fixture\OrderInjectable\Price',
    ];

    public function getEntityId()
    {
        return $this->getData('entity_id');
    }

    public function getState()
    {
        return $this->getData('state');
    }

    public function getStatus()
    {
        return $this->getData('status');
    }

    public function getCouponCode()
    {
        return $this->getData('coupon_code');
    }

    public function getProtectCode()
    {
        return $this->getData('protect_code');
    }

    public function getShippingDescription()
    {
        return $this->getData('shipping_description');
    }

    public function getIsVirtual()
    {
        return $this->getData('is_virtual');
    }

    public function getStoreId()
    {
        return $this->getData('store_id');
    }

    public function getCustomerId()
    {
        return $this->getData('customer_id');
    }

    public function getBaseDiscountAmount()
    {
        return $this->getData('base_discount_amount');
    }

    public function getBaseDiscountCanceled()
    {
        return $this->getData('base_discount_canceled');
    }

    public function getBaseDiscountInvoiced()
    {
        return $this->getData('base_discount_invoiced');
    }

    public function getBaseDiscountRefunded()
    {
        return $this->getData('base_discount_refunded');
    }

    public function getBaseGrandTotal()
    {
        return $this->getData('base_grand_total');
    }

    public function getBaseShippingAmount()
    {
        return $this->getData('base_shipping_amount');
    }

    public function getBaseShippingCanceled()
    {
        return $this->getData('base_shipping_canceled');
    }

    public function getBaseShippingInvoiced()
    {
        return $this->getData('base_shipping_invoiced');
    }

    public function getBaseShippingRefunded()
    {
        return $this->getData('base_shipping_refunded');
    }

    public function getBaseShippingTaxAmount()
    {
        return $this->getData('base_shipping_tax_amount');
    }

    public function getBaseShippingTaxRefunded()
    {
        return $this->getData('base_shipping_tax_refunded');
    }

    public function getBaseSubtotal()
    {
        return $this->getData('base_subtotal');
    }

    public function getBaseSubtotalCanceled()
    {
        return $this->getData('base_subtotal_canceled');
    }

    public function getBaseSubtotalInvoiced()
    {
        return $this->getData('base_subtotal_invoiced');
    }

    public function getBaseSubtotalRefunded()
    {
        return $this->getData('base_subtotal_refunded');
    }

    public function getBaseTaxAmount()
    {
        return $this->getData('base_tax_amount');
    }

    public function getBaseTaxCanceled()
    {
        return $this->getData('base_tax_canceled');
    }

    public function getBaseTaxInvoiced()
    {
        return $this->getData('base_tax_invoiced');
    }

    public function getBaseTaxRefunded()
    {
        return $this->getData('base_tax_refunded');
    }

    public function getBaseToGlobalRate()
    {
        return $this->getData('base_to_global_rate');
    }

    public function getBaseToOrderRate()
    {
        return $this->getData('base_to_order_rate');
    }

    public function getBaseTotalCanceled()
    {
        return $this->getData('base_total_canceled');
    }

    public function getBaseTotalInvoiced()
    {
        return $this->getData('base_total_invoiced');
    }

    public function getBaseTotalInvoicedCost()
    {
        return $this->getData('base_total_invoiced_cost');
    }

    public function getBaseTotalOfflineRefunded()
    {
        return $this->getData('base_total_offline_refunded');
    }

    public function getBaseTotalOnlineRefunded()
    {
        return $this->getData('base_total_online_refunded');
    }

    public function getBaseTotalPaid()
    {
        return $this->getData('base_total_paid');
    }

    public function getBaseTotalQtyOrdered()
    {
        return $this->getData('base_total_qty_ordered');
    }

    public function getBaseTotalRefunded()
    {
        return $this->getData('base_total_refunded');
    }

    public function getDiscountAmount()
    {
        return $this->getData('discount_amount');
    }

    public function getDiscountCanceled()
    {
        return $this->getData('discount_canceled');
    }

    public function getDiscountInvoiced()
    {
        return $this->getData('discount_invoiced');
    }

    public function getDiscountRefunded()
    {
        return $this->getData('discount_refunded');
    }

    public function getGrandTotal()
    {
        return $this->getData('grand_total');
    }

    public function getShippingAmount()
    {
        return $this->getData('shipping_amount');
    }

    public function getShippingCanceled()
    {
        return $this->getData('shipping_canceled');
    }

    public function getShippingInvoiced()
    {
        return $this->getData('shipping_invoiced');
    }

    public function getShippingRefunded()
    {
        return $this->getData('shipping_refunded');
    }

    public function getShippingTaxAmount()
    {
        return $this->getData('shipping_tax_amount');
    }

    public function getShippingTaxRefunded()
    {
        return $this->getData('shipping_tax_refunded');
    }

    public function getStoreToBaseRate()
    {
        return $this->getData('store_to_base_rate');
    }

    public function getStoreToOrderRate()
    {
        return $this->getData('store_to_order_rate');
    }

    public function getSubtotal()
    {
        return $this->getData('subtotal');
    }

    public function getSubtotalCanceled()
    {
        return $this->getData('subtotal_canceled');
    }

    public function getSubtotalInvoiced()
    {
        return $this->getData('subtotal_invoiced');
    }

    public function getSubtotalRefunded()
    {
        return $this->getData('subtotal_refunded');
    }

    public function getTaxAmount()
    {
        return $this->getData('tax_amount');
    }

    public function getTaxCanceled()
    {
        return $this->getData('tax_canceled');
    }

    public function getTaxInvoiced()
    {
        return $this->getData('tax_invoiced');
    }

    public function getTaxRefunded()
    {
        return $this->getData('tax_refunded');
    }

    public function getTotalCanceled()
    {
        return $this->getData('total_canceled');
    }

    public function getTotalInvoiced()
    {
        return $this->getData('total_invoiced');
    }

    public function getTotalOfflineRefunded()
    {
        return $this->getData('total_offline_refunded');
    }

    public function getTotalOnlineRefunded()
    {
        return $this->getData('total_online_refunded');
    }

    public function getTotalPaid()
    {
        return $this->getData('total_paid');
    }

    public function getTotalQtyOrdered()
    {
        return $this->getData('total_qty_ordered');
    }

    public function getTotalRefunded()
    {
        return $this->getData('total_refunded');
    }

    public function getCanShipPartially()
    {
        return $this->getData('can_ship_partially');
    }

    public function getCanShipPartiallyItem()
    {
        return $this->getData('can_ship_partially_item');
    }

    public function getCustomerIsGuest()
    {
        return $this->getData('customer_is_guest');
    }

    public function getCustomerNoteNotify()
    {
        return $this->getData('customer_note_notify');
    }

    public function getBillingAddressId()
    {
        return $this->getData('billing_address_id');
    }

    public function getCustomerGroupId()
    {
        return $this->getData('customer_group_id');
    }

    public function getEditIncrement()
    {
        return $this->getData('edit_increment');
    }

    public function getEmailSent()
    {
        return $this->getData('email_sent');
    }

    public function getForcedShipmentWithInvoice()
    {
        return $this->getData('forced_shipment_with_invoice');
    }

    public function getPaymentAuthExpiration()
    {
        return $this->getData('payment_auth_expiration');
    }

    public function getQuoteAddressId()
    {
        return $this->getData('quote_address_id');
    }

    public function getQuoteId()
    {
        return $this->getData('quote_id');
    }

    public function getShippingAddressId()
    {
        return $this->getData('shipping_address_id');
    }

    public function getAdjustmentNegative()
    {
        return $this->getData('adjustment_negative');
    }

    public function getAdjustmentPositive()
    {
        return $this->getData('adjustment_positive');
    }

    public function getBaseAdjustmentNegative()
    {
        return $this->getData('base_adjustment_negative');
    }

    public function getBaseAdjustmentPositive()
    {
        return $this->getData('base_adjustment_positive');
    }

    public function getBaseShippingDiscountAmount()
    {
        return $this->getData('base_shipping_discount_amount');
    }

    public function getBaseSubtotalInclTax()
    {
        return $this->getData('base_subtotal_incl_tax');
    }

    public function getBaseTotalDue()
    {
        return $this->getData('base_total_due');
    }

    public function getPaymentAuthorizationAmount()
    {
        return $this->getData('payment_authorization_amount');
    }

    public function getShippingDiscountAmount()
    {
        return $this->getData('shipping_discount_amount');
    }

    public function getSubtotalInclTax()
    {
        return $this->getData('subtotal_incl_tax');
    }

    public function getTotalDue()
    {
        return $this->getData('total_due');
    }

    public function getWeight()
    {
        return $this->getData('weight');
    }

    public function getCustomerDob()
    {
        return $this->getData('customer_dob');
    }

    public function getIncrementId()
    {
        return $this->getData('increment_id');
    }

    public function getAppliedRuleIds()
    {
        return $this->getData('applied_rule_ids');
    }

    public function getBaseCurrencyCode()
    {
        return $this->getData('base_currency_code');
    }

    public function getCustomerEmail()
    {
        return $this->getData('customer_email');
    }

    public function getCustomerFirstname()
    {
        return $this->getData('customer_firstname');
    }

    public function getCustomerLastname()
    {
        return $this->getData('customer_lastname');
    }

    public function getCustomerMiddlename()
    {
        return $this->getData('customer_middlename');
    }

    public function getCustomerPrefix()
    {
        return $this->getData('customer_prefix');
    }

    public function getCustomerSuffix()
    {
        return $this->getData('customer_suffix');
    }

    public function getCustomerTaxvat()
    {
        return $this->getData('customer_taxvat');
    }

    public function getDiscountDescription()
    {
        return $this->getData('discount_description');
    }

    public function getExtCustomerId()
    {
        return $this->getData('ext_customer_id');
    }

    public function getExtOrderId()
    {
        return $this->getData('ext_order_id');
    }

    public function getGlobalCurrencyCode()
    {
        return $this->getData('global_currency_code');
    }

    public function getHoldBeforeState()
    {
        return $this->getData('hold_before_state');
    }

    public function getHoldBeforeStatus()
    {
        return $this->getData('hold_before_status');
    }

    public function getOrderCurrencyCode()
    {
        return $this->getData('order_currency_code');
    }

    public function getOriginalIncrementId()
    {
        return $this->getData('original_increment_id');
    }

    public function getRelationChildId()
    {
        return $this->getData('relation_child_id');
    }

    public function getRelationChildRealId()
    {
        return $this->getData('relation_child_real_id');
    }

    public function getRelationParentId()
    {
        return $this->getData('relation_parent_id');
    }

    public function getRelationParentRealId()
    {
        return $this->getData('relation_parent_real_id');
    }

    public function getRemoteIp()
    {
        return $this->getData('remote_ip');
    }

    public function getShippingMethod()
    {
        return $this->getData('shipping_method');
    }

    public function getStoreCurrencyCode()
    {
        return $this->getData('store_currency_code');
    }

    public function getStoreName()
    {
        return $this->getData('store_name');
    }

    public function getXForwardedFor()
    {
        return $this->getData('x_forwarded_for');
    }

    public function getCustomerNote()
    {
        return $this->getData('customer_note');
    }

    public function getCreatedAt()
    {
        return $this->getData('created_at');
    }

    public function getUpdatedAt()
    {
        return $this->getData('updated_at');
    }

    public function getTotalItemCount()
    {
        return $this->getData('total_item_count');
    }

    public function getCustomerGender()
    {
        return $this->getData('customer_gender');
    }

    public function getHiddenTaxAmount()
    {
        return $this->getData('hidden_tax_amount');
    }

    public function getBaseHiddenTaxAmount()
    {
        return $this->getData('base_hidden_tax_amount');
    }

    public function getShippingHiddenTaxAmount()
    {
        return $this->getData('shipping_hidden_tax_amount');
    }

    public function getBaseShippingHiddenTaxAmnt()
    {
        return $this->getData('base_shipping_hidden_tax_amnt');
    }

    public function getHiddenTaxInvoiced()
    {
        return $this->getData('hidden_tax_invoiced');
    }

    public function getBaseHiddenTaxInvoiced()
    {
        return $this->getData('base_hidden_tax_invoiced');
    }

    public function getHiddenTaxRefunded()
    {
        return $this->getData('hidden_tax_refunded');
    }

    public function getBaseHiddenTaxRefunded()
    {
        return $this->getData('base_hidden_tax_refunded');
    }

    public function getShippingInclTax()
    {
        return $this->getData('shipping_incl_tax');
    }

    public function getBaseShippingInclTax()
    {
        return $this->getData('base_shipping_incl_tax');
    }

    public function getCouponRuleName()
    {
        return $this->getData('coupon_rule_name');
    }

    public function getBaseCustomerBalanceAmount()
    {
        return $this->getData('base_customer_balance_amount');
    }

    public function getCustomerBalanceAmount()
    {
        return $this->getData('customer_balance_amount');
    }

    public function getBaseCustomerBalanceInvoiced()
    {
        return $this->getData('base_customer_balance_invoiced');
    }

    public function getCustomerBalanceInvoiced()
    {
        return $this->getData('customer_balance_invoiced');
    }

    public function getBaseCustomerBalanceRefunded()
    {
        return $this->getData('base_customer_balance_refunded');
    }

    public function getCustomerBalanceRefunded()
    {
        return $this->getData('customer_balance_refunded');
    }

    public function getBsCustomerBalTotalRefunded()
    {
        return $this->getData('bs_customer_bal_total_refunded');
    }

    public function getCustomerBalTotalRefunded()
    {
        return $this->getData('customer_bal_total_refunded');
    }

    public function getGiftCards()
    {
        return $this->getData('gift_cards');
    }

    public function getBaseGiftCardsAmount()
    {
        return $this->getData('base_gift_cards_amount');
    }

    public function getGiftCardsAmount()
    {
        return $this->getData('gift_cards_amount');
    }

    public function getBaseGiftCardsInvoiced()
    {
        return $this->getData('base_gift_cards_invoiced');
    }

    public function getGiftCardsInvoiced()
    {
        return $this->getData('gift_cards_invoiced');
    }

    public function getBaseGiftCardsRefunded()
    {
        return $this->getData('base_gift_cards_refunded');
    }

    public function getGiftCardsRefunded()
    {
        return $this->getData('gift_cards_refunded');
    }

    public function getGiftMessageId()
    {
        return $this->getData('gift_message_id');
    }

    public function getGwId()
    {
        return $this->getData('gw_id');
    }

    public function getGwAllowGiftReceipt()
    {
        return $this->getData('gw_allow_gift_receipt');
    }

    public function getGwAddCard()
    {
        return $this->getData('gw_add_card');
    }

    public function getGwBasePrice()
    {
        return $this->getData('gw_base_price');
    }

    public function getGwPrice()
    {
        return $this->getData('gw_price');
    }

    public function getGwItemsBasePrice()
    {
        return $this->getData('gw_items_base_price');
    }

    public function getGwItemsPrice()
    {
        return $this->getData('gw_items_price');
    }

    public function getGwCardBasePrice()
    {
        return $this->getData('gw_card_base_price');
    }

    public function getGwCardPrice()
    {
        return $this->getData('gw_card_price');
    }

    public function getGwBaseTaxAmount()
    {
        return $this->getData('gw_base_tax_amount');
    }

    public function getGwTaxAmount()
    {
        return $this->getData('gw_tax_amount');
    }

    public function getGwItemsBaseTaxAmount()
    {
        return $this->getData('gw_items_base_tax_amount');
    }

    public function getGwItemsTaxAmount()
    {
        return $this->getData('gw_items_tax_amount');
    }

    public function getGwCardBaseTaxAmount()
    {
        return $this->getData('gw_card_base_tax_amount');
    }

    public function getGwCardTaxAmount()
    {
        return $this->getData('gw_card_tax_amount');
    }

    public function getGwBasePriceInvoiced()
    {
        return $this->getData('gw_base_price_invoiced');
    }

    public function getGwPriceInvoiced()
    {
        return $this->getData('gw_price_invoiced');
    }

    public function getGwItemsBasePriceInvoiced()
    {
        return $this->getData('gw_items_base_price_invoiced');
    }

    public function getGwItemsPriceInvoiced()
    {
        return $this->getData('gw_items_price_invoiced');
    }

    public function getGwCardBasePriceInvoiced()
    {
        return $this->getData('gw_card_base_price_invoiced');
    }

    public function getGwCardPriceInvoiced()
    {
        return $this->getData('gw_card_price_invoiced');
    }

    public function getGwBaseTaxAmountInvoiced()
    {
        return $this->getData('gw_base_tax_amount_invoiced');
    }

    public function getGwTaxAmountInvoiced()
    {
        return $this->getData('gw_tax_amount_invoiced');
    }

    public function getGwItemsBaseTaxInvoiced()
    {
        return $this->getData('gw_items_base_tax_invoiced');
    }

    public function getGwItemsTaxInvoiced()
    {
        return $this->getData('gw_items_tax_invoiced');
    }

    public function getGwCardBaseTaxInvoiced()
    {
        return $this->getData('gw_card_base_tax_invoiced');
    }

    public function getGwCardTaxInvoiced()
    {
        return $this->getData('gw_card_tax_invoiced');
    }

    public function getGwBasePriceRefunded()
    {
        return $this->getData('gw_base_price_refunded');
    }

    public function getGwPriceRefunded()
    {
        return $this->getData('gw_price_refunded');
    }

    public function getGwItemsBasePriceRefunded()
    {
        return $this->getData('gw_items_base_price_refunded');
    }

    public function getGwItemsPriceRefunded()
    {
        return $this->getData('gw_items_price_refunded');
    }

    public function getGwCardBasePriceRefunded()
    {
        return $this->getData('gw_card_base_price_refunded');
    }

    public function getGwCardPriceRefunded()
    {
        return $this->getData('gw_card_price_refunded');
    }

    public function getGwBaseTaxAmountRefunded()
    {
        return $this->getData('gw_base_tax_amount_refunded');
    }

    public function getGwTaxAmountRefunded()
    {
        return $this->getData('gw_tax_amount_refunded');
    }

    public function getGwItemsBaseTaxRefunded()
    {
        return $this->getData('gw_items_base_tax_refunded');
    }

    public function getGwItemsTaxRefunded()
    {
        return $this->getData('gw_items_tax_refunded');
    }

    public function getGwCardBaseTaxRefunded()
    {
        return $this->getData('gw_card_base_tax_refunded');
    }

    public function getGwCardTaxRefunded()
    {
        return $this->getData('gw_card_tax_refunded');
    }

    public function getPaypalIpnCustomerNotified()
    {
        return $this->getData('paypal_ipn_customer_notified');
    }

    public function getRewardPointsBalance()
    {
        return $this->getData('reward_points_balance');
    }

    public function getBaseRewardCurrencyAmount()
    {
        return $this->getData('base_reward_currency_amount');
    }

    public function getRewardCurrencyAmount()
    {
        return $this->getData('reward_currency_amount');
    }

    public function getBaseRwrdCrrncyAmtInvoiced()
    {
        return $this->getData('base_rwrd_crrncy_amt_invoiced');
    }

    public function getRwrdCurrencyAmountInvoiced()
    {
        return $this->getData('rwrd_currency_amount_invoiced');
    }

    public function getBaseRwrdCrrncyAmntRefnded()
    {
        return $this->getData('base_rwrd_crrncy_amnt_refnded');
    }

    public function getRwrdCrrncyAmntRefunded()
    {
        return $this->getData('rwrd_crrncy_amnt_refunded');
    }

    public function getRewardPointsBalanceRefund()
    {
        return $this->getData('reward_points_balance_refund');
    }

    public function getRewardPointsBalanceRefunded()
    {
        return $this->getData('reward_points_balance_refunded');
    }

    public function getRewardSalesrulePoints()
    {
        return $this->getData('reward_salesrule_points');
    }

    public function getId()
    {
        return $this->getData('id');
    }

    public function getPrice()
    {
        return $this->getData('price');
    }
}
