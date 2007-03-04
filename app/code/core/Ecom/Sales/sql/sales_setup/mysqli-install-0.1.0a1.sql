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

/*Table structure for table `sales_invoice` */

DROP TABLE IF EXISTS `sales_invoice`;

CREATE TABLE `sales_invoice` (
  `sales_invoice_id` int(10) unsigned NOT NULL auto_increment,
  `sales_order_id` int(10) unsigned NOT NULL default '0',
  `sales_address_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sales_invoice_id`),
  KEY `sales_order_id` (`sales_order_id`),
  KEY `sales_address_id` (`sales_address_id`),
  CONSTRAINT `sales_invoice_ibfk_1` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_order` (`sales_order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sales_invoice_ibfk_2` FOREIGN KEY (`sales_address_id`) REFERENCES `sales_address` (`sales_address_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `sales_invoice` */

/*Table structure for table `sales_order` */

DROP TABLE IF EXISTS `sales_order`;

CREATE TABLE `sales_order` (
  `sales_order_id` int(10) unsigned NOT NULL auto_increment,
  `customer_id` int(10) unsigned NOT NULL default '0',
  `sales_quote_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sales_order_id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `sales_order_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `sales_order` */

/*Table structure for table `sales_order_product` */

DROP TABLE IF EXISTS `sales_order_product`;

CREATE TABLE `sales_order_product` (
  `sales_order_item_id` int(10) unsigned NOT NULL auto_increment,
  `sales_order_id` int(10) unsigned NOT NULL default '0',
  `sales_address_id` int(10) unsigned NOT NULL default '0',
  `sales_payment_id` int(10) unsigned NOT NULL default '0',
  `item_id` int(10) unsigned NOT NULL default '0',
  `item_qty` decimal(10,2) NOT NULL default '1.00',
  `item_orig_price` decimal(10,2) NOT NULL default '0.00',
  PRIMARY KEY  (`sales_order_item_id`),
  KEY `sales_order_id` (`sales_order_id`),
  KEY `sales_address_id` (`sales_address_id`),
  KEY `sales_payment_id` (`sales_payment_id`),
  KEY `FK_sales_order_item` (`item_id`),
  CONSTRAINT `sales_order_product_ibfk_1` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_order` (`sales_order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sales_order_product_ibfk_2` FOREIGN KEY (`sales_address_id`) REFERENCES `sales_address` (`sales_address_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sales_order_product_ibfk_3` FOREIGN KEY (`sales_payment_id`) REFERENCES `sales_payment` (`sales_payment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sales_order_product_ibfk_4` FOREIGN KEY (`item_id`) REFERENCES `catalog_product` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `sales_order_product` */

/*Table structure for table `sales_payment` */

DROP TABLE IF EXISTS `sales_payment`;

CREATE TABLE `sales_payment` (
  `sales_payment_id` int(10) unsigned NOT NULL auto_increment,
  `sales_order_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sales_payment_id`),
  KEY `sales_order_id` (`sales_order_id`),
  CONSTRAINT `sales_payment_ibfk_1` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_order` (`sales_order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `sales_payment` */

/*Table structure for table `sales_quote` */

DROP TABLE IF EXISTS `sales_quote`;

CREATE TABLE `sales_quote` (
  `sales_quote_id` int(10) unsigned NOT NULL auto_increment,
  `customers_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sales_quote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

/*Data for the table `sales_quote` */

/*Table structure for table `sales_quote_product` */

DROP TABLE IF EXISTS `sales_quote_product`;

CREATE TABLE `sales_quote_product` (
  `sales_quote_item_id` int(10) unsigned NOT NULL auto_increment,
  `sales_quote_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sales_quote_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED;

/*Data for the table `sales_quote_product` */

/*Table structure for table `sales_tracking` */

DROP TABLE IF EXISTS `sales_tracking`;

CREATE TABLE `sales_tracking` (
  `sales_tracking_id` int(10) unsigned NOT NULL auto_increment,
  `sales_invoice_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sales_tracking_id`),
  KEY `sales_invoice_id` (`sales_invoice_id`),
  CONSTRAINT `sales_tracking_ibfk_1` FOREIGN KEY (`sales_invoice_id`) REFERENCES `sales_invoice` (`sales_invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `sales_tracking` */

/*Table structure for table `sales_transaction` */

DROP TABLE IF EXISTS `sales_transaction`;

CREATE TABLE `sales_transaction` (
  `sales_transaction_id` int(10) unsigned NOT NULL auto_increment,
  `sales_invoice_id` int(10) unsigned NOT NULL default '0',
  `sales_payment_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sales_transaction_id`),
  KEY `sales_invoice_id` (`sales_invoice_id`),
  KEY `sales_payment_id` (`sales_payment_id`),
  CONSTRAINT `sales_transaction_ibfk_1` FOREIGN KEY (`sales_invoice_id`) REFERENCES `sales_invoice` (`sales_invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `sales_transaction_ibfk_2` FOREIGN KEY (`sales_payment_id`) REFERENCES `sales_payment` (`sales_payment_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `sales_transaction` */

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
