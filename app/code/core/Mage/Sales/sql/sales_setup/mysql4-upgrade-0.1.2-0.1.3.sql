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


CREATE TABLE `sales_order` (
  `order_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`order_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='orders info';

CREATE TABLE `sales_order_entity` (
  `order_entity_id` int(11) unsigned NOT NULL auto_increment,
  `order_id` int(11) unsigned NOT NULL default '0',
  `entity_type` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`order_entity_id`),
  KEY `FK_ENTITY_order` (`order_id`),
  CONSTRAINT `FK_ENTITY_order` FOREIGN KEY (`order_id`) REFERENCES `sales_order` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='order entity';

CREATE TABLE `sales_order_attribute` (
  `order_attribute_id` int(11) unsigned NOT NULL auto_increment,
  `order_id` int(11) unsigned NOT NULL default '0',
  `order_entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_decimal` decimal(12,4) NOT NULL default '0.0000',
  `attribute_text` text NOT NULL,
  `attribute_int` int(11) NOT NULL default '0',
  `attribute_datetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `attribute_varchar` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`order_attribute_id`),
  KEY `FK_order_ATTRIBUTE` (`order_id`),
  KEY `FK_order_ATTRIBUTE_CODE` (`attribute_code`),
  KEY `order_id` (`order_id`,`order_entity_id`),
  CONSTRAINT `FK_order_ATTRIBUTE` FOREIGN KEY (`order_id`) REFERENCES `sales_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='order attributes';


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
