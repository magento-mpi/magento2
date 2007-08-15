SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

drop table if exists `salesrule`;
CREATE TABLE `salesrule` (
  `rule_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `from_date` date NOT NULL default '0000-00-00',
  `to_date` date NOT NULL default '0000-00-00',
  `store_ids` varchar(255) NOT NULL default '',
  `coupon_code` varchar(255) default NULL,
  `customer_group_ids` varchar(255) NOT NULL default '',
  `is_active` tinyint(1) NOT NULL default '0',
  `conditions_serialized` text NOT NULL,
  `actions_serialized` text NOT NULL,
  `stop_rules_processing` tinyint(1) NOT NULL default '1',
  `is_advanced` tinyint(3) unsigned NOT NULL default '1',
  `product_ids` text,
  `sort_order` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`rule_id`),
  KEY `sort_order` (`is_active`,`sort_order`,`to_date`,`from_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table if exists `salesrule_product`;
CREATE TABLE `salesrule_product` (
  `rule_product_id` int(10) unsigned NOT NULL auto_increment,
  `rule_id` int(10) unsigned NOT NULL default '0',
  `from_time` int(10) unsigned NOT NULL default '0',
  `to_time` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `customer_group_id` smallint(5) unsigned NOT NULL default '0',
  `product_id` int(10) unsigned NOT NULL default '0',
  `coupon_code` varchar(255) default NULL,
  `sort_order` int(10) unsigned NOT NULL default '0',
  `action_stop` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`rule_product_id`),
  UNIQUE KEY `sort_order` (`from_time`,`to_time`,`store_id`,`customer_group_id`,`product_id`,`coupon_code`,`sort_order`),
  KEY `FK_salesrule_product_rule` (`rule_id`),
  KEY `FK_salesrule_product_store` (`store_id`),
  KEY `FK_salesrule_product_customergroup` (`customer_group_id`),
  CONSTRAINT `FK_salesrule_product_customergroup` FOREIGN KEY (`customer_group_id`) REFERENCES `customer_group` (`customer_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_salesrule_product_rule` FOREIGN KEY (`rule_id`) REFERENCES `salesrule` (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_salesrule_product_store` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table if exists `salesrule_product_action`;
CREATE TABLE `salesrule_product_action` (
  `rule_product_action_id` int(10) unsigned NOT NULL auto_increment,
  `rule_product_id` int(10) unsigned NOT NULL default '0',
  `action_type` varchar(255) NOT NULL default '',
  `action_attribute` varchar(255) NOT NULL default '',
  `action_operator` varchar(255) NOT NULL default '',
  `action_value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`rule_product_action_id`),
  KEY `rule_product_id` (`rule_product_id`),
  CONSTRAINT `salesrule_product_action_ibfk_1` FOREIGN KEY (`rule_product_id`) REFERENCES `salesrule_product` (`rule_product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
