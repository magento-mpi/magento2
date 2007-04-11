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

/*Table structure for table `customer` */

DROP TABLE IF EXISTS `customer`;

CREATE TABLE `customer` (
  `customer_id` int(11) unsigned NOT NULL auto_increment,
  `customer_type_id` tinyint(3) unsigned NOT NULL default '1',
  `email` varchar(128) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `firstname` varchar(64) NOT NULL default '',
  `lastname` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`customer_id`),
  KEY `FK_CUSTOMER_TYPE` (`customer_type_id`),
  CONSTRAINT `FK_CUSTOMER_TYPE` FOREIGN KEY (`customer_type_id`) REFERENCES `customer_type` (`customer_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Base customers information';

/*Data for the table `customer` */

insert into `customer` (`customer_id`,`customer_type_id`,`email`,`password`,`firstname`,`lastname`) values (1,1,'qa@varien.com','4297f44b13955235245b2497399d7a93','',''),(14,1,'dmitriy@varien.com','31d6f9170ca43c9e8f6df9fa206cd8f6','Dmitriy','Soroka');

/*Table structure for table `customer_address_type` */

DROP TABLE IF EXISTS `customer_address_type`;

CREATE TABLE `customer_address_type` (
  `address_type_id` int(10) unsigned NOT NULL auto_increment,
  `address_type_code` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`address_type_id`),
  UNIQUE KEY `address_type_code` (`address_type_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `customer_address_type` */

insert into `customer_address_type` (`address_type_id`,`address_type_code`) values (1,'billing'),(2,'shipping'),(3,'service');

/*Table structure for table `customer_address_type_language` */

DROP TABLE IF EXISTS `customer_address_type_language`;

CREATE TABLE `customer_address_type_language` (
  `address_type_id` int(10) unsigned NOT NULL default '0',
  `language_code` varchar(2) NOT NULL default '',
  `address_type_name` varchar(255) NOT NULL default '',
  UNIQUE KEY `address_type_id` (`address_type_id`,`language_code`),
  CONSTRAINT `FK_customer_address_type_language` FOREIGN KEY (`address_type_id`) REFERENCES `customer_address_type` (`address_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `customer_address_type_language` */

insert into `customer_address_type_language` (`address_type_id`,`language_code`,`address_type_name`) values (1,'en','Billing'),(2,'en','Shipping'),(3,'en','Service');

/*Table structure for table `customer_address_type_link` */

DROP TABLE IF EXISTS `customer_address_type_link`;

CREATE TABLE `customer_address_type_link` (
  `customer_id` int(10) unsigned NOT NULL default '0',
  `address_id` int(10) unsigned NOT NULL default '0',
  `address_type_id` int(10) unsigned NOT NULL default '0',
  `is_primary` tinyint(3) unsigned NOT NULL default '0',
  UNIQUE KEY `customer_address_type` (`customer_id`,`address_id`,`address_type_id`),
  UNIQUE KEY `customer_primary_type` (`customer_id`,`is_primary`,`address_type_id`),
  KEY `FK_customer_address_type_link1` (`address_id`),
  KEY `FK_customer_address_type_link2` (`address_type_id`),
  CONSTRAINT `FK_customer_address_type_link` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_customer_address_type_link1` FOREIGN KEY (`address_id`) REFERENCES `customer_address` (`address_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_customer_address_type_link2` FOREIGN KEY (`address_type_id`) REFERENCES `customer_address_type` (`address_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `customer_address_type_link` */

/*Table structure for table `customer_payment` */

DROP TABLE IF EXISTS `customer_payment`;

CREATE TABLE `customer_payment` (
  `customer_id` int(11) unsigned NOT NULL default '0',
  `payment_method_id` tinyint(3) unsigned NOT NULL default '0',
  `payment_details` varchar(255) default NULL,
  PRIMARY KEY  (`customer_id`,`payment_method_id`),
  KEY `FK_CUSTOMER_PAYMENT_METHOD` (`payment_method_id`),
  CONSTRAINT `FK_CUSTOMER_PAYMENT_METHOD` FOREIGN KEY (`payment_method_id`) REFERENCES `customer_payment_method` (`payment_method_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PAYMENT_METHOD_CUSTOMER` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Payment methods per customers';

/*Data for the table `customer_payment` */

/*Table structure for table `customer_payment_method` */

DROP TABLE IF EXISTS `customer_payment_method`;

CREATE TABLE `customer_payment_method` (
  `payment_method_id` tinyint(3) unsigned NOT NULL auto_increment,
  `payment_method_code` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`payment_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Customers payment methods (Credit card, etc.)';

/*Data for the table `customer_payment_method` */

/*Table structure for table `customer_type` */

DROP TABLE IF EXISTS `customer_type`;

CREATE TABLE `customer_type` (
  `customer_type_id` tinyint(3) unsigned NOT NULL auto_increment,
  `customer_type_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`customer_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Customers type';

/*Data for the table `customer_type` */

insert into `customer_type` (`customer_type_id`,`customer_type_code`) values (1,'default');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
