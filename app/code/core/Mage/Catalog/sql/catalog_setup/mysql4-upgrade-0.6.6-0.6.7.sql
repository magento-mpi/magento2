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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `catalog_product_super_attribute` */

DROP TABLE IF EXISTS `catalog_product_super_attribute`;

CREATE TABLE `catalog_product_super_attribute` (
  `product_super_attribute_id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `position` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`product_super_attribute_id`),
  KEY `FK_SUPER_PRODUCT_ATTRIBUTE_PRODUCT` (`product_id`),
  CONSTRAINT `FK_SUPER_PRODUCT_ATTRIBUTE_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `catalog_product_super_attribute` */

/*Table structure for table `catalog_product_super_attribute_label` */

DROP TABLE IF EXISTS `catalog_product_super_attribute_label`;

CREATE TABLE `catalog_product_super_attribute_label` (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `product_super_attribute_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `value` varchar(255) character set utf8 NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_SUPER_PRODUCT_ATTRIBUTE_LABEL` (`product_super_attribute_id`),
  CONSTRAINT `FK_SUPER_PRODUCT_ATTRIBUTE_LABEL` FOREIGN KEY (`product_super_attribute_id`) REFERENCES `catalog_product_super_attribute` (`product_super_attribute_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

/*Data for the table `catalog_product_super_attribute_label` */

/*Table structure for table `catalog_product_super_attribute_pricing` */

DROP TABLE IF EXISTS `catalog_product_super_attribute_pricing`;

CREATE TABLE `catalog_product_super_attribute_pricing` (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `product_super_attribute_id` int(10) unsigned NOT NULL default '0',
  `value_index` varchar(255) character set utf8 NOT NULL default '',
  `is_percent` tinyint(1) unsigned default '0',
  `pricing_value` decimal(10,4) default NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_SUPER_PRODUCT_ATTRIBUTE_PRICING` (`product_super_attribute_id`),
  CONSTRAINT `FK_SUPER_PRODUCT_ATTRIBUTE_PRICING` FOREIGN KEY (`product_super_attribute_id`) REFERENCES `catalog_product_super_attribute` (`product_super_attribute_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `catalog_product_super_attribute_pricing` */

/*Table structure for table `catalog_product_super_link` */

DROP TABLE IF EXISTS `catalog_product_super_link`;

CREATE TABLE `catalog_product_super_link` (
  `link_id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL default '0',
  `parent_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`link_id`),
  KEY `FK_SUPER_PRODUCT_LINK_PARENT` (`parent_id`),
  KEY `FK_catalog_product_super_link` (`product_id`),
  CONSTRAINT `FK_SUPER_PRODUCT_LINK_ENTITY` FOREIGN KEY (`product_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_SUPER_PRODUCT_LINK_PARENT` FOREIGN KEY (`parent_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `catalog_product_super_link` */



alter table `catalog_category_entity_datetime` add index `value_by_attribute` (`attribute_id`, `value`);
alter table `catalog_category_entity_datetime` add index `value_by_entity_type` (`entity_type_id`, `value`);
alter table `catalog_category_entity_decimal` add index `value_by_attribute` (`attribute_id`, `value`);
alter table `catalog_category_entity_decimal` add index `value_by_entity_type` (`entity_type_id`, `value`);
alter table `catalog_category_entity_int` add index `value_by_attribute` (`attribute_id`, `value`);
alter table `catalog_category_entity_int` add index `value_by_entity_type` (`entity_type_id`, `value`);
alter table `catalog_category_entity_varchar` add index `value_by_attribute` (`attribute_id`, `value`);
alter table `catalog_category_entity_varchar` add index `value_by_entity_type` (`entity_type_id`, `value`);

alter table `catalog_product_entity_datetime` add index `value_by_attribute` (`attribute_id`, `value`);
alter table `catalog_product_entity_datetime` add index `value_by_entity_type` (`entity_type_id`, `value`);
alter table `catalog_product_entity_decimal` add index `value_by_attribute` (`attribute_id`, `value`);
alter table `catalog_product_entity_decimal` add index `value_by_entity_type` (`entity_type_id`, `value`);
alter table `catalog_product_entity_int` add index `value_by_attribute` (`attribute_id`, `value`);
alter table `catalog_product_entity_int` add index `value_by_entity_type` (`entity_type_id`, `value`);
alter table `catalog_product_entity_varchar` add index `value_by_attribute` (`attribute_id`, `value`);
alter table `catalog_product_entity_varchar` add index `value_by_entity_type` (`entity_type_id`, `value`);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;

