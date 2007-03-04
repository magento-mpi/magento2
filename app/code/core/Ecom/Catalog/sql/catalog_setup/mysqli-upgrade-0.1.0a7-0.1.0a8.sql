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
  `website_id` smallint(6) unsigned default '0',
  `pid` mediumint(9) unsigned NOT NULL default '0',
  `left_key` mediumint(9) unsigned NOT NULL default '0',
  `right_key` mediumint(9) unsigned NOT NULL default '0',
  `level` smallint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`category_id`),
  KEY `FK_CATALOG_WEBSITE` (`website_id`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=latin1 COMMENT='Categories tree';

/*Data for the table `catalog_category` */

insert into `catalog_category` (`category_id`,`website_id`,`pid`,`left_key`,`right_key`,`level`) values (1,1,0,1,46,0),(2,1,1,2,23,1),(3,1,2,3,4,2),(4,1,2,5,6,2),(5,1,2,7,8,2),(6,1,2,9,10,2),(7,1,2,11,12,2),(8,1,2,13,14,2),(9,1,2,15,16,2),(10,1,2,17,18,2),(11,1,2,19,20,2),(12,1,2,21,22,2),(13,1,1,24,45,1),(14,1,13,25,26,2),(15,1,13,27,28,2),(16,1,13,29,30,2),(17,1,13,31,32,2),(18,1,13,33,34,2),(19,1,13,35,36,2),(20,1,13,37,38,2),(21,1,13,39,40,2),(22,1,13,41,42,2),(23,1,13,43,44,2);

/*Table structure for table `catalog_category_attribute` */

DROP TABLE IF EXISTS `catalog_category_attribute`;

CREATE TABLE `catalog_category_attribute` (
  `attribute_id` smallint(6) unsigned NOT NULL auto_increment,
  `field_name` varchar(32) NOT NULL default '',
  `input_type` varchar(32) NOT NULL default '',
  `data_type` varchar(32) NOT NULL default '',
  `is_extension` tinyint(1) unsigned default NULL,
  `is_required` tinyint(1) unsigned default NULL,
  `sort_order` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attribute_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COMMENT='Category attributes defination';

/*Data for the table `catalog_category_attribute` */

insert into `catalog_category_attribute` (`attribute_id`,`field_name`,`input_type`,`data_type`,`is_extension`,`is_required`,`sort_order`) values (1,'name','string','string',1,1,1),(2,'description','text','string',1,1,2),(3,'image','file','string',1,0,3);

/*Table structure for table `catalog_category_extension` */

DROP TABLE IF EXISTS `catalog_category_extension`;

CREATE TABLE `catalog_category_extension` (
  `category_id` mediumint(9) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `name` varchar(128) character set latin1 collate latin1_general_ci NOT NULL default '',
  `description` text character set latin1 collate latin1_general_ci NOT NULL,
  `image` text character set latin1 collate latin1_general_ci,
  PRIMARY KEY  (`category_id`,`website_id`),
  KEY `FK_CATEGORY_ATTRIBUTE_WEBSITE` (`website_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COMMENT='Categories detale information';

/*Data for the table `catalog_category_extension` */

insert into `catalog_category_extension` (`category_id`,`website_id`,`name`,`description`,`image`) values (1,1,'Root','Root',NULL),(2,1,'BROWSE BY TOPIC','BROWSE BY TOPIC',NULL),(3,1,'9-1-1','9-1-1',NULL),(4,1,'Bicycle Safety','Bicycle Safety',NULL),(5,1,'Bullying','Bullying',NULL),(6,1,'Drug Abuse','Drug Abuse',NULL),(7,1,'Halloween Safety','Halloween Safety',NULL),(8,1,'Internet Safety','Internet Safety',NULL),(9,1,'Law Enforcement','Law Enforcement',NULL),(10,1,'School Safety','School Safety',NULL),(11,1,'Senior Safety','Senior Safety',NULL),(12,1,'Stranger Awareness','Stranger Awareness',NULL),(13,1,'BROWSE BY PRODUCT','BROWSE BY PRODUCT',NULL),(14,1,'Bookmarks Store','Bookmarks Store',NULL),(15,1,'Brochures','Brochures',NULL),(16,1,'Coloring Books','Coloring Books',NULL),(17,1,'Evidence Packaging','Evidence Packaging',NULL),(18,1,'Litter/Literature Bags','Litter/Literature Bags',NULL),(19,1,'Pencils','Pencils',NULL),(20,1,'Reflectives','Reflectives',NULL),(21,1,'Safety Kits','Safety Kits',NULL),(22,1,'Slide Guides','Slide Guides',NULL),(23,1,'Spanish Products','Spanish Products',NULL);

/*Table structure for table `catalog_category_product` */

DROP TABLE IF EXISTS `catalog_category_product`;

CREATE TABLE `catalog_category_product` (
  `category_id` mediumint(9) unsigned NOT NULL default '0',
  `product_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`category_id`,`product_id`),
  KEY `FK_CATEGORY_PRODUCT` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COMMENT='Products in categories';

/*Data for the table `catalog_category_product` */

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
