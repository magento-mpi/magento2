<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Enterprise
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource setup model with methods needed for migration process between Magento versions in Enterprise edition
 */
class Magento_Enterprise_Model_Resource_Setup_Migration extends Magento_Core_Model_Resource_Setup_Migration
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
                'magento_admingws'             => 'Magento_AdminGws',
                'magento_catalogevent'         => 'Magento_CatalogEvent',
                'magento_catalogpermissions'   => 'Magento_CatalogPermissions',
                'magento_customerbalance'      => 'Magento_CustomerBalance',
                'magento_customersegment'      => 'Magento_CustomerSegment',
                'magento_giftcard'             => 'Magento_GiftCard',
                'magento_giftcardaccount'      => 'Magento_GiftCardAccount',
                'enterprise_giftregistry'         => 'Enterprise_GiftRegistry',
                'enterprise_giftwrapping'         => 'Enterprise_GiftWrapping',
                'enterprise_importexport'         => 'Enterprise_ImportExport',
                'enterprise_pagecache'            => 'Enterprise_PageCache',
                'enterprise_pricepermissions'     => 'Enterprise_PricePermissions',
                'enterprise_promotionpermissions' => 'Enterprise_PromotionPermissions',
                'enterprise_salesarchive'         => 'Enterprise_SalesArchive',
                'enterprise_targetrule'           => 'Enterprise_TargetRule',
                'enterprise_websiterestriction'   => 'Enterprise_WebsiteRestriction',
            )
        );
    }
}
