/*
SQLyog Enterprise - MySQL GUI v5.13
Host - 4.1.21-community-nt : Database - pepper_dev
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
  `customer_type_id` tinyint(3) unsigned default NULL,
  `customer_name` varchar(64) NOT NULL default '',
  `customer_email` varchar(128) NOT NULL default '',
  `customer_pass` varchar(32) NOT NULL default '',
  `customer_login` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`customer_id`),
  KEY `FK_CUSTOMER_TYPE` (`customer_type_id`),
  CONSTRAINT `FK_CUSTOMER_TYPE` FOREIGN KEY (`customer_type_id`) REFERENCES `customer_type` (`customer_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Base customers information';

/*Data for the table `customer` */

/*Table structure for table `customer_address` */

DROP TABLE IF EXISTS `customer_address`;

CREATE TABLE `customer_address` (
  `address_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned NOT NULL default '0',
  `company` varchar(128) NOT NULL default '',
  `firstname` varchar(128) NOT NULL default '',
  `lastname` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`address_id`),
  KEY `FK_ADDRESS_CUSTOMER` (`customer_id`),
  CONSTRAINT `FK_ADDRESS_CUSTOMER` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Customer addresses books';

/*Data for the table `customer_address` */

/*Table structure for table `customer_payment` */

DROP TABLE IF EXISTS `customer_payment`;

CREATE TABLE `customer_payment` (
  `customer_id` int(11) unsigned NOT NULL default '0',
  `payment_method_id` tinyint(3) unsigned NOT NULL default '0',
  `payment_details` varchar(255) default NULL,
  PRIMARY KEY  (`customer_id`,`payment_method_id`),
  KEY `FK_CUSTOMER_PAYMENT_METHOD` (`payment_method_id`),
  CONSTRAINT `FK_PAYMENT_METHOD_CUSTOMER` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CUSTOMER_PAYMENT_METHOD` FOREIGN KEY (`payment_method_id`) REFERENCES `customer_payment_method` (`payment_method_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Payment methods per customers';

/*Data for the table `customer_payment` */

/*Table structure for table `customer_payment_method` */

DROP TABLE IF EXISTS `customer_payment_method`;

CREATE TABLE `customer_payment_method` (
  `payment_method_id` tinyint(3) unsigned NOT NULL auto_increment,
  `payment_method_code` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`payment_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Customers payment methods (Credit card, etc.)';

/*Data for the table `customer_payment_method` */

/*Table structure for table `customer_type` */

DROP TABLE IF EXISTS `customer_type`;

CREATE TABLE `customer_type` (
  `customer_type_id` tinyint(3) unsigned NOT NULL auto_increment,
  `customer_type_code` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`customer_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Customers type';

/*Data for the table `customer_type` */

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
