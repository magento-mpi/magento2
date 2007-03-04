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
  `sales_invoice_id` int(10) unsigned NOT NULL default '0',
  `sales_order_id` int(10) unsigned NOT NULL default '0',
  `sales_address_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sales_invoice_id`),
  KEY `FK_INVOICE_ORDER` (`sales_order_id`),
  CONSTRAINT `FK_INVOICE_ORDER` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_order` (`sales_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='MySQL table';

/*Data for the table `sales_invoice` */

/*Table structure for table `sales_order` */

DROP TABLE IF EXISTS `sales_order`;

CREATE TABLE `sales_order` (
  `sales_order_id` int(10) unsigned NOT NULL default '0',
  `customer_id` int(10) unsigned NOT NULL default '0',
  `sales_quote_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sales_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='MySQL table';

/*Data for the table `sales_order` */

/*Table structure for table `sales_payment` */

DROP TABLE IF EXISTS `sales_payment`;

CREATE TABLE `sales_payment` (
  `sales_payment_id` int(10) unsigned NOT NULL default '0',
  `sales_order_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sales_payment_id`),
  KEY `FK_PAYMENT_ORDER` (`sales_order_id`),
  CONSTRAINT `FK_PAYMENT_ORDER` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_order` (`sales_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='MySQL table';

/*Data for the table `sales_payment` */

/*Table structure for table `sales_quote` */

DROP TABLE IF EXISTS `sales_quote`;

CREATE TABLE `sales_quote` (
  `sales_quote_id` int(10) unsigned NOT NULL default '0',
  `customers_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sales_quote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='MySQL table';

/*Data for the table `sales_quote` */

/*Table structure for table `sales_quote_item` */

DROP TABLE IF EXISTS `sales_quote_item`;

CREATE TABLE `sales_quote_item` (
  `sales_quote_item_id` int(10) unsigned NOT NULL default '0',
  `sales_quote_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sales_quote_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='MySQL table';

/*Data for the table `sales_quote_item` */

/*Table structure for table `sales_quote_product` */

DROP TABLE IF EXISTS `sales_quote_product`;

CREATE TABLE `sales_quote_product` (
  `sales_quote_item_id` int(10) unsigned NOT NULL default '0',
  `sales_quote_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sales_quote_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='MySQL table';

/*Data for the table `sales_quote_product` */

/*Table structure for table `sales_tracking` */

DROP TABLE IF EXISTS `sales_tracking`;

CREATE TABLE `sales_tracking` (
  `sales_tracking_id` int(10) unsigned NOT NULL default '0',
  `sales_invoice_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sales_tracking_id`),
  KEY `FK_TRACKING_INVOICE` (`sales_invoice_id`),
  CONSTRAINT `FK_TRACKING_INVOICE` FOREIGN KEY (`sales_invoice_id`) REFERENCES `sales_invoice` (`sales_invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='MySQL table';

/*Data for the table `sales_tracking` */

/*Table structure for table `sales_transaction` */

DROP TABLE IF EXISTS `sales_transaction`;

CREATE TABLE `sales_transaction` (
  `sales_transaction_id` int(10) unsigned NOT NULL default '0',
  `sales_invoice_id` int(10) unsigned NOT NULL default '0',
  `sales_payment_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sales_transaction_id`),
  KEY `FK_TRANSACTION_INVOICE` (`sales_invoice_id`),
  CONSTRAINT `FK_TRANSACTION_INVOICE` FOREIGN KEY (`sales_invoice_id`) REFERENCES `sales_invoice` (`sales_invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='MySQL table';

/*Data for the table `sales_transaction` */

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
