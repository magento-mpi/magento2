/*
SQLyog Enterprise - MySQL GUI v5.13
Host - 4.1.22 : Database - magento_dmitriy
*********************************************************************
Server version : 4.1.22
*/

SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `core_attribute` */

DROP TABLE IF EXISTS `core_attribute`;

CREATE TABLE `core_attribute` (
  `attribute_id` smallint(5) unsigned NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned default NULL,
  `store_id` smallint(5) unsigned default NULL,
  `attribute_code` varchar(64) NOT NULL default '',
  `attribute_type` varchar(32) NOT NULL default '',
  `position` tinyint(3) unsigned NOT NULL default '1',
  `is_required` tinyint(1) unsigned default NULL,
  `is_deletable` tinyint(1) unsigned NOT NULL default '1',
  `is_visible` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`attribute_id`),
  KEY `FK_ATTRIBUTE_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_STORE` (`store_id`),
  CONSTRAINT `FK_ATTRIBUTE_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `core_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Base entity attribute table';

/*Data for the table `core_attribute` */

insert into `core_attribute` (`attribute_id`,`entity_type_id`,`store_id`,`attribute_code`,`attribute_type`,`position`,`is_required`,`is_deletable`,`is_visible`) values (1,1,1,'firstname','varchar',1,1,0,1),(2,1,1,'lastname','varchar',2,1,0,1),(3,1,1,'email','varchar',3,1,0,1),(4,1,1,'password','varchar',4,1,0,1),(5,1,1,'balance','decimal',5,1,0,1),(6,2,1,'firstname','varchar',1,1,0,1),(7,2,1,'lastname','varchar',2,1,0,1),(8,2,1,'postcode','varchar',3,1,0,1),(9,2,1,'street','text',4,1,0,1),(10,2,1,'city','varchar',5,1,0,1),(11,2,1,'region','varchar',6,1,0,1),(12,2,1,'region_id','int',7,0,0,0),(13,2,1,'country_id','int',8,1,0,1),(14,2,1,'company','varchar',9,0,1,1),(15,2,1,'telephone','varchar',10,1,0,1),(16,2,1,'fax','varchar',11,0,1,1);

/*Table structure for table `core_attribute_datetime` */

DROP TABLE IF EXISTS `core_attribute_datetime`;

CREATE TABLE `core_attribute_datetime` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_DATETIME_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_DATETIME_ATTRIBUTE` (`attribute_id`),
  KEY `FK_ATTRIBUTE_DATETIME_STORE` (`store_id`),
  KEY `FK_ATTRIBUTE_DATETIME_ENTITY` (`entity_id`),
  CONSTRAINT `FK_ATTRIBUTE_DATETIME_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `core_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_DATETIME_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `core_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_DATETIME_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_DATETIME_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `core_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Date values of attributes';

/*Data for the table `core_attribute_datetime` */

insert into `core_attribute_datetime` (`value_id`,`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values (1,1,3,1,1,'2007-12-00 00:00:00');

/*Table structure for table `core_attribute_decimal` */

DROP TABLE IF EXISTS `core_attribute_decimal`;

CREATE TABLE `core_attribute_decimal` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_DECIMAL_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_DECIMAL_ATTRIBUTE` (`attribute_id`),
  KEY `FK_ATTRIBUTE_DECIMAL_STORE` (`store_id`),
  KEY `FK_ATTRIBUTE_DECIMAL_ENTITY` (`entity_id`),
  CONSTRAINT `FK_ATTRIBUTE_DECIMAL_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `core_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_DECIMAL_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `core_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_DECIMAL_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_DECIMAL_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `core_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Decimal values of attributes';

/*Data for the table `core_attribute_decimal` */

/*Table structure for table `core_attribute_int` */

DROP TABLE IF EXISTS `core_attribute_int`;

CREATE TABLE `core_attribute_int` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_INT_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_INT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_ATTRIBUTE_INT_STORE` (`store_id`),
  KEY `FK_ATTRIBUTE_INT_ENTITY` (`entity_id`),
  CONSTRAINT `FK_ATTRIBUTE_INT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `core_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_INT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `core_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_INT_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_INT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `core_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Integer values of attributes';

/*Data for the table `core_attribute_int` */

/*Table structure for table `core_attribute_text` */

