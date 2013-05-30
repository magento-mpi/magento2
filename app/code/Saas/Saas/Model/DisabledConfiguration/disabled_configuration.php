<?php
/**
 * List of system config options, that are disabled for an end-user. These options are not present at the system
 * configuration page, and there is no ability to change them.
 *
 * {license_notice}
 * @copyright   {copyright}
 * @license     {license_link}
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
    'web/unsecure',
    'web/secure',

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
    'catalog/search/solr_server_suggestion_count',
    'catalog/search/solr_server_use_in_catalog_navigation',
    'catalog/search/engine_commit_mode',
    'catalog/search/search_type',
    'catalog/search/use_layered_navigation_count',
    'catalog/search/search_recommendations_count_results_enabled',
    'catalog/search/search_recommendations_count',

    'sendfriend/email/max_recipients',
    'sendfriend/email/max_per_hour',
    'sendfriend/email/check_by',

    'general/store_information/merchant_vat_number',
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
    'customer/captcha',

    'sales/minimum_order/multi_address',
    'sales/minimum_order/multi_address_description',
    'sales/minimum_order/multi_address_error_message',

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

    'carriers/ups/debug',
    'carriers/usps/debug',
    'carriers/fedex/debug',
    'carriers/dhl/debug',
    'carriers/dhlint/debug',

    'payment/pbridge/debug',
    'payment/pbridge_authorizenet/debug',
    'payment/authorizenet/debug',

    'payment_services/centinel/debug',

    'payment/paypal_payments/payflow_advanced/settings_express_checkout/settings_express_checkout_advanced/debug',
    'payment/paypal_payments/wps/settings_payments_standart/settings_payments_standart_advanced/debug',
    'payment/paypal_payments/wpp/wpp_settings/wpp_settings_advanced/debug',
    'payment/paypal_payments/payflow_advanced/settings_payments_advanced/settings_payments_advanced_advanced'
        . '/billing_agreement/debug',
    'payment/paypal_payments/paypal_verisign/settings_paypal_payflow/settings_paypal_payflow_advanced/debug',
    'payment/paypal_payments/payflow_advanced/settings_express_checkout/settings_express_checkout_advanced/debug',
    'payment/paypal_payments/payflow_link/settings_payflow_link/settings_payflow_link_advanced/debug',
    'payment/paypal_payments/payflow_advanced/settings_payments_advanced/settings_payments_advanced_advanced/debug',

    'google/checkout/debug',
    'google/googleshopping/debug',

    'payment/authorizenet/currency',
);
