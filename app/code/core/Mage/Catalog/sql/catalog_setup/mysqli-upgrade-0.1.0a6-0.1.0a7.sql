/*
SQLyog Enterprise - MySQL GUI v5.13
Host - 4.1.21-community-nt : Database - pepper
*********************************************************************
Server version : 4.1.21-community-nt
*/

SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `catalog_category_attribute` */

DROP TABLE IF EXISTS `catalog_category_attribute`;

CREATE TABLE `catalog_category_attribute` (
  `attribute_id` int(10) unsigned NOT NULL auto_increment,
  `attribute_code` varchar(32) NOT NULL default '',
  `input_type` varchar(32) NOT NULL default '',
  `is_user_defined` tinyint(1) unsigned NOT NULL default '0',
  `is_required` tinyint(1) unsigned NOT NULL default '0',
  `sort_order` smallint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Categories and products attributes set';

/*Data for the table `catalog_category_attribute` */

/*Table structure for table `catalog_category_attribute_value` */

DROP TABLE IF EXISTS `catalog_category_attribute_value`;

CREATE TABLE `catalog_category_attribute_value` (
  `category_id` mediumint(9) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `attribute_id` int(10) unsigned NOT NULL default '0',
  `attribute_value` text NOT NULL,
  PRIMARY KEY  (`category_id`,`website_id`),
  KEY `FK_CATEGORY_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CATEGORY_ATTRIBUTE_WEBSITE` (`website_id`),
  CONSTRAINT `FK_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES `catalog_category_tree` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATEGORY_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_category_attribute` (`attribute_id`),
  CONSTRAINT `FK_CATEGORY_ATTRIBUTE_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Categories detale information';

/*Data for the table `catalog_category_attribute_value` */

/*Table structure for table `catalog_category_product` */

DROP TABLE IF EXISTS `catalog_category_product`;

CREATE TABLE `catalog_category_product` (
  `category_id` mediumint(9) unsigned NOT NULL default '0',
  `product_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`category_id`,`product_id`),
  KEY `FK_CATEGORY_PRODUCT` (`product_id`),
  CONSTRAINT `FK_CATEGORY_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES `catalog_category_tree` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Products in categories';

/*Data for the table `catalog_category_product` */

/*Table structure for table `catalog_category_tree` */

DROP TABLE IF EXISTS `catalog_category_tree`;

CREATE TABLE `catalog_category_tree` (
  `category_id` mediumint(9) unsigned NOT NULL auto_increment,
  `website_id` smallint(6) unsigned default '0',
  `pid` mediumint(9) unsigned NOT NULL default '0',
  `left_key` mediumint(9) unsigned NOT NULL default '0',
  `right_key` mediumint(9) unsigned NOT NULL default '0',
  `level` smallint(4) unsigned NOT NULL default '0',
  `title` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`category_id`),
  KEY `FK_CATALOG_WEBSITE` (`website_id`),
  CONSTRAINT `FK_CATALOG_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Categories tree';

/*Data for the table `catalog_category_tree` */

insert into `catalog_category_tree` (`category_id`,`website_id`,`pid`,`left_key`,`right_key`,`level`,`title`) values (1,1,0,1,46,0,'Root'),(2,1,1,2,23,1,'BROWSE BY TOPIC'),(3,1,2,3,4,2,'9-1-1'),(4,1,2,5,6,2,'Bicycle Safety'),(5,1,2,7,8,2,'Bullying'),(6,1,2,9,10,2,'Drug Abuse'),(7,1,2,11,12,2,'Halloween Safety'),(8,1,2,13,14,2,'Internet Safety'),(9,1,2,15,16,2,'Law Enforcement'),(10,1,2,17,18,2,'School Safety'),(11,1,2,19,20,2,'Senior Safety'),(12,1,2,21,22,2,'Stranger Awareness'),(13,1,1,24,45,1,'BROWSE BY PRODUCT'),(14,1,13,25,26,2,'Bookmarks Store'),(15,1,13,27,28,2,'Brochures'),(16,1,13,29,30,2,'Coloring Books'),(17,1,13,31,32,2,'Evidence Packaging'),(18,1,13,33,34,2,'Litter/Literature Bags'),(19,1,13,35,36,2,'Pencils'),(20,1,13,37,38,2,'Reflectives'),(21,1,13,39,40,2,'Safety Kits'),(22,1,13,41,42,2,'Slide Guides'),(23,1,13,43,44,2,'Spanish Products');

/*Table structure for table `catalog_product` */

DROP TABLE IF EXISTS `catalog_product`;

CREATE TABLE `catalog_product` (
  `product_id` int(11) unsigned NOT NULL auto_increment,
  `category_id` mediumint(9) unsigned NOT NULL default '0',
  `weight` smallint(6) unsigned NOT NULL default '0',
  `price` decimal(6,2) NOT NULL default '0.00',
  `base_prop1` int(4) NOT NULL default '0',
  `base_prop2` int(4) NOT NULL default '0',
  PRIMARY KEY  (`product_id`),
  KEY `FK_DEFAULT_CATEGORY` (`category_id`),
  KEY `IDX_CATEGORY` (`category_id`),
  KEY `IDX_WEIGHT` (`weight`),
  KEY `IDX_PRICE` (`price`),
  CONSTRAINT `FK_DEFAULT_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES `catalog_category_tree` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Products';

/*Data for the table `catalog_product` */

/*Table structure for table `catalog_product_attribute` */

DROP TABLE IF EXISTS `catalog_product_attribute`;

CREATE TABLE `catalog_product_attribute` (
  `attribute_id` smallint(6) unsigned NOT NULL auto_increment,
  `field_name` varchar(32) NOT NULL default '',
  `input_type` varchar(32) NOT NULL default '',
  `data_type` varchar(32) NOT NULL default '',
  `is_extension` tinyint(1) unsigned default NULL,
  `is_required` tinyint(1) unsigned default NULL,
  `sort_order` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Product attributes defination';

/*Data for the table `catalog_product_attribute` */

/*Table structure for table `catalog_product_attribute_group` */

DROP TABLE IF EXISTS `catalog_product_attribute_group`;

CREATE TABLE `catalog_product_attribute_group` (
  `group_id` smallint(6) unsigned NOT NULL auto_increment,
  `group_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Group of attributes';

/*Data for the table `catalog_product_attribute_group` */

/*Table structure for table `catalog_product_attribute_in_group` */

DROP TABLE IF EXISTS `catalog_product_attribute_in_group`;

CREATE TABLE `catalog_product_attribute_in_group` (
  `attribute_id` smallint(6) unsigned default NULL,
  `group_id` smallint(6) unsigned default NULL,
  KEY `FK_PRODUCT_ATTRIBUTE_GROUP` (`group_id`),
  KEY `FK_PRODUCT_GROUP_ATTRIBUTE` (`attribute_id`),
  CONSTRAINT `FK_PRODUCT_ATTRIBUTE_GROUP` FOREIGN KEY (`group_id`) REFERENCES `catalog_product_attribute_group` (`group_id`),
  CONSTRAINT `FK_PRODUCT_GROUP_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Attributes in group';

/*Data for the table `catalog_product_attribute_in_group` */

/*Table structure for table `catalog_product_extension` */

DROP TABLE IF EXISTS `catalog_product_extension`;

CREATE TABLE `catalog_product_extension` (
  `product_id` int(11) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `name` varchar(128) NOT NULL default '',
  `description` text NOT NULL,
  `ext_prop1` text,
  `ext_prop2` date NOT NULL default '0000-00-00',
  `ext_prop3` datetime NOT NULL default '0000-00-00 00:00:00',
  `ext_prop4` text,
  `ext_prop5` text,
  `ext_prop6` int(11) NOT NULL default '0',
  `ext_prop7` tinytext,
  `ext_prop8` float default NULL,
  PRIMARY KEY  (`product_id`,`website_id`),
  KEY `FK_PRODUCT_ATTRIBUTE_WEBSITE` (`website_id`),
  KEY `IDX_name` (`name`),
  KEY `IDX_ext_prop2` (`ext_prop2`),
  KEY `IDX_ext_prop3` (`ext_prop3`),
  CONSTRAINT `FK_ATTRIBUTE_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`),
  CONSTRAINT `FK_PRODUCT_ATTRIBUTE_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Products attributes';

/*Data for the table `catalog_product_extension` */

/*Table structure for table `catalog_product_link` */

DROP TABLE IF EXISTS `catalog_product_link`;

CREATE TABLE `catalog_product_link` (
  `product_id` int(11) unsigned NOT NULL default '0',
  `linked_product_id` int(11) unsigned NOT NULL default '0',
  `link_type_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`product_id`,`linked_product_id`,`link_type_id`),
  KEY `FK_LINKED_PRODUCT` (`linked_product_id`),
  KEY `FK_PRODUCT_LINK_TYPE` (`link_type_id`),
  CONSTRAINT `FK_LINKED_PRODUCT` FOREIGN KEY (`linked_product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_LINK_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_LINK_TYPE` FOREIGN KEY (`link_type_id`) REFERENCES `catalog_product_link_type` (`link_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Related products';

/*Data for the table `catalog_product_link` */

/*Table structure for table `catalog_product_link_type` */

DROP TABLE IF EXISTS `catalog_product_link_type`;

CREATE TABLE `catalog_product_link_type` (
  `link_type_id` tinyint(3) unsigned NOT NULL auto_increment,
  `link_type_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`link_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Types of product link(Related, superproduct, bundles)';

/*Data for the table `catalog_product_link_type` */

insert into `catalog_product_link_type` (`link_type_id`,`link_type_code`) values (1,'related');

/*Table structure for table `core_website` */

DROP TABLE IF EXISTS `core_website`;

CREATE TABLE `core_website` (
  `website_id` smallint(6) unsigned NOT NULL auto_increment,
  `language_code` char(2) default NULL,
  `website_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`website_id`),
  KEY `FK_WEBSITE_LANGUAGE` (`language_code`),
  CONSTRAINT `FK_WEBSITE_LANGUAGE` FOREIGN KEY (`language_code`) REFERENCES `core_language` (`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='MySQL table';

/*Data for the table `core_website` */

insert into `core_website` (`website_id`,`language_code`,`website_code`) values (1,'en','Pepper eCommerce'),(2,'en','WebSite 2'),(3,'en','WebSite 3'),(4,'en','WebSite 4'),(5,'en','WebSite 5');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
