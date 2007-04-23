/*
SQLyog Enterprise - MySQL GUI v5.13
Host - 4.1.21-community-nt : Database - magenta
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
  `pid` mediumint(9) unsigned NOT NULL default '0',
  `left_key` mediumint(9) unsigned NOT NULL default '0',
  `right_key` mediumint(9) unsigned NOT NULL default '0',
  `level` smallint(4) unsigned NOT NULL default '0',
  `order` smallint(6) unsigned NOT NULL default '1',
  `attribute_set_id` smallint(6) unsigned NOT NULL default '1',
  PRIMARY KEY  (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categories tree';

/*Data for the table `catalog_category` */

insert into `catalog_category` (`category_id`,`pid`,`left_key`,`right_key`,`level`,`order`,`attribute_set_id`) values (1,0,1,46,0,1,1),(2,1,2,23,1,1,1),(3,2,3,4,2,1,1),(4,2,5,6,2,2,1),(5,2,7,8,2,3,1),(6,2,9,10,2,4,1),(7,2,11,12,2,5,1),(8,2,13,14,2,6,1),(9,2,15,16,2,7,1),(10,2,17,18,2,8,1),(11,2,19,20,2,9,1),(12,2,21,22,2,10,1),(13,1,24,45,1,2,1),(14,13,25,26,2,1,1),(15,13,27,28,2,2,1),(16,13,29,30,2,3,1),(17,13,31,32,2,4,1),(18,13,33,34,2,5,1),(19,13,35,36,2,6,1),(20,13,37,38,2,7,1),(21,13,39,40,2,8,1),(22,13,41,42,2,9,1),(23,13,43,44,2,10,1);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
