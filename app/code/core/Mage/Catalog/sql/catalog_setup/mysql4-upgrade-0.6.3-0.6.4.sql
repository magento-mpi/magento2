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
/*
SQLyog Enterprise - MySQL GUI v5.13
Host - 4.1.22 : Database - magento_dmitriy
*********************************************************************
Server version : 4.1.22
*/

SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `catalog_category_entity` */

DROP TABLE IF EXISTS `catalog_category_entity`;

CREATE TABLE `catalog_category_entity` (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
  `parent_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `is_active` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`entity_id`),
  KEY `FK_catalog_category_ENTITY_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_catalog_category_ENTITY_STORE` (`store_id`),
  CONSTRAINT `FK_CATALOG_CATEGORY_ENTITY_TREE_NODE` FOREIGN KEY (`entity_id`) REFERENCES `catalog_category_tree` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Category Entityies';

/*Data for the table `catalog_category_entity` */

insert into `catalog_category_entity` (`entity_id`,`entity_type_id`,`attribute_set_id`,`parent_id`,`store_id`,`created_at`,`updated_at`,`is_active`) values (1,9,12,0,1,'2007-07-20 18:46:08','2007-08-07 09:50:15',1),(158,9,12,1,0,'2007-08-07 09:37:05','2007-08-07 10:34:46',1),(159,9,12,1,0,'2007-08-07 09:38:15','2007-08-07 10:04:22',1),(160,9,12,1,0,'2007-08-07 09:38:54','2007-08-07 09:50:11',1),(161,9,12,158,0,'2007-08-07 09:50:48','2007-08-07 10:34:46',1),(162,9,12,158,0,'2007-08-07 09:51:50','2007-08-07 10:22:31',1),(163,9,12,158,0,'2007-08-07 09:52:25','2007-08-07 10:22:31',1),(164,9,12,158,0,'2007-08-07 09:53:07','2007-08-07 10:22:31',1),(165,9,12,158,0,'2007-08-07 09:53:29','2007-08-07 10:22:31',1),(166,9,12,161,0,'2007-08-07 10:24:03','2007-08-07 10:24:03',1),(167,9,12,161,0,'2007-08-07 10:33:34','2007-08-07 10:33:34',1),(168,9,12,161,0,'2007-08-07 10:33:57','2007-08-07 10:33:57',1),(169,9,12,161,0,'2007-08-07 10:34:24','2007-08-07 10:34:24',1),(170,9,12,161,0,'2007-08-07 10:34:46','2007-08-07 10:34:46',1);

/*Table structure for table `catalog_category_entity_datetime` */

DROP TABLE IF EXISTS `catalog_category_entity_datetime`;

CREATE TABLE `catalog_category_entity_datetime` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_BASE` (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_ATTRIBUTE_DATETIME_ENTITY` (`entity_id`),
  KEY `FK_CATALOG_CATEGORY_ENTITY_DATETIME_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CATALOG_CATEGORY_ENTITY_DATETIME_STORE` (`store_id`),
  CONSTRAINT `FK_CATALOG_CATEGORY_ENTITY_DATETIME_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `eav_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_CATEGORY_ENTITY_DATETIME_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `catalog_category_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_CATEGORY_ENTITY_DATETIME_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_category_entity_datetime` */

/*Table structure for table `catalog_category_entity_decimal` */

DROP TABLE IF EXISTS `catalog_category_entity_decimal`;

CREATE TABLE `catalog_category_entity_decimal` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_BASE` (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_ATTRIBUTE_DECIMAL_ENTITY` (`entity_id`),
  KEY `FK_CATALOG_CATEGORY_ENTITY_DECIMAL_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CATALOG_CATEGORY_ENTITY_DECIMAL_STORE` (`store_id`),
  CONSTRAINT `FK_CATALOG_CATEGORY_ENTITY_DECIMAL_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `eav_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_CATEGORY_ENTITY_DECIMAL_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `catalog_category_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_CATEGORY_ENTITY_DECIMAL_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_category_entity_decimal` */

/*Table structure for table `catalog_category_entity_int` */

DROP TABLE IF EXISTS `catalog_category_entity_int`;

CREATE TABLE `catalog_category_entity_int` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_BASE` (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_ATTRIBUTE_INT_ENTITY` (`entity_id`),
  KEY `FK_CATALOG_CATEGORY_EMTITY_INT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CATALOG_CATEGORY_EMTITY_INT_STORE` (`store_id`),
  CONSTRAINT `FK_CATALOG_CATEGORY_EMTITY_INT_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_CATEGORY_EMTITY_INT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `eav_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_CATEGORY_EMTITY_INT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `catalog_category_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_category_entity_int` */

insert into `catalog_category_entity_int` (`value_id`,`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values (38,9,120,0,166,1),(39,9,120,1,166,1);

/*Table structure for table `catalog_category_entity_text` */

DROP TABLE IF EXISTS `catalog_category_entity_text`;

CREATE TABLE `catalog_category_entity_text` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_BASE` (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_ATTRIBUTE_TEXT_ENTITY` (`entity_id`),
  KEY `FK_CATALOG_CATEGORY_ENTITY_TEXT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CATALOG_CATEGORY_ENTITY_TEXT_STORE` (`store_id`),
  CONSTRAINT `FK_CATALOG_CATEGORY_ENTITY_TEXT_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_CATEGORY_ENTITY_TEXT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `eav_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_CATEGORY_ENTITY_TEXT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `catalog_category_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_category_entity_text` */

insert into `catalog_category_entity_text` (`value_id`,`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values (625,9,121,0,158,'158,161,166,167,168,169,170,162,163,164,165'),(628,9,121,0,159,'159'),(629,9,122,0,159,'159'),(631,9,121,0,160,'160'),(632,9,122,0,160,'160'),(633,9,123,0,160,''),(634,9,123,0,159,''),(641,9,121,0,1,'1,158,159,160'),(646,9,122,0,1,''),(651,9,123,0,1,'158,159,160'),(657,9,121,0,161,'161,166,167,168,169,170'),(658,9,122,0,161,'161'),(660,9,123,0,158,'161,162,163,164,165'),(661,9,121,0,162,'162'),(662,9,122,0,162,'162'),(663,9,123,0,162,''),(664,9,121,0,163,'163'),(665,9,122,0,163,'163'),(666,9,123,0,163,''),(667,9,121,0,164,'164'),(668,9,122,0,164,'164'),(669,9,123,0,164,''),(670,9,121,0,165,'165'),(671,9,122,0,165,'165'),(672,9,123,0,165,''),(692,9,121,1,158,'158,161,166,167,168,169,170,162,163,164,165'),(694,9,123,1,158,'161,162,163,164,165'),(695,9,121,1,161,'161,166,167,168,169,170'),(696,9,122,1,161,'161'),(697,9,123,1,161,'166,167,168,169,170'),(699,9,121,1,162,'162'),(700,9,122,1,162,'162'),(701,9,123,1,162,''),(703,9,121,1,163,'163'),(704,9,122,1,163,'163'),(705,9,123,1,163,''),(707,9,121,1,164,'164'),(708,9,122,1,164,'164'),(709,9,123,1,164,''),(711,9,121,1,165,'165'),(712,9,122,1,165,'165'),(713,9,123,1,165,''),(714,9,122,1,158,''),(715,9,121,0,166,'166'),(716,9,121,1,166,'166'),(717,9,122,0,166,'166,161'),(718,9,122,1,166,'166,161'),(719,9,123,0,166,''),(720,9,123,1,166,'');
insert into `catalog_category_entity_text` (`value_id`,`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values (721,9,123,0,161,'166,167,168,169,170'),(724,9,121,0,167,'167'),(725,9,121,1,167,'167'),(726,9,122,0,167,'167,161'),(727,9,122,1,167,'167,161'),(728,9,123,0,167,''),(729,9,123,1,167,''),(732,9,121,0,168,'168'),(733,9,121,1,168,'168'),(734,9,122,0,168,'168,161'),(735,9,122,1,168,'168,161'),(736,9,123,0,168,''),(737,9,123,1,168,''),(740,9,121,0,169,'169'),(741,9,121,1,169,'169'),(742,9,122,0,169,'169,161'),(743,9,122,1,169,'169,161'),(744,9,123,0,169,''),(745,9,123,1,169,''),(748,9,121,0,170,'170'),(749,9,121,1,170,'170'),(750,9,122,0,170,'170,161'),(751,9,122,1,170,'170,161'),(752,9,123,0,170,''),(753,9,123,1,170,''),(755,9,122,0,158,'');

/*Table structure for table `catalog_category_entity_varchar` */

DROP TABLE IF EXISTS `catalog_category_entity_varchar`;

CREATE TABLE `catalog_category_entity_varchar` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_BASE` USING BTREE (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_ATTRIBUTE_VARCHAR_ENTITY` (`entity_id`),
  KEY `FK_CATALOG_CATEGORY_ENTITY_VARCHAR_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CATALOG_CATEGORY_ENTITY_VARCHAR_STORE` (`store_id`),
  CONSTRAINT `FK_CATALOG_CATEGORY_ENTITY_VARCHAR_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_CATEGORY_ENTITY_VARCHAR_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `eav_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_CATEGORY_ENTITY_VARCHAR_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `catalog_category_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_category_entity_varchar` */

insert into `catalog_category_entity_varchar` (`value_id`,`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values (1,9,111,0,1,'ROOT'),(490,9,111,0,158,'Base Store Root'),(491,9,118,0,158,'PRODUCTS'),(492,9,111,0,159,'Summer Store'),(493,9,118,0,159,'PRODUCTS'),(494,9,111,0,160,'Winter Store'),(495,9,118,0,160,'PRODUCTS'),(496,9,111,0,161,'Apparel'),(497,9,118,0,161,'PRODUCTS'),(498,9,111,0,162,'Electronics'),(499,9,118,0,162,'PRODUCTS'),(500,9,111,0,163,'Books'),(501,9,118,0,163,'PRODUCTS'),(502,9,111,0,164,'Entertainment'),(503,9,118,0,164,'PRODUCTS'),(504,9,111,0,165,'Garden'),(505,9,118,0,165,'PRODUCTS'),(520,9,111,1,158,'Base Store Root'),(521,9,118,1,158,'PRODUCTS'),(522,9,111,1,161,'Apparel'),(523,9,118,1,161,'PRODUCTS'),(524,9,111,1,162,'Electronics'),(525,9,118,1,162,'PRODUCTS'),(526,9,111,1,163,'Books'),(527,9,118,1,163,'PRODUCTS'),(528,9,111,1,164,'Entertainment'),(529,9,118,1,164,'PRODUCTS'),(530,9,111,1,165,'Garden'),(531,9,118,1,165,'PRODUCTS'),(532,9,111,0,166,'Accessories'),(533,9,111,1,166,'Accessories'),(534,9,118,0,166,'PRODUCTS'),(535,9,118,1,166,'PRODUCTS'),(536,9,111,0,167,'Bags'),(537,9,111,1,167,'Bags'),(538,9,118,0,167,'PRODUCTS'),(539,9,118,1,167,'PRODUCTS'),(540,9,111,0,168,'Shoes'),(541,9,111,1,168,'Shoes'),(542,9,118,0,168,'PRODUCTS'),(543,9,118,1,168,'PRODUCTS'),(544,9,111,0,169,'Mittens'),(545,9,111,1,169,'Mittens'),(546,9,118,0,169,'PRODUCTS'),(547,9,118,1,169,'PRODUCTS');
insert into `catalog_category_entity_varchar` (`value_id`,`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values (548,9,111,0,170,'Gloves'),(549,9,111,1,170,'Gloves'),(550,9,118,0,170,'PRODUCTS'),(551,9,118,1,170,'PRODUCTS');

/*Table structure for table `catalog_category_tree` */

DROP TABLE IF EXISTS `catalog_category_tree`;

CREATE TABLE `catalog_category_tree` (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned default '0',
  `left_key` int(10) unsigned default '0',
  `right_key` int(10) unsigned default '0',
  `level` smallint(4) unsigned NOT NULL default '0',
  `order` smallint(6) unsigned NOT NULL default '1',
  PRIMARY KEY  (`entity_id`),
  KEY `FK_CATEGORY_PARENT` (`pid`),
  KEY `IDX_ORDER` (`order`),
  KEY `IDX_LEVEL` (`level`),
  KEY `IDX_ORDER_LEVEL` (`level`,`order`),
  CONSTRAINT `FK_CATALOG_CATEGORY_TREE_PARENT` FOREIGN KEY (`pid`) REFERENCES `catalog_category_tree` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categories tree';

/*Data for the table `catalog_category_tree` */

insert into `catalog_category_tree` (`entity_id`,`pid`,`left_key`,`right_key`,`level`,`order`) values (1,NULL,0,0,0,1),(158,1,0,0,1,1),(159,1,0,0,1,2),(160,1,0,0,1,3),(161,158,0,0,2,1),(162,158,0,0,2,2),(163,158,0,0,2,3),(164,158,0,0,2,4),(165,158,0,0,2,5),(166,161,0,0,3,1),(167,161,0,0,3,2),(168,161,0,0,3,3),(169,161,0,0,3,4),(170,161,0,0,3,5);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