DROP TABLE IF EXISTS `core_attribute_text`;

CREATE TABLE `core_attribute_text` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_TEXT_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_TEXT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_ATTRIBUTE_TEXT_STORE` (`store_id`),
  KEY `FK_ATTRIBUTE_TEXT_ENTITY` (`entity_id`),
  CONSTRAINT `FK_ATTRIBUTE_TEXT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `core_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_TEXT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `core_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_TEXT_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_TEXT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `core_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Text values of attributes';

/*Data for the table `core_attribute_text` */

/*Table structure for table `core_attribute_varchar` */

DROP TABLE IF EXISTS `core_attribute_varchar`;

CREATE TABLE `core_attribute_varchar` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_VARCHAR_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_VARCHAR_ATTRIBUTE` (`attribute_id`),
  KEY `FK_ATTRIBUTE_VARCHAR_STORE` (`store_id`),
  KEY `FK_ATTRIBUTE_VARCHAR_ENTITY` (`entity_id`),
  CONSTRAINT `FK_ATTRIBUTE_VARCHAR_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `core_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_VARCHAR_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `core_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_VARCHAR_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_VARCHAR_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `core_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Varchar values of attributes';

/*Data for the table `core_attribute_varchar` */

insert into `core_attribute_varchar` (`value_id`,`entity_type_id`,`attribute_id`,`store_id`,`entity_id`,`value`) values (1,1,1,1,1,'dmitriy'),(2,1,2,1,1,'soroka');

/*Table structure for table `core_entity` */

DROP TABLE IF EXISTS `core_entity`;

CREATE TABLE `core_entity` (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `is_active` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`entity_id`),
  KEY `FK_ENTITY_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ENTITY_STORE` (`store_id`),
  CONSTRAINT `FK_ENTITY_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `core_entity_type` (`entity_type_id`),
  CONSTRAINT `FK_ENTITY_STORE` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Entityies';

/*Data for the table `core_entity` */

insert into `core_entity` (`entity_id`,`entity_type_id`,`store_id`,`created_at`,`updated_at`,`is_active`) values (1,1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00',1);

/*Table structure for table `core_entity_type` */

DROP TABLE IF EXISTS `core_entity_type`;

CREATE TABLE `core_entity_type` (
  `entity_type_id` mediumint(8) unsigned NOT NULL auto_increment,
  `parent_id` mediumint(8) unsigned default NULL,
  `left_id` mediumint(8) unsigned NOT NULL default '0',
  `right_id` mediumint(8) unsigned NOT NULL default '0',
  `level` tinyint(3) unsigned NOT NULL default '0',
  `entity_code` varchar(64) NOT NULL default '',
  `entity_table` varchar(64) NOT NULL default '',
  `attribute_table` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`entity_type_id`),
  KEY `FK_ENTITY_TYPE_PARENT_ENTITY_TYPE` (`parent_id`),
  CONSTRAINT `FK_ENTITY_TYPE_PARENT_ENTITY_TYPE` FOREIGN KEY (`parent_id`) REFERENCES `core_entity_type` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='All system entity types';

/*Data for the table `core_entity_type` */

insert into `core_entity_type` (`entity_type_id`,`parent_id`,`left_id`,`right_id`,`level`,`entity_code`,`entity_table`,`attribute_table`) values (1,NULL,0,0,0,'customer','core/entity','core/attribute'),(2,NULL,0,0,0,'address','core/entity','core/attribute');

/*Table structure for table `core_language` */

DROP TABLE IF EXISTS `core_language`;

CREATE TABLE `core_language` (
  `language_code` varchar(2) NOT NULL default '',
  `language_title` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Languages';

/*Data for the table `core_language` */

insert into `core_language` (`language_code`,`language_title`) values ('en','English'),('ru','Russian');

/*Table structure for table `core_resource` */

DROP TABLE IF EXISTS `core_resource`;

CREATE TABLE `core_resource` (
  `code` varchar(50) NOT NULL default '',
  `version` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Resource version registry';

/*Data for the table `core_resource` */



/*Table structure for table `core_session` */

DROP TABLE IF EXISTS `core_session`;

CREATE TABLE `core_session` (
  `session_id` varchar(255) NOT NULL default '',
  `website_id` smallint(5) unsigned default NULL,
  `session_expires` int(10) unsigned NOT NULL default '0',
  `session_data` text NOT NULL,
  PRIMARY KEY  (`session_id`),
  KEY `FK_SESSION_WEBSITE` (`website_id`),
  CONSTRAINT `FK_SESSION_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Session data store';

