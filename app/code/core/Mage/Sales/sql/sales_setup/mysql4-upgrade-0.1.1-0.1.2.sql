drop table if exists sales_quote_item_bak;
drop table if exists sales_quote_item_attribute_bak;

alter table `magenta`.`sales_quote_address` ,add column `firstname` varchar (128)  NOT NULL  after `address_type_id`, add column `lastname` varchar (128)  NOT NULL  after `firstname`, add column `postcode` varchar (16)  NOT NULL  after `lastname`, add column `street` text   NOT NULL  after `postcode`, add column `city` varchar (128)  NOT NULL  after `street`, add column `region` varchar (128)  NOT NULL  after `city`, add column `region_id` mediumint (8)  NOT NULL  after `region`, add column `country_id` smallint (6)  NOT NULL  after `region_id`, add column `company` varchar (128)  NOT NULL  after `country_id`, add column `telephone` varchar (32)  NOT NULL  after `company`, add column `fax` varchar (32)  NOT NULL  after `telephone`,change `quote_id` `quote_id` int (11)UNSIGNED   NOT NULL , change `address_id` `address_id` int (11)UNSIGNED   NOT NULL , change `address_type_id` `address_type_id` tinyint (3)UNSIGNED   NOT NULL ;

rename table sales_qoute to sales_quote;

create table sales_quote_attribute (


DROP TABLE IF EXISTS `sales_quote_attribute`;

CREATE TABLE `sales_quote_attribute` (
  `quote_attribute_id` int(11) unsigned NOT NULL default '0',
  `quote_id` int(11) unsigned NOT NULL default '0',
  `attribute_code` varchar(32) default NULL,
  `attribute_decimal` decimal(12,4) default NULL,
  `attribute_text` varchar(128) default NULL,
  PRIMARY KEY  (`quote_attribute_id`),
  KEY `FK_QUOTE_ATTRIBUTE` (`quote_id`),
  KEY `FK_QUOTE_ATTRIBUTE_CODE` (`attribute_code`),
  CONSTRAINT `FK_QUOTE_ATTRIBUTE_CODE` FOREIGN KEY (`attribute_code`) REFERENCES `sales_attribute` (`attribute_code`) ON UPDATE CASCADE,
  CONSTRAINT `FK_QUOTE_ATTRIBUTE` FOREIGN KEY (`quote_id`) REFERENCES `sales_quote` (`quote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Quote attributes';

alter table `magenta`.`sales_quote` ,add column `quote_status` tinyint (6)  NOT NULL  after `customer_id`, add column `converted_at` datetime   NOT NULL  after `created_at`,change `customer_id` `customer_id` int (11)UNSIGNED   NOT NULL , change `quote_add_date` `created_at` datetime  DEFAULT '0000-00-00 00:00:00' NOT NULL;

CREATE TABLE `sales_quote_address_type` (
  `address_type_id` tinyint(3) unsigned NOT NULL default '0',
  `address_type_code` varchar(16) default NULL,
  PRIMARY KEY  (`address_type_id`),
  KEY `address_type_code` (`address_type_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

rename table `sales_payment` to `sales_order_payment`;
rename table `sales_payment_type` to `sales_order_payment_type`;
rename table `sales_payment_transaction` to `sales_invoice_transaction`;

CREATE TABLE `sales_quote_payment` (
  `quote_payment_id` int(10) unsigned NOT NULL auto_increment,
  `quote_id` int(10) unsigned NOT NULL default '0',
  `payment_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`quote_payment_id`),
  KEY `quote_id` (`quote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

alter table .`sales_quote_attribute` ,add column `entity_type` enum ('quote','item','payment') DEFAULT 'quote' NOT NULL  after `quote_id`, add column `entity_id` int (11)  NOT NULL  after `attribute_type`,change `attribute_code` `attribute_code` varchar (32)  NOT NULL  COLLATE utf8_general_ci , change `attribute_decimal` `attribute_decimal` decimal (12,4)  NOT NULL , change `attribute_text` `attribute_text` varchar (128)  NULL  COLLATE utf8_general_ci , add index `quote_id` (`quote_id`, `entity_type`, `entity_id`);