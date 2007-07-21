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

/*Table structure for table `tax_rate` */

DROP TABLE IF EXISTS `tax_rate`;

CREATE TABLE `tax_rate` (
  `tax_rate_id` tinyint(4) NOT NULL auto_increment,
  `tax_county_id` smallint(6) default NULL,
  `tax_region_id` mediumint(9) unsigned default NULL,
  `tax_zip_code` varchar(12) default NULL,
  PRIMARY KEY  (`tax_rate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Base tax rates';

/*Data for the table `tax_rate` */

/*Table structure for table `tax_rate_data` */

DROP TABLE IF EXISTS `tax_rate_data`;

CREATE TABLE `tax_rate_data` (
  `tax_rate_data_id` tinyint(4) NOT NULL auto_increment,
  `tax_rate_id` tinyint(4) NOT NULL default '0',
  `rate_value` decimal(12,4) NOT NULL default '0.0000',
  `rate_type_id` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`tax_rate_data_id`),
  KEY `rate_id` (`tax_rate_id`),
  KEY `rate_type_id` (`rate_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tax_rate_data` */

insert into `tax_rate_data` (`tax_rate_data_id`,`tax_rate_id`,`rate_value`,`rate_type_id`) values (3,2,0.0000,1),(4,2,0.0000,2),(5,2,0.0000,3),(6,2,0.0000,4),(7,2,0.0000,5);

/*Table structure for table `tax_rate_type` */

DROP TABLE IF EXISTS `tax_rate_type`;

CREATE TABLE `tax_rate_type` (
  `type_id` tinyint(4) NOT NULL auto_increment,
  `type_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tax_rate_type` */

insert into `tax_rate_type` (`type_id`,`type_name`) values (1,'Rate 1'),(2,'Rate 2'),(3,'Rate 3'),(4,'Rate 4'),(5,'Rate 5');

/*Table structure for table `tax_rule` */

DROP TABLE IF EXISTS `tax_rule`;

CREATE TABLE `tax_rule` (
  `tax_rule_id` tinyint(4) NOT NULL auto_increment,
  `tax_customer_class_id` smallint(6) NOT NULL default '0',
  `tax_product_class_id` smallint(6) NOT NULL default '0',
  `tax_rate_id` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`tax_rule_id`),
  KEY `tax_customer_class_id` (`tax_customer_class_id`,`tax_product_class_id`),
  KEY `tax_customer_class_id_2` (`tax_customer_class_id`),
  KEY `tax_product_class_id` (`tax_product_class_id`),
  KEY `tax_rate_id` (`tax_rate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tax_rule` */

insert into `tax_rule` (`tax_rule_id`,`tax_customer_class_id`,`tax_product_class_id`,`tax_rate_id`) values (3,2,1,3),(4,2,1,2);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
