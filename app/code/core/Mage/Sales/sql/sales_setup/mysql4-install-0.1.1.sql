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

/*Table structure for table `sales_attribute` */

DROP TABLE IF EXISTS `sales_attribute`;

CREATE TABLE `sales_attribute` (
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_type_id` tinyint(3) NOT NULL default '0',
  `data_type` varchar(16) NOT NULL default '',
  `sort_order` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attribute_code`),
  KEY `FK_ATTRIBUTE_TYPE` (`attribute_type_id`),
  CONSTRAINT `FK_ATTRIBUTE_TYPE` FOREIGN KEY (`attribute_type_id`) REFERENCES `sales_attribute_type` (`attribute_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Sales entity attributes';

/*Data for the table `sales_attribute` */

/*Table structure for table `sales_attribute_type` */

DROP TABLE IF EXISTS `sales_attribute_type`;

CREATE TABLE `sales_attribute_type` (
  `attribute_type_id` tinyint(3) NOT NULL default '0',
  `type_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`attribute_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Attributes type(entity)';

/*Data for the table `sales_attribute_type` */

/*Table structure for table `sales_invoice` */

DROP TABLE IF EXISTS `sales_invoice`;

CREATE TABLE `sales_invoice` (
  `invoice_id` int(11) unsigned NOT NULL auto_increment,
  `order_id` int(11) unsigned NOT NULL default '0',
  `invoice_status_id` tinyint(3) unsigned default NULL,
  `sales_address_id` int(10) unsigned NOT NULL default '0',
  `invoice_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`invoice_id`),
  KEY `FK_INVOICE_ORDER` (`order_id`),
  KEY `FK_INVOICE_STATUS` (`invoice_status_id`),
  CONSTRAINT `FK_INVOICE_STATUS` FOREIGN KEY (`invoice_status_id`) REFERENCES `sales_invoice_status` (`invoice_status_id`),
  CONSTRAINT `FK_INVOICE_ORDER` FOREIGN KEY (`order_id`) REFERENCES `sales_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Invoices';

/*Data for the table `sales_invoice` */

/*Table structure for table `sales_invoice_item` */

DROP TABLE IF EXISTS `sales_invoice_item`;

CREATE TABLE `sales_invoice_item` (
  `invoice_item_id` int(11) unsigned NOT NULL default '0',
  `order_item_id` int(11) unsigned default NULL,
  `invoice_id` int(11) unsigned default '0',
  `product_id` int(11) unsigned default NULL,
  PRIMARY KEY  (`invoice_item_id`),
  KEY `FK_INVOICE_ITEM` (`order_item_id`),
  KEY `FK_INVOICE_ITEM_PRODUCT` (`product_id`),
  KEY `FK_ITEM_INVOICE` (`invoice_id`),
  CONSTRAINT `FK_ITEM_INVOICE` FOREIGN KEY (`invoice_id`) REFERENCES `sales_invoice` (`invoice_id`),
  CONSTRAINT `FK_INVOICE_ITEM` FOREIGN KEY (`order_item_id`) REFERENCES `sales_order_item` (`order_item_id`),
  CONSTRAINT `FK_INVOICE_ITEM_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Invoice items';

/*Data for the table `sales_invoice_item` */

/*Table structure for table `sales_invoice_item_attribute` */

DROP TABLE IF EXISTS `sales_invoice_item_attribute`;

