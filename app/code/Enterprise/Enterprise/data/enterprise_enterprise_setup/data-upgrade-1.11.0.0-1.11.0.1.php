<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Enterprise
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$map = array(
    'admin/cms/enterprise_banner' => 'Enterprise_Banner::enterprise_banner',
    'admin/catalog/events' => 'Enterprise_CatalogEvent::events',
    'admin/catalog/enterprise_catalogpermissions' =>
        'Enterprise_CatalogPermissions::catalog_enterprise_catalogpermissions',
    'admin/system/config/enterprise_catalogpermissions' =>
        'Enterprise_CatalogPermissions::enterprise_catalogpermissions',
    'admin/sales/enterprise_checkout' => 'Enterprise_Checkout::enterprise_checkout',
    'admin/sales/enterprise_checkout/update' => 'Enterprise_Checkout::update',
    'admin/sales/enterprise_checkout/view' => 'Enterprise_Checkout::view',
    'admin/cms/page/delete_revision' => 'Enterprise_Cms::delete_revision',
    'admin/cms/hierarchy' => 'Enterprise_Cms::hierarchy',
    'admin/cms/page/publish_revision' => 'Enterprise_Cms::publish_revision',
    'admin/cms/page/save_revision' => 'Enterprise_Cms::save_revision',
    'admin/customer/attributes' => 'Enterprise_Customer::attributes',
    'admin/customer/attributes/customer_address_attributes' => 'Enterprise_Customer::customer_address_attributes',
    'admin/customer/attributes/customer_attributes' => 'Enterprise_Customer::customer_attributes',
    'admin/customer/customersegment' => 'Enterprise_CustomerSegment::customersegment',
    'admin/report/customers/segment' => 'Enterprise_CustomerSegment::segment',
    'admin/system/config/giftcard' => 'Enterprise_GiftCard::giftcard',
    'admin/customer/giftcardaccount' => 'Enterprise_GiftCardAccount::customer_giftcardaccount',
    'admin/system/config/giftcardaccount' => 'Enterprise_GiftCardAccount::giftcardaccount',
    'admin/customer/enterprise_giftregistry' => 'Enterprise_GiftRegistry::customer_enterprise_giftregistry',
    'admin/system/config/enterprise_giftregistry' => 'Enterprise_GiftRegistry::enterprise_giftregistry',
    'admin/sales/enterprise_giftwrapping' => 'Enterprise_GiftWrapping::enterprise_giftwrapping',
    'admin/system/convert/enterprise_scheduled_operation' => 'Enterprise_ImportExport::enterprise_scheduled_operation',
    'admin/system/config/enterprise_invitation' => 'Enterprise_Invitation::config_enterprise_invitation',
    'admin/customer/enterprise_invitation' => 'Enterprise_Invitation::enterprise_invitation',
    'admin/report/enterprise_invitation/customer' => 'Enterprise_Invitation::enterprise_invitation_customer',
    'admin/report/enterprise_invitation/general' => 'Enterprise_Invitation::general',
    'admin/report/enterprise_invitation/order' => 'Enterprise_Invitation::order',
    'admin/report/enterprise_invitation' => 'Enterprise_Invitation::report_enterprise_invitation',
    'admin/system/enterprise_logging/backups' => 'Enterprise_Logging::backups',
    'admin/system/enterprise_logging' => 'Enterprise_Logging::enterprise_logging',
    'admin/system/enterprise_logging/events' => 'Enterprise_Logging::enterprise_logging_events',
    'admin/system/config/logging' => 'Enterprise_Logging::logging',
    'admin/system/crypt_key' => 'Enterprise_Pci::crypt_key',
    'admin/system/acl/locks' => 'Enterprise_Pci::locks',
    'admin/catalog/products/read_product_price/edit_product_price' => 'Enterprise_PricePermissions::edit_product_price',
    'admin/catalog/products/edit_product_status' => 'Enterprise_PricePermissions::edit_product_status',
    'admin/catalog/products/read_product_price' => 'Enterprise_PricePermissions::read_product_price',
    'admin/promo/catalog/edit' => 'Enterprise_PromotionPermissions::edit',
    'admin/promo/enterprise_reminder/edit' => 'Enterprise_PromotionPermissions::enterprise_reminder_edit',
    'admin/promo/quote/edit' => 'Enterprise_PromotionPermissions::quote_edit',
    'admin/promo/enterprise_reminder' => 'Enterprise_Reminder::enterprise_reminder',
    'admin/system/config/enterprise_reward' => 'Enterprise_Reward::enterprise_reward',
    'admin/customer/rates' => 'Enterprise_Reward::rates',
    'admin/customer/manage/reward_balance' => 'Enterprise_Reward::reward_balance',
    'admin/sales/order/actions/create/reward_spend' => 'Enterprise_Reward::reward_spend',
    'admin/sales/enterprise_rma' => 'Enterprise_Rma::enterprise_rma',
    'admin/sales/enterprise_rma/rma_attribute' => 'Enterprise_Rma::rma_attribute',
    'admin/sales/enterprise_rma/rma_manage' => 'Enterprise_Rma::rma_manage',
    'admin/sales/archive/orders/add' => 'Enterprise_SalesArchive::add',
    'admin/sales/archive' => 'Enterprise_SalesArchive::archive',
    'admin/sales/archive/creditmemos' => 'Enterprise_SalesArchive::creditmemos',
    'admin/sales/archive/invoices' => 'Enterprise_SalesArchive::invoices',
    'admin/sales/archive/orders' => 'Enterprise_SalesArchive::orders',
    'admin/sales/archive/orders/remove' => 'Enterprise_SalesArchive::remove',
    'admin/sales/archive/shipments' => 'Enterprise_SalesArchive::shipments',
    'admin/system/config/content_staging' => 'Enterprise_Staging::content_staging',
    'admin/system/enterprise_staging' => 'Enterprise_Staging::enterprise_staging',
    'admin/system/enterprise_staging/staging_backup' => 'Enterprise_Staging::staging_backup',
    'admin/system/enterprise_staging/staging_grid' => 'Enterprise_Staging::staging_grid',
    'admin/system/enterprise_staging/staging_log' => 'Enterprise_Staging::staging_log',
    'admin/catalog/targetrule' => 'Enterprise_TargetRule::targetrule',
    'admin/report/customers/wishlist' => 'Enterprise_Wishlist::wishlist',
);


$tableName = $installer->getTable('admin_rule');

if ($tableName) {
    /** @var Magento_DB_Adapter_Interface $connection */
    $connection = $installer->getConnection();

    $select = $connection->select();
    $select->from($tableName, array())
        ->columns(array('resource_id' => 'resource_id'))
        ->group('resource_id');

    foreach ($connection->fetchCol($select) as $oldKey) {
        /**
         * If used ACL key is converted previously or we haven't map for specified ACL resource item
         * than go to the next item
         */
        if (in_array($oldKey, $map) || false == isset($map[$oldKey])) {
            continue;
        }

        /** Update rule ACL key from xpath format to identifier format */
        $connection->update($tableName, array('resource_id' => $map[$oldKey]), array('resource_id = ?' => $oldKey));
    }
}

$installer->endSetup();