/*Data for the table `core_session` */

insert into `core_session` (`session_id`,`website_id`,`session_expires`,`session_data`) values ('bf1da2a2eebd6a792517380202d87ca4',NULL,1182732292,'core|a:1:{s:4:\"data\";O:13:\"Varien_Object\":4:{s:8:\"\0*\0_data\";a:0:{}s:13:\"\0*\0_isChanged\";b:0;s:13:\"\0*\0_isDeleted\";b:0;s:15:\"\0*\0_idFieldName\";N;}}install|a:2:{s:8:\"messages\";O:34:\"Mage_Core_Model_Message_Collection\":1:{s:12:\"\0*\0_messages\";a:0:{}}s:4:\"data\";O:13:\"Varien_Object\":4:{s:8:\"\0*\0_data\";a:0:{}s:13:\"\0*\0_isChanged\";b:0;s:13:\"\0*\0_isDeleted\";b:0;s:15:\"\0*\0_idFieldName\";N;}}admin|a:1:{s:4:\"data\";O:13:\"Varien_Object\":4:{s:8:\"\0*\0_data\";a:2:{s:4:\"user\";O:21:\"Mage_Admin_Model_User\":4:{s:8:\"\0*\0_data\";a:11:{s:7:\"user_id\";s:1:\"1\";s:9:\"firstname\";s:7:\"dmitriy\";s:8:\"lastname\";s:6:\"soroka\";s:5:\"email\";s:18:\"dmitriy@varien.com\";s:8:\"username\";s:7:\"dmitriy\";s:8:\"password\";s:32:\"31d6f9170ca43c9e8f6df9fa206cd8f6\";s:7:\"created\";s:19:\"2007-06-24 14:50:22\";s:8:\"modified\";s:19:\"2007-06-24 14:50:22\";s:7:\"logdate\";s:19:\"2007-06-24 14:50:26\";s:6:\"lognum\";s:1:\"1\";s:15:\"reload_acl_flag\";s:1:\"0\";}s:13:\"\0*\0_isChanged\";b:0;s:13:\"\0*\0_isDeleted\";b:0;s:15:\"\0*\0_idFieldName\";N;}s:3:\"acl\";O:20:\"Mage_Admin_Model_Acl\":3:{s:16:\"\0*\0_roleRegistry\";O:34:\"Mage_Admin_Model_Acl_Role_Registry\":1:{s:9:\"\0*\0_roles\";a:7:{s:2:\"G1\";a:3:{s:8:\"instance\";O:31:\"Mage_Admin_Model_Acl_Role_Group\":1:{s:10:\"\0*\0_roleId\";s:2:\"G1\";}s:7:\"parents\";a:0:{}s:8:\"children\";a:4:{s:2:\"U1\";O:30:\"Mage_Admin_Model_Acl_Role_User\":1:{s:10:\"\0*\0_roleId\";s:2:\"U1\";}s:2:\"U2\";O:30:\"Mage_Admin_Model_Acl_Role_User\":1:{s:10:\"\0*\0_roleId\";s:2:\"U2\";}s:2:\"U3\";O:30:\"Mage_Admin_Model_Acl_Role_User\":1:{s:10:\"\0*\0_roleId\";s:2:\"U3\";}s:2:\"U5\";O:30:\"Mage_Admin_Model_Acl_Role_User\":1:{s:10:\"\0*\0_roleId\";s:2:\"U5\";}}}s:2:\"G2\";a:3:{s:8:\"instance\";O:31:\"Mage_Admin_Model_Acl_Role_Group\":1:{s:10:\"\0*\0_roleId\";s:2:\"G2\";}s:7:\"parents\";a:0:{}s:8:\"children\";a:0:{}}s:2:\"G3\";a:3:{s:8:\"instance\";O:31:\"Mage_Admin_Model_Acl_Role_Group\":1:{s:10:\"\0*\0_roleId\";s:2:\"G3\";}s:7:\"parents\";a:0:{}s:8:\"children\";a:0:{}}s:2:\"U1\";a:3:{s:8:\"instance\";r:42;s:7:\"parents\";a:1:{s:2:\"G1\";r:38;}s:8:\"children\";a:0:{}}s:2:\"U2\";a:3:{s:8:\"instance\";r:44;s:7:\"parents\";a:1:{s:2:\"G1\";r:38;}s:8:\"children\";a:0:{}}s:2:\"U3\";a:3:{s:8:\"instance\";r:46;s:7:\"parents\";a:1:{s:2:\"G1\";r:38;}s:8:\"children\";a:0:{}}s:2:\"U5\";a:3:{s:8:\"instance\";r:48;s:7:\"parents\";a:1:{s:2:\"G1\";r:38;}s:8:\"children\";a:0:{}}}}s:13:\"\0*\0_resources\";a:20:{s:5:\"admin\";a:3:{s:8:\"instance\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:5:\"admin\";}s:6:\"parent\";N;s:8:\"children\";a:8:{s:15:\"admin/dashboard\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:15:\"admin/dashboard\";}s:14:\"admin/customer\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:14:\"admin/customer\";}s:21:\"admin/customer_online\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:21:\"admin/customer_online\";}s:21:\"admin/customer_config\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:21:\"admin/customer_config\";}s:13:\"admin/catalog\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:13:\"admin/catalog\";}s:13:\"admin/paygate\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:13:\"admin/paygate\";}s:11:\"admin/sales\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:11:\"admin/sales\";}s:12:\"admin/system\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:12:\"admin/system\";}}}s:15:\"admin/dashboard\";a:3:{s:8:\"instance\";r:86;s:6:\"parent\";r:82;s:8:\"children\";a:0:{}}s:14:\"admin/customer\";a:3:{s:8:\"instance\";r:88;s:6:\"parent\";r:82;s:8:\"children\";a:0:{}}s:21:\"admin/customer_online\";a:3:{s:8:\"instance\";r:90;s:6:\"parent\";r:82;s:8:\"children\";a:0:{}}s:21:\"admin/customer_config\";a:3:{s:8:\"instance\";r:92;s:6:\"parent\";r:82;s:8:\"children\";a:0:{}}s:13:\"admin/catalog\";a:3:{s:8:\"instance\";r:94;s:6:\"parent\";r:82;s:8:\"children\";a:4:{s:20:\"admin/catalog/manage\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:20:\"admin/catalog/manage\";}s:32:\"admin/catalog/product_attributes\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:32:\"admin/catalog/product_attributes\";}s:23:\"admin/catalog/datafeeds\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:23:\"admin/catalog/datafeeds\";}s:20:\"admin/catalog/config\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:20:\"admin/catalog/config\";}}}s:20:\"admin/catalog/manage\";a:3:{s:8:\"instance\";r:122;s:6:\"parent\";r:94;s:8:\"children\";a:0:{}}s:32:\"admin/catalog/product_attributes\";a:3:{s:8:\"instance\";r:124;s:6:\"parent\";r:94;s:8:\"children\";a:0:{}}s:23:\"admin/catalog/datafeeds\";a:3:{s:8:\"instance\";r:126;s:6:\"parent\";r:94;s:8:\"children\";a:0:{}}s:20:\"admin/catalog/config\";a:3:{s:8:\"instance\";r:128;s:6:\"parent\";r:94;s:8:\"children\";a:0:{}}s:13:\"admin/paygate\";a:3:{s:8:\"instance\";r:96;s:6:\"parent\";r:82;s:8:\"children\";a:3:{s:26:\"admin/paygate/authorizenet\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:26:\"admin/paygate/authorizenet\";}s:20:\"admin/paygate/paypal\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:20:\"admin/paygate/paypal\";}s:22:\"admin/paygate/verisign\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:22:\"admin/paygate/verisign\";}}}s:26:\"admin/paygate/authorizenet\";a:3:{s:8:\"instance\";r:150;s:6:\"parent\";r:96;s:8:\"children\";a:0:{}}s:20:\"admin/paygate/paypal\";a:3:{s:8:\"instance\";r:152;s:6:\"parent\";r:96;s:8:\"children\";a:0:{}}s:22:\"admin/paygate/verisign\";a:3:{s:8:\"instance\";r:154;s:6:\"parent\";r:96;s:8:\"children\";a:0:{}}s:11:\"admin/sales\";a:3:{s:8:\"instance\";r:98;s:6:\"parent\";r:82;s:8:\"children\";a:1:{s:18:\"admin/sales/orders\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:18:\"admin/sales/orders\";}}}s:18:\"admin/sales/orders\";a:3:{s:8:\"instance\";r:172;s:6:\"parent\";r:98;s:8:\"children\";a:0:{}}s:12:\"admin/system\";a:3:{s:8:\"instance\";r:100;s:6:\"parent\";r:82;s:8:\"children\";a:3:{s:24:\"admin/system/permissions\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:24:\"admin/system/permissions\";}s:20:\"admin/system/modules\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:20:\"admin/system/modules\";}s:19:\"admin/system/stores\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:19:\"admin/system/stores\";}}}s:24:\"admin/system/permissions\";a:3:{s:8:\"instance\";r:182;s:6:\"parent\";r:100;s:8:\"children\";a:0:{}}s:20:\"admin/system/modules\";a:3:{s:8:\"instance\";r:184;s:6:\"parent\";r:100;s:8:\"children\";a:0:{}}s:19:\"admin/system/stores\";a:3:{s:8:\"instance\";r:186;s:6:\"parent\";r:100;s:8:\"children\";a:0:{}}}s:9:\"\0*\0_rules\";a:2:{s:12:\"allResources\";a:2:{s:8:\"allRoles\";a:2:{s:13:\"allPrivileges\";a:2:{s:4:\"type\";s:9:\"TYPE_DENY\";s:6:\"assert\";N;}s:13:\"byPrivilegeId\";a:0:{}}s:8:\"byRoleId\";a:0:{}}s:12:\"byResourceId\";a:3:{s:5:\"admin\";a:1:{s:8:\"byRoleId\";a:1:{s:2:\"G1\";a:2:{s:13:\"byPrivilegeId\";a:0:{}s:13:\"allPrivileges\";a:2:{s:4:\"type\";s:10:\"TYPE_ALLOW\";s:6:\"assert\";N;}}}}s:13:\"admin/catalog\";a:1:{s:8:\"byRoleId\";a:1:{s:2:\"U1\";a:1:{s:13:\"byPrivilegeId\";a:2:{s:6:\"create\";a:2:{s:4:\"type\";s:10:\"TYPE_ALLOW\";s:6:\"assert\";N;}s:6:\"delete\";a:2:{s:4:\"type\";s:10:\"TYPE_ALLOW\";s:6:\"assert\";N;}}}}}s:19:\"admin/system/stores\";a:1:{s:8:\"byRoleId\";a:1:{s:2:\"U2\";a:1:{s:13:\"byPrivilegeId\";a:1:{s:6:\"delete\";a:2:{s:4:\"type\";s:9:\"TYPE_DENY\";s:6:\"assert\";N;}}}}}}}}}s:13:\"\0*\0_isChanged\";b:0;s:13:\"\0*\0_isDeleted\";b:0;s:15:\"\0*\0_idFieldName\";N;}}');

/*Table structure for table `core_store` */

DROP TABLE IF EXISTS `core_store`;

CREATE TABLE `core_store` (
  `store_id` smallint(5) unsigned NOT NULL auto_increment,
  `language_code` varchar(2) default NULL,
  `website_id` smallint(5) unsigned default '0',
  `name` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`store_id`),
  KEY `FK_STORE_LANGUAGE` (`language_code`),
  KEY `FK_STORE_WEBSITE` (`website_id`),
  CONSTRAINT `FK_STORE_LANGUAGE` FOREIGN KEY (`language_code`) REFERENCES `core_language` (`language_code`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `FK_STORE_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores';

/*Data for the table `core_store` */

insert into `core_store` (`store_id`,`language_code`,`website_id`,`name`) values (1,'en',1,'english'),(2,'ru',1,'russian');

/*Table structure for table `core_website` */

DROP TABLE IF EXISTS `core_website`;

CREATE TABLE `core_website` (
  `website_id` smallint(5) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Websites';

/*Data for the table `core_website` */

insert into `core_website` (`website_id`,`code`) values (1,'base');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