CREATE TABLE `sales_invoice_item_attribute` (
  `invoice_item_attribte_id` int(11) unsigned NOT NULL default '0',
  `invoice_item_id` int(11) unsigned default NULL,
  `attribute_code` varchar(32) default NULL,
  `attribute_decimal` decimal(12,4) default NULL,
  `attribute_text` varchar(128) default NULL,
  PRIMARY KEY  (`invoice_item_attribte_id`),
  KEY `FK_INVOICE_ITEM_ATTRIBUTE` (`invoice_item_id`),
  KEY `FK_INVOICE_ITEM_ATTRIBUTE_CODE` (`attribute_code`),
  CONSTRAINT `FK_INVOICE_ITEM_ATTRIBUTE_CODE` FOREIGN KEY (`attribute_code`) REFERENCES `sales_attribute` (`attribute_code`) ON UPDATE CASCADE,
  CONSTRAINT `FK_INVOICE_ITEM_ATTRIBUTE` FOREIGN KEY (`invoice_item_id`) REFERENCES `sales_invoice_item` (`invoice_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_invoice_item_attribute` */

/*Table structure for table `sales_invoice_status` */

DROP TABLE IF EXISTS `sales_invoice_status`;

CREATE TABLE `sales_invoice_status` (
  `invoice_status_id` tinyint(3) unsigned NOT NULL auto_increment,
  `invoice_status_code` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`invoice_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Invoice status';

/*Data for the table `sales_invoice_status` */

/*Table structure for table `sales_order` */

DROP TABLE IF EXISTS `sales_order`;

CREATE TABLE `sales_order` (
  `order_id` int(11) unsigned NOT NULL auto_increment,
  `order_status_id` tinyint(3) unsigned NOT NULL default '0',
  `customer_id` int(11) unsigned default NULL,
  `quote_id` int(11) unsigned default NULL,
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `order_add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`order_id`),
  KEY `FK_ORDER_CUSTOMER` (`customer_id`),
  KEY `FK_ORDER_QUOTE` (`quote_id`),
  KEY `FK_ORDER_STATUS` (`order_status_id`),
  KEY `FK_ORDER_WEBSITE` (`website_id`),
  CONSTRAINT `FK_ORDER_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`),
  CONSTRAINT `FK_ORDER_CUSTOMER` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_ORDER_QUOTE` FOREIGN KEY (`quote_id`) REFERENCES `sales_qoute` (`quote_id`) ON DELETE SET NULL,
  CONSTRAINT `FK_ORDER_STATUS` FOREIGN KEY (`order_status_id`) REFERENCES `sales_order_status` (`order_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Orders';

/*Data for the table `sales_order` */

/*Table structure for table `sales_order_address` */

DROP TABLE IF EXISTS `sales_order_address`;

CREATE TABLE `sales_order_address` (
  `address_id` int(11) unsigned NOT NULL auto_increment,
  `address_type_id` tinyint(3) unsigned NOT NULL default '0',
  `order_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`address_id`),
  KEY `FK_ORDER_ADDRESS` (`order_id`),
  KEY `FK_ORDER_ADDRESS_TYPE` (`address_type_id`),
  CONSTRAINT `FK_ORDER_ADDRESS_TYPE` FOREIGN KEY (`address_type_id`) REFERENCES `sales_order_address_type` (`address_type_id`),
  CONSTRAINT `FK_ORDER_ADDRESS` FOREIGN KEY (`order_id`) REFERENCES `sales_order` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Customer addresses info per order';

/*Data for the table `sales_order_address` */

/*Table structure for table `sales_order_address_type` */

DROP TABLE IF EXISTS `sales_order_address_type`;

CREATE TABLE `sales_order_address_type` (
  `address_type_id` tinyint(3) unsigned NOT NULL auto_increment,
  `address_type_code` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`address_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Order adress type(shipment, payment, etc.)';

/*Data for the table `sales_order_address_type` */

/*Table structure for table `sales_order_attribute` */

DROP TABLE IF EXISTS `sales_order_attribute`;

CREATE TABLE `sales_order_attribute` (
  `order_attribute_id` int(11) unsigned NOT NULL default '0',
  `order_id` int(11) unsigned NOT NULL default '0',
  `attribute_code` varchar(32) default NULL,
  `attribute_decimal` decimal(12,4) default NULL,
  `attribute_text` varchar(128) default NULL,
  PRIMARY KEY  (`order_attribute_id`),
  KEY `FK_ORDER_ATTRIBUTE` (`order_id`),
  KEY `FK_ORDER_ATTRIBUTE_CODE` (`attribute_code`),
  CONSTRAINT `FK_ORDER_ATTRIBUTE_CODE` FOREIGN KEY (`attribute_code`) REFERENCES `sales_attribute` (`attribute_code`) ON UPDATE CASCADE,
  CONSTRAINT `FK_ORDER_ATTRIBUTE` FOREIGN KEY (`order_id`) REFERENCES `sales_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Order attributes';

/*Data for the table `sales_order_attribute` */

/*Table structure for table `sales_order_item` */

DROP TABLE IF EXISTS `sales_order_item`;

CREATE TABLE `sales_order_item` (
  `order_item_id` int(11) unsigned NOT NULL auto_increment,
  `order_id` int(11) unsigned NOT NULL default '0',
  `product_id` int(11) unsigned default NULL,
  `order_item_status_id` tinyint(3) unsigned NOT NULL default '0',
  `item_info` text NOT NULL,
  PRIMARY KEY  (`order_item_id`),
  KEY `FK_ITEM_ORDER` (`order_id`),
  KEY `FK_ITEM_PRODUCT` (`product_id`),
  KEY `FK_ORDER_ITEM_STATUS` (`order_item_status_id`),
  CONSTRAINT `FK_ORDER_ITEM_STATUS` FOREIGN KEY (`order_item_status_id`) REFERENCES `sales_order_item_status` (`order_item_status_id`),
  CONSTRAINT `FK_ITEM_ORDER` FOREIGN KEY (`order_id`) REFERENCES `sales_order` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ITEM_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Order products';

/*Data for the table `sales_order_item` */

/*Table structure for table `sales_order_item_attribute` */

DROP TABLE IF EXISTS `sales_order_item_attribute`;

CREATE TABLE `sales_order_item_attribute` (
  `order_item_attribute_id` int(11) unsigned NOT NULL default '0',
  `order_item_id` int(11) unsigned NOT NULL default '0',
  `attribute_code` varchar(32) default NULL,
  `attribute_decimal` decimal(12,4) default NULL,
  `attribute_text` text,
  PRIMARY KEY  (`order_item_attribute_id`),
  KEY `FK_ORDER_ITEM_ATTRIBUTE` (`order_item_id`),
  KEY `FK_ORDER_ITEM_ATTRIBUTE_CODE` (`attribute_code`),
  CONSTRAINT `FK_ORDER_ITEM_ATTRIBUTE_CODE` FOREIGN KEY (`attribute_code`) REFERENCES `sales_attribute` (`attribute_code`),
  CONSTRAINT `FK_ORDER_ITEM_ATTRIBUTE` FOREIGN KEY (`order_item_id`) REFERENCES `sales_order_item` (`order_item_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product information per order';

/*Data for the table `sales_order_item_attribute` */

/*Table structure for table `sales_order_item_status` */

DROP TABLE IF EXISTS `sales_order_item_status`;

CREATE TABLE `sales_order_item_status` (
  `order_item_status_id` tinyint(3) unsigned NOT NULL auto_increment,
  `item_status_code` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`order_item_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Statuses of order item';

/*Data for the table `sales_order_item_status` */

/*Table structure for table `sales_order_status` */

DROP TABLE IF EXISTS `sales_order_status`;

CREATE TABLE `sales_order_status` (
  `order_status_id` tinyint(3) unsigned NOT NULL auto_increment,
  `order_status_code` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`order_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Orders status';

/*Data for the table `sales_order_status` */

/*Table structure for table `sales_order_status_history` */

DROP TABLE IF EXISTS `sales_order_status_history`;

CREATE TABLE `sales_order_status_history` (
  `order_id` int(11) unsigned NOT NULL default '0',
  `order_status_id` tinyint(3) unsigned NOT NULL default '0',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`order_id`,`order_status_id`),
  KEY `FK_HYSTORY_STATUS` (`order_status_id`),
  CONSTRAINT `FK_HYSTORY_STATUS` FOREIGN KEY (`order_status_id`) REFERENCES `sales_order_status` (`order_status_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_HYSTORY_ORDER` FOREIGN KEY (`order_id`) REFERENCES `sales_order` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Order status change history';

/*Data for the table `sales_order_status_history` */

/*Table structure for table `sales_payment` */

DROP TABLE IF EXISTS `sales_payment`;

CREATE TABLE `sales_payment` (
  `payment_id` int(11) unsigned NOT NULL auto_increment,
  `order_id` int(11) unsigned NOT NULL default '0',
  `payment_type_id` tinyint(3) unsigned default NULL,
  `payment_method_id` tinyint(3) unsigned default NULL,
  `payment_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`payment_id`),
  KEY `FK_PAYMENT_METHOD` (`payment_method_id`),
  KEY `FK_PAYMENT_ORDER` (`order_id`),
  KEY `FK_PAYMENT_TYPE` (`payment_type_id`),
  CONSTRAINT `FK_PAYMENT_TYPE` FOREIGN KEY (`payment_type_id`) REFERENCES `sales_payment_type` (`payment_type_id`),
  CONSTRAINT `FK_PAYMENT_METHOD` FOREIGN KEY (`payment_method_id`) REFERENCES `customer_payment_method` (`payment_method_id`),
  CONSTRAINT `FK_PAYMENT_ORDER` FOREIGN KEY (`order_id`) REFERENCES `sales_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Payments';

/*Data for the table `sales_payment` */

/*Table structure for table `sales_payment_transaction` */

DROP TABLE IF EXISTS `sales_payment_transaction`;

CREATE TABLE `sales_payment_transaction` (
  `transaction_id` int(11) unsigned NOT NULL auto_increment,
  `invoice_id` int(11) unsigned default '0',
  `payment_id` int(11) unsigned default '0',
  `transaction_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`transaction_id`),
  KEY `FK_TRANSACTION_INVOICE` (`invoice_id`),
  KEY `FK_TRANSACTION_PAYMENT` (`payment_id`),
  CONSTRAINT `FK_TRANSACTION_PAYMENT` FOREIGN KEY (`payment_id`) REFERENCES `sales_payment` (`payment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_TRANSACTION_INVOICE` FOREIGN KEY (`invoice_id`) REFERENCES `sales_invoice` (`invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Payment transactions';

/*Data for the table `sales_payment_transaction` */

/*Table structure for table `sales_payment_type` */

DROP TABLE IF EXISTS `sales_payment_type`;

CREATE TABLE `sales_payment_type` (
  `payment_type_id` tinyint(3) unsigned NOT NULL auto_increment,
  `payment_type_code` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`payment_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Types of sales';

/*Data for the table `sales_payment_type` */

/*Table structure for table `sales_qoute` */

DROP TABLE IF EXISTS `sales_qoute`;

CREATE TABLE `sales_qoute` (
  `quote_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned default NULL,
  `quote_add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`quote_id`),
  KEY `FK_QOUTE_CUSTOMER` (`customer_id`),
  CONSTRAINT `FK_QOUTE_CUSTOMER` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Quotes info';

/*Data for the table `sales_qoute` */

/*Table structure for table `sales_quote_address` */

DROP TABLE IF EXISTS `sales_quote_address`;

CREATE TABLE `sales_quote_address` (
  `quote_address_id` int(11) unsigned NOT NULL auto_increment,
  `quote_id` int(11) unsigned default NULL,
  `address_id` int(11) unsigned default NULL,
  `address_type_id` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`quote_address_id`),
  KEY `FK_ADDRESS_QUOTE` (`quote_id`),
  KEY `FK_QUOTE_ADDRESS` (`address_id`),
  KEY `FK_QUOTE_ADDRESS_TYPE` (`address_type_id`),
  CONSTRAINT `FK_QUOTE_ADDRESS_TYPE` FOREIGN KEY (`address_type_id`) REFERENCES `sales_order_address_type` (`address_type_id`),
  CONSTRAINT `FK_ADDRESS_QUOTE` FOREIGN KEY (`quote_id`) REFERENCES `sales_qoute` (`quote_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_QUOTE_ADDRESS` FOREIGN KEY (`address_id`) REFERENCES `customer_address` (`address_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Quote address';

/*Data for the table `sales_quote_address` */

/*Table structure for table `sales_quote_item` */

DROP TABLE IF EXISTS `sales_quote_item`;

CREATE TABLE `sales_quote_item` (
  `quote_item_id` int(11) unsigned NOT NULL default '0',
  `quote_id` int(11) unsigned NOT NULL default '0',
  `product_id` int(11) unsigned NOT NULL default '0',
  `quote_address_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`quote_item_id`),
  KEY `FK_ITEM_QUOTE` (`quote_id`),
  KEY `FK_QUOTE_ITEM_ADDRESS` (`quote_address_id`),
  KEY `FK_QUOTE_ITEM_PRODUCT` (`product_id`),
  CONSTRAINT `FK_QUOTE_ITEM_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`),
  CONSTRAINT `FK_ITEM_QUOTE` FOREIGN KEY (`quote_id`) REFERENCES `sales_qoute` (`quote_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_QUOTE_ITEM_ADDRESS` FOREIGN KEY (`quote_address_id`) REFERENCES `sales_quote_address` (`quote_address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='quote items';

/*Data for the table `sales_quote_item` */

/*Table structure for table `sales_quote_item_attribute` */

DROP TABLE IF EXISTS `sales_quote_item_attribute`;

CREATE TABLE `sales_quote_item_attribute` (
  `quote_item_attribute_id` int(11) unsigned NOT NULL default '0',
  `quote_item_id` int(11) unsigned NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_decimal` decimal(12,4) default NULL,
  `attribute_text` varchar(128) default NULL,
  PRIMARY KEY  (`quote_item_attribute_id`),
  KEY `FK_QUOTE_ITEM_ATTRIBUTE` (`quote_item_id`),
  KEY `FK_QUOTE_ITEM_ATTRIBUTE_CODE` (`attribute_code`),
  CONSTRAINT `FK_QUOTE_ITEM_ATTRIBUTE_CODE` FOREIGN KEY (`attribute_code`) REFERENCES `sales_attribute` (`attribute_code`),
  CONSTRAINT `FK_QUOTE_ITEM_ATTRIBUTE` FOREIGN KEY (`quote_item_id`) REFERENCES `sales_quote_item` (`quote_item_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Quote item attributes';

/*Data for the table `sales_quote_item_attribute` */

/*Table structure for table `sales_shipment` */

DROP TABLE IF EXISTS `sales_shipment`;

CREATE TABLE `sales_shipment` (
  `shipment_id` int(11) unsigned NOT NULL auto_increment,
  `order_id` int(11) unsigned NOT NULL default '0',
  `shipment_method_id` tinyint(3) unsigned default NULL,
  `invoice_id` int(11) unsigned default '0',
  `shipment_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `tracking_code` varchar(255) default NULL,
  `shipment_cost` decimal(12,4) default NULL,
  PRIMARY KEY  (`shipment_id`),
  KEY `FK_SHIPMENT_INVOICE` (`invoice_id`),
  KEY `FK_SHIPMENT_METHOD` (`shipment_method_id`),
  KEY `FK_SHPMENT_ORDER` (`order_id`),
  CONSTRAINT `FK_SHPMENT_ORDER` FOREIGN KEY (`order_id`) REFERENCES `sales_order` (`order_id`),
  CONSTRAINT `FK_SHIPMENT_INVOICE` FOREIGN KEY (`invoice_id`) REFERENCES `sales_invoice` (`invoice_id`),
  CONSTRAINT `FK_SHIPMENT_METHOD` FOREIGN KEY (`shipment_method_id`) REFERENCES `sales_shipment_method` (`shipment_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Orders shipmpments';

/*Data for the table `sales_shipment` */

/*Table structure for table `sales_shipment_method` */

DROP TABLE IF EXISTS `sales_shipment_method`;

CREATE TABLE `sales_shipment_method` (
  `shipment_method_id` tinyint(3) unsigned NOT NULL auto_increment,
  `method_code` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`shipment_method_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Shipment methods';

/*Data for the table `sales_shipment_method` */

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
