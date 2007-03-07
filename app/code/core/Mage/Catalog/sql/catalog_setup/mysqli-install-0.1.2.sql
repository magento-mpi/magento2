/*
SQLyog Enterprise - MySQL GUI v5.13
Host - 4.1.21-community-nt : Database - magenta_dev
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categories tree';

/*Data for the table `catalog_category` */

insert into `catalog_category` (`category_id`,`website_id`,`pid`,`left_key`,`right_key`,`level`) values (1,1,0,1,46,0),(2,1,1,2,23,1),(3,1,2,3,4,2),(4,1,2,5,6,2),(5,1,2,7,8,2),(6,1,2,9,10,2),(7,1,2,11,12,2),(8,1,2,13,14,2),(9,1,2,15,16,2),(10,1,2,17,18,2),(11,1,2,19,20,2),(12,1,2,21,22,2),(13,1,1,24,45,1),(14,1,13,25,26,2),(15,1,13,27,28,2),(16,1,13,29,30,2),(17,1,13,31,32,2),(18,1,13,33,34,2),(19,1,13,35,36,2),(20,1,13,37,38,2),(21,1,13,39,40,2),(22,1,13,41,42,2),(23,1,13,43,44,2);

/*Table structure for table `catalog_category_attribute` */

DROP TABLE IF EXISTS `catalog_category_attribute`;

