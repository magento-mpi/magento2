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

/*Table structure for table `customer_entity` */

DROP TABLE IF EXISTS `customer_entity`;

CREATE TABLE `customer_entity` (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
  `parent_id` int(10) unsigned default NULL,
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `is_active` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`entity_id`),
  KEY `FK_CUSTOMER_ENTITY_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_CUSTOMER_ENTITY_STORE` (`store_id`),
  KEY `FK_CUSTOMER_ENTITY_PARENT_ENTITY` (`parent_id`),
  CONSTRAINT `FK_CUSTOMER_ENTITY_PARENT_ENTITY` FOREIGN KEY (`parent_id`) REFERENCES `customer_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Customer Entityies';

/*Data for the table `customer_entity` */

insert into `customer_entity` (`entity_id`,`entity_type_id`,`attribute_set_id`,`parent_id`,`store_id`,`created_at`,`updated_at`,`is_active`) values (3,1,0,NULL,1,'2007-07-20 00:23:01','2007-07-20 00:53:14',1),(4,2,0,3,1,'2007-07-20 00:23:01','2007-07-20 00:53:14',1),(5,2,0,3,1,'2007-07-20 00:24:45','2007-07-20 00:53:14',1);

/*Table structure for table `customer_entity_datetime` */

DROP TABLE IF EXISTS `customer_entity_datetime`;

CREATE TABLE `customer_entity_datetime` (
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
  CONSTRAINT `FK_CUSTOMER_ENTITY_DATETIME_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `customer_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `customer_entity_datetime` */

/*Table structure for table `customer_entity_decimal` */

DROP TABLE IF EXISTS `customer_entity_decimal`;

CREATE TABLE `customer_entity_decimal` (
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
  CONSTRAINT `FK_CUSTOMER_ENTITY_DECIMAL_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `customer_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `customer_entity_decimal` */

insert into `customer_entity_decimal` (`value_id`,`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values (238,1,6,1,3,0.0000);

/*Table structure for table `customer_entity_int` */

DROP TABLE IF EXISTS `customer_entity_int`;

CREATE TABLE `customer_entity_int` (
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
  CONSTRAINT `FK_CUSTOMER_ENTITY_INT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `customer_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `customer_entity_int` */

insert into `customer_entity_int` (`value_id`,`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values (12068,1,5,1,3,1),(12071,2,11,1,4,223),(12072,2,13,1,4,12),(12077,2,11,1,5,223),(12078,2,13,1,5,12),(12079,1,7,1,3,5),(12080,1,8,1,3,4);

/*Table structure for table `customer_entity_text` */

DROP TABLE IF EXISTS `customer_entity_text`;

CREATE TABLE `customer_entity_text` (
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
  CONSTRAINT `FK_CUSTOMER_ENTITY_TEXT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `customer_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `customer_entity_text` */

insert into `customer_entity_text` (`value_id`,`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values (3979,2,16,1,4,'street\r\naddress'),(3980,2,16,1,5,'QA shipping');

/*Table structure for table `customer_entity_varchar` */

DROP TABLE IF EXISTS `customer_entity_varchar`;

CREATE TABLE `customer_entity_varchar` (
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
  CONSTRAINT `FK_CUSTOMER_ENTITY_VARCHAR_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `customer_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `customer_entity_varchar` */

insert into `customer_entity_varchar` (`value_id`,`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values (32397,1,1,1,3,'QA'),(32398,1,2,1,3,'QA'),(32399,1,3,1,3,'qa@varien.com'),(32400,1,4,1,3,'4297f44b13955235245b2497399d7a93'),(32401,2,9,1,4,'QA'),(32402,2,10,1,4,'QA'),(32403,2,95,1,4,'QA'),(32404,2,12,1,4,'California'),(32405,2,14,1,4,'90034'),(32406,2,15,1,4,'LA'),(32407,2,17,1,4,'111-1111-111'),(32408,2,9,1,5,'QA shipping'),(32409,2,10,1,5,'QA shipping'),(32410,2,95,1,5,'QA shipping'),(32411,2,12,1,5,'California'),(32412,2,14,1,5,'90034'),(32413,2,15,1,5,'QA shipping'),(32414,2,17,1,5,'111111111111');

/*Table structure for table `customer_group` */

DROP TABLE IF EXISTS `customer_group`;

CREATE TABLE `customer_group` (
  `customer_group_id` tinyint(3) unsigned NOT NULL auto_increment,
  `customer_group_code` varchar(32) NOT NULL default '',
  `tax_class_id` int unsigned not null,
  PRIMARY KEY  (`customer_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Customer groups';

/*Data for the table `customer_group` */

insert into `customer_group` (`customer_group_id`,`customer_group_code`) values (0,'NOT LOGGED IN'),(1,'General'),(2,'Wholesale');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
