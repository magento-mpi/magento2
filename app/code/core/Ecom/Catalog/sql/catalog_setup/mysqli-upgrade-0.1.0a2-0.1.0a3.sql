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

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
