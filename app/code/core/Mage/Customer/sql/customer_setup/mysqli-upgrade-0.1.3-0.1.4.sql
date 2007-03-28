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
  `customer_type_id` tinyint(3) unsigned NOT NULL default '0',
  `customer_email` varchar(128) character set utf8 NOT NULL default '',
  `customer_pass` varchar(32) character set utf8 NOT NULL default '',
  `customer_firstname` varchar(64) NOT NULL default '',
  `customer_lastname` varchar(64) NOT NULL default '',
  `default_address_id` int(11) default NULL,
  PRIMARY KEY  (`customer_id`),
  KEY `FK_CUSTOMER_TYPE` (`customer_type_id`),
  CONSTRAINT `FK_CUSTOMER_TYPE` FOREIGN KEY (`customer_type_id`) REFERENCES `customer_type` (`customer_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Base customers information';

/*Data for the table `customer` */

insert into `customer` (`customer_id`,`customer_type_id`,`customer_email`,`customer_pass`,`customer_firstname`,`customer_lastname`,`default_address_id`) values (1,1,'qa@varien.com','4297f44b13955235245b2497399d7a93','','',NULL),(14,1,'dmitriy@varien.com','31d6f9170ca43c9e8f6df9fa206cd8f6','Dmitriy','Soroka',6);

/*Table structure for table `customer_address` */

DROP TABLE IF EXISTS `customer_address`;

CREATE TABLE `customer_address` (
  `address_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned NOT NULL default '0',
  `firstname` varchar(128) NOT NULL default '',
  `lastname` varchar(128) NOT NULL default '',
  `postcode` varchar(16) NOT NULL default '',
  `street` varchar(128) NOT NULL default '',
  `city` varchar(64) NOT NULL default '',
  `region_id` mediumint(8) unsigned NOT NULL default '0',
  `country_id` smallint(6) NOT NULL default '0',
  `company` varchar(128) NOT NULL default '',
  `telephone` varchar(32) default NULL,
  `fax` varchar(32) default NULL,
  PRIMARY KEY  (`address_id`),
  KEY `FK_ADDRESS_COUNTRY` (`country_id`),
  KEY `FK_ADDRESS_CUSTOMER` (`customer_id`),
  KEY `FK_ADDRESS_REGION` (`region_id`),
  CONSTRAINT `FK_ADDRESS_CUSTOMER` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Customer addresses books';

/*Data for the table `customer_address` */

insert into `customer_address` (`address_id`,`customer_id`,`firstname`,`lastname`,`postcode`,`street`,`city`,`region_id`,`country_id`,`company`,`telephone`,`fax`) values (6,14,'Dmitriy','Soroka','03057','street 2','Kiev',1,1,'Varien','111-111-1111','111-111-1111');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Payment methods per customers';

/*Data for the table `customer_payment` */

/*Table structure for table `customer_payment_method` */

DROP TABLE IF EXISTS `customer_payment_method`;

CREATE TABLE `customer_payment_method` (
  `payment_method_id` tinyint(3) unsigned NOT NULL auto_increment,
  `payment_method_code` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`payment_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Customers payment methods (Credit card, etc.)';

/*Data for the table `customer_payment_method` */

/*Table structure for table `customer_type` */

DROP TABLE IF EXISTS `customer_type`;

CREATE TABLE `customer_type` (
  `customer_type_id` tinyint(3) unsigned NOT NULL auto_increment,
  `customer_type_code` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`customer_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Customers type';

/*Data for the table `customer_type` */

insert into `customer_type` (`customer_type_id`,`customer_type_code`) values (1,'');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
