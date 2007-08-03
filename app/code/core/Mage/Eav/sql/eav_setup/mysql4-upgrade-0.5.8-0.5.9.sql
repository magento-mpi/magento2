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

/*Table structure for table `eav_attribute_option` */
DROP TABLE IF EXISTS `eav_value_option`;
DROP TABLE IF EXISTS `eav_attribute_option`;

CREATE TABLE `eav_attribute_option` (
  `option_id` int(10) unsigned NOT NULL auto_increment,
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `sort_order` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`option_id`),
  KEY `FK_ATTRIBUTE_OPTION_ATTRIBUTE` (`attribute_id`),
  CONSTRAINT `FK_ATTRIBUTE_OPTION_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `eav_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Attributes option (for source model)';

/*Data for the table `eav_attribute_option` */

insert into `eav_attribute_option` (`option_id`,`attribute_id`,`sort_order`) values (1,102,1),(2,102,2),(3,102,3),(4,102,4),(5,102,5),(6,102,6),(7,102,7),(8,102,8),(9,102,9),(10,102,10),(11,465,1),(12,465,2),(13,465,3);

/*Table structure for table `eav_attribute_option_value` */

DROP TABLE IF EXISTS `eav_attribute_option_value`;

CREATE TABLE `eav_attribute_option_value` (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `option_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_OPTION_VALUE_OPTION` (`option_id`),
  KEY `FK_ATTRIBUTE_OPTION_VALUE_STORE` (`store_id`),
  CONSTRAINT `FK_ATTRIBUTE_OPTION_VALUE_OPTION` FOREIGN KEY (`option_id`) REFERENCES `eav_attribute_option` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_OPTION_VALUE_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Attribute option values per store';

/*Data for the table `eav_attribute_option_value` */

insert into `eav_attribute_option_value` (`value_id`,`option_id`,`store_id`,`value`) values (1,1,0,'LG'),(2,2,0,'Sony'),(3,3,0,'Samsung'),(4,4,0,'HP'),(5,5,0,'JVC'),(6,6,0,'Panasonic'),(7,7,0,'Yamaha'),(8,8,0,'Philips'),(9,9,0,'Acco'),(10,10,0,'Aiwa'),(11,1,1,'LG'),(12,2,1,'Sony'),(13,3,1,'Samsung'),(14,4,1,'HP'),(15,5,1,'JVC'),(16,6,1,'Panasonic'),(17,7,1,'Yamaha'),(18,8,1,'Philips'),(19,9,1,'Acco'),(20,10,1,'Aiwa'),(21,1,2,'LG'),(22,2,2,'Sony'),(23,3,2,'Samsung'),(24,4,2,'HP'),(25,5,2,'JVC'),(26,6,2,'Panasonic'),(27,7,2,'Yamaha'),(28,8,2,'Philips'),(29,9,2,'Acco'),(30,10,2,'Aiwa'),(31,1,3,'LG'),(32,2,3,'Sony'),(33,3,3,'Samsung'),(34,4,3,'HP'),(35,5,3,'JVC'),(36,6,3,'Panasonic'),(37,7,3,'Yamaha'),(38,8,3,'Philips'),(39,9,3,'Acco'),(40,10,3,'Aiwa'),(41,1,4,'LG'),(42,2,4,'Sony'),(43,3,4,'Samsung'),(44,4,4,'HP'),(45,5,4,'JVC'),(46,6,4,'Panasonic'),(47,7,4,'Yamaha'),(48,8,4,'Philips'),(49,9,4,'Acco'),(50,10,4,'Aiwa'),(51,11,1,'Red'),(52,12,1,'Blue'),(53,13,1,'Yellow'),(54,11,0,'Red'),(55,12,0,'Blue'),(56,13,0,'Yellow'),(57,11,2,'Red(rus)'),(58,12,2,'Blue(rus)'),(59,13,2,'Yellow(rus)');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
