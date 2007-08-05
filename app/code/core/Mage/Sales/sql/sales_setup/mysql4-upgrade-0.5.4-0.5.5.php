<?php

$this->installEntities($this->getDefaultEntities());

$conn->multi_query(<<<EOT

SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';


drop table if exists `sales_quote_rate`;
drop table if exists `sales_quote_temp`;
CREATE TABLE `sales_quote_temp` (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
  `increment_id` varchar (50) NOT NULL default '',
  `parent_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `is_active` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`entity_id`)
 ,CONSTRAINT `FK_sales_quote_temp_type` FOREIGN KEY (`entity_type_id`) REFERENCES `eav_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT `FK_sales_quote_temp_store` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table if exists `sales_quote_rate_datetime`;
drop table if exists `sales_quote_temp_datetime`;
CREATE TABLE `sales_quote_temp_datetime` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`)
 ,CONSTRAINT `FK_sales_quote_temp_datetime_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `eav_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT `FK_sales_quote_temp_datetime_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `eav_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT `FK_sales_quote_temp_datetime_store` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT `FK_sales_quote_temp_datetime` FOREIGN KEY (`entity_id`) REFERENCES `sales_quote_temp` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table if exists `sales_quote_rate_decimal`;
drop table if exists `sales_quote_temp_decimal`;
CREATE TABLE `sales_quote_temp_decimal` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`)
 ,CONSTRAINT `FK_sales_quote_temp_decimal_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `eav_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT `FK_sales_quote_temp_decimal_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `eav_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT `FK_sales_quote_temp_decimal_store` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT `FK_sales_quote_temp_decimal` FOREIGN KEY (`entity_id`) REFERENCES `sales_quote_temp` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table if exists `sales_quote_rate_int`;
drop table if exists `sales_quote_temp_int`;
CREATE TABLE `sales_quote_temp_int` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`)
 ,CONSTRAINT `FK_sales_quote_temp_int_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `eav_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT `FK_sales_quote_temp_int_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `eav_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT `FK_sales_quote_temp_int_store` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT `FK_sales_quote_temp_int` FOREIGN KEY (`entity_id`) REFERENCES `sales_quote_temp` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table if exists `sales_quote_rate_text`;
drop table if exists `sales_quote_temp_text`;
CREATE TABLE `sales_quote_temp_text` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`)
 ,CONSTRAINT `FK_sales_quote_temp_text_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `eav_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT `FK_sales_quote_temp_text_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `eav_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT `FK_sales_quote_temp_text_store` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT `FK_sales_quote_temp_text` FOREIGN KEY (`entity_id`) REFERENCES `sales_quote_temp` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table if exists `sales_quote_rate_varchar`;
drop table if exists `sales_quote_temp_varchar`;
CREATE TABLE `sales_quote_temp_varchar` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`)
 ,CONSTRAINT `FK_sales_quote_temp_varchar_entity_type` FOREIGN KEY (`entity_type_id`) REFERENCES `eav_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT `FK_sales_quote_temp_varchar_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `eav_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT `FK_sales_quote_temp_varchar_store` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
 ,CONSTRAINT `FK_sales_quote_temp_varchar` FOREIGN KEY (`entity_id`) REFERENCES `sales_quote_temp` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
EOT
);