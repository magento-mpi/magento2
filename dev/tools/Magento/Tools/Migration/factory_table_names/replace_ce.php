<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Table names association between Magento1 and Magento2 for Community Edition
 * key => Magento1 table name
 * value => Magento2 table name
 */

return array(
    'admin/assert' => 'admin_assert',
    'admin/role' => 'admin_role',
    'admin/rule' => 'admin_rule',
    'admin/user' => 'admin_user',
    'adminnotification/inbox' => 'adminnotification_inbox',
    'amazonpayments/api_debug' => 'amazonpayments_api_debug',
    'array(\'catalog/category\', \'datetime\')' => 'catalog_category_entity_datetime',
    'array(\'catalog/category\', \'decimal\')' => 'catalog_category_entity_decimal',
    'array(\'catalog/category\', \'int\')' => 'catalog_category_entity_int',
    'array(\'catalog/category\', \'text\')' => 'catalog_category_entity_text',
    'array(\'catalog/category\', \'varchar\')' => 'catalog_category_entity_varchar',
    'array(\'catalog/product\', \'datetime\')' => 'catalog_product_entity_datetime',
    'array(\'catalog/product\', \'decimal\')' => 'catalog_product_entity_decimal',
    'array(\'catalog/product\', \'gallery\'' => 'catalog_product_entity_gallery',
    'array(\'catalog/product\', \'int\')' => 'catalog_product_entity_int',
    'array(\'catalog/product\', \'text\')' => 'catalog_product_entity_text',
    'array(\'catalog/product\', \'varchar\')' => 'catalog_product_entity_varchar',
    'array(\'catalog_product\', \'group_price\')' => 'catalog_product_entity_group_price',
    'bundle/option' => 'catalog_product_bundle_option',
    'bundle/option_indexer_idx' => 'catalog_product_index_price_bundle_opt_idx',
    'bundle/option_indexer_tmp' => 'catalog_product_index_price_bundle_opt_tmp',
    'bundle/option_value' => 'catalog_product_bundle_option_value',
    'bundle/price_index' => 'catalog_product_bundle_price_index',
    'bundle/price_indexer_idx' => 'catalog_product_index_price_bundle_idx',
    'bundle/price_indexer_tmp' => 'catalog_product_index_price_bundle_tmp',
    'bundle/selection' => 'catalog_product_bundle_selection',
    'bundle/selection_indexer_idx' => 'catalog_product_index_price_bundle_sel_idx',
    'bundle/selection_indexer_tmp' => 'catalog_product_index_price_bundle_sel_tmp',
    'bundle/selection_price' => 'catalog_product_bundle_selection_price',
    'bundle/stock_index' => 'catalog_product_bundle_stock_index',
    'captcha/log' => 'captcha_log',
    'catalog/category' => 'catalog_category_entity',
    'catalog/category_flat' => 'catalog_category_flat',
    'catalog/category_product' => 'catalog_category_product',
    'catalog/category_product_index' => 'catalog_category_product_index',
    'catalog/compare_item' => 'catalog_compare_item',
    'catalog/eav_attribute' => 'catalog_eav_attribute',
    'catalog/product' => 'catalog_product_entity',
    'catalog/product_attribute_group_price' => 'catalog_product_entity_group_price',
    'catalog/product_attribute_media_gallery' => 'catalog_product_entity_media_gallery',
    'catalog/product_attribute_media_gallery_value' => 'catalog_product_entity_media_gallery_value',
    'catalog/product_attribute_tier_price' => 'catalog_product_entity_tier_price',
    'catalog/product_eav_decimal_indexer_idx' => 'catalog_product_index_eav_decimal_idx',
    'catalog/product_eav_decimal_indexer_tmp' => 'catalog_product_index_eav_decimal_tmp',
    'catalog/product_eav_indexer_idx' => 'catalog_product_index_eav_idx',
    'catalog/product_eav_indexer_tmp' => 'catalog_product_index_eav_tmp',
    'catalog/product_flat' => 'catalog_product_flat',
    'catalog/product_index_eav' => 'catalog_product_index_eav',
    'catalog/product_index_eav_decimal' => 'catalog_product_index_eav_decimal',
    'catalog/product_index_group_price' => 'catalog_product_index_group_price',
    'catalog/product_index_price' => 'catalog_product_index_price',
    'catalog/product_index_tier_price' => 'catalog_product_index_tier_price',
    'catalog/product_index_website' => 'catalog_product_index_website',
    'catalog/product_link' => 'catalog_product_link',
    'catalog/product_link_attribute' => 'catalog_product_link_attribute',
    'catalog/product_link_attribute_decimal' => 'catalog_product_link_attribute_decimal',
    'catalog/product_link_attribute_int' => 'catalog_product_link_attribute_int',
    'catalog/product_link_attribute_varchar' => 'catalog_product_link_attribute_varchar',
    'catalog/product_link_type' => 'catalog_product_link_type',
    'catalog/product_option' => 'catalog_product_option',
    'catalog/product_option_price' => 'catalog_product_option_price',
    'catalog/product_option_title' => 'catalog_product_option_title',
    'catalog/product_option_type_price' => 'catalog_product_option_type_price',
    'catalog/product_option_type_title' => 'catalog_product_option_type_title',
    'catalog/product_option_type_value' => 'catalog_product_option_type_value',
    'catalog/product_price_indexer_cfg_option_aggregate_idx' => 'catalog_product_index_price_cfg_opt_agr_idx',
    'catalog/product_price_indexer_cfg_option_aggregate_tmp' => 'catalog_product_index_price_cfg_opt_agr_tmp',
    'catalog/product_price_indexer_cfg_option_idx' => 'catalog_product_index_price_cfg_opt_idx',
    'catalog/product_price_indexer_cfg_option_tmp' => 'catalog_product_index_price_cfg_opt_tmp',
    'catalog/product_price_indexer_final_idx' => 'catalog_product_index_price_final_idx',
    'catalog/product_price_indexer_final_tmp' => 'catalog_product_index_price_final_tmp',
    'catalog/product_price_indexer_idx' => 'catalog_product_index_price_idx',
    'catalog/product_price_indexer_option_aggregate_idx' => 'catalog_product_index_price_opt_agr_idx',
    'catalog/product_price_indexer_option_aggregate_tmp' => 'catalog_product_index_price_opt_agr_tmp',
    'catalog/product_price_indexer_option_idx' => 'catalog_product_index_price_opt_idx',
    'catalog/product_price_indexer_option_tmp' => 'catalog_product_index_price_opt_tmp',
    'catalog/product_price_indexer_tmp' => 'catalog_product_index_price_tmp',
    'catalog/product_relation' => 'catalog_product_relation',
    'catalog/product_super_attribute' => 'catalog_product_super_attribute',
    'catalog/product_super_attribute_label' => 'catalog_product_super_attribute_label',
    'catalog/product_super_attribute_pricing' => 'catalog_product_super_attribute_pricing',
    'catalog/product_super_link' => 'catalog_product_super_link',
    'catalog/product_website' => 'catalog_product_website',
    'catalogindex/aggregation' => 'catalogindex_aggregation',
    'catalogindex/aggregation_tag' => 'catalogindex_aggregation_tag',
    'catalogindex/aggregation_to_tag' => 'catalogindex_aggregation_to_tag',
    'catalogindex/eav' => 'catalog_product_index_eav',
    'catalogindex/minimal_price' => 'catalogindex_minimal_price',
    'catalogindex/price' => 'catalog_product_index_price',
    'cataloginventory/stock' => 'cataloginventory_stock',
    'cataloginventory/stock_item' => 'cataloginventory_stock_item',
    'cataloginventory/stock_status' => 'cataloginventory_stock_status',
    'cataloginventory/stock_status_indexer_idx' => 'cataloginventory_stock_status_idx',
    'cataloginventory/stock_status_indexer_tmp' => 'cataloginventory_stock_status_tmp',
    'catalogrule/affected_product' => 'catalogrule_affected_product',
    'catalogrule/customer_group' => 'catalogrule_customer_group',
    'catalogrule/rule' => 'catalogrule',
    'catalogrule/rule_group_website' => 'catalogrule_group_website',
    'catalogrule/rule_product' => 'catalogrule_product',
    'catalogrule/rule_product_price' => 'catalogrule_product_price',
    'catalogrule/website' => 'catalogrule_website',
    'catalogsearch/fulltext' => 'catalogsearch_fulltext',
    'catalogsearch/result' => 'catalogsearch_result',
    'catalogsearch/search_query' => 'catalogsearch_query',
    'checkout/agreement' => 'checkout_agreement',
    'checkout/agreement_store' => 'checkout_agreement_store',
    'chronopay/api_debug' => 'chronopay_api_debug',
    'cms/block' => 'cms_block',
    'cms/block_store' => 'cms_block_store',
    'cms/page' => 'cms_page',
    'cms/page_store' => 'cms_page_store',
    'compiler/configuration' => 'compiler_configuration',
    'core/cache' => 'core_cache',
    'core/cache_option' => 'core_cache_option',
    'core/cache_tag' => 'core_cache_tag',
    'core/config_data' => 'core_config_data',
    'core/config_field' => 'core_config_field',
    'core/design_change' => 'design_change',
    'core/directory_storage' => 'core_directory_storage',
    'core/email_template' => 'core_email_template',
    'core/file_storage' => 'core_file_storage',
    'core/flag' => 'core_flag',
    'core/layout_link' => 'core_layout_link',
    'core/layout_update' => 'core_layout_update',
    'core/resource' => 'core_resource',
    'core/session' => 'core_session',
    'core/store' => 'core_store',
    'core/store_group' => 'core_store_group',
    'core/translate' => 'core_translate',
    'core/url_rewrite' => 'core_url_rewrite',
    'core/url_rewrite_tag' => 'core_url_rewrite_tag',
    'core/variable' => 'core_variable',
    'core/variable_value' => 'core_variable_value',
    'core/website' => 'core_website',
    'cron/schedule' => 'cron_schedule',
    'customer/address_entity' => 'customer_address_entity',
    'customer/customer_group' => 'customer_group',
    'customer/eav_attribute' => 'customer_eav_attribute',
    'customer/eav_attribute_website' => 'customer_eav_attribute_website',
    'customer/entity' => 'customer_entity',
    'customer/form_attribute' => 'customer_form_attribute',
    'customer/value_prefix' => 'customer_entity',
    'customer_address_entity_datetime' => 'customer_address_entity_datetime',
    'customer_address_entity_decimal' => 'customer_address_entity_decimal',
    'customer_address_entity_int' => 'customer_address_entity_int',
    'customer_address_entity_text' => 'customer_address_entity_text',
    'customer_address_entity_varchar' => 'customer_address_entity_varchar',
    'customer_entity_datetime' => 'customer_entity_datetime',
    'customer_entity_decimal' => 'customer_entity_decimal',
    'customer_entity_int' => 'customer_entity_int',
    'customer_entity_text' => 'customer_entity_text',
    'customer_entity_varchar' => 'customer_entity_varchar',
    'cybermut/api_debug' => 'cybermut_api_debug',
    'cybersource/api_debug' => 'cybersource_api_debug',
    'directory/country' => 'directory_country',
    'directory/country_format' => 'directory_country_format',
    'directory/country_name' => 'directory_country_name',
    'directory/country_region' => 'directory_country_region',
    'directory/country_region_name' => 'directory_country_region_name',
    'directory/currency_rate' => 'directory_currency_rate',
    'downloadable/link' => 'downloadable_link',
    'downloadable/link_price' => 'downloadable_link_price',
    'downloadable/link_purchased' => 'downloadable_link_purchased',
    'downloadable/link_purchased_item' => 'downloadable_link_purchased_item',
    'downloadable/link_title' => 'downloadable_link_title',
    'downloadable/product_price_indexer_idx' => 'catalog_product_index_price_downlod_idx',
    'downloadable/product_price_indexer_tmp' => 'catalog_product_index_price_downlod_tmp',
    'downloadable/sample' => 'downloadable_sample',
    'downloadable/sample_title' => 'downloadable_sample_title',
    'eav/attribute' => 'eav_attribute',
    'eav/attribute_group' => 'eav_attribute_group',
    'eav/attribute_label' => 'eav_attribute_label',
    'eav/attribute_option' => 'eav_attribute_option',
    'eav/attribute_option_value' => 'eav_attribute_option_value',
    'eav/attribute_set' => 'eav_attribute_set',
    'eav/entity' => 'eav_entity',
    'eav/entity_attribute' => 'eav_entity_attribute',
    'eav/entity_attribute_source_table' => 'eav_entity_attribute_source_table',
    'eav/entity_store' => 'eav_entity_store',
    'eav/entity_type' => 'eav_entity_type',
    'eav/entity_value_prefix' => 'eav_entity',
    'eav/form_element' => 'eav_form_element',
    'eav/form_fieldset' => 'eav_form_fieldset',
    'eav/form_fieldset_label' => 'eav_form_fieldset_label',
    'eav/form_type' => 'eav_form_type',
    'eav/form_type_entity' => 'eav_form_type_entity',
    'eav_entity_datetime' => 'eav_entity_datetime',
    'eav_entity_decimal' => 'eav_entity_decimal',
    'eav_entity_int' => 'eav_entity_int',
    'eav_entity_text' => 'eav_entity_text',
    'eav_entity_varchar' => 'eav_entity_varchar',
    'eway/api_debug' => 'eway_api_debug',
    'flo2cash/api_debug' => 'flo2cash_api_debug',
    'giftmessage/message' => 'gift_message',
    'googlebase/attributes' => 'googlebase_attributes',
    'googlebase/items' => 'googlebase_items',
    'googlebase/types' => 'googlebase_types',
    'googleoptimizer/code' => 'googleoptimizer_code',
    'googleshopping/attributes' => 'googleshopping_attributes',
    'googleshopping/items' => 'googleshopping_items',
    'googleshopping/types' => 'googleshopping_types',
    'ideal/api_debug' => 'ideal_api_debug',
    'importexport/importdata' => 'importexport_importdata',
    'index/event' => 'index_event',
    'index/process' => 'index_process',
    'index/process_event' => 'index_process_event',
    'log/customer' => 'log_customer',
    'log/quote_table' => 'log_quote',
    'log/summary_table' => 'log_summary',
    'log/summary_type_table' => 'log_summary_type',
    'log/url_info_table' => 'log_url_info',
    'log/url_table' => 'log_url',
    'log/visitor' => 'log_visitor',
    'log/visitor_info' => 'log_visitor_info',
    'log/visitor_online' => 'log_visitor_online',
    'newsletter/problem' => 'newsletter_problem',
    'newsletter/queue' => 'newsletter_queue',
    'newsletter/queue_link' => 'newsletter_queue_link',
    'newsletter/queue_store_link' => 'newsletter_queue_store_link',
    'newsletter/subscriber' => 'newsletter_subscriber',
    'newsletter/template' => 'newsletter_template',
    'oauth/consumer' => 'oauth_consumer',
    'oauth/nonce' => 'oauth_nonce',
    'oauth/token' => 'oauth_token',
    'ogone/api_debug' => 'ogone_api_debug',
    'oscommerce/catalog_category' => 'catalog_category_entity',
    'oscommerce/catalog_product_website' => 'catalog_product_website',
    'oscommerce/oscommerce' => 'oscommerce_import',
    'oscommerce/oscommerce_order' => 'oscommerce_orders',
    'oscommerce/oscommerce_order_history' => 'oscommerce_orders_status_history',
    'oscommerce/oscommerce_order_products' => 'oscommerce_orders_products',
    'oscommerce/oscommerce_order_total' => 'oscommerce_orders_total',
    'oscommerce/oscommerce_ref' => 'oscommerce_ref',
    'oscommerce/oscommerce_type' => 'oscommerce_import_type',
    'paybox/api_debug' => 'paybox_api_debug',
    'paybox/question_number' => 'paybox_question_number',
    'paygate/authorizenet_debug' => 'authorizenet_debug',
    'paypal/cert' => 'paypal_cert',
    'paypal/payment_transaction' => 'paypal_payment_transaction',
    'paypal/settlement_report' => 'paypal_settlement_report',
    'paypal/settlement_report_row' => 'paypal_settlement_report_row',
    'persistent/session' => 'persistent_session',
    'poll/poll' => 'poll',
    'poll/poll_answer' => 'poll_answer',
    'poll/poll_store' => 'poll_store',
    'poll/poll_vote' => 'poll_vote',
    'productalert/price' => 'product_alert_price',
    'productalert/stock' => 'product_alert_stock',
    'protx/api_debug' => 'protx_api_debug',
    'rating/rating' => 'rating',
    'rating/rating_entity' => 'rating_entity',
    'rating/rating_option' => 'rating_option',
    'rating/rating_option_vote' => 'rating_option_vote',
    'rating/rating_store' => 'rating_store',
    'rating/rating_title' => 'rating_title',
    'rating/rating_vote_aggregated' => 'rating_option_vote_aggregated',
    'reports/compared_product_index' => 'report_compared_product_index',
    'reports/event' => 'report_event',
    'reports/event_type' => 'report_event_types',
    'reports/viewed_product_index' => 'report_viewed_product_index',
    'review/review' => 'review',
    'review/review_aggregate' => 'review_entity_summary',
    'review/review_detail' => 'review_detail',
    'review/review_entity' => 'review_entity',
    'review/review_status' => 'review_status',
    'review/review_store' => 'review_store',
    'sales/bestsellers_aggregated_daily' => 'sales_bestsellers_aggregated_daily',
    'sales/bestsellers_aggregated_monthly' => 'sales_bestsellers_aggregated_monthly',
    'sales/bestsellers_aggregated_yearly' => 'sales_bestsellers_aggregated_yearly',
    'sales/billing_agreement' => 'paypal_billing_agreement',
    'sales/billing_agreement_order' => 'paypal_billing_agreement_order',
    'sales/creditmemo' => 'sales_flat_creditmemo',
    'sales/creditmemo_comment' => 'sales_flat_creditmemo_comment',
    'sales/creditmemo_grid' => 'sales_flat_creditmemo_grid',
    'sales/creditmemo_item' => 'sales_flat_creditmemo_item',
    'sales/invoice' => 'sales_flat_invoice',
    'sales/invoice_comment' => 'sales_flat_invoice_comment',
    'sales/invoice_grid' => 'sales_flat_invoice_grid',
    'sales/invoice_item' => 'sales_flat_invoice_item',
    'sales/invoiced_aggregated' => 'sales_invoiced_aggregated',
    'sales/invoiced_aggregated_order' => 'sales_invoiced_aggregated_order',
    'sales/order' => 'sales_flat_order',
    'sales/order_address' => 'sales_flat_order_address',
    'sales/order_aggregated_created' => 'sales_order_aggregated_created',
    'sales/order_aggregated_updated' => 'sales_order_aggregated_updated',
    'sales/order_entity' => 'sales_order_entity',
    'sales/order_grid' => 'sales_flat_order_grid',
    'sales/order_item' => 'sales_flat_order_item',
    'sales/order_item_option' => 'sales_flat_order_item_option',
    'sales/order_payment' => 'sales_flat_order_payment',
    'sales/order_status' => 'sales_order_status',
    'sales/order_status_history' => 'sales_flat_order_status_history',
    'sales/order_status_label' => 'sales_order_status_label',
    'sales/order_status_state' => 'sales_order_status_state',
    'sales/order_tax' => 'sales_order_tax',
    'sales/payment_transaction' => 'sales_payment_transaction',
    'sales/quote' => 'sales_flat_quote',
    'sales/quote_address' => 'sales_flat_quote_address',
    'sales/quote_address_item' => 'sales_flat_quote_address_item',
    'sales/quote_address_shipping_rate' => 'sales_flat_quote_shipping_rate',
    'sales/quote_item' => 'sales_flat_quote_item',
    'sales/quote_item_option' => 'sales_flat_quote_item_option',
    'sales/quote_payment' => 'sales_flat_quote_payment',
    'sales/recurring_payment' => 'recurring_payment',
    'sales/recurring_payment_order' => 'recurring_payment_order',
    'sales/refunded_aggregated' => 'sales_refunded_aggregated',
    'sales/refunded_aggregated_order' => 'sales_refunded_aggregated_order',
    'sales/shipment' => 'sales_flat_shipment',
    'sales/shipment_comment' => 'sales_flat_shipment_comment',
    'sales/shipment_grid' => 'sales_flat_shipment_grid',
    'sales/shipment_item' => 'sales_flat_shipment_item',
    'sales/shipment_track' => 'sales_flat_shipment_track',
    'sales/shipping_aggregated' => 'sales_shipping_aggregated',
    'sales/shipping_aggregated_order' => 'sales_shipping_aggregated_order',
    'sales_entity/order' => 'sales_order',
    'sales_entity/order_entity' => 'sales_order_entity',
    'sales_entity/quote' => 'sales_quote',
    'sales_entity/quote_address' => 'sales_quote_address',
    'sales_entity/quote_entity' => 'sales_quote_entity',
    'sales_entity/quote_item' => 'sales_quote_item',
    'sales_entity/quote_temp' => 'sales_quote_temp',
    'salesrule/coupon' => 'salesrule_coupon',
    'salesrule/coupon_aggregated' => 'coupon_aggregated',
    'salesrule/coupon_aggregated_order' => 'coupon_aggregated_order',
    'salesrule/coupon_aggregated_updated' => 'coupon_aggregated_updated',
    'salesrule/coupon_usage' => 'salesrule_coupon_usage',
    'salesrule/customer_group' => 'salesrule_website',
    'salesrule/label' => 'salesrule_label',
    'salesrule/product_attribute' => 'salesrule_product_attribute',
    'salesrule/rule' => 'salesrule',
    'salesrule/rule_customer' => 'salesrule_customer',
    'salesrule/website' => 'salesrule_customer_group',
    'sendfriend/sendfriend' => 'sendfriend_log',
    'shipping/tablerate' => 'shipping_tablerate',
    'sitemap/sitemap' => 'sitemap',
    'strikeiron/tax_rate' => 'strikeiron_tax_rate',
    'tax/sales_order_tax' => 'sales_order_tax',
    'tax/sales_order_tax_item' => 'sales_order_tax_item',
    'tax/tax_calculation' => 'tax_calculation',
    'tax/tax_calculation_rate' => 'tax_calculation_rate',
    'tax/tax_calculation_rate_title' => 'tax_calculation_rate_title',
    'tax/tax_calculation_rule' => 'tax_calculation_rule',
    'tax/tax_class' => 'tax_class',
    'tax/tax_order_aggregated_created' => 'tax_order_aggregated_created',
    'tax/tax_order_aggregated_updated' => 'tax_order_aggregated_updated',
    'weee/discount' => 'weee_discount',
    'weee/tax' => 'weee_tax',
    'widget/widget' => 'widget',
    'widget/widget_instance' => 'widget_instance',
    'widget/widget_instance_page' => 'widget_instance_page',
    'widget/widget_instance_page_layout' => 'widget_instance_page_layout',
    'wishlist/item' => 'wishlist_item',
    'wishlist/item_option' => 'wishlist_item_option',
    'wishlist/wishlist' => 'wishlist',
);
