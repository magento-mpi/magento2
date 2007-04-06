alter table `customer` drop column `default_address_id`;

DROP TABLE IF EXISTS `customer_address_type_link`;
DROP TABLE IF EXISTS `customer_address_type_language`;
DROP TABLE IF EXISTS `customer_address_type`;

CREATE TABLE `customer_address_type` (
  `address_type_id` int(10) unsigned NOT NULL auto_increment,
  `address_type_code` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`address_type_id`),
  UNIQUE KEY `address_type_code` (`address_type_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `customer_address_type_language` (
  `address_type_id` int(10) unsigned NOT NULL default '0',
  `language_code` char(2) NOT NULL default '',
  `address_type_name` varchar(255) NOT NULL default '',
  UNIQUE KEY `address_type_id` (`address_type_id`,`language_code`),
  CONSTRAINT `FK_customer_address_type_language` FOREIGN KEY (`address_type_id`) REFERENCES `customer_address_type` (`address_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `customer_address_type_link` (
  `address_type_link_id` int(10) unsigned NOT NULL auto_increment,
  `customer_id` int(10) unsigned NOT NULL default '0',
  `address_id` int(10) unsigned NOT NULL default '0',
  `address_type_id` int(10) unsigned NOT NULL default '0',
  `is_primary` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`address_type_link_id`),
  UNIQUE KEY `customer_address_type` (`customer_id`,`address_id`,`address_type_id`),
  UNIQUE KEY `customer_primary_type` (`customer_id`,`is_primary`,`address_type_id`),
  CONSTRAINT `FK_customer_address_type_link` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_customer_address_type_link1` FOREIGN KEY (`address_id`) REFERENCES `customer_address` (`address_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_customer_address_type_link2` FOREIGN KEY (`address_type_id`) REFERENCES `customer_address_type` (`address_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;