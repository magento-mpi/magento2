<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;
$adapter = $installer->getConnection();

//set NULL to order_id column
$adapter->query("
    ALTER TABLE `{$installer->getTable('downloadable/link_purchased')}`
        CHANGE COLUMN `order_id` `order_id` INT(10) UNSIGNED NULL DEFAULT '0'
        AFTER `purchased_id`;
");
//set NULL to order_item_id column
$adapter->query("
    ALTER TABLE `{$installer->getTable('downloadable/link_purchased_item')}`
        CHANGE COLUMN `order_item_id` `order_item_id` INT(10) UNSIGNED NULL DEFAULT '0'
        AFTER `purchased_id`;
");



/**
 * Update order_id/order_item_id to NULL which contained invalid values
 */
//update downloadable purchased data
$select = $adapter->select()
    ->from(array('d' => $installer->getTable('downloadable/link_purchased')), array('purchased_id', 'purchased_id'))
    ->joinLeft(array('o' => $installer->getTable('sales/order')),
        'd.order_id = o.entity_id', array())
    ->where('o.entity_id IS NULL')
    ->where('d.order_id IS NOT NULL')
;
$orderIds = $adapter->fetchPairs($select);
if ($orderIds) {
    $adapter->update($installer->getTable('downloadable/link_purchased'),
        array('order_id' => new Zend_Db_Expr('(NULL)')),
        $adapter->quoteInto('purchased_id IN (?)', $orderIds)
    );
}

//update downloadable purchased items data
$select = $adapter->select()
    ->from(array('d' => $installer->getTable('downloadable/link_purchased_item')), array('item_id', 'item_id'))
    ->joinLeft(array('o' => $installer->getTable('sales/order_item')),
        'd.order_item_id = o.item_id', array())
    ->where('o.item_id IS NULL')
    ->where('d.order_item_id IS NOT NULL')
;
$orderItemIds = $adapter->fetchPairs($select);
if ($orderItemIds) {
    $adapter->update($installer->getTable('downloadable/link_purchased_item'),
        array('order_item_id' => new Zend_Db_Expr('(NULL)')),
        $adapter->quoteInto('item_id IN (?)', $orderItemIds)
    );
}

//add foreign keys
$sql = "
    ALTER TABLE `{$installer->getTable('downloadable/link_purchased')}`
        ADD CONSTRAINT `FK_DOWNLOADABLE_LINK_ORDER_ID`
        FOREIGN KEY (`order_id`) REFERENCES `{$installer->getTable('sales/order')}` (`entity_id`)
        ON UPDATE CASCADE ON DELETE SET NULL;";
$adapter->query($sql);

$sql = "
    ALTER TABLE `{$installer->getTable('downloadable/link_purchased_item')}`
        ADD CONSTRAINT `FK_DOWNLOADABLE_LINK_ORDER_ITEM_ID`
        FOREIGN KEY (`order_item_id`) REFERENCES `{$installer->getTable('sales/order_item')}` (`item_id`)
        ON UPDATE CASCADE ON DELETE SET NULL;
";
$adapter->query($sql);

