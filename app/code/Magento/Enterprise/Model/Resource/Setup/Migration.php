<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Enterprise
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Enterprise\Model\Resource\Setup;

/**
 * Resource setup model with methods needed for migration process between Magento versions in Enterprise edition
 */
class Migration extends \Magento\Core\Model\Resource\Setup\Migration
{
    /**
     * List of correspondence between composite module aliases and module names
     *
     * @static
     * @return array
     */
    public static function getCompositeModules()
    {
        return array_merge(
            parent::getCompositeModules(),
            array(
                'magento_admingws' => 'Magento_AdminGws',
                'magento_catalogevent' => 'Magento_CatalogEvent',
                'magento_catalogpermissions' => 'Magento_CatalogPermissions',
                'magento_customerbalance' => 'Magento_CustomerBalance',
                'magento_customersegment' => 'Magento_CustomerSegment',
                'magento_giftcard' => 'Magento_GiftCard',
                'magento_giftcardaccount' => 'Magento_GiftCardAccount',
                'magento_giftregistry' => 'Magento_GiftRegistry',
                'magento_giftwrapping' => 'Magento_GiftWrapping',
                'magento_scheduledimportexport' => 'Magento_ScheduledImportExport',
                'magento_pricepermissions' => 'Magento_PricePermissions',
                'magento_promotionpermissions' => 'Magento_PromotionPermissions',
                'magento_salesarchive' => 'Magento_SalesArchive',
                'magento_targetrule' => 'Magento_TargetRule',
                'magento_websiterestriction' => 'Magento_WebsiteRestriction',
            )
        );
    }
}
