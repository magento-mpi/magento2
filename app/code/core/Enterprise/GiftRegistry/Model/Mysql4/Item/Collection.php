<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * GiftRegistry entity item collection
 */
class Enterprise_GiftRegistry_Model_Mysql4_Item_Collection
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{
    /**
     * Add gift registry filter to collection
     *
     * @param int $entityId
     * @return Enterprise_GiftRegistry_Model_Mysql4_Item_Collection
     */
    public function addRegistryFilter($entityId)
    {
        $this->joinTable(
            array('item' => 'enterprise_giftregistry/item'),
            'product_id=entity_id',
            array(
                'item_id' => 'item_id',
                'product_id' => 'product_id',
                'qty' => 'qty',
                'note' => 'note',
                'qty_fulfilled' => 'qty_fulfilled',
                'added_at' => 'added_at'
            ),
            array(
                'entity_id' => $entityId,
            )
        );
        return $this;
    }
}