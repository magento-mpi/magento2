<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Enterprise
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$map = array(
    'admin/cms/magento_banner' => 'Magento_Banner::magento_banner',
    'admin/catalog/events' => 'Magento_CatalogEvent::events',
    'admin/catalog/magento_catalogpermissions' =>
        'Magento_CatalogPermissions::catalog_magento_catalogpermissions',
    'admin/system/config/magento_catalogpermissions' =>
        'Magento_CatalogPermissions::magento_catalogpermissions',
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
    'admin/customer/customersegment' => 'Magento_CustomerSegment::customersegment',
    'admin/report/customers/segment' => 'Magento_CustomerSegment::segment',
    'admin/system/config/giftcard' => 'Magento_GiftCard::giftcard',
    'admin/customer/giftcardaccount' => 'Magento_GiftCardAccount::customer_giftcardaccount',
    'admin/system/config/giftcardaccount' => 'Magento_GiftCardAccount::giftcardaccount',
    'admin/customer/magento_giftregistry' => 'Magento_GiftRegistry::customer_magento_giftregistry',
    'admin/system/config/magento_giftregistry' => 'Magento_GiftRegistry::magento_giftregistry',
    'admin/sales/magento_giftwrapping' => 'Magento_GiftWrapping::magento_giftwrapping',
    'admin/system/convert/enterprise_scheduled_operation' => 'Enterprise_ImportExport::enterprise_scheduled_operation',
    'admin/system/config/magento_invitation' => 'Magento_Invitation::config_magento_invitation',
    'admin/customer/magento_invitation' => 'Magento_Invitation::magento_invitation',
    'admin/report/magento_invitation/customer' => 'Magento_Invitation::magento_invitation_customer',
    'admin/report/magento_invitation/general' => 'Magento_Invitation::general',
    'admin/report/magento_invitation/order' => 'Magento_Invitation::order',
    'admin/report/magento_invitation' => 'Magento_Invitation::report_magento_invitation',
    'admin/system/magento_logging/backups' => 'Magento_Logging::backups',
    'admin/system/magento_logging' => 'Magento_Logging::magento_logging',
    'admin/system/magento_logging/events' => 'Magento_Logging::magento_logging_events',
    'admin/system/config/logging' => 'Magento_Logging::logging',
    'admin/system/crypt_key' => 'Magento_Pci::crypt_key',
    'admin/system/acl/locks' => 'Magento_Pci::locks',
    'admin/catalog/products/read_product_price/edit_product_price' => 'Magento_PricePermissions::edit_product_price',
    'admin/catalog/products/edit_product_status' => 'Magento_PricePermissions::edit_product_status',
    'admin/catalog/products/read_product_price' => 'Magento_PricePermissions::read_product_price',
    'admin/promo/catalog/edit' => 'Magento_PromotionPermissions::edit',
    'admin/promo/enterprise_reminder/edit' => 'Magento_PromotionPermissions::enterprise_reminder_edit',
    'admin/promo/quote/edit' => 'Magento_PromotionPermissions::quote_edit',
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
