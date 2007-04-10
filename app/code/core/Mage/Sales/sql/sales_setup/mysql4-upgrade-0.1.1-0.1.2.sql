drop table if exists sales_quote_item_bak;
drop table if exists sales_quote_item_attribute_bak;

rename table sales_qoute to sales_quote;

rename table `sales_payment` to `sales_order_payment`;
rename table `sales_payment_type` to `sales_order_payment_type`;
rename table `sales_payment_transaction` to `sales_invoice_transaction`;

alter table `sales_quote_address` 
, change `quote_id` `quote_id` int (11)UNSIGNED   NOT NULL 
, change `address_id` `address_id` int (11)UNSIGNED   NOT NULL 
, change `address_type_id` `address_type_id` tinyint (3)UNSIGNED   NOT NULL ;

alter table `sales_quote` 
, add column `quote_status` tinyint (6)  NOT NULL  after `customer_id`
, add column `converted_at` datetime   NOT NULL  after `created_at`
, change `customer_id` `customer_id` int (11)UNSIGNED   NOT NULL 
, change `quote_add_date` `created_at` datetime  DEFAULT '0000-00-00 00:00:00' NOT NULL;


DROP TABLE IF EXISTS `sales_quote_attribute`;
CREATE TABLE `sales_quote_attribute` (
  `quote_attribute_id` int(11) unsigned NOT NULL default '0',
  `quote_id` int(11) unsigned NOT NULL default '0',
  `entity_type` enum('quote','address','item','payment') NOT NULL default 'quote',
  `entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_decimal` decimal(12,4) NOT NULL default '0.0000',
  `attribute_text` varchar(128) NOT NULL default '',
  `attribute_int` int(11) NOT NULL default '0',
  `attribute_datetime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`quote_attribute_id`),
  KEY `FK_QUOTE_ATTRIBUTE` (`quote_id`),
  KEY `FK_QUOTE_ATTRIBUTE_CODE` (`attribute_code`),
  KEY `quote_id` (`quote_id`,`entity_type`,`entity_id`),
  CONSTRAINT `FK_QUOTE_ATTRIBUTE` FOREIGN KEY (`quote_id`) REFERENCES `sales_quote` (`quote_id`),
  CONSTRAINT `FK_QUOTE_ATTRIBUTE_CODE` FOREIGN KEY (`attribute_code`) REFERENCES `sales_attribute` (`attribute_code`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Quote attributes'

drop table if exists `sales_quote_payment`;
CREATE TABLE `sales_quote_payment` (
  `quote_payment_id` int(10) unsigned NOT NULL auto_increment,
  `quote_id` int(10) unsigned NOT NULL default '0',
  `payment_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`quote_payment_id`),
  KEY `quote_id` (`quote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table if exists `sales_quote_address_type`;
CREATE TABLE `sales_quote_address_type` (
  `address_type_id` tinyint(3) unsigned NOT NULL default '0',
  `address_type_code` varchar(16) default NULL,
  PRIMARY KEY  (`address_type_id`),
  KEY `address_type_code` (`address_type_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table if exists `sales_invoice_attribute`;
CREATE TABLE `sales_invoice_attribute` (
  `invoice_attribute_id` int(11) unsigned NOT NULL default '0',
  `invoice_id` int(11) unsigned NOT NULL default '0',
  `attribute_code` varchar(32) default NULL,
  `attribute_decimal` decimal(12,4) default NULL,
  `attribute_text` varchar(128) default NULL,
  PRIMARY KEY  (`invoice_attribute_id`),
  KEY `FK_INVOICE_ATTRIBUTE` (`invoice_id`),
  KEY `FK_INVOICE_ATTRIBUTE_CODE` (`attribute_code`),
  CONSTRAINT `FK_INVOICE_ATTRIBUTE_CODE` FOREIGN KEY (`attribute_code`) REFERENCES `sales_attribute` (`attribute_code`) ON UPDATE CASCADE,
  CONSTRAINT `FK_INVOICE_ATTRIBUTE` FOREIGN KEY (`invoice_id`) REFERENCES `sales_invoice` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Invoice attributes';

alter table `magenta`.`sales_quote_address` 
, drop foreign key `FK_QUOTE_ADDRESS_TYPE`
, drop foreign key `FK_QUOTE_ADDRESS` 
, drop `address_type_id`
, change `address_id` `customer_address_id` int (11)UNSIGNED  DEFAULT '0' NOT NULL 
, add column `address_type_code` varchar (32)  NOT NULL  after `customer_address_id`;

drop table sales_quote_address_type;