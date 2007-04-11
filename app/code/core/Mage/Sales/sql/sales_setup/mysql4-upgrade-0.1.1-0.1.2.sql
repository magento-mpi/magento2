drop table if exists sales_quote_item_bak;
drop table if exists sales_quote_item_attribute_bak;

rename table sales_qoute to sales_quote;

rename table `sales_payment` to `sales_order_payment`;
rename table `sales_payment_type` to `sales_order_payment_type`;
rename table `sales_payment_transaction` to `sales_invoice_transaction`;

drop table if exists `sales_quote_entity`;
create table `sales_quote_entity` (
`quote_entity_id` int(11) unsigned not null auto_increment primary key,
`quote_id` int(11) unsigned not null,
KEY `FK_ENTITY_QUOTE` (`quote_id`),
CONSTRAINT `FK_ENTITY_QUOTE` FOREIGN KEY (`quote_id`) REFERENCES `sales_quote` (`quote_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Quote entity';

DROP TABLE IF EXISTS `sales_quote_attribute`;
CREATE TABLE `sales_quote_attribute` (
  `quote_attribute_id` int(11) unsigned NOT NULL default '0',
  `quote_id` int(11) unsigned NOT NULL default '0',
  `quote_entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_decimal` decimal(12,4) NOT NULL default '0.0000',
  `attribute_text` text NOT NULL default '',
  `attribute_varchar` varhcar(255) NOT NULL default '',
  `attribute_int` int(11) NOT NULL default '0',
  `attribute_datetime` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`quote_attribute_id`),
  KEY `FK_QUOTE_ATTRIBUTE` (`quote_id`),
  KEY `FK_QUOTE_ATTRIBUTE_CODE` (`attribute_code`),
  KEY `quote_id` (`quote_id`,`entity_type`,`entity_id`),
  CONSTRAINT `FK_QUOTE_ATTRIBUTE` FOREIGN KEY (`quote_id`) REFERENCES `sales_quote` (`quote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Quote attributes';

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
drop table if exists `sales_quote_address_type`;
drop table if exists `sales_quote_item_attribute`;

drop table if exists `cart_product`;
drop table if exists `cart`;

drop table `sales_quote_payment`;
drop table `sales_quote_item`;
drop table `sales_quote_address`;

alter table `sales_quote` 
, change `customer_id` `customer_id` int (11)UNSIGNED   NOT NULL 
, drop `converted_at`
, drop `created_at`
,  drop `quote_status`
, drop foreign key `FK_QOUTE_CUSTOMER`;
