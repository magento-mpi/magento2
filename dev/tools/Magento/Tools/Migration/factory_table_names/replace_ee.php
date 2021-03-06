<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Table names association between Magento1 and Magento2 for Enterprise Edition
 * key => Magento1 table name
 * value => Magento2 table name
 */

return [
    'array(\'enterprise_rma/item\', \'datetime\')' => 'magento_rma_item_entity_datetime',
    'array(\'enterprise_rma/item\', \'decimal\')' => 'magento_rma_item_entity_decimal',
    'array(\'enterprise_rma/item\', \'int\')' => 'magento_rma_item_entity_int',
    'array(\'enterprise_rma/item\', \'text\')' => 'magento_rma_item_entity_text',
    'array(\'enterprise_rma/item\', \'varchar\')' => 'magento_rma_item_entity_varchar',
    'enterprise_banner/banner' => 'magento_banner',
    'enterprise_banner/catalogrule' => 'magento_banner_catalogrule',
    'enterprise_banner/content' => 'magento_banner_content',
    'enterprise_banner/customersegment' => 'magento_banner_customersegment',
    'enterprise_banner/salesrule' => 'magento_banner_salesrule',
    'enterprise_catalogevent/event' => 'magento_catalogevent_event',
    'enterprise_catalogevent/event_image' => 'magento_catalogevent_event_image',
    'enterprise_catalogpermissions/permission' => 'magento_catalogpermissions',
    'enterprise_catalogpermissions/permission_index' => 'magento_catalogpermissions_index',
    'enterprise_catalogpermissions/permission_index_product' => 'magento_catalogpermissions_index_product',
    'enterprise_cms/hierarchy_lock' => 'magento_versionscms_hierarchy_lock',
    'enterprise_cms/hierarchy_metadata' => 'magento_versionscms_hierarchy_metadata',
    'enterprise_cms/hierarchy_node' => 'magento_versionscms_hierarchy_node',
    'enterprise_cms/increment' => 'magento_versionscms_increment',
    'enterprise_cms/page_revision' => 'magento_versionscms_page_revision',
    'enterprise_cms/page_version' => 'magento_versionscms_page_version',
    'enterprise_customer/sales_order' => 'magento_customercustomattributes_sales_flat_order',
    'enterprise_customer/sales_order_address' => 'magento_customercustomattributes_sales_flat_order_address',
    'enterprise_customer/sales_quote' => 'magento_customercustomattributes_sales_flat_quote',
    'enterprise_customer/sales_quote_address' => 'magento_customercustomattributes_sales_flat_quote_address',
    'enterprise_customerbalance/balance' => 'magento_customerbalance',
    'enterprise_customerbalance/balance_history' => 'magento_customerbalance_history',
    'enterprise_customersegment/customer' => 'magento_customersegment_customer',
    'enterprise_customersegment/event' => 'magento_customersegment_event',
    'enterprise_customersegment/segment' => 'magento_customersegment_segment',
    'enterprise_customersegment/website' => 'magento_customersegment_website',
    'enterprise_giftcard/amount' => 'magento_giftcard_amount',
    'enterprise_giftcardaccount/giftcardaccount' => 'magento_giftcardaccount',
    'enterprise_giftcardaccount/history' => 'magento_giftcardaccount_history',
    'enterprise_giftcardaccount/pool' => 'magento_giftcardaccount_pool',
    'enterprise_giftregistry/data' => 'magento_giftregistry_data',
    'enterprise_giftregistry/entity' => 'magento_giftregistry_entity',
    'enterprise_giftregistry/info' => 'magento_giftregistry_type_info',
    'enterprise_giftregistry/item' => 'magento_giftregistry_item',
    'enterprise_giftregistry/item_option' => 'magento_giftregistry_item_option',
    'enterprise_giftregistry/label' => 'magento_giftregistry_label',
    'enterprise_giftregistry/person' => 'magento_giftregistry_person',
    'enterprise_giftregistry/type' => 'magento_giftregistry_type',
    'enterprise_giftwrapping/attribute' => 'magento_giftwrapping_store_attributes',
    'enterprise_giftwrapping/website' => 'magento_giftwrapping_website',
    'enterprise_giftwrapping/wrapping' => 'magento_giftwrapping',
    'enterprise_importexport/scheduled_operation' => 'magento_scheduled_operations',
    'enterprise_invitation/invitation' => 'magento_invitation',
    'enterprise_invitation/invitation_history' => 'magento_invitation_status_history',
    'enterprise_invitation/invitation_track' => 'magento_invitation_track',
    'enterprise_logging/event' => 'magento_logging_event',
    'enterprise_logging/event_changes' => 'magento_logging_event_changes',
    'enterprise_pci/admin_passwords' => 'enterprise_admin_passwords',
    'enterprise_reminder/coupon' => 'magento_reminder_rule_coupon',
    'enterprise_reminder/log' => 'magento_reminder_rule_log',
    'enterprise_reminder/rule' => 'magento_reminder_rule',
    'enterprise_reminder/template' => 'magento_reminder_template',
    'enterprise_reminder/website' => 'magento_reminder_rule_website',
    'enterprise_reward/reward' => 'magento_reward',
    'enterprise_reward/reward_history' => 'magento_reward_history',
    'enterprise_reward/reward_rate' => 'magento_reward_rate',
    'enterprise_reward/reward_salesrule' => 'magento_reward_salesrule',
    'enterprise_rma/item_eav_attribute' => 'magento_rma_item_eav_attribute',
    'enterprise_rma/item_eav_attribute_website' => 'magento_rma_item_eav_attribute_website',
    'enterprise_rma/item_entity' => 'magento_rma_item_entity',
    'enterprise_rma/item_form_attribute' => 'magento_rma_item_form_attribute',
    'enterprise_rma/rma' => 'magento_rma',
    'enterprise_rma/rma_grid' => 'magento_rma_grid',
    'enterprise_rma/rma_shipping_label' => 'magento_rma_shipping_label',
    'enterprise_rma/rma_status_history' => 'magento_rma_status_history',
    'enterprise_salesarchive/creditmemo_grid' => 'magento_sales_creditmemo_grid_archive',
    'enterprise_salesarchive/invoice_grid' => 'magento_sales_invoice_grid_archive',
    'enterprise_salesarchive/order_grid' => 'magento_sales_order_grid_archive',
    'enterprise_salesarchive/shipment_grid' => 'magento_sales_shipment_grid_archive',
    'enterprise_search/recommendations' => 'catalogsearch_recommendations',
    'enterprise_targetrule/customersegment' => 'magento_targetrule_customersegment',
    'enterprise_targetrule/index' => 'magento_targetrule_index',
    'enterprise_targetrule/index_crosssell' => 'magento_targetrule_index_crosssell',
    'enterprise_targetrule/index_related' => 'magento_targetrule_index_related',
    'enterprise_targetrule/index_upsell' => 'magento_targetrule_index_upsell',
    'enterprise_targetrule/product' => 'magento_targetrule_product',
    'enterprise_targetrule/rule' => 'magento_targetrule'
];
