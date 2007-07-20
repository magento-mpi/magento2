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

insert into `catalog_category_entity` (`entity_id`,`entity_type_id`,`attribute_set_id`,`parent_id`,`store_id`,`created_at`,`updated_at`,`is_active`) values (1,9,12,0,1,'2007-07-20 18:46:08','2007-07-20 18:46:08',1),(2,9,12,1,1,'2007-07-20 18:46:08','2007-07-20 18:46:08',1),(3,9,12,1,1,'2007-07-20 18:46:08','2007-07-20 11:31:40',1),(15,9,12,3,1,'2007-07-20 18:46:08','2007-07-20 18:46:08',1),(16,9,12,3,1,'2007-07-20 18:46:08','2007-07-20 18:46:08',0),(17,9,12,3,1,'2007-07-20 18:46:08','2007-07-20 18:46:08',1),(18,9,12,2,1,'2007-07-20 18:46:08','2007-07-20 18:46:08',1),(19,9,12,2,1,'2007-07-20 18:46:08','2007-07-20 18:46:08',0),(37,9,12,15,0,'2007-07-20 11:20:59','2007-07-20 11:20:59',1),(38,9,12,15,0,'2007-07-20 11:22:10','2007-07-20 11:22:10',1);

/*Table structure for table `catalog_category_entity_datetime` */

DROP TABLE IF EXISTS `catalog_category_entity_datetime`;

CREATE TABLE `catalog_category_entity_datetime` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_DATETIME_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_DATETIME_ATTRIBUTE` (`attribute_id`),
  KEY `FK_ATTRIBUTE_DATETIME_STORE` (`store_id`),
  KEY `FK_ATTRIBUTE_DATETIME_ENTITY` (`entity_id`),
  CONSTRAINT `FK_CATALOG_CATEGORY_ENTITY_DATETIME_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `catalog_category_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_category_entity_datetime` */

/*Table structure for table `catalog_category_entity_decimal` */

DROP TABLE IF EXISTS `catalog_category_entity_decimal`;

CREATE TABLE `catalog_category_entity_decimal` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_DECIMAL_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_DECIMAL_ATTRIBUTE` (`attribute_id`),
  KEY `FK_ATTRIBUTE_DECIMAL_STORE` (`store_id`),
  KEY `FK_ATTRIBUTE_DECIMAL_ENTITY` (`entity_id`),
  CONSTRAINT `FK_CATALOG_CATEGORY_ENTITY_DECIMAL_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `catalog_category_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_category_entity_decimal` */

/*Table structure for table `catalog_category_entity_int` */

DROP TABLE IF EXISTS `catalog_category_entity_int`;

CREATE TABLE `catalog_category_entity_int` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_INT_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_INT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_ATTRIBUTE_INT_STORE` (`store_id`),
  KEY `FK_ATTRIBUTE_INT_ENTITY` (`entity_id`),
  CONSTRAINT `FK_CATALOG_CATEGORY_EMTITY_INT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `catalog_category_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_category_entity_int` */

/*Table structure for table `catalog_category_entity_text` */

DROP TABLE IF EXISTS `catalog_category_entity_text`;

CREATE TABLE `catalog_category_entity_text` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_TEXT_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_TEXT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_ATTRIBUTE_TEXT_STORE` (`store_id`),
  KEY `FK_ATTRIBUTE_TEXT_ENTITY` (`entity_id`),
  CONSTRAINT `FK_CATALOG_CATEGORY_ENTITY_TEXT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `catalog_category_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_category_entity_text` */

insert into `catalog_category_entity_text` (`value_id`,`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values (3,9,112,1,37,'My description'),(4,9,112,1,38,'My description'),(6,9,112,1,3,'123123');

/*Table structure for table `catalog_category_entity_varchar` */

DROP TABLE IF EXISTS `catalog_category_entity_varchar`;

CREATE TABLE `catalog_category_entity_varchar` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_VARCHAR_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_VARCHAR_ATTRIBUTE` (`attribute_id`),
  KEY `FK_ATTRIBUTE_VARCHAR_STORE` (`store_id`),
  KEY `FK_ATTRIBUTE_VARCHAR_ENTITY` (`entity_id`),
  CONSTRAINT `FK_CATALOG_CATEGORY_ENTITY_VARCHAR_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `catalog_category_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_category_entity_varchar` */

insert into `catalog_category_entity_varchar` (`value_id`,`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values (1,9,111,1,1,'ROOT'),(2,9,111,1,3,'Store 1 root'),(9,9,111,1,2,'Store 2 root'),(12,9,111,1,15,'Category 1'),(13,9,111,1,16,'Category 2'),(14,9,111,1,17,'Category 3'),(15,9,111,1,18,'Category 4'),(16,9,111,1,19,'Category 5'),(19,9,111,1,37,'My category'),(20,9,111,1,38,'My category');

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
  CONSTRAINT `FK_CATALOG_CATEGORY_TREE_PARENT` FOREIGN KEY (`pid`) REFERENCES `catalog_category_tree` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categories tree';

/*Data for the table `catalog_category_tree` */

insert into `catalog_category_tree` (`entity_id`,`pid`,`left_key`,`right_key`,`level`,`order`) values (1,NULL,0,0,0,1),(2,1,0,0,1,2),(3,1,0,0,1,1),(15,3,0,0,2,1),(16,3,0,0,2,2),(17,2,0,0,2,1),(18,2,0,0,2,2),(19,2,0,0,2,3),(37,15,0,0,3,1),(38,15,0,0,3,2);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
