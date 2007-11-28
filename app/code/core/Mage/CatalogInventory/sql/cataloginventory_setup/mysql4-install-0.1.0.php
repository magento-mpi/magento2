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
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer->addConfigField('cataloginventory', 'Inventory', array(
    'show_in_website'   => 0,
    'show_in_store'     => 0,
));
$installer->addConfigField('cataloginventory/options', 'Stock Options');
$installer->addConfigField('cataloginventory/options/min_qty', 'Minimum Qty for Items', array(
    'frontend_type'     => 'text',
    'show_in_website'   => 0,
    'show_in_store'     => 0,
), 0);
$installer->addConfigField('cataloginventory/options/backorders', 'Backorders', array(
    'frontend_type'     => 'select',
    'source_model'      => 'cataloginventory/source_backorders',
    'show_in_website'   => 0,
    'show_in_store'     => 0,
), 0);

$installer->startSetup();
$installer->run("
DROP TABLE IF EXISTS `cataloginventory_stock`;

CREATE TABLE `cataloginventory_stock` (
  `stock_id` smallint(4) unsigned NOT NULL auto_increment,
  `stock_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`stock_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Catalog inventory Stocks list';

insert  into `cataloginventory_stock`(`stock_id`,`stock_name`) values (1, 'Default');

DROP TABLE IF EXISTS `cataloginventory_stock_item`;

CREATE TABLE `cataloginventory_stock_item` (
  `item_id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL default '0',
  `stock_id` smallint(4) unsigned NOT NULL default '0',
  `qty` decimal(10,0) NOT NULL default '0',
  `min_qty` decimal(10,0) NOT NULL default '0',
  `use_config_min_qty` tinyint(1) unsigned NOT NULL default '0',
  `is_qty_decimal` tinyint(1) unsigned NOT NULL default '0',
  `backorders` tinyint(3) unsigned NOT NULL default '0',
  `use_config_backorders` tinyint(1) unsigned NOT NULL default '0',
  `is_in_stock` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`item_id`),
  KEY `FK_CATALOGINVENTORY_STOCK_ITEM_PRODUCT` (`product_id`),
  KEY `FK_CATALOGINVENTORY_STOCK_ITEM_STOCK` (`stock_id`),
  CONSTRAINT `FK_CATALOGINVENTORY_STOCK_ITEM_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOGINVENTORY_STOCK_ITEM_STOCK` FOREIGN KEY (`stock_id`) REFERENCES `cataloginventory_stock` (`stock_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Invetory Stock Item Data';

ALTER TABLE `cataloginventory_stock_item` ADD UNIQUE INDEX IDX_STOCK_PRODUCT(`product_id`, `stock_id`);

insert into cataloginventory_stock_item select null, entity_id, 1, 100, 0, 1, 0, 0, 1, 1 from catalog_product_entity;

");
$installer->endSetup();