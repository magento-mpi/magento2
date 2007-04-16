SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

drop table if exists sales_attribute;
drop table if exists sales_attribute_type;
drop table if exists sales_invoice;
drop table if exists sales_invoice_attribute;
drop table if exists sales_invoice_item;
drop table if exists sales_invoice_item_attribute;
drop table if exists sales_invoice_status;
drop table if exists sales_invoice_transaction;
drop table if exists sales_order;
drop table if exists sales_order_address;
drop table if exists sales_order_address_type;
drop table if exists sales_order_attribute;
drop table if exists sales_order_item;
drop table if exists sales_order_item_attribute;
drop table if exists sales_order_item_status;
drop table if exists sales_order_payment;
drop table if exists sales_order_payment_type;
drop table if exists sales_order_status;
drop table if exists sales_order_status_history;

drop table if exists sales_quote;
drop table if exists sales_quote_entity;
drop table if exists sales_quote_attribute;
drop table if exists sales_quote_attribute_text;
drop table if exists sales_quote_attribute_int;
drop table if exists sales_quote_attribute_datetime;
drop table if exists sales_quote_attribute_varchar;
drop table if exists sales_quote_attribute_decimal;

CREATE TABLE `sales_quote` (
  `quote_id` int(11) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`quote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Quote header';

CREATE TABLE `sales_quote_attribute_text` (
  `quote_id` int(11) unsigned NOT NULL default '0',
  `entity_type` enum('self','item','address','payment','shipping') NOT NULL default 'self',
  `entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_value` text NOT NULL,
  KEY `document_id` (`quote_id`,`entity_id`),
  KEY `entity_id` (`entity_id`,`attribute_code`),
  CONSTRAINT `FK_sales_quote_attribute_text` FOREIGN KEY (`quote_id`) REFERENCES `sales_quote` (`quote_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Quote attributes TEXT';

CREATE TABLE `sales_quote_attribute_int` (
  `quote_id` int(11) unsigned NOT NULL default '0',
  `entity_type` enum('self','item','address','payment','shipping') NOT NULL default 'self',
  `entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_value` int(11) NOT NULL default '0',
  KEY `document_id` (`quote_id`,`entity_id`),
  KEY `entity_id` (`entity_id`,`attribute_code`),
  KEY `key` (`attribute_code`,`attribute_value`),
  CONSTRAINT `FK_sales_quote_attribute_int` FOREIGN KEY (`quote_id`) REFERENCES `sales_quote` (`quote_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Quote attributes INT';

CREATE TABLE `sales_quote_attribute_decimal` (
  `quote_id` int(11) unsigned NOT NULL default '0',
  `entity_type` enum('self','item','address','payment','shipping') NOT NULL default 'self',
  `entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_value` decimal(12,4) NOT NULL default '0.0000',
  KEY `document_id` (`quote_id`,`entity_id`),
  KEY `entity_id` (`entity_id`,`attribute_code`),
  KEY `key` (`attribute_code`,`attribute_value`),
  CONSTRAINT `FK_sales_quote_attribute_decimal` FOREIGN KEY (`quote_id`) REFERENCES `sales_quote` (`quote_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Quote attributes DECIMAL';

CREATE TABLE `sales_quote_attribute_varchar` (
  `quote_id` int(11) unsigned NOT NULL default '0',
  `entity_type` enum('self','item','address','payment','shipping') NOT NULL default 'self',
  `entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_value` varchar(255) NOT NULL default '',
  KEY `document_id` (`quote_id`,`entity_id`),
  KEY `entity_id` (`entity_id`,`attribute_code`),
  KEY `key` (`attribute_code`,`attribute_value`),
  CONSTRAINT `FK_sales_quote_attribute_varchar` FOREIGN KEY (`quote_id`) REFERENCES `sales_quote` (`quote_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Quote attributes VARCHAR';

CREATE TABLE `sales_quote_attribute_datetime` (
  `quote_id` int(11) unsigned NOT NULL default '0',
  `entity_type` enum('self','item','address','payment','shipping') NOT NULL default 'self',
  `entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_value` datetime NOT NULL default '0000-00-00 00:00:00',
  KEY `document_id` (`quote_id`,`entity_id`),
  KEY `entity_id` (`entity_id`,`attribute_code`),
  KEY `key` (`attribute_code`,`attribute_value`),
  CONSTRAINT `FK_sales_quote_attribute_datetime` FOREIGN KEY (`quote_id`) REFERENCES `sales_quote` (`quote_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Quote attributes DATETIME';


CREATE TABLE `sales_order` (
  `order_id` int(11) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Order header';

CREATE TABLE `sales_order_attribute_text` (
  `order_id` int(11) unsigned NOT NULL default '0',
  `entity_type` enum('self','item','address','payment') NOT NULL default 'self',
  `entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_value` text NOT NULL,
  KEY `document_id` (`order_id`,`entity_id`),
  KEY `entity_id` (`entity_id`,`attribute_code`),
  CONSTRAINT `FK_sales_order_attribute_text` FOREIGN KEY (`order_id`) REFERENCES `sales_order` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Order attributes TEXT';

CREATE TABLE `sales_order_attribute_int` (
  `order_id` int(11) unsigned NOT NULL default '0',
  `entity_type` enum('self','item','address','payment') NOT NULL default 'self',
  `entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_value` int(11) NOT NULL default '0',
  KEY `document_id` (`order_id`,`entity_id`),
  KEY `entity_id` (`entity_id`,`attribute_code`),
  KEY `key` (`attribute_code`,`attribute_value`),
  CONSTRAINT `FK_sales_order_attribute_int` FOREIGN KEY (`order_id`) REFERENCES `sales_order` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Order attributes INT';

CREATE TABLE `sales_order_attribute_decimal` (
  `order_id` int(11) unsigned NOT NULL default '0',
  `entity_type` enum('self','item','address','payment') NOT NULL default 'self',
  `entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_value` decimal(12,4) NOT NULL default '0.0000',
  KEY `document_id` (`order_id`,`entity_id`),
  KEY `entity_id` (`entity_id`,`attribute_code`),
  KEY `key` (`attribute_code`,`attribute_value`),
  CONSTRAINT `FK_sales_order_attribute_decimal` FOREIGN KEY (`order_id`) REFERENCES `sales_order` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Order attributes DECIMAL';

CREATE TABLE `sales_order_attribute_varchar` (
  `order_id` int(11) unsigned NOT NULL default '0',
  `entity_type` enum('self','item','address','payment') NOT NULL default 'self',
  `entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_value` varchar(255) NOT NULL default '',
  KEY `document_id` (`order_id`,`entity_id`),
  KEY `entity_id` (`entity_id`,`attribute_code`),
  KEY `key` (`attribute_code`,`attribute_value`),
  CONSTRAINT `FK_sales_order_attribute_varchar` FOREIGN KEY (`order_id`) REFERENCES `sales_order` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Order attributes VARCHAR';

CREATE TABLE `sales_order_attribute_datetime` (
  `order_id` int(11) unsigned NOT NULL default '0',
  `entity_type` enum('self','item','address','payment') NOT NULL default 'self',
  `entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_value` datetime NOT NULL default '0000-00-00 00:00:00',
  KEY `document_id` (`order_id`,`entity_id`),
  KEY `entity_id` (`entity_id`,`attribute_code`),
  KEY `key` (`attribute_code`,`attribute_value`),
  CONSTRAINT `FK_sales_order_attribute_datetime` FOREIGN KEY (`order_id`) REFERENCES `sales_order` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Order attributes DATETIME';


CREATE TABLE `sales_invoice` (
  `invoice_id` int(11) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Invoice header';

CREATE TABLE `sales_invoice_attribute_text` (
  `invoice_id` int(11) unsigned NOT NULL default '0',
  `entity_type` enum('self','item','address','transaction','shipment') NOT NULL default 'self',
  `entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_value` text NOT NULL,
  KEY `document_id` (`invoice_id`,`entity_id`),
  KEY `entity_id` (`entity_id`,`attribute_code`),
  CONSTRAINT `FK_sales_invoice_attribute_text` FOREIGN KEY (`invoice_id`) REFERENCES `sales_invoice` (`invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Invoice attributes TEXT';

CREATE TABLE `sales_invoice_attribute_int` (
  `invoice_id` int(11) unsigned NOT NULL default '0',
  `entity_type` enum('self','item','address','transaction','shipment') NOT NULL default 'self',
  `entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_value` int(11) NOT NULL default '0',
  KEY `document_id` (`invoice_id`,`entity_id`),
  KEY `entity_id` (`entity_id`,`attribute_code`),
  KEY `key` (`attribute_code`,`attribute_value`),
  CONSTRAINT `FK_sales_invoice_attribute_int` FOREIGN KEY (`invoice_id`) REFERENCES `sales_invoice` (`invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Invoice attributes INT';

CREATE TABLE `sales_invoice_attribute_decimal` (
  `invoice_id` int(11) unsigned NOT NULL default '0',
  `entity_type` enum('self','item','address','transaction','shipment') NOT NULL default 'self',
  `entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_value` decimal(12,4) NOT NULL default '0.0000',
  KEY `document_id` (`invoice_id`,`entity_id`),
  KEY `entity_id` (`entity_id`,`attribute_code`),
  KEY `key` (`attribute_code`,`attribute_value`),
  CONSTRAINT `FK_sales_invoice_attribute_decimal` FOREIGN KEY (`invoice_id`) REFERENCES `sales_invoice` (`invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Invoice attributes DECIMAL';

CREATE TABLE `sales_invoice_attribute_varchar` (
  `invoice_id` int(11) unsigned NOT NULL default '0',
  `entity_type` enum('self','item','address','transaction','shipment') NOT NULL default 'self',
  `entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_value` varchar(255) NOT NULL default '',
  KEY `document_id` (`invoice_id`,`entity_id`),
  KEY `entity_id` (`entity_id`,`attribute_code`),
  KEY `key` (`attribute_code`,`attribute_value`),
  CONSTRAINT `FK_sales_invoice_attribute_varchar` FOREIGN KEY (`invoice_id`) REFERENCES `sales_invoice` (`invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Invoice attributes VARCHAR';

CREATE TABLE `sales_invoice_attribute_datetime` (
  `invoice_id` int(11) unsigned NOT NULL default '0',
  `entity_type` enum('self','item','address','transaction','shipment') NOT NULL default 'self',
  `entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_value` datetime NOT NULL default '0000-00-00 00:00:00',
  KEY `document_id` (`invoice_id`,`entity_id`),
  KEY `entity_id` (`entity_id`,`attribute_code`),
  KEY `key` (`attribute_code`,`attribute_value`),
  CONSTRAINT `FK_sales_invoice_attribute_datetime` FOREIGN KEY (`invoice_id`) REFERENCES `sales_invoice` (`invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Invoice attributes DATETIME';


drop table if exists `sales_discount_coupon`;
CREATE TABLE `sales_discount_coupon` (
  `coupon_id` int(10) unsigned NOT NULL auto_increment,
  `coupon_code` varchar(50) NOT NULL default '',
  `discount_percent` decimal(10,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`coupon_id`),
  unique (`coupon_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into `sales_discount_coupon` (coupon_code, discount_percent) values ('test', 10);

drop table if exists `sales_giftcert`;
CREATE TABLE `sales_giftcert` (
  `giftcert_id` int(10) unsigned NOT NULL auto_increment,
  `giftcert_code` varchar(50) NOT NULL default '',
  `balance_amount` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`giftcert_id`),
  UNIQUE KEY `gift_code` (`giftcert_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into `sales_giftcert` (giftcert_code, balance_amount) values ('test', 10);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
