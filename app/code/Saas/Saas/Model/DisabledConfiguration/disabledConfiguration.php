<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Saas
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * List of system config options, that are disabled for an end-user. These options are not present at the system
 * configuration page, and there is no ability to change them.
 */
return array(
    'web/url/use_store',
    'web/seo',
    'web/default/front',
    'web/default/no_route',
    'web/cookie/cookie_lifetime',
    'web/cookie/cookie_path',
    'web/cookie/cookie_domain',
    'web/cookie/cookie_httponly',
    'web/session',
    'web/browser_capabilities',

    'design/theme',
    'design/header/logo_src',

    'currency/webservicex',
    'currency/import',

    'catalog/frontend/flat_catalog_category',
    'catalog/frontend/flat_catalog_product',
    'catalog/search/engine',
    'catalog/search/solr_server_hostname',
    'catalog/search/solr_server_port',
    'catalog/search/solr_server_username',
    'catalog/search/solr_server_password',
    'catalog/search/solr_server_timeout',
    'catalog/search/solr_server_path',
    'catalog/search/solr_test_connect_wizard',
    'catalog/search/engine_commit_mode',
    'catalog/search/search_type',
    'catalog/search/use_layered_navigation_count',
    'catalog/search/solr_server_suggestion_count',
    'catalog/search/search_recommendations_count_results_enabled',
    'catalog/search/search_recommendations_count',
    'catalog/search/solr_server_use_in_catalog_navigation',
    'catalog/downloadable',

    'sendfriend/email/max_recipients',
    'sendfriend/email/max_per_hour',
    'sendfriend/email/check_by',

    'customer/create_account/auto_group_assign',
    'customer/create_account/tax_calculation_address_type',
    'customer/create_account/viv_domestic_group',
    'customer/create_account/viv_intra_union_group',
    'customer/create_account/viv_invalid_group',
    'customer/create_account/viv_error_group',
    'customer/create_account/viv_on_each_transaction',
    'customer/create_account/viv_disable_auto_group_assign_default',
    'customer/create_account/vat_frontend_visibility',

    'customer/online_customers',

    'customer/account_share',
    'customer/enterprise_customersegment',
    'customer/captcha',

    'wishlist/general/multiple_enabled',
    'wishlist/general/multiple_wishlist_number',

    'enterprise_invitation',

    'promo',

    'persistent/options/wishlist',
    'persistent/options/recently_ordered',
    'persistent/options/compare_current',
    'persistent/options/compare_history',
    'persistent/options/recently_viewed',
    'persistent/options/customer',

    'sales/minimum_order/multi_address',
    'sales/minimum_order/multi_address_description',
    'sales/minimum_order/multi_address_error_message',
    'sales/product_sku',

    'sales_email/enterprise_rma',
    'sales_email/enterprise_rma_auth',
    'sales_email/enterprise_rma_comment',
    'sales_email/enterprise_rma_customer_comment',

    'tax/classes/wrapping_tax_class',
    'sales/cart_display/gift_wrapping',
    'sales/cart_display/printed_card',
    'sales/sales_display/gift_wrapping',
    'sales/sales_display/printed_card',

    'checkout/cart/delete_quote_after',

    'shipping/option/checkout_multiple',
    'shipping/option/checkout_multiple_maximum_qty',

    'carriers/ups/shipment_requesttype',
    'carriers/usps/shipment_requesttype',
    'carriers/fedex/shipment_requesttype',
    'carriers/dhl/shipment_requesttype',

    'api/config/wsdl_cache_enabled',

    'oauth',

    'admin/url',
    'admin/security',
    'admin/captcha',

    'system/cron',
    'system/smtp',
    'system/log',
    'system/adminnotification',
    'system/rotation',
    'system/backup',
    'system/page_cache',
    'system/page_crawl',
    'system/media_storage_configuration',

    'advanced/modules_disable_output',

    'dev',
);
