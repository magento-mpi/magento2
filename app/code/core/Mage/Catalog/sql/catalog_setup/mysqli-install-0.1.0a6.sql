/*
SQLyog Enterprise - MySQL GUI v5.13
Host - 4.1.20 : Database - pepper
*********************************************************************
Server version : 4.1.20
*/

SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `catalog_attribute` */

DROP TABLE IF EXISTS `catalog_attribute`;

CREATE TABLE `catalog_attribute` (
  `attribute_id` int(10) unsigned NOT NULL auto_increment,
  `attribute_source_id` smallint(6) unsigned default NULL,
  `attribute_type_id` smallint(4) unsigned default NULL,
  `attribute_code` varchar(32) NOT NULL default '',
  `is_user_defined` tinyint(1) unsigned NOT NULL default '0',
  `is_required` tinyint(1) unsigned NOT NULL default '0',
  `sort_order` smallint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attribute_id`),
  KEY `FK_ATTRIBUTE_SOURCE` (`attribute_source_id`),
  KEY `FK_ATTRIBUTE_TYPE` (`attribute_type_id`),
  CONSTRAINT `FK_ATTRIBUTE_SOURCE` FOREIGN KEY (`attribute_source_id`) REFERENCES `catalog_attribute_source` (`attribute_source_id`),
  CONSTRAINT `FK_ATTRIBUTE_TYPE` FOREIGN KEY (`attribute_type_id`) REFERENCES `catalog_attribute_type` (`attribute_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='MySQL table';

/*Data for the table `catalog_attribute` */

insert into `catalog_attribute` (`attribute_id`,`attribute_source_id`,`attribute_type_id`,`attribute_code`,`is_user_defined`,`is_required`,`sort_order`) values (1,1,1,'title',0,1,1),(2,1,2,'description',0,1,2),(3,2,1,'name',0,1,1),(4,2,2,'description',0,1,2),(5,2,1,'price',0,1,3),(6,2,1,'weight',0,1,4),(7,2,1,'image',0,1,5),(8,2,1,'qty',0,1,6);

/*Table structure for table `catalog_attribute_property` */

DROP TABLE IF EXISTS `catalog_attribute_property`;

CREATE TABLE `catalog_attribute_property` (
  `property_id` int(10) unsigned NOT NULL auto_increment,
  `attribute_id` int(10) unsigned NOT NULL default '0',
  `property_type_id` int(10) unsigned NOT NULL default '0',
  `property_value` text,
  PRIMARY KEY  (`property_id`),
  KEY `FK_CATALOG_ATTRIBUTE` (`attribute_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `catalog_attribute_property` */

/*Table structure for table `catalog_category_attribute` */

DROP TABLE IF EXISTS `catalog_category_attribute`;

CREATE TABLE `catalog_category_attribute` (
  `category_id` mediumint(9) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `attribute_id` int(10) unsigned NOT NULL default '0',
  `attribute_value` text NOT NULL,
  PRIMARY KEY  (`category_id`,`website_id`),
  KEY `FK_CATEGORY_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CATEGORY_ATTRIBUTE_WEBSITE` (`website_id`),
  CONSTRAINT `FK_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES `catalog_category_tree` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATEGORY_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_attribute` (`attribute_id`),
  CONSTRAINT `FK_CATEGORY_ATTRIBUTE_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Categories detale information';

/*Data for the table `catalog_category_attribute` */

insert into `catalog_category_attribute` (`category_id`,`website_id`,`attribute_id`,`attribute_value`) values (2,1,1,'Category 1');

/*Table structure for table `catalog_category_product` */

DROP TABLE IF EXISTS `catalog_category_product`;

CREATE TABLE `catalog_category_product` (
  `category_id` mediumint(9) unsigned NOT NULL default '0',
  `product_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`category_id`,`product_id`),
  KEY `FK_CATEGORY_PRODUCT` (`product_id`),
  CONSTRAINT `FK_PRODUCT_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES `catalog_category_tree` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATEGORY_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  `default_category_id` mediumint(9) unsigned NOT NULL default '0',
  PRIMARY KEY  (`product_id`),
  KEY `FK_DEFAULT_CATEGORY` (`default_category_id`),
  CONSTRAINT `FK_DEFAULT_CATEGORY` FOREIGN KEY (`default_category_id`) REFERENCES `catalog_category_tree` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Products';

/*Data for the table `catalog_product` */

insert into `catalog_product` (`product_id`,`default_category_id`) values (1,5),(2,4),(3,4),(4,4),(5,3),(6,6),(7,7),(8,9),(9,5),(10,3),(11,6),(12,8);

/*Table structure for table `catalog_product_attribute` */

DROP TABLE IF EXISTS `catalog_product_attribute`;

CREATE TABLE `catalog_product_attribute` (
  `product_id` int(11) unsigned NOT NULL default '0',
  `attribute_id` int(10) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned default '0',
  `attribute_value` text,
  PRIMARY KEY  (`product_id`,`attribute_id`),
  KEY `FK_PRODUCT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_PRODUCT_ATTRIBUTE_WEBSITE` (`website_id`),
  CONSTRAINT `FK_ATTRIBUTE_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`),
  CONSTRAINT `FK_PRODUCT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_attribute` (`attribute_id`),
  CONSTRAINT `FK_PRODUCT_ATTRIBUTE_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Products attributes';

/*Data for the table `catalog_product_attribute` */

insert into `catalog_product_attribute` (`product_id`,`attribute_id`,`website_id`,`attribute_value`) values (1,3,1,'Product 1'),(1,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(1,5,1,'22'),(1,8,1,'10'),(2,3,1,'Product 2'),(2,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(2,5,1,'22'),(2,8,1,'12'),(3,3,1,'Product 3'),(3,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(3,5,1,'33'),(3,8,1,'13'),(4,3,1,'Product 4'),(4,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(4,5,1,'44'),(4,8,1,'22'),(5,3,1,'Product 5'),(5,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(5,5,1,'55'),(5,8,1,'43'),(6,3,1,'Product 6'),(6,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(6,5,1,'66'),(6,8,1,'21'),(7,3,1,'Product 7'),(7,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(7,5,1,'77'),(7,8,1,'0'),(8,3,1,'Product 8'),(8,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(8,5,1,'88'),(8,8,1,'54'),(9,3,1,'Product 9'),(9,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(9,5,1,'99'),(9,8,1,'22'),(10,3,1,'Product 10'),(10,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(10,5,1,'10'),(10,8,1,'87'),(11,3,1,'Product 11'),(11,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(11,5,1,'11'),(11,8,1,'33'),(12,3,1,'Product 12'),(12,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(12,5,1,'12'),(12,8,1,'55');

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

insert into `core_website` (`website_id`,`language_code`,`website_code`) values (1,'en','Pepper eCommerce');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
