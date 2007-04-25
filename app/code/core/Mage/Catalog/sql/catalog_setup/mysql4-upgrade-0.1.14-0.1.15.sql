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

/*Table structure for table `catalog_product_attribute_in_set` */

DROP TABLE IF EXISTS `catalog_product_attribute_in_set`;

CREATE TABLE `catalog_product_attribute_in_set` (
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `set_id` smallint(6) unsigned NOT NULL default '0',
  `group_id` smallint(6) unsigned NOT NULL default '1',
  `position` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attribute_id`,`set_id`),
  UNIQUE KEY `IDX_SET_ATTRIBUTE` USING BTREE (`set_id`,`attribute_id`),
  UNIQUE KEY `IDX_GROUP_ATTRIBUTE` USING BTREE (`group_id`,`attribute_id`),
  KEY `FK_PRODUCT_ATTRIBUTE_SET` USING BTREE (`set_id`),
  KEY `FK_PRODUCT_SET_GROUP` USING BTREE (`group_id`),
  CONSTRAINT `FK_PRODUCT_ATTRIBUTE_IN_GROUP` FOREIGN KEY (`group_id`) REFERENCES `catalog_product_attribute_group` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_ATTRIBUTE_IN_SET` FOREIGN KEY (`set_id`) REFERENCES `catalog_product_attribute_set` (`set_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_SET_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product attributes in set';

/*Data for the table `catalog_product_attribute_in_set` */

insert into `catalog_product_attribute_in_set` (`attribute_id`,`set_id`,`group_id`,`position`) values (1,1,1,1),(2,1,2,2),(3,1,3,3),(4,1,1,4),(5,1,1,5),(6,1,1,6),(7,1,1,7),(8,1,1,8),(9,1,2,9),(10,1,1,10),(11,1,1,11),(12,1,1,12);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
