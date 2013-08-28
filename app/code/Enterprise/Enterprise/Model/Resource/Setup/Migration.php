<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Enterprise
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource setup model with methods needed for migration process between Magento versions in Enterprise edition
 */
class Enterprise_Enterprise_Model_Resource_Setup_Migration extends Magento_Core_Model_Resource_Setup_Migration
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
                'enterprise_admingws'             => 'Enterprise_AdminGws',
                'enterprise_catalogevent'         => 'Enterprise_CatalogEvent',
                'enterprise_catalogpermissions'   => 'Enterprise_CatalogPermissions',
                'enterprise_customerbalance'      => 'Enterprise_CustomerBalance',
                'enterprise_customersegment'      => 'Enterprise_CustomerSegment',
                'enterprise_giftcard'             => 'Enterprise_GiftCard',
                'enterprise_giftcardaccount'      => 'Enterprise_GiftCardAccount',
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
