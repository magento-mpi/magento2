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

/*Table structure for table `catalog_category` */

DROP TABLE IF EXISTS `catalog_category`;

CREATE TABLE `catalog_category` (
  `category_id` mediumint(9) unsigned NOT NULL auto_increment,
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `pid` mediumint(9) unsigned NOT NULL default '0',
  `left_key` mediumint(9) unsigned NOT NULL default '0',
  `right_key` mediumint(9) unsigned NOT NULL default '0',
  `level` smallint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`category_id`),
  KEY `FK_CATALOG_WEBSITE` (`website_id`),
  CONSTRAINT `FK_CATALOG_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Categories tree';

/*Data for the table `catalog_category` */

insert into `catalog_category` (`category_id`,`website_id`,`pid`,`left_key`,`right_key`,`level`) values (1,1,0,1,46,0),(2,1,1,2,23,1),(3,1,2,3,4,2),(4,1,2,5,6,2),(5,1,2,7,8,2),(6,1,2,9,10,2),(7,1,2,11,12,2),(8,1,2,13,14,2),(9,1,2,15,16,2),(10,1,2,17,18,2),(11,1,2,19,20,2),(12,1,2,21,22,2),(13,1,1,24,45,1),(14,1,13,25,26,2),(15,1,13,27,28,2),(16,1,13,29,30,2),(17,1,13,31,32,2),(18,1,13,33,34,2),(19,1,13,35,36,2),(20,1,13,37,38,2),(21,1,13,39,40,2),(22,1,13,41,42,2),(23,1,13,43,44,2);

/*Table structure for table `catalog_category_extension` */

DROP TABLE IF EXISTS `catalog_category_extension`;

CREATE TABLE `catalog_category_extension` (
  `category_id` mediumint(9) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `name` varchar(128) NOT NULL default '',
  `description` text,
  `image` varchar(128) default NULL,
  PRIMARY KEY  (`category_id`,`website_id`),
  KEY `FK_CATEGORY_EXTENSION_WEBSITE` (`website_id`),
  CONSTRAINT `FK_CATEGORY_EXTENSION_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`),
  CONSTRAINT `FK_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES `catalog_category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Categories detale information';

/*Data for the table `catalog_category_extension` */

insert into `catalog_category_extension` (`category_id`,`website_id`,`name`,`description`,`image`) values (1,1,'Root','Root',NULL),(2,1,'BROWSE BY TOPIC','BROWSE BY TOPIC',NULL),(3,1,'9-1-1','9-1-1',NULL),(4,1,'Bicycle Safety','Bicycle Safety',NULL),(5,1,'Bullying','Bullying',NULL),(6,1,'Drug Abuse','Drug Abuse',NULL),(7,1,'Halloween Safety','Halloween Safety',NULL),(8,1,'Internet Safety','Internet Safety',NULL),(9,1,'Law Enforcement','Law Enforcement',NULL),(10,1,'School Safety','School Safety',NULL),(11,1,'Senior Safety','Senior Safety',NULL),(12,1,'Stranger Awareness','Stranger Awareness',NULL),(13,1,'BROWSE BY PRODUCT','BROWSE BY PRODUCT',NULL),(14,1,'Bookmarks Store','Bookmarks Store',NULL),(15,1,'Brochures','Brochures',NULL),(16,1,'Coloring Books','Coloring Books',NULL),(17,1,'Evidence Packaging','Evidence Packaging',NULL),(18,1,'Litter/Literature Bags','Litter/Literature Bags',NULL),(19,1,'Pencils','Pencils',NULL),(20,1,'Reflectives','Reflectives',NULL),(21,1,'Safety Kits','Safety Kits',NULL),(22,1,'Slide Guides','Slide Guides',NULL),(23,1,'Spanish Products','Spanish Products',NULL);

/*Table structure for table `catalog_category_product` */

DROP TABLE IF EXISTS `catalog_category_product`;

CREATE TABLE `catalog_category_product` (
  `category_id` mediumint(9) unsigned NOT NULL default '0',
  `product_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`category_id`,`product_id`),
  KEY `FK_CATEGORY_PRODUCT` (`product_id`),
  CONSTRAINT `FK_PRODUCT_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES `catalog_category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATEGORY_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Products in categories';

/*Data for the table `catalog_category_product` */

/*Table structure for table `catalog_product` */

DROP TABLE IF EXISTS `catalog_product`;

CREATE TABLE `catalog_product` (
  `product_id` int(11) unsigned NOT NULL auto_increment,
  `category_id` mediumint(9) unsigned NOT NULL default '0',
  PRIMARY KEY  (`product_id`),
  KEY `FK_DEFAULT_CATEGORY` (`category_id`),
  CONSTRAINT `FK_DEFAULT_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES `catalog_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Products';

/*Data for the table `catalog_product` */

/*Table structure for table `catalog_product_attribute` */

DROP TABLE IF EXISTS `catalog_product_attribute`;

CREATE TABLE `catalog_product_attribute` (
  `attribute_id` smallint(6) unsigned NOT NULL auto_increment,
  `parent_attribute_id` smallint(6) unsigned default NULL,
  `attribute_name` varchar(32) NOT NULL default '',
  `input_type` varchar(32) NOT NULL default '',
  `data_type` enum('int','decimal','varchar','text','date') NOT NULL default 'int',
  `is_required` tinyint(1) unsigned default NULL,
  `sort_order` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attribute_id`),
  KEY `FK_PARENT_ATTRIBUTE` (`parent_attribute_id`),
  CONSTRAINT `FK_PARENT_ATTRIBUTE` FOREIGN KEY (`parent_attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Product attributes defination';

/*Data for the table `catalog_product_attribute` */

insert into `catalog_product_attribute` (`attribute_id`,`parent_attribute_id`,`attribute_name`,`input_type`,`data_type`,`is_required`,`sort_order`) values (1,NULL,'name','text','varchar',1,1),(2,NULL,'description','text','text',1,2),(3,NULL,'price','text','decimal',1,3),(4,NULL,'cost','text','decimal',1,4),(5,NULL,'weight','text','int',1,5),(6,NULL,'image','text','varchar',1,6),(7,6,'width','text','int',1,1),(8,6,'height','text','int',1,2);

/*Table structure for table `catalog_product_attribute_date` */

DROP TABLE IF EXISTS `catalog_product_attribute_date`;

CREATE TABLE `catalog_product_attribute_date` (
  `product_id` int(11) unsigned NOT NULL default '0',
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `attribute_value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`product_id`,`attribute_id`,`website_id`),
  KEY `FK_ATTRIBUTE_DATE` (`attribute_id`),
  KEY `FK_WEBSITE_DATE` (`website_id`),
  CONSTRAINT `FK_WEBSITE_DATE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_DATE` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_DATE` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Date attribute value';

/*Data for the table `catalog_product_attribute_date` */

/*Table structure for table `catalog_product_attribute_decimal` */

DROP TABLE IF EXISTS `catalog_product_attribute_decimal`;

CREATE TABLE `catalog_product_attribute_decimal` (
  `product_id` int(11) unsigned NOT NULL default '0',
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `attribute_value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`product_id`,`attribute_id`,`website_id`),
  KEY `FK_ATTRIBUTE_DECIMAL` (`attribute_id`),
  KEY `FK_WEBSITE_DECIMAL` (`website_id`),
  CONSTRAINT `FK_WEBSITE_DECIMAL` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_DECIMAL` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_DECIMAL` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Decimal values for product attributes';

/*Data for the table `catalog_product_attribute_decimal` */

/*Table structure for table `catalog_product_attribute_group` */

DROP TABLE IF EXISTS `catalog_product_attribute_group`;

CREATE TABLE `catalog_product_attribute_group` (
  `group_id` smallint(6) unsigned NOT NULL auto_increment,
  `group_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Group of attributes';

/*Data for the table `catalog_product_attribute_group` */

/*Table structure for table `catalog_product_attribute_in_group` */

DROP TABLE IF EXISTS `catalog_product_attribute_in_group`;

CREATE TABLE `catalog_product_attribute_in_group` (
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `group_id` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attribute_id`,`group_id`),
  KEY `FK_PRODUCT_ATTRIBUTE_GROUP` (`group_id`),
  CONSTRAINT `FK_PRODUCT_GROUP_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`),
  CONSTRAINT `FK_PRODUCT_ATTRIBUTE_GROUP` FOREIGN KEY (`group_id`) REFERENCES `catalog_product_attribute_group` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Attributes in group';

/*Data for the table `catalog_product_attribute_in_group` */

/*Table structure for table `catalog_product_attribute_int` */

DROP TABLE IF EXISTS `catalog_product_attribute_int`;

CREATE TABLE `catalog_product_attribute_int` (
  `product_id` int(11) unsigned NOT NULL default '0',
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `attribute_value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`product_id`,`attribute_id`,`website_id`),
  KEY `FK_ATTRIBUTE_INT` (`attribute_id`),
  KEY `FK_WEBSITE_INT` (`website_id`),
  CONSTRAINT `FK_WEBSITE_INT` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_INT` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_INT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Int values for product attribute';

/*Data for the table `catalog_product_attribute_int` */

/*Table structure for table `catalog_product_attribute_text` */

DROP TABLE IF EXISTS `catalog_product_attribute_text`;

CREATE TABLE `catalog_product_attribute_text` (
  `product_id` int(11) unsigned NOT NULL default '0',
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `attribute_value` text NOT NULL,
  PRIMARY KEY  (`product_id`,`attribute_id`,`website_id`),
  KEY `FK_ATTRIBUTE_TEXT` (`attribute_id`),
  KEY `FK_WEBSITE_TEXT` (`website_id`),
  CONSTRAINT `FK_WEBSITE_TEXT` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_TEXT` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_TEXT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Text attributes values';

/*Data for the table `catalog_product_attribute_text` */

/*Table structure for table `catalog_product_attribute_varchar` */

DROP TABLE IF EXISTS `catalog_product_attribute_varchar`;

CREATE TABLE `catalog_product_attribute_varchar` (
  `product_id` int(11) unsigned NOT NULL default '0',
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `attribute_value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`product_id`,`attribute_id`,`website_id`),
  KEY `FK_ATTRIBUTE_VARCHAR` (`attribute_id`),
  KEY `FK_WEBSITE_VARCHAR` (`website_id`),
  CONSTRAINT `FK_WEBSITE_VARCHAR` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_VARCHAR` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_VARCHAR` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Varchar attributes value';

/*Data for the table `catalog_product_attribute_varchar` */

/*Table structure for table `catalog_product_link` */

DROP TABLE IF EXISTS `catalog_product_link`;

CREATE TABLE `catalog_product_link` (
  `product_id` int(11) unsigned NOT NULL default '0',
  `linked_product_id` int(11) unsigned NOT NULL default '0',
  `link_type_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`product_id`,`linked_product_id`,`link_type_id`),
  KEY `FK_LINKED_PRODUCT` (`linked_product_id`),
  KEY `FK_PRODUCT_LINK_TYPE` (`link_type_id`),
  CONSTRAINT `FK_PRODUCT_LINK_TYPE` FOREIGN KEY (`link_type_id`) REFERENCES `catalog_product_link_type` (`link_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_LINKED_PRODUCT` FOREIGN KEY (`linked_product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_LINK_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
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

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