CREATE TABLE `catalog_category_attribute` (
  `attribute_id` smallint(6) unsigned NOT NULL auto_increment,
  `attribute_code` varchar(32) NOT NULL default '',
  `data_input` varchar(32) NOT NULL default '',
  `data_saver` varchar(32) NOT NULL default '',
  `data_source` varchar(32) default NULL,
  `validation` varchar(64) default NULL,
  `required` tinyint(1) unsigned default NULL,
  `inheritable` tinyint(1) unsigned default NULL,
  `multiple` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Category attributes defination';

/*Data for the table `catalog_category_attribute` */

insert into `catalog_category_attribute` (`attribute_id`,`attribute_code`,`data_input`,`data_saver`,`data_source`,`validation`,`required`,`inheritable`,`multiple`) values (1,'name','text','attribute_value',NULL,NULL,1,0,0),(2,'description','textarea','attribute_value',NULL,NULL,1,1,0),(3,'main_image','imagefile','attribute_image',NULL,NULL,0,0,0),(4,'category_set','select','attribute_value','category_set',NULL,1,1,1),(5,'meta_title','text','attribute_value',NULL,NULL,0,1,0),(6,'meta_keywords','text','attribute_value',NULL,NULL,0,1,0),(7,'meta_description','text','attribute_value',NULL,NULL,0,1,0);

/*Table structure for table `catalog_category_attribute_set` */

DROP TABLE IF EXISTS `catalog_category_attribute_set`;

CREATE TABLE `catalog_category_attribute_set` (
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `category_set_id` smallint(6) unsigned NOT NULL default '0',
  `position` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attribute_id`,`category_set_id`),
  KEY `FK_CATEGORY_SET` (`category_set_id`),
  CONSTRAINT `FK_CATEGOTY_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_category_attribute` (`attribute_id`),
  CONSTRAINT `FK_CATEGORY_SET` FOREIGN KEY (`category_set_id`) REFERENCES `catalog_category_set` (`category_set_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Category attributes set';

/*Data for the table `catalog_category_attribute_set` */

insert into `catalog_category_attribute_set` (`attribute_id`,`category_set_id`,`position`) values (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,1,5),(6,1,6),(7,1,7);

/*Table structure for table `catalog_category_attribute_value` */

DROP TABLE IF EXISTS `catalog_category_attribute_value`;

CREATE TABLE `catalog_category_attribute_value` (
  `category_id` mediumint(9) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `attribute_value` text NOT NULL,
  PRIMARY KEY  (`category_id`,`website_id`,`attribute_id`),
  KEY `FK_CATEGORY_EXTENSION_WEBSITE` (`website_id`),
  KEY `FK_CATEGORY_ATTRIBUTE_VALUE` (`attribute_id`),
  CONSTRAINT `FK_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES `catalog_category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATEGORY_ATTRIBUTE_VALUE` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_category_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATEGORY_EXTENSION_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categories detale information';

/*Data for the table `catalog_category_attribute_value` */

insert into `catalog_category_attribute_value` (`category_id`,`website_id`,`attribute_id`,`attribute_value`) values (1,1,1,'Root'),(1,1,2,'Root'),(2,1,1,'BROWSE BY TOPIC'),(2,1,2,'BROWSE BY TOPIC'),(3,1,1,'9-1-1'),(3,1,2,'9-1-1'),(4,1,1,'Bicycle Safety'),(4,1,2,'Bicycle Safety'),(5,1,1,'Bullying'),(5,1,2,'Bullying'),(6,1,1,'Drug Abuse'),(6,1,2,'Drug Abuse'),(7,1,1,'Halloween Safety'),(7,1,2,'Halloween Safety'),(8,1,1,'Internet Safety'),(8,1,2,'Internet Safety'),(9,1,1,'Law Enforcement'),(9,1,2,'Law Enforcement'),(10,1,1,'School Safety'),(10,1,2,'School Safety'),(11,1,1,'Senior Safety'),(11,1,2,'Senior Safety'),(12,1,1,'Stranger Awareness'),(12,1,2,'Stranger Awareness'),(13,1,1,'BROWSE BY PRODUCT'),(13,1,2,'BROWSE BY PRODUCT'),(14,1,1,'Bookmarks Store'),(14,1,2,'Bookmarks Store'),(15,1,1,'Brochures'),(15,1,2,'Brochures'),(16,1,1,'Coloring Books'),(16,1,2,'Coloring Books'),(17,1,1,'Evidence Packaging'),(17,1,2,'Evidence Packaging'),(18,1,1,'Litter/Literature Bags'),(18,1,2,'Litter/Literature Bags'),(19,1,1,'Pencils'),(19,1,2,'Pencils'),(20,1,1,'Reflectives'),(20,1,2,'Reflectives'),(21,1,1,'Safety Kits'),(21,1,2,'Safety Kits'),(22,1,1,'Slide Guides'),(22,1,2,'Slide Guides'),(23,1,1,'Spanish Products'),(23,1,2,'Spanish Products');

/*Table structure for table `catalog_category_product` */

DROP TABLE IF EXISTS `catalog_category_product`;

CREATE TABLE `catalog_category_product` (
  `category_id` mediumint(9) unsigned NOT NULL default '0',
  `product_id` int(11) unsigned NOT NULL default '0',
  `position` mediumint(9) unsigned NOT NULL default '0',
  PRIMARY KEY  (`category_id`,`product_id`),
  KEY `FK_CATEGORY_PRODUCT` (`product_id`),
  CONSTRAINT `FK_PRODUCT_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES `catalog_category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATEGORY_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Products in categories';

/*Data for the table `catalog_category_product` */

/*Table structure for table `catalog_category_set` */

DROP TABLE IF EXISTS `catalog_category_set`;

CREATE TABLE `catalog_category_set` (
  `category_set_id` smallint(6) unsigned NOT NULL auto_increment,
  `category_set_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`category_set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Category attributes set';

/*Data for the table `catalog_category_set` */

insert into `catalog_category_set` (`category_set_id`,`category_set_code`) values (1,'Base category');

/*Table structure for table `catalog_product` */

DROP TABLE IF EXISTS `catalog_product`;

CREATE TABLE `catalog_product` (
  `product_id` int(11) unsigned NOT NULL auto_increment,
  `create_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Products';

/*Data for the table `catalog_product` */

/*Table structure for table `catalog_product_attribute` */

DROP TABLE IF EXISTS `catalog_product_attribute`;

CREATE TABLE `catalog_product_attribute` (
  `attribute_id` smallint(6) unsigned NOT NULL auto_increment,
  `attribute_code` varchar(32) NOT NULL default '',
  `data_input` varchar(32) NOT NULL default '',
  `data_saver` varchar(32) NOT NULL default '',
  `data_source` varchar(32) default NULL,
  `validation` varchar(64) default NULL,
  `required` tinyint(1) unsigned default NULL,
  `inheritable` tinyint(1) unsigned default NULL,
  `searchable` tinyint(1) unsigned default NULL,
  `filterable` tinyint(1) unsigned default NULL,
  `multiple` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product attributes defination';

/*Data for the table `catalog_product_attribute` */

insert into `catalog_product_attribute` (`attribute_id`,`attribute_code`,`data_input`,`data_saver`,`data_source`,`validation`,`required`,`inheritable`,`searchable`,`filterable`,`multiple`) values (1,'name','text','attribute_varchar',NULL,NULL,1,1,1,0,0),(2,'description','textarea','attribute_text',NULL,NULL,1,1,1,0,0),(3,'image','imagefile','attribute_image',NULL,NULL,0,1,0,0,0),(4,'model','text','attribute_varchar',NULL,NULL,1,1,1,0,0),(5,'price','text','attribute_decimal',NULL,'decimal',1,1,0,1,0),(6,'cost','text','attribute_decimal',NULL,'decimal',1,1,0,0,0),(7,'add_date','hidden','attribute_date',NULL,NULL,1,0,0,0,0),(8,'weight','text','attribute_decimal',NULL,'decimal',1,1,0,1,0),(9,'status','select','attribute_int','product_status',NULL,1,0,1,1,0),(10,'manufacturers','select','attribute_int','product_manufacturer',NULL,0,1,1,1,0);

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
  CONSTRAINT `FK_PRODUCT_DATE` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_DATE` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_WEBSITE_DATE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Date attribute value';

/*Data for the table `catalog_product_attribute_date` */

/*Table structure for table `catalog_product_attribute_decimal` */

DROP TABLE IF EXISTS `catalog_product_attribute_decimal`;

CREATE TABLE `catalog_product_attribute_decimal` (
  `product_id` int(11) unsigned NOT NULL default '0',
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `attribute_value` decimal(12,4) NOT NULL default '0.0000',
  `attribute_qty` int(11) NOT NULL default '0',
  PRIMARY KEY  (`product_id`,`attribute_id`,`website_id`),
  KEY `FK_ATTRIBUTE_DECIMAL` (`attribute_id`),
  KEY `FK_WEBSITE_DECIMAL` (`website_id`),
  CONSTRAINT `FK_PRODUCT_DECIMAL` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_DECIMAL` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_WEBSITE_DECIMAL` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Decimal values for product attributes';

/*Data for the table `catalog_product_attribute_decimal` */

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
  CONSTRAINT `FK_PRODUCT_INT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_INT` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_WEBSITE_INT` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Int values for product attribute';

/*Data for the table `catalog_product_attribute_int` */

/*Table structure for table `catalog_product_attribute_set` */

DROP TABLE IF EXISTS `catalog_product_attribute_set`;

CREATE TABLE `catalog_product_attribute_set` (
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `product_set_id` smallint(6) unsigned NOT NULL default '0',
  `position` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attribute_id`),
  KEY `FK_PRODUCT_ATTRIBUTE_SET` (`product_set_id`),
  CONSTRAINT `FK_PRODUCT_ATTRIBUTE_SET` FOREIGN KEY (`product_set_id`) REFERENCES `catalog_product_set` (`product_set_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_SET_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product attributes in set';

/*Data for the table `catalog_product_attribute_set` */

insert into `catalog_product_attribute_set` (`attribute_id`,`product_set_id`,`position`) values (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,1,5),(6,1,6),(7,1,7),(8,1,8),(9,1,9),(10,1,10);

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
  CONSTRAINT `FK_PRODUCT_TEXT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_TEXT` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_WEBSITE_TEXT` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Text attributes values';

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
  CONSTRAINT `FK_PRODUCT_VARCHAR` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_VARCHAR` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_WEBSITE_VARCHAR` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Varchar attributes value';

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
  CONSTRAINT `FK_LINK_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_LINKED_PRODUCT` FOREIGN KEY (`linked_product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_LINK_TYPE` FOREIGN KEY (`link_type_id`) REFERENCES `catalog_product_link_type` (`link_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Related products';

/*Data for the table `catalog_product_link` */

/*Table structure for table `catalog_product_link_type` */

DROP TABLE IF EXISTS `catalog_product_link_type`;

CREATE TABLE `catalog_product_link_type` (
  `link_type_id` tinyint(3) unsigned NOT NULL auto_increment,
  `link_type_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`link_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Types of product link(Related, superproduct, bundles)';

/*Data for the table `catalog_product_link_type` */

/*Table structure for table `catalog_product_set` */

DROP TABLE IF EXISTS `catalog_product_set`;

CREATE TABLE `catalog_product_set` (
  `product_set_id` smallint(6) unsigned NOT NULL auto_increment,
  `product_set_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`product_set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product attributes set';

/*Data for the table `catalog_product_set` */

insert into `catalog_product_set` (`product_set_id`,`product_set_code`) values (1,'Base product');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
