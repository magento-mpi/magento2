SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

drop table if exists sales_order;
drop table if exists sales_order_attribute;
drop table if exists sales_order_attribute_text;
drop table if exists sales_order_attribute_int;
drop table if exists sales_order_attribute_datetime;
drop table if exists sales_order_attribute_varchar;
drop table if exists sales_order_attribute_decimal;

CREATE TABLE `sales_order` (
  `order_id` int(11) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Order header';

CREATE TABLE `sales_order_attribute_text` (
  `order_id` int(11) unsigned NOT NULL default '0',
  `entity_type` enum('self','item','address','payment','status','status') NOT NULL default 'self',
  `entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_value` text NOT NULL,
  KEY `document_id` (`order_id`,`entity_id`),
  KEY `entity_id` (`entity_id`,`attribute_code`),
  CONSTRAINT `FK_sales_order_attribute_text` FOREIGN KEY (`order_id`) REFERENCES `sales_order` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Order attributes TEXT';

CREATE TABLE `sales_order_attribute_int` (
  `order_id` int(11) unsigned NOT NULL default '0',
  `entity_type` enum('self','item','address','payment','status') NOT NULL default 'self',
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
  `entity_type` enum('self','item','address','payment','status') NOT NULL default 'self',
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
  `entity_type` enum('self','item','address','payment','status') NOT NULL default 'self',
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
  `entity_type` enum('self','item','address','payment','status') NOT NULL default 'self',
  `entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_value` datetime NOT NULL default '0000-00-00 00:00:00',
  KEY `document_id` (`order_id`,`entity_id`),
  KEY `entity_id` (`entity_id`,`attribute_code`),
  KEY `key` (`attribute_code`,`attribute_value`),
  CONSTRAINT `FK_sales_order_attribute_datetime` FOREIGN KEY (`order_id`) REFERENCES `sales_order` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Order attributes DATETIME';

CREATE TABLE `sales_counter` (
  `counter_id` int(10) unsigned NOT NULL auto_increment,
  `website_id` int(10) unsigned NOT NULL default '0',
  `counter_type` varchar(50) NOT NULL default '',
  `counter_value` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`counter_id`),
  UNIQUE KEY `website_id` (`website_id`,`counter_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
