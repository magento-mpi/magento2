<?php

$this->startSetup();

$this->run(<<<EOT

DROP TABLE IF EXISTS `catalogrule`;

CREATE TABLE `catalogrule` (
  `rule_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `from_date` date default NULL,
  `to_date` date default NULL,
  `store_ids` varchar(255) NOT NULL default '',
  `customer_group_ids` varchar(255) NOT NULL default '',
  `is_active` tinyint(1) NOT NULL default '0',
  `conditions_serialized` text NOT NULL,
  `actions_serialized` text NOT NULL,
  `stop_rules_processing` tinyint(1) NOT NULL default '1',
  `sort_order` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`rule_id`),
  KEY `sort_order` (`is_active`,`sort_order`,`to_date`,`from_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalogrule` */

/*Table structure for table `catalogrule_product` */

DROP TABLE IF EXISTS `catalogrule_product`;

CREATE TABLE `catalogrule_product` (
  `rule_product_id` int(10) unsigned NOT NULL auto_increment,
  `rule_id` int(10) unsigned NOT NULL default '0',
  `from_time` int(10) unsigned NOT NULL default '0',
  `to_time` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `customer_group_id` smallint(5) unsigned NOT NULL default '0',
  `product_id` int(10) unsigned NOT NULL default '0',
  `action_operator` enum('to_fixed','to_percent','by_fixed','by_percent') NOT NULL default 'to_fixed',
  `action_amount` decimal(12,4) NOT NULL default '0.0000',
  `action_stop` tinyint(1) NOT NULL default '0',
  `sort_order` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`rule_product_id`),
  UNIQUE KEY `sort_order` (`from_time`,`to_time`,`store_id`,`customer_group_id`,`product_id`,`sort_order`),
  KEY `FK_catalogrule_product_rule` (`rule_id`),
  KEY `FK_catalogrule_product_store` (`store_id`),
  KEY `FK_catalogrule_product_customergroup` (`customer_group_id`),
  CONSTRAINT `FK_catalogrule_product_customergroup` FOREIGN KEY (`customer_group_id`) REFERENCES `customer_group` (`customer_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_catalogrule_product_rule` FOREIGN KEY (`rule_id`) REFERENCES `catalogrule` (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_catalogrule_product_store` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalogrule_product` */

/*Table structure for table `catalogrule_product_price` */

DROP TABLE IF EXISTS `catalogrule_product_price`;

CREATE TABLE `catalogrule_product_price` (
  `rule_product_price_id` int(10) unsigned NOT NULL auto_increment,
  `rule_date` date NOT NULL default '0000-00-00',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `customer_group_id` smallint(5) unsigned NOT NULL default '0',
  `product_id` int(10) unsigned NOT NULL default '0',
  `rule_price` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`rule_product_price_id`),
  UNIQUE KEY `rule_date` (`rule_date`,`store_id`,`customer_group_id`,`product_id`),
  KEY `FK_catalogrule_product_price_store` (`store_id`),
  KEY `FK_catalogrule_product_price_customergroup` (`customer_group_id`),
  CONSTRAINT `FK_catalogrule_product_price_customergroup` FOREIGN KEY (`customer_group_id`) REFERENCES `customer_group` (`customer_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_catalogrule_product_price_store` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

EOT
);

$this->endSetup();