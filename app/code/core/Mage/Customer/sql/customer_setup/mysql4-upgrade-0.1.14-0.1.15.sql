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
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`customer_id`),
  UNIQUE KEY `customer_type_id` (`customer_type_id`,`email`),
  KEY `FK_CUSTOMER_TYPE` (`customer_type_id`),
  CONSTRAINT `FK_CUSTOMER_TYPE` FOREIGN KEY (`customer_type_id`) REFERENCES `customer_type` (`customer_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Base customers information';

/*Data for the table `customer` */

insert into `customer` (`customer_id`,`customer_type_id`,`email`,`password`,`firstname`,`lastname`,`store_balance`,`created_at`) values (1,1,'qa@varien.com','4297f44b13955235245b2497399d7a93','QA','Tester',10.0000,'0000-00-00 00:00:00'),(37,1,'andrey@varien.com','4297f44b13955235245b2497399d7a93','Andrey','Korolyov',200.0000,'0000-00-00 00:00:00'),(38,1,'moshe@varien.com','58141f2b0153b2e4af7f2c108133c286','Moshe','Gurvich',300.0000,'0000-00-00 00:00:00'),(67,1,'dmitriy1@varien.com','','Dmitriy','Soroka',0.0000,'0000-00-00 00:00:00'),(68,1,'yoav@varien.com','','yoav','kutner',0.0000,'0000-00-00 00:00:00'),(71,1,'dmitriy.soroka@gmail.com','31d6f9170ca43c9e8f6df9fa206cd8f6','dmitriy','soroka',0.0000,'0000-00-00 00:00:00'),(72,1,'moshe@irubin.com','915cdcc5dbcb600a716ed1bf3e4dfbdc','moshe','gurvich',0.0000,'0000-00-00 00:00:00'),(73,1,'m0sh3@hotmail.com','915cdcc5dbcb600a716ed1bf3e4dfbdc','moshe','gurvich',0.0000,'0000-00-00 00:00:00'),(74,1,'chris@varien.com','a7461721eb9221fb6898dfe919ed1a17','Chris','Marshall',0.0000,'0000-00-00 00:00:00'),(75,1,'yoav@irubin.com','','yoav','kutner',0.0000,'0000-00-00 00:00:00');
insert into `customer` (`customer_id`,`customer_type_id`,`email`,`password`,`firstname`,`lastname`,`store_balance`,`created_at`) values (76,1,'dmitriy@varien.com','31d6f9170ca43c9e8f6df9fa206cd8f6','Dmitriy','Soroka',0.0000,'2007-05-14 23:09:40');

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

insert into `customer_address` (`address_id`,`customer_id`,`firstname`,`lastname`,`postcode`,`street`,`city`,`region`,`region_id`,`country_id`,`company`,`telephone`,`fax`) values (9,37,'Andrey','Korolyov','03057','street 1\nstreet 2','Kiev','',NULL,223,'Varien','222-222-2222',''),(10,38,'Moshe','Gurvich','90034','street 1\nstreet 2','Los Angeles','California',12,223,'Varien','222-222-2222',''),(20,67,'test1','test','03057','test\ntest','test','test',NULL,220,'test','222-222-2222',''),(21,1,'QA','shopping','90034','3402 Motor Ave Suite B\naddress street 2','Los Angeles','California',12,223,'Varien','(123)123-1234',''),(26,71,'dmitriy','soroka','03057','Metalistov\nb6','kiev','kiev',NULL,220,'varien','111-111-1111',''),(27,72,'moshe','gurvich','34533','rwtrt','wret','California',12,223,'gfsgf','345-534-3453',NULL),(28,73,'moshe','gurvich','90034','werswfrswfg\nsdfgsf','los angeles','California',12,223,'asdfasdf','310-926-6363',NULL),(29,74,'Chris','Marshall','90034','3402 motor ave','Los Angeles','CA',NULL,223,'','310-280-3908',''),(30,75,'yoav','kutner','90034','555 test dr','Los Angeles','California',12,223,'Varien','310-555-1212',''),(31,38,'Yoav','Kutner','90034','3402 Motor Ave\nSuite B','Los Angeles','California',12,223,'Varien','310-280-3908',''),(32,76,'Dmitriy','Soroka','03057','Metalistov\n6','Kiev','Kiev',NULL,220,'Varien','111-111-1111','');

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

insert into `customer_address_type_link` (`address_id`,`address_type_id`,`is_primary`) values (9,3,1),(10,1,1),(10,2,0),(10,3,1),(20,1,0),(20,2,0),(20,3,1),(21,1,1),(21,2,1),(21,3,1),(26,1,1),(26,2,1),(26,3,1),(29,1,1),(29,2,1),(29,3,1),(30,1,1),(30,2,1),(30,3,1),(31,2,1),(32,1,1),(32,2,1),(32,3,1);

/*Table structure for table `customer_log` */

DROP TABLE IF EXISTS `customer_log`;

CREATE TABLE `customer_log` (
  `log_id` int(10) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned NOT NULL default '0',
  `login_at` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`log_id`),
  KEY `FK_CUSTOMER_LOG_CUSTOMER_ID` (`customer_id`),
  CONSTRAINT `FK_CUSTOMER_LOG_CUSTOMER_ID` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Customer login log';

/*Data for the table `customer_log` */

insert into `customer_log` (`log_id`,`customer_id`,`login_at`) values (1,76,'2007-05-14 23:28:07'),(2,76,'2007-05-14 23:28:25');

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

/*Table structure for table `customer_wishlist` */

DROP TABLE IF EXISTS `customer_wishlist`;

CREATE TABLE `customer_wishlist` (
  `item_id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(11) unsigned NOT NULL default '0',
  `customer_id` int(11) unsigned NOT NULL default '0',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`item_id`),
  KEY `FK_WISHLIST_PRODUCT` (`product_id`),
  KEY `FK_WISHLIST_CUSTOMER` (`customer_id`),
  CONSTRAINT `FK_WISHLIST_CUSTOMER` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_WISHLIST_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=cp1251 COMMENT='Customer wishlist products';

/*Data for the table `customer_wishlist` */

insert into `customer_wishlist` (`item_id`,`product_id`,`customer_id`,`add_date`) values (11,2444,1,'2007-05-07 10:30:45'),(12,2493,76,'2007-05-14 23:28:58');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
