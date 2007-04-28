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
  `store_balance` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`customer_id`),
  UNIQUE KEY `customer_type_id` (`customer_type_id`,`email`),
  KEY `FK_CUSTOMER_TYPE` (`customer_type_id`),
  CONSTRAINT `FK_CUSTOMER_TYPE` FOREIGN KEY (`customer_type_id`) REFERENCES `customer_type` (`customer_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Base customers information';

/*Data for the table `customer` */

insert into `customer` (`customer_id`,`customer_type_id`,`email`,`password`,`firstname`,`lastname`,`store_balance`) values (1,1,'qa@varien.com','4297f44b13955235245b2497399d7a93','QA','Tester',10.0000),(14,1,'dmitriy@varien.com','31d6f9170ca43c9e8f6df9fa206cd8f6','Dmitriy','Soroka',100.0000),(37,1,'andrey@varien.com','4297f44b13955235245b2497399d7a93','Andrey','Korolyov',200.0000),(38,1,'moshe@varien.com','4297f44b13955235245b2497399d7a93','Moshe','Gurvich',300.0000),(67,1,'dmitriy1@varien.com','31d6f9170ca43c9e8f6df9fa206cd8f6','Dmitriy 1','Soroka',0.0000);

/*Table structure for table `customer_address` */

DROP TABLE IF EXISTS `customer_address`;

CREATE TABLE `customer_address` (
  `address_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned NOT NULL default '0',
  `firstname` varchar(128) NOT NULL default '',
  `lastname` varchar(128) NOT NULL default '',
  `postcode` varchar(16) NOT NULL default '',
  `street` text NOT NULL,
  `city` varchar(64) NOT NULL default '',
  `region` varchar(128) default NULL,
  `region_id` mediumint(8) unsigned default '0',
  `country_id` smallint(6) NOT NULL default '0',
  `company` varchar(128) NOT NULL default '',
  `telephone` varchar(32) default NULL,
  `fax` varchar(32) default NULL,
  PRIMARY KEY  (`address_id`),
  KEY `FK_ADDRESS_COUNTRY` (`country_id`),
  KEY `FK_ADDRESS_CUSTOMER` (`customer_id`),
  KEY `FK_ADDRESS_REGION` (`region_id`),
  CONSTRAINT `FK_ADDRESS_CUSTOMER` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Customer addresses books';

/*Data for the table `customer_address` */

insert into `customer_address` (`address_id`,`customer_id`,`firstname`,`lastname`,`postcode`,`street`,`city`,`region`,`region_id`,`country_id`,`company`,`telephone`,`fax`) values (6,14,'Dmitriy','Soroka','03057','street 1\nste','Kiev','American Samoa',NULL,223,'Varien','111-111-1111','111-111-1111'),(9,37,'Andrey','Korolyov','03057','street 1\nstreet 2','Kiev','',NULL,223,'Varien','222-222-2222',''),(10,38,'Moshe','Gurvich','90034','street 1\nstreet 2','Los Angeles',NULL,12,223,'Varien','222-222-2222',''),(19,67,'Dmitriy','Soroka','90034','street 1\nstreet 2','Alabama','American Samoa',3,223,'Varien','222-222-2222',''),(20,67,'test','test','03057','test\ntest','test','test',NULL,220,'test','222-222-2222',''),(21,1,'QA','shopping','03057','address street 1\naddress street 2','Kiev','Armed Forces Canada',8,223,'Varien','222-222-2222',''),(22,14,'test','test','03057','wer wer wer wer wer\n123123123','test','Ontario',74,38,'test','111-111-1111','');

/*Table structure for table `customer_address_type` */

DROP TABLE IF EXISTS `customer_address_type`;

CREATE TABLE `customer_address_type` (
  `address_type_id` int(10) unsigned NOT NULL auto_increment,
  `code` char(16) character set latin1 collate latin1_general_ci NOT NULL default '',
  PRIMARY KEY  (`address_type_id`),
  UNIQUE KEY `address_type_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `customer_address_type` */

insert into `customer_address_type` (`address_type_id`,`code`) values (1,'billing'),(2,'shipping'),(3,'service');

/*Table structure for table `customer_address_type_language` */

DROP TABLE IF EXISTS `customer_address_type_language`;

CREATE TABLE `customer_address_type_language` (
  `address_type_id` int(10) unsigned NOT NULL default '0',
  `language_code` varchar(2) NOT NULL default '',
  `name` varchar(64) NOT NULL default '',
  UNIQUE KEY `address_type_id` (`address_type_id`,`language_code`),
  CONSTRAINT `FK_customer_address_type_language` FOREIGN KEY (`address_type_id`) REFERENCES `customer_address_type` (`address_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `customer_address_type_language` */

insert into `customer_address_type_language` (`address_type_id`,`language_code`,`name`) values (1,'en','Billing'),(2,'en','Shipping'),(3,'en','Service');

/*Table structure for table `customer_address_type_link` */

DROP TABLE IF EXISTS `customer_address_type_link`;

CREATE TABLE `customer_address_type_link` (
  `address_id` int(10) unsigned NOT NULL default '0',
  `address_type_id` int(10) unsigned NOT NULL default '0',
  `is_primary` tinyint(1) unsigned NOT NULL default '0',
  UNIQUE KEY `PK` (`address_id`,`address_type_id`),
  KEY `FK_customer_address_type_link2` (`address_type_id`),
  CONSTRAINT `FK_customer_address_type_link1` FOREIGN KEY (`address_id`) REFERENCES `customer_address` (`address_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_customer_address_type_link2` FOREIGN KEY (`address_type_id`) REFERENCES `customer_address_type` (`address_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `customer_address_type_link` */

insert into `customer_address_type_link` (`address_id`,`address_type_id`,`is_primary`) values (6,1,0),(6,2,0),(6,3,0),(9,3,1),(19,1,0),(19,2,0),(19,3,0),(20,1,0),(20,2,0),(21,1,1),(21,2,1),(21,3,1),(22,1,1),(22,2,1),(22,3,1);

/*Table structure for table `customer_newsletter` */

DROP TABLE IF EXISTS `customer_newsletter`;

CREATE TABLE `customer_newsletter` (
  `newsletter_id` int(11) unsigned NOT NULL auto_increment,
  `email` varchar(64) NOT NULL default '',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`newsletter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

/*Data for the table `customer_newsletter` */

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
