<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Sales_Model_Entity_Setup extends Mage_Eav_Model_Entity_Setup
{
    public function getDefaultEntities()
    {
        return array(
            'quote'=>array(
                'entity_model'      => 'sales/quote',
                'table'=>'sales/quote',
                'increment_model'=>'eav/entity_increment_alphanum',
                'increment_per_store'=>true,
                'attributes' => array(
                    'entity_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/quote_attribute_backend_parent'),
                    'is_active' => array('type'=>'static', 'visible'=>false),
                    'store_id'  => array('type'=>'static'),
                    'remote_ip' => array('visible'=>false),
                    'checkout_method'   => array(),
                    'password_hash'     => array(),
                    'quote_status_id'   => array(
                        'label'=>'Quote Status',
                        'type'=>'int',
                        'source'=>'sales_entity/quote_attribute_source_status'
                    ),
                    'billing_address_id'=> array('type'=>'int', 'visible'=>false),
                    'orig_order_id'     => array('label'=>'Original order ID'),
                    'converted_at'      => array('type'=>'datetime', 'visible'=>false),

                    'coupon_code'   => array('label'=>'Coupon'),
                    'giftcert_code' => array('label'=>'Gift certificate'),
                    'base_currency_code'    => array('label'=>'Base currency'),
                    'store_currency_code'   => array('label'=>'Store currency'),
                    'quote_currency_code'   => array('label'=>'Quote currency'),
                    'store_to_base_rate'    => array('type'=>'decimal', 'label'=>'Store to Base rate'),
                    'store_to_quote_rate'   => array('type'=>'decimal', 'label'=>'Store to Quote rate'),

                    'custbalance_amount' => array('type'=>'decimal'),
                    'grand_total' => array('type'=>'decimal'),

                    'base_grand_total' => array('type'=>'decimal'),

                    'applied_rule_ids' => array('type'=>'text', 'visible'=>false),

                    'is_virtual'        => array('type'=>'int', 'visible'=>false),
                    'is_multi_shipping' => array('type'=>'int', 'visible'=>false),
                    'is_multi_payment'  => array('type'=>'int', 'visible'=>false),

                    'customer_id' => array('type'=>'int', 'visible'=>false),
                    'customer_tax_class_id' => array('type'=>'int', 'visible'=>false),
                    'customer_group_id' => array('type'=>'int', 'visible'=>false),
                    'customer_email'    => array('type'=>'varchar', 'visible'=>false),
                    'customer_firstname'=> array('type'=>'varchar', 'visible'=>false),
                    'customer_lastname' => array('type'=>'varchar', 'visible'=>false),
                    'customer_note'     => array('type'=>'text', 'visible'=>false),
                    'customer_note_notify' => array('type'=>'int', 'visible'=>false),
                    'customer_is_guest' => array('type'=>'int', 'visible'=>false),
                ),
            ),
            'quote_address' => array(
                'entity_model'      => 'sales/quote_address',
                'table'=>'sales/quote',
                'attributes' => array(
                    'entity_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/quote_address_attribute_backend_parent',
                        'visible'=>false),
                    'parent_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/quote_attribute_backend_child',
                        'visible'=>false),
                    'address_type' => array('visible'=>false),

                    'customer_id' => array('type'=>'int', 'visible'=>false),
                    'customer_address_id' => array('type'=>'int', 'visible'=>false),
                    'email'     => array('label'=>'Email', 'visible'=>false),
                    'firstname' => array('label'=>'First Name'),
                    'lastname'  => array('label'=>'Last Name'),
                    'company'   => array('label'=>'Company'),
                    'street'    => array('label'=>'Street Address'),
                    'city'      => array('label'=>'City'),
                    'region'    => array('label'=>'State/Province'),
                    'region_id' => array('type'=>'int', 'visible'=>false),
                    'postcode'  => array('label'=>'Zip/Postal Code'),
                    'country_id'=> array('type'=>'varchar', 'visible'=>false),
                    'telephone' => array('label'=>'Telephone'),
                    'fax'       => array('label'=>'Fax'),

                    'same_as_billing'   => array('type'=>'int', 'label'=>'Same as billing', 'visible'=>false),
                    'free_shipping'     => array('type'=>'int'),

                    'weight' => array('type'=>'decimal', 'label'=>'Weight', 'visible'=>false),
                    'collect_shipping_rates' => array('type'=>'int'),

                    'shipping_method'       => array('label'=>'Shipping Method', 'visible'=>false),
                    'shipping_description'  => array('type'=>'text', 'visible'=>false),

                    'subtotal' => array('type'=>'decimal', 'visible'=>false),
                    'subtotal_with_discount' => array('type'=>'decimal', 'visible'=>false),
                    'tax_amount'        => array('type'=>'decimal', 'visible'=>false),
                    'shipping_amount'   => array('type'=>'decimal', 'visible'=>false),
                    'discount_amount'   => array('type'=>'decimal', 'visible'=>false),
                    'custbalance_amount'=> array('type'=>'decimal', 'visible'=>false),
                    'grand_total'       => array('type'=>'decimal', 'visible'=>false),

                    'base_subtotal' => array('type'=>'decimal', 'visible'=>false),
                    'base_subtotal_with_discount' => array('type'=>'decimal', 'visible'=>false),
                    'base_tax_amount'        => array('type'=>'decimal', 'visible'=>false),
                    'base_shipping_amount'   => array('type'=>'decimal', 'visible'=>false),
                    'base_discount_amount'   => array('type'=>'decimal', 'visible'=>false),
                    'base_custbalance_amount'=> array('type'=>'decimal', 'visible'=>false),
                    'base_grand_total'       => array('type'=>'decimal', 'visible'=>false),

                    'customer_notes' => array('type'=>'text', 'label'=>'Customer Notes'),
                ),
            ),
            'quote_address_rate' => array(
                'entity_model'      => 'sales/quote_address_rate',
                'table'=>'sales/quote_temp',
                'attributes' => array(
                    'parent_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/quote_address_attribute_backend_child'),
                    'code'      => array(),
                    'carrier'   => array(),
                    'carrier_title' => array(),
                    'method'    => array(),
                    'method_description' => array('type'=>'text'),
                    'price'     => array('type'=>'decimal'),
                    'error_message' => array('type'=>'text'),
                ),
            ),
            'quote_address_item' => array(
                'entity_model'      => 'sales/quote_address_item',
                'table'=>'sales/quote_temp',
                'attributes' => array(
                    'parent_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/quote_address_attribute_backend_child'),
                    'quote_item_id' => array('type'=>'int'),
                    'product_id' => array('type'=>'int'),
                    'super_product_id' => array('type'=>'int'),
                    'parent_product_id' => array('type'=>'int'),
                    'sku'   => array(),
                    'image' => array(),
                    'name'  => array(),
                    'description' => array('type'=>'text'),

                    'weight' => array('type'=>'decimal'),
                    'free_shipping' => array('type'=>'int'),
                    'qty' => array('type'=>'decimal'),
                    'is_qty_decimal' => array('type'=>'int'),

                    'price'             => array('type'=>'decimal'),
                    'discount_percent'  => array('type'=>'decimal'),
                    'discount_amount'   => array('type'=>'decimal'),
                    'no_discount'       => array('type'=>'int'),
                    'tax_percent'       => array('type'=>'decimal'),
                    'tax_amount'        => array('type'=>'decimal'),
                    'row_total'         => array('type'=>'decimal'),
                    'row_total_with_discount' => array('type'=>'decimal'),

                    'base_price'             => array('type'=>'decimal'),
                    'base_discount_amount'   => array('type'=>'decimal'),
                    'base_tax_amount'        => array('type'=>'decimal'),
                    'base_row_total'         => array('type'=>'decimal'),

                    'row_weight'        => array('type'=>'decimal'),
                    'applied_rule_ids'  => array(),
                    'additional_data'   => array('type'=>'text'),
                ),
            ),
            'quote_item' => array(
                'entity_model'      => 'sales/quote_item',
                'table'=>'sales/quote',
                'attributes' => array(
                    'parent_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/quote_attribute_backend_child'),
                    'product_id'        => array('type'=>'int'),
                    'super_product_id'  => array('type'=>'int'),
                    'parent_product_id' => array('type'=>'int'),
                    'sku'   => array(),
                    'image' => array(),
                    'name'  => array(),
                    'description' => array('type'=>'text'),

                    'weight' => array('type'=>'decimal'),
                    'free_shipping' => array('type'=>'int'),
                    'qty' => array('type'=>'decimal'),
                    'is_qty_decimal' => array('type'=>'int'),

                    'price'             => array('type'=>'decimal'),
                    'discount_percent'  => array('type'=>'decimal'),
                    'discount_amount'   => array('type'=>'decimal'),
                    'no_discount'       => array('type'=>'int'),
                    'tax_percent'       => array('type'=>'decimal'),
                    'tax_amount'        => array('type'=>'decimal'),
                    'row_total'         => array('type'=>'decimal'),
                    'row_total_with_discount' => array('type'=>'decimal'),

                    'base_price'             => array('type'=>'decimal'),
                    'base_discount_amount'   => array('type'=>'decimal'),
                    'base_tax_amount'        => array('type'=>'decimal'),
                    'base_row_total'         => array('type'=>'decimal'),

                    'row_weight' => array('type'=>'decimal'),
                    'applied_rule_ids' => array(),
                    'additional_data'   => array('type'=>'text'),
                ),
            ),
            'quote_payment' => array(
                'entity_model'      => 'sales/quote_payment',
                'table'=>'sales/quote',
                'attributes' => array(
                    'parent_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/quote_attribute_backend_child'),
                    'method' => array(),
                    'additional_data' => array('type'=>'text'),
                    'po_number' => array(),
                    'cc_type' => array(),
                    'cc_number_enc' => array(),
                    'cc_last4' => array(),
                    'cc_owner' => array(),
                    'cc_exp_month' => array('type'=>'int'),
                    'cc_exp_year' => array('type'=>'int'),
                    'cc_cid_enc' => array(),
                    'cc_ss_issue' => array(),
                    'cc_ss_start_month' => array('type'=>'int'),
                    'cc_ss_start_year' => array('type'=>'int'),
                ),
            ),

            'order' => array(
                'entity_model'      => 'sales/order',
                'table'=>'sales/order',
                'increment_model'=>'eav/entity_increment_numeric',
                'increment_per_store'=>true,
                'backend_prefix'=>'sales_entity/order_attribute_backend',
                'attributes' => array(
                    'entity_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_attribute_backend_parent'
                    ),
                    'store_id'  => array('type'=>'static'),
                    'store_name' => array('type'=>'varchar'),
                    'remote_ip' => array(),

                    'status'    => array('type'=>'varchar'),
                    'state'     => array('type'=>'varchar'),
                    'hold_before_status' => array('type'=>'varchar'),
                    'hold_before_state'  => array('type'=>'varchar'),

                    'relation_parent_id'        => array('type'=>'varchar'),
                    'relation_parent_real_id'   => array('type'=>'varchar'),
                    'relation_child_id'         => array('type'=>'varchar'),
                    'relation_child_real_id'    => array('type'=>'varchar'),

                    'ext_order_id'         => array('type'=>'varchar'),
                    'ext_customer_id'      => array('type'=>'varchar'),

                    'quote_id' => array('type'=>'int'),
                    'quote_address_id' => array('type'=>'int'),
                    'billing_address_id' => array('type'=>'int', 'backend'=>'_billing'),
                    'shipping_address_id' => array('type'=>'int', 'backend'=>'_shipping'),

                    'coupon_code'       => array(),
                    'applied_rule_ids'  => array(),
                    'giftcert_code'     => array(),

                    'base_currency_code'    => array(),
                    'store_currency_code'   => array(),
                    'order_currency_code'   => array(),
                    'store_to_base_rate'    => array('type'=>'decimal'),
                    'store_to_order_rate'   => array('type'=>'decimal'),

                    'is_virtual'        => array('type'=>'int'),
                    'is_multi_payment'  => array('type'=>'int'),

                    'shipping_method' => array(),
                    'shipping_description' => array(),
                    'weight' => array('type'=>'decimal'),

                    'tax_amount'        => array('type'=>'static'),
                    'shipping_amount'   => array('type'=>'static'),
                    'discount_amount'   => array('type'=>'static'),
                    'giftcert_amount'   => array('type'=>'decimal'),
                    'custbalance_amount'=> array('type'=>'decimal'),

                    'subtotal'          => array('type'=>'static'),
                    'grand_total'       => array('type'=>'static'),
                    'total_paid'        => array('type'=>'static'),
                    'total_due'         => array('type'=>'decimal'),
                    'total_refunded'    => array('type'=>'static'),
                    'total_qty_ordered' => array('type'=>'static'),
                    'total_canceled'    => array('type'=>'static'),
                    'total_invoiced'    => array('type'=>'static'),
                    'total_online_refunded' => array('type'=>'static'),
                    'total_offline_refunded'=> array('type'=>'static'),
                    'adjustment_positive' => array('type'=>'decimal'),
                    'adjustment_negative' => array('type'=>'decimal'),

                    'base_tax_amount'        => array('type'=>'static'),
                    'base_shipping_amount'   => array('type'=>'static'),
                    'base_discount_amount'   => array('type'=>'static'),
                    'base_giftcert_amount'   => array('type'=>'decimal'),
                    'base_custbalance_amount'=> array('type'=>'decimal'),

                    'base_subtotal'          => array('type'=>'static'),
                    'base_grand_total'       => array('type'=>'static'),
                    'base_total_paid'        => array('type'=>'static'),
                    'base_total_due'         => array('type'=>'decimal'),
                    'base_total_refunded'    => array('type'=>'static'),
                    'base_total_qty_ordered' => array('type'=>'static'),
                    'base_total_canceled'    => array('type'=>'static'),
                    'base_total_invoiced'    => array('type'=>'static'),
                    'base_total_online_refunded' => array('type'=>'static'),
                    'base_total_offline_refunded'=> array('type'=>'static'),
                    'base_adjustment_positive' => array('type'=>'decimal'),
                    'base_adjustment_negative' => array('type'=>'decimal'),

                    'customer_id'       => array('type'=>'static', 'visible'=>false),
                    'customer_group_id' => array('type'=>'int', 'visible'=>false),
                    'customer_email'    => array('type'=>'varchar', 'visible'=>false),
                    'customer_firstname'=> array('type'=>'varchar', 'visible'=>false),
                    'customer_lastname' => array('type'=>'varchar', 'visible'=>false),
                    'customer_note'     => array('type'=>'text', 'visible'=>false),
                    'customer_note_notify' => array('type'=>'int', 'visible'=>false),
                    'customer_is_guest' => array('type'=>'int', 'visible'=>false),
                    'email_sent' => array('type'=>'int', 'visible'=>false),
                ),
            ),
            'order_address' => array(
                'entity_model'      => 'sales/order_address',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id' => array('type'=>'static', 'backend'=>'sales_entity/order_attribute_backend_child'),
                    'quote_address_id' => array('type'=>'int'),
                    'address_type' => array(),
                    'customer_id' => array('type'=>'int'),
                    'customer_address_id' => array('type'=>'int'),
                    'email' => array(),
                    'firstname' => array(),
                    'lastname'  => array(),
                    'company'   => array(),
                    'street'    => array(),
                    'city'      => array(),
                    'region'    => array(),
                    'region_id' => array('type'=>'int'),
                    'postcode'  => array(),
                    'country_id'=> array('type'=>'varchar'),
                    'telephone' => array(),
                    'fax'       => array(),

                ),
            ),
            'order_item' => array(
                'entity_model'      => 'sales/order_item',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_attribute_backend_child'
                    ),

                    'quote_item_id'     => array('type'=>'int'),
                    'product_id'        => array('type'=>'int'),
                    'super_product_id'  => array('type'=>'int'),
                    'parent_product_id' => array('type'=>'int'),
                    'sku'               => array(),
                    'name'              => array(),
                    'description'       => array('type'=>'text'),
                    'weight'            => array('type'=>'decimal'),

                    'is_qty_decimal'    => array('type'=>'int'),
                    'qty_ordered'       => array('type'=>'decimal'),
                    'qty_backordered'   => array('type'=>'decimal'),
                    'qty_invoiced'      => array('type'=>'decimal'),
                    'qty_canceled'      => array('type'=>'decimal'),
                    'qty_shipped'       => array('type'=>'decimal'),
                    'qty_refunded'      => array('type'=>'decimal'),

                    'original_price'    => array('type'=>'decimal'),
                    'price'             => array('type'=>'decimal'),
                    'cost'              => array('type'=>'decimal'),

                    'discount_percent'  => array('type'=>'decimal'),
                    'discount_amount'   => array('type'=>'decimal'),
                    'discount_invoiced' => array('type'=>'decimal'),

                    'tax_percent'       => array('type'=>'decimal'),
                    'tax_amount'        => array('type'=>'decimal'),
                    'tax_invoiced'      => array('type'=>'decimal'),

                    'row_total'         => array('type'=>'decimal'),
                    'row_weight'        => array('type'=>'decimal'),
                    'row_invoiced'      => array('type'=>'decimal'),
                    'invoiced_total'    => array('type'=>'decimal'),
                    'amount_refunded'   => array('type'=>'decimal'),

                    'base_price'             => array('type'=>'decimal'),
                    'base_original_price'    => array('type'=>'decimal'),
                    'base_discount_amount'   => array('type'=>'decimal'),
                    'base_discount_invoiced' => array('type'=>'decimal'),
                    'base_tax_amount'        => array('type'=>'decimal'),
                    'base_tax_invoiced'      => array('type'=>'decimal'),
                    'base_row_total'         => array('type'=>'decimal'),
                    'base_row_invoiced'      => array('type'=>'decimal'),
                    'base_invoiced_total'    => array('type'=>'decimal'),
                    'base_amount_refunded'   => array('type'=>'decimal'),

                    'applied_rule_ids'  => array(),
                    'additional_data'   => array('type'=>'text'),
                ),
            ),
            'order_payment' => array(
                'entity_model'      => 'sales/order_payment',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_attribute_backend_child'
                    ),
                    'quote_payment_id'      => array('type'=>'int'),
                    'method'                => array(),
                    'additional_data'       => array('type'=>'text'),

                    'po_number'     => array(),

                    'cc_type'       => array(),
                    'cc_number_enc' => array(),
                    'cc_last4'      => array(),
                    'cc_owner'      => array(),
                    'cc_exp_month'  => array(),
                    'cc_exp_year'   => array(),

                    'cc_ss_issue' => array(),
                    'cc_ss_start_month' => array(),
                    'cc_ss_start_year' => array(),

                    'cc_status'             => array(),
                    'cc_status_description' => array(),
                    'cc_trans_id'           => array(),
                    'cc_approval'           => array(),
                    'cc_avs_status'         => array(),
                    'cc_cid_status'         => array(),

                    'cc_debug_request_body' => array(),
                    'cc_debug_response_body'=> array(),
                    'cc_debug_response_serialized' => array(),

                    'anet_trans_method'     => array(),
                    'echeck_routing_number' => array(),
                    'echeck_bank_name'      => array(),
                    'echeck_account_type'   => array(),
                    'echeck_account_name'   => array(),
                    'echeck_type'           => array(),

                    'amount_ordered'    => array('type'=>'decimal'),
                    'amount_authorized' => array('type'=>'decimal'),
                    'amount_paid'       => array('type'=>'decimal'),
                    'amount_canceled'   => array('type'=>'decimal'),
                    'amount_refunded'   => array('type'=>'decimal'),
                    'shipping_amount'   => array('type'=>'decimal'),
                    'shipping_captured' => array('type'=>'decimal'),
                    'shipping_refunded' => array('type'=>'decimal'),

                    'base_amount_ordered'    => array('type'=>'decimal'),
                    'base_amount_authorized' => array('type'=>'decimal'),
                    'base_amount_paid'       => array('type'=>'decimal'),
                    'base_amount_canceled'   => array('type'=>'decimal'),
                    'base_amount_refunded'   => array('type'=>'decimal'),
                    'base_shipping_amount'   => array('type'=>'decimal'),
                    'base_shipping_captured' => array('type'=>'decimal'),
                    'base_shipping_refunded' => array('type'=>'decimal'),
                ),
            ),

            'order_status_history' => array(
                'entity_model'      => 'sales/order_status_history',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_attribute_backend_child'
                    ),
                    'status'    => array('type'=>'varchar'),
                    'comment'   => array('type'=>'text'),
                    'is_customer_notified' => array('type'=>'int'),
                ),
            ),

            'invoice' => array(
                'entity_model'      => 'sales/order_invoice',
                //'table'=>'sales/invoice',
                'table'=>'sales/order_entity',
                'increment_model'=>'eav/entity_increment_numeric',
                'increment_per_store'=>true,
                'backend_prefix'=>'sales_entity/order_attribute_backend',
                'attributes' => array(
                    'entity_id'     => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_invoice_attribute_backend_parent'
                    ),
                    'state'    => array('type'=>'int'),
                    'is_used_for_refund' => array('type'=>'int'),
                    'transaction_id' => array(),


                    'order_id'              => array(
                        'type'=>'int',
                        'backend'=>'sales_entity/order_invoice_attribute_backend_order'
                    ),

                    'billing_address_id'    => array('type'=>'int'),
                    'shipping_address_id'   => array('type'=>'int'),

                    'base_currency_code'    => array(),
                    'store_currency_code'   => array(),
                    'order_currency_code'   => array(),
                    'store_to_base_rate'    => array('type'=>'decimal'),
                    'store_to_order_rate'   => array('type'=>'decimal'),

                    'subtotal'          => array('type'=>'decimal'),
                    'discount_amount'   => array('type'=>'decimal'),
                    'tax_amount'        => array('type'=>'decimal'),
                    'shipping_amount'   => array('type'=>'decimal'),
                    'grand_total'       => array('type'=>'decimal'),
                    'total_qty'         => array('type'=>'decimal'),

                    'can_void_flag'     => array('type'=>'int'),

                    'base_subtotal'          => array('type'=>'decimal'),
                    'base_discount_amount'   => array('type'=>'decimal'),
                    'base_tax_amount'        => array('type'=>'decimal'),
                    'base_shipping_amount'   => array('type'=>'decimal'),
                    'base_grand_total'       => array('type'=>'decimal'),
                    'email_sent' => array('type'=>'int'),
                ),
            ),

            'invoice_item' => array(
                'entity_model'      => 'sales/order_invoice_item',
                //'table'=>'sales/invoice',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id'     => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_invoice_attribute_backend_child'
                    ),
                    'order_item_id' => array('type'=>'int'),
                    'product_id'    => array('type'=>'int'),
                    'name'          => array(),
                    'description'   => array('type'=>'text'),
                    'sku'           => array(),
                    'qty'           => array('type'=>'decimal'),
                    'cost'          => array('type'=>'decimal'),
                    'price'         => array('type'=>'decimal'),
                    'discount_amount' => array('type'=>'decimal'),
                    'tax_amount'    => array('type'=>'decimal'),
                    'row_total'     => array('type'=>'decimal'),

                    'base_price'         => array('type'=>'decimal'),
                    'base_discount_amount' => array('type'=>'decimal'),
                    'base_tax_amount'    => array('type'=>'decimal'),
                    'base_row_total'     => array('type'=>'decimal'),

                    'additional_data'   => array('type'=>'text'),
                ),
            ),

            'invoice_comment' => array(
                'entity_model'      => 'sales/order_invoice_comment',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_invoice_attribute_backend_child'
                    ),
                    'comment' => array('type'=>'text'),
                    'is_customer_notified' => array('type'=>'int'),
                ),
            ),



            'shipment' => array(
                'entity_model'      => 'sales/order_shipment',
                //'table'=>'sales/shipment',
                'table'=>'sales/order_entity',
                'increment_model'=>'eav/entity_increment_numeric',
                'increment_per_store'=>true,
                'backend_prefix'=>'sales_entity/order_attribute_backend',
                'attributes' => array(
                    'entity_id'     => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_shipment_attribute_backend_parent'
                    ),

                    'customer_id'   => array('type'=>'int'),
                    'order_id'      => array('type'=>'int'),
                    'shipment_status'     => array('type'=>'int'),
                    'billing_address_id'    => array('type'=>'int'),
                    'shipping_address_id'   => array('type'=>'int'),

                    'total_qty'         => array('type'=>'decimal'),
                    'total_weight'      => array('type'=>'decimal'),
                    'email_sent'        => array('type'=>'int'),
                ),
            ),

            'shipment_item' => array(
                'entity_model'      => 'sales/order_shipment_item',
                //'table'=>'sales/shipment',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id'     => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_shipment_attribute_backend_child'
                    ),
                    'order_item_id' => array('type'=>'int'),
                    'product_id'    => array('type'=>'int'),
                    'name'          => array(),
                    'description'   => array('type'=>'text'),
                    'sku'           => array(),
                    'qty'           => array('type'=>'decimal'),
                    'price'         => array('type'=>'decimal'),
                    'weight'        => array('type'=>'decimal'),
                    'row_total'     => array('type'=>'decimal'),

                    'additional_data'   => array('type'=>'text'),
                ),
            ),

            'shipment_comment' => array(
                'entity_model'      => 'sales/order_shipment_comment',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_shipment_attribute_backend_child'
                    ),
                    'comment' => array('type'=>'text'),
                    'is_customer_notified' => array('type'=>'int'),
                ),
            ),

            'shipment_track' => array(
                'entity_model'      => 'sales/order_shipment_track',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id'     => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_shipment_attribute_backend_child'
                    ),
                    'order_id'      => array('type'=>'int'),
                    'number'        => array('type'=>'text'),
                    'carrier_code'  => array('type'=>'varchar'),
                    'title'         => array('type'=>'varchar'),
                    'description'   => array('type'=>'text'),
                    'qty'           => array('type'=>'decimal'),
                    'weight'        => array('type'=>'decimal'),
                ),
            ),

            'creditmemo' => array(
                'entity_model'      => 'sales/order_creditmemo',
                //'table'=>'sales/creditmemo',
                'table'=>'sales/order_entity',
                'increment_model'=>'eav/entity_increment_numeric',
                'increment_per_store'=>true,
                'backend_prefix'=>'sales_entity/order_attribute_backend',
                'attributes' => array(
                    'entity_id'     => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_creditmemo_attribute_backend_parent'
                    ),
                    'state'         => array('type'=>'int'),
                    'invoice_id'    => array('type'=>'int'),
                    'transaction_id'=> array(),

                    'order_id'      => array('type'=>'int'),
                    'creditmemo_status'     => array('type'=>'int'),
                    'billing_address_id'    => array('type'=>'int'),
                    'shipping_address_id'   => array('type'=>'int'),

                    'base_currency_code'    => array(),
                    'store_currency_code'   => array(),
                    'order_currency_code'   => array(),
                    'store_to_base_rate'    => array('type'=>'decimal'),
                    'store_to_order_rate'   => array('type'=>'decimal'),

                    'subtotal'          => array('type'=>'decimal'),
                    'discount_amount'   => array('type'=>'decimal'),
                    'tax_amount'        => array('type'=>'decimal'),
                    'shipping_amount'   => array('type'=>'decimal'),
                    'adjustment'        => array('type'=>'decimal'),
                    'adjustment_positive' => array('type'=>'decimal'),
                    'adjustment_negative' => array('type'=>'decimal'),
                    'grand_total'       => array('type'=>'decimal'),

                    'base_subtotal'          => array('type'=>'decimal'),
                    'base_discount_amount'   => array('type'=>'decimal'),
                    'base_tax_amount'        => array('type'=>'decimal'),
                    'base_shipping_amount'   => array('type'=>'decimal'),
                    'base_adjustment'        => array('type'=>'decimal'),
                    'base_adjustment_positive' => array('type'=>'decimal'),
                    'base_adjustment_negative' => array('type'=>'decimal'),
                    'base_grand_total'       => array('type'=>'decimal'),
                    'email_sent' => array('type'=>'int'),
                ),
            ),

            'creditmemo_item' => array(
                'entity_model'      => 'sales/order_creditmemo_item',
                //'table'=>'sales/creditmemo',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id'     => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_creditmemo_attribute_backend_child'
                    ),
                    'order_item_id' => array('type'=>'int'),
                    'product_id'    => array('type'=>'int'),
                    'name'          => array(),
                    'description'   => array('type'=>'text'),
                    'sku'           => array(),
                    'qty'           => array('type'=>'decimal'),
                    'cost'          => array('type'=>'decimal'),
                    'price'         => array('type'=>'decimal'),
                    'discount_amount' => array('type'=>'decimal'),
                    'tax_amount'    => array('type'=>'decimal'),
                    'row_total'     => array('type'=>'decimal'),

                    'base_price'         => array('type'=>'decimal'),
                    'base_discount_amount' => array('type'=>'decimal'),
                    'base_tax_amount'    => array('type'=>'decimal'),
                    'base_row_total'     => array('type'=>'decimal'),

                    'additional_data'   => array('type'=>'text'),
                ),
            ),

            'creditmemo_comment' => array(
                'entity_model'      => 'sales/order_creditmemo_comment',
                'table'=>'sales/order_entity',
                'attributes' => array(
                    'parent_id' => array(
                        'type'=>'static',
                        'backend'=>'sales_entity/order_creditmemo_attribute_backend_child'
                    ),
                    'comment' => array('type'=>'text'),
                    'is_customer_notified' => array('type'=>'int'),
                ),
            ),

        );
    }
}
