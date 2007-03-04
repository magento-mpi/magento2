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
  CONSTRAINT `FK_ATTRIBUTE_TYPE` FOREIGN KEY (`attribute_type_id`) REFERENCES `catalog_attribute_type` (`attribute_type_id`),
  CONSTRAINT `FK_ATTRIBUTE_SOURCE` FOREIGN KEY (`attribute_source_id`) REFERENCES `catalog_attribute_source` (`attribute_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='MySQL table';

/*Data for the table `catalog_attribute` */

insert into `catalog_attribute` (`attribute_id`,`attribute_source_id`,`attribute_type_id`,`attribute_code`,`is_user_defined`,`is_required`,`sort_order`) values (1,1,1,'title',0,1,1),(2,1,2,'description',0,1,2),(3,2,1,'title',0,1,1),(4,2,2,'description',0,1,2);

/*Table structure for table `catalog_attribute_source` */

DROP TABLE IF EXISTS `catalog_attribute_source`;

CREATE TABLE `catalog_attribute_source` (
  `attribute_source_id` smallint(6) unsigned NOT NULL auto_increment,
  `source_name` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`attribute_source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Attribute sources';

/*Data for the table `catalog_attribute_source` */

insert into `catalog_attribute_source` (`attribute_source_id`,`source_name`) values (1,'catalog'),(2,'product');

/*Table structure for table `catalog_attribute_type` */

DROP TABLE IF EXISTS `catalog_attribute_type`;

CREATE TABLE `catalog_attribute_type` (
  `attribute_type_id` smallint(4) unsigned NOT NULL auto_increment,
  `type_name` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`attribute_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Attribute types';

/*Data for the table `catalog_attribute_type` */

insert into `catalog_attribute_type` (`attribute_type_id`,`type_name`) values (1,'Text Box Field'),(2,'Textarea'),(3,'RichEditor'),(4,'Checkbox');

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

insert into `catalog_category_tree` (`category_id`,`website_id`,`pid`,`left_key`,`right_key`,`level`,`title`) values (1,1,0,1,24,0,'Root'),(2,1,0,2,9,1,'Category 1'),(3,1,0,10,11,1,'Category 2'),(4,1,0,12,13,1,'Category 3'),(5,1,0,14,15,1,'Category 4'),(6,1,0,16,17,1,'Category 5'),(7,1,0,18,19,1,'Category 6'),(8,1,0,20,21,1,'Category 7'),(9,1,0,22,23,1,'Category 8'),(10,1,2,3,4,2,'Category 1.1'),(11,1,2,5,8,2,'Category 1.2'),(12,1,11,6,7,3,'Category 1.2.1');

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
  CONSTRAINT `FK_PRODUCT_ATTRIBUTE_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`),
  CONSTRAINT `FK_ATTRIBUTE_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`),
  CONSTRAINT `FK_PRODUCT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_attribute` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Products attributes';

/*Data for the table `catalog_product_attribute` */

insert into `catalog_product_attribute` (`product_id`,`attribute_id`,`website_id`,`attribute_value`) values (1,3,1,'Product 1'),(2,3,1,'Product 2'),(3,3,1,'Product 3'),(4,3,1,'Product 4'),(5,3,1,'Product 5'),(6,3,1,'Product 6'),(7,3,1,'Product 7'),(8,3,1,'Product 8'),(9,3,1,'Product 9'),(10,3,1,'Product 10'),(11,3,1,'Product 11'),(12,3,1,'Product 12');

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

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
