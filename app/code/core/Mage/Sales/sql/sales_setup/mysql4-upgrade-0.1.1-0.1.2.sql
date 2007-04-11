drop table if exists `sales_quote_item_bak`;
drop table if exists `sales_quote_item_attribute_bak`;

drop table if exists `cart_product`;
drop table if exists `cart`;

drop table if exists `sales_quote_address_type`;
drop table if exists `sales_quote_item_attribute`;

drop table if exists `sales_quote_payment`;
drop table if exists `sales_quote_item`;
drop table if exists `sales_quote_address`;
drop table if exists `sales_quote_address_type`;
drop table if exists `sales_quote_attribute`;
drop table if exists `sales_quote_entity`;
drop table if exists `sales_qoute`;
drop table if exists `sales_quote`;

CREATE TABLE `sales_quote` (
  `quote_id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`quote_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Quotes info';

CREATE TABLE `sales_quote_entity` (
  `quote_entity_id` int(11) unsigned NOT NULL auto_increment,
  `quote_id` int(11) unsigned NOT NULL default '0',
  `entity_type` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`quote_entity_id`),
  KEY `FK_ENTITY_QUOTE` (`quote_id`),
  CONSTRAINT `FK_ENTITY_QUOTE` FOREIGN KEY (`quote_id`) REFERENCES `sales_quote` (`quote_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Quote entity';

CREATE TABLE `sales_quote_attribute` (
  `quote_attribute_id` int(11) unsigned NOT NULL auto_increment,
  `quote_id` int(11) unsigned NOT NULL default '0',
  `quote_entity_id` int(11) NOT NULL default '0',
  `attribute_code` varchar(32) NOT NULL default '',
  `attribute_decimal` decimal(12,4) NOT NULL default '0.0000',
  `attribute_text` text NOT NULL,
  `attribute_int` int(11) NOT NULL default '0',
  `attribute_datetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `attribute_varchar` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`quote_attribute_id`),
  KEY `FK_QUOTE_ATTRIBUTE` (`quote_id`),
  KEY `FK_QUOTE_ATTRIBUTE_CODE` (`attribute_code`),
  KEY `quote_id` (`quote_id`,`quote_entity_id`),
  CONSTRAINT `FK_QUOTE_ATTRIBUTE` FOREIGN KEY (`quote_id`) REFERENCES `sales_quote` (`quote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Quote attributes';
