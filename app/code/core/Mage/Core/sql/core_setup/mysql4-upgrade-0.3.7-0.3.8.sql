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

/*Table structure for table `core_config_data` */

DROP TABLE IF EXISTS `core_config_data`;

CREATE TABLE `core_config_data` (
  `config_id` int(10) unsigned NOT NULL auto_increment,
  `scope` enum('default','website','store','config') NOT NULL default 'default',
  `scope_id` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default 'general',
  `data` varchar(255) NOT NULL default '',
  `inherit` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`config_id`),
  UNIQUE KEY `config_scope` (`scope`,`scope_id`,`path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `core_config_data` */

insert into `core_config_data` (`config_id`,`scope`,`scope_id`,`path`,`data`,`inherit`) values (1,'default',0,'general/currency/base','USD',0),(2,'default',0,'general/currency/allow','USD,CAD,UAH,RUB',0),(3,'default',0,'general/currency/default','USD',0),(4,'default',0,'general/local/language','en',0),(6,'default',0,'system/filesystem/layout','{{root_dir}}/app/view/layout',0),(7,'default',0,'system/filesystem/template','{{root_dir}}/app/view/template',0),(8,'default',0,'system/filesystem/translate','{{root_dir}}/app/view/translate',0),(9,'default',0,'system/filesystem/base','{{root_dir}}',0),(10,'default',0,'system/filesystem/media','{{root_dir}}/media',0),(11,'default',0,'system/filesystem/skin','{{root_dir}}/skins/default',0),(12,'default',0,'web/unsecure/protocol','{{protocol}}',0),(13,'default',0,'web/unsecure/host','{{host}}',0),(14,'default',0,'web/unsecure/port','{{port}}',0),(15,'default',0,'web/unsecure/base_path','{{base_path}}',0),(16,'default',0,'web/secure/protocol','{{protocol}}',0),(17,'default',0,'web/secure/host','{{host}}',0),(18,'default',0,'web/secure/port','{{port}}',0),(19,'default',0,'web/secure/base_path','{{base_path}}',0);
insert into `core_config_data` (`config_id`,`scope`,`scope_id`,`path`,`data`,`inherit`) values (20,'default',0,'web/url/media','{{base_path}}media/',0),(21,'default',0,'web/url/skin','{{base_path}}skins/default/',0),(22,'default',0,'web/url/js','{{base_path}}js/',0),(23,'default',0,'system/filesystem/etc','{{root_dir}}/app/etc/',0),(24,'default',0,'system/filesystem/code','{{root_dir}}/app/code/',0),(25,'default',0,'system/filesystem/upload','{{root_dir}}/media/upload/',0),(26,'default',0,'system/filesystem/var','{{var_dir}}',0),(27,'default',0,'system/filesystem/session','{{var_dir}}/session/',0),(28,'default',0,'system/filesystem/cache_config','{{var_dir}}/cache/config/',0),(29,'default',0,'system/filesystem/cache_layout','{{var_dir}}/cache/layout/',0),(32,'default',0,'web/default/front','catalog',0),(34,'default',0,'web/default/no_route','core/index/noRoute',0),(35,'default',0,'general/country/default','US',0),(36,'default',0,'general/country/allow','US,CA,UA',0),(39,'default',0,'advanced/datashare/customer','1',0),(40,'default',0,'advanced/datashare/customer_address','1',0),(41,'default',0,'advanced/datashare/quote','1',0),(42,'default',0,'advanced/datashare/quote_address','1',0),(43,'default',0,'advanced/datashare/order','1',0),(44,'default',0,'advanced/datashare/order_address','1',0),(45,'default',0,'advanced/datashare/order_payment','1',0);
insert into `core_config_data` (`config_id`,`scope`,`scope_id`,`path`,`data`,`inherit`) values (46,'default',0,'advanced/datashare/wishlist','1',0),(47,'default',0,'general/local/date_format_short','%m/%d/%y',0),(48,'default',0,'general/local/date_format_medium','%a, %b %e %Y',0),(49,'default',0,'general/local/date_format_long',' %A, %B %e %Y',0),(50,'default',0,'general/local/datetime_format_short','%m/%d/%y [%I:%M %p]',0),(51,'default',0,'general/local/datetime_format_medium','%a, %b %e %Y [%I:%M %p]',0),(52,'default',0,'general/local/datetime_format_long','%A, %B %e %Y [%I:%M %p]',0);

/*Table structure for table `core_config_field` */

DROP TABLE IF EXISTS `core_config_field`;

CREATE TABLE `core_config_field` (
  `field_id` int(10) unsigned NOT NULL auto_increment,
  `path` varchar(255) NOT NULL default '',
  `frontend_label` varchar(255) NOT NULL default '',
  `frontend_type` varchar(64) NOT NULL default 'text',
  `frontend_class` varchar(255) NOT NULL default '',
  `frontend_model` varchar(255) NOT NULL default '',
  `backend_model` varchar(255) NOT NULL default '',
  `source_model` varchar(255) NOT NULL default '',
  `sort_order` smallint(5) unsigned NOT NULL default '0',
  `show_in_default` tinyint(4) NOT NULL default '1',
  `show_in_website` tinyint(4) NOT NULL default '1',
  `show_in_store` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`field_id`),
  KEY `path` (`path`,`sort_order`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=latin1;

/*Data for the table `core_config_field` */

insert into `core_config_field` (`field_id`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`) values (1,'advanced/datashare/customer','Customer','text','','','','',0,1,1,1),(2,'advanced/datashare/customer_address','Customer Address','text','','','','',0,1,1,1),(3,'advanced/datashare/order','Order','text','','','','',0,1,1,1),(4,'advanced/datashare/order_address','Order Address','text','','','','',0,1,1,1),(5,'advanced/datashare/order_payment','Order Payment','text','','','','',0,1,1,1),(6,'advanced/datashare/quote','Shopping cart / Quote','text','','','','',0,1,1,1),(7,'advanced/datashare/quote_address','Shopping cart / Quote Address','text','','','','',0,1,1,1),(8,'advanced/datashare/wishlist','Wishlist','text','','','','',0,1,1,1),(9,'general/country/allow','Allow countries','text','','','','',0,1,1,1),(10,'general/country/default','Default country','text','','','','',0,1,1,1),(11,'general/currency/allow','Allow currencies','text','','','','',0,1,1,1),(12,'general/currency/base','Base currency','text','','','','',0,1,1,1),(13,'general/currency/default','Default currency','text','','','','',0,1,1,1),(14,'general/local/date_format_mysql','Date format (db/deprecated)','text','','','','',0,1,1,1),(15,'general/local/date_format_php','Date format','text','','','','',0,1,1,1),(16,'general/local/language','Language','text','','','','',0,1,1,1),(17,'system/filesystem/base','Base directory','text','','','','',0,1,1,1),(18,'system/filesystem/cache_config','Config cache directory','text','','','','',0,1,1,1);
insert into `core_config_field` (`field_id`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`) values (19,'system/filesystem/cache_layout','Layout cache directory','text','','','','',0,1,1,1),(20,'system/filesystem/code','Code pools root directory','text','','','','',0,1,1,1),(21,'system/filesystem/etc','Configuration directory','text','','','','',0,1,1,1),(22,'system/filesystem/layout','Layout files directory','text','','','','',0,1,1,1),(23,'system/filesystem/media','Media files directory','text','','','','',0,1,1,1),(24,'system/filesystem/session','Session files directory','text','','','','',0,1,1,1),(25,'system/filesystem/skin','Skin directory','text','','','','',0,1,1,1),(26,'system/filesystem/template','Template directory','text','','','','',0,1,1,1),(27,'system/filesystem/translate','Translactions directory','text','','','','',0,1,1,1),(28,'system/filesystem/upload','Upload directory','text','','','','',0,1,1,1),(29,'system/filesystem/var','Var (temporary files) directory','text','','','','',0,1,1,1),(30,'web/default/front','Default web url','text','','','','',0,1,1,1),(31,'web/default/no_route','Default no-route url','text','','','','',0,1,1,1),(32,'web/secure/base_path','Base url','text','','','','',0,1,1,1),(33,'web/secure/host','Host','text','','','','',0,1,1,1),(34,'web/secure/port','Port','text','','','','',0,1,1,1),(35,'web/secure/protocol','Protocol','text','','','','',0,1,1,1),(36,'web/unsecure/base_path','Base url','text','','','','',0,1,1,1),(37,'web/unsecure/host','Host','text','','','','',0,1,1,1),(38,'web/unsecure/port','Port','text','','','','',0,1,1,1),(39,'web/unsecure/protocol','Protocol','text','','','','',0,1,1,1);
insert into `core_config_field` (`field_id`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`) values (40,'web/url/js','Js base url','text','','','','',0,1,1,1),(41,'web/url/media','Media base url','text','','','','',0,1,1,1),(42,'web/url/skin','Skin base url','text','','','','',0,1,1,1),(43,'general','General','text','','','','',0,1,1,1),(44,'web','Web','text','','','','',0,1,1,1),(45,'system','System','text','','','','',0,1,1,1),(46,'advanced','Advanced','text','','','','',0,1,1,1),(47,'web/default','Default','text','','','','',0,1,1,1),(48,'web/secure','Secure','text','','','','',0,1,1,1),(49,'web/unsecure','Unsecure','text','','','','',0,1,1,1),(50,'web/url','URLs','text','','','','',0,1,1,1),(51,'system/filesystem','Filesystem','text','','','','',0,1,1,1),(52,'general/currency','Currency options','text','','','','',0,1,1,1),(53,'general/local','Local options','text','','','','',0,1,1,1),(54,'general/country','Countries options','text','','','','',0,1,1,1),(55,'advanced/datashare','Datasharing','text','','','','',0,1,1,1);

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

insert into `core_resource` (`code`,`version`) values ('admin_setup','0.2.3'),('catalog_setup','0.3.0'),('cms_setup','0.1.2'),('core_setup','0.3.7'),('cron_setup','0.2.0'),('customer_setup','0.2.5'),('directory_setup','0.2.2'),('eav_setup','0.1.3'),('log_setup','0.1.6'),('newsletter_setup','0.1.5'),('permissions_setup','0.0.2'),('poll_setup','0.2.0'),('rating_setup','0.1.3'),('review_setup','0.1.1'),('sales_setup','0.2.2'),('shiptable_setup','0.2.0'),('tag_setup','0.0.5'),('tax_setup','0.1.2'),('usa_setup','0.2.0'),('wishlist_setup','0.1.0');

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

insert into `core_session` (`session_id`,`website_id`,`session_expires`,`session_data`) values ('2c676af1c281e0d1244212f4db234857',NULL,1184817491,'core|a:1:{s:4:\"data\";O:13:\"Varien_Object\":2:{s:8:\"\0*\0_data\";a:1:{s:14:\"log_visitor_id\";s:2:\"33\";}s:15:\"\0*\0_idFieldName\";N;}}admin|a:1:{s:4:\"data\";O:13:\"Varien_Object\":2:{s:8:\"\0*\0_data\";a:2:{s:4:\"user\";O:21:\"Mage_Admin_Model_User\":2:{s:8:\"\0*\0_data\";a:11:{s:7:\"user_id\";s:1:\"1\";s:9:\"firstname\";s:7:\"dmitriy\";s:8:\"lastname\";s:6:\"soroka\";s:5:\"email\";s:18:\"dmitriy@varien.com\";s:8:\"username\";s:7:\"dmitriy\";s:8:\"password\";s:32:\"31d6f9170ca43c9e8f6df9fa206cd8f6\";s:7:\"created\";s:19:\"2007-06-25 14:53:12\";s:8:\"modified\";s:19:\"2007-06-25 14:53:12\";s:7:\"logdate\";s:19:\"2007-07-18 11:14:41\";s:6:\"lognum\";s:2:\"48\";s:15:\"reload_acl_flag\";s:1:\"0\";}s:15:\"\0*\0_idFieldName\";N;}s:3:\"acl\";O:20:\"Mage_Admin_Model_Acl\":3:{s:16:\"\0*\0_roleRegistry\";O:34:\"Mage_Admin_Model_Acl_Role_Registry\":1:{s:9:\"\0*\0_roles\";a:7:{s:2:\"G1\";a:3:{s:8:\"instance\";O:31:\"Mage_Admin_Model_Acl_Role_Group\":1:{s:10:\"\0*\0_roleId\";s:2:\"G1\";}s:7:\"parents\";a:0:{}s:8:\"children\";a:4:{s:2:\"U1\";O:30:\"Mage_Admin_Model_Acl_Role_User\":1:{s:10:\"\0*\0_roleId\";s:2:\"U1\";}s:2:\"U2\";O:30:\"Mage_Admin_Model_Acl_Role_User\":1:{s:10:\"\0*\0_roleId\";s:2:\"U2\";}s:2:\"U3\";O:30:\"Mage_Admin_Model_Acl_Role_User\":1:{s:10:\"\0*\0_roleId\";s:2:\"U3\";}s:2:\"U5\";O:30:\"Mage_Admin_Model_Acl_Role_User\":1:{s:10:\"\0*\0_roleId\";s:2:\"U5\";}}}s:2:\"G2\";a:3:{s:8:\"instance\";O:31:\"Mage_Admin_Model_Acl_Role_Group\":1:{s:10:\"\0*\0_roleId\";s:2:\"G2\";}s:7:\"parents\";a:0:{}s:8:\"children\";a:0:{}}s:2:\"G3\";a:3:{s:8:\"instance\";O:31:\"Mage_Admin_Model_Acl_Role_Group\":1:{s:10:\"\0*\0_roleId\";s:2:\"G3\";}s:7:\"parents\";a:0:{}s:8:\"children\";a:0:{}}s:2:\"U1\";a:3:{s:8:\"instance\";r:31;s:7:\"parents\";a:1:{s:2:\"G1\";r:27;}s:8:\"children\";a:0:{}}s:2:\"U2\";a:3:{s:8:\"instance\";r:33;s:7:\"parents\";a:1:{s:2:\"G1\";r:27;}s:8:\"children\";a:0:{}}s:2:\"U3\";a:3:{s:8:\"instance\";r:35;s:7:\"parents\";a:1:{s:2:\"G1\";r:27;}s:8:\"children\";a:0:{}}s:2:\"U5\";a:3:{s:8:\"instance\";r:37;s:7:\"parents\";a:1:{s:2:\"G1\";r:27;}s:8:\"children\";a:0:{}}}}s:13:\"\0*\0_resources\";a:20:{s:5:\"admin\";a:3:{s:8:\"instance\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:5:\"admin\";}s:6:\"parent\";N;s:8:\"children\";a:8:{s:15:\"admin/dashboard\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:15:\"admin/dashboard\";}s:14:\"admin/customer\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:14:\"admin/customer\";}s:21:\"admin/customer_online\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:21:\"admin/customer_online\";}s:21:\"admin/customer_config\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:21:\"admin/customer_config\";}s:13:\"admin/catalog\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:13:\"admin/catalog\";}s:13:\"admin/paygate\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:13:\"admin/paygate\";}s:11:\"admin/sales\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:11:\"admin/sales\";}s:12:\"admin/system\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:12:\"admin/system\";}}}s:15:\"admin/dashboard\";a:3:{s:8:\"instance\";r:75;s:6:\"parent\";r:71;s:8:\"children\";a:0:{}}s:14:\"admin/customer\";a:3:{s:8:\"instance\";r:77;s:6:\"parent\";r:71;s:8:\"children\";a:0:{}}s:21:\"admin/customer_online\";a:3:{s:8:\"instance\";r:79;s:6:\"parent\";r:71;s:8:\"children\";a:0:{}}s:21:\"admin/customer_config\";a:3:{s:8:\"instance\";r:81;s:6:\"parent\";r:71;s:8:\"children\";a:0:{}}s:13:\"admin/catalog\";a:3:{s:8:\"instance\";r:83;s:6:\"parent\";r:71;s:8:\"children\";a:4:{s:20:\"admin/catalog/manage\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:20:\"admin/catalog/manage\";}s:32:\"admin/catalog/product_attributes\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:32:\"admin/catalog/product_attributes\";}s:23:\"admin/catalog/datafeeds\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:23:\"admin/catalog/datafeeds\";}s:20:\"admin/catalog/config\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:20:\"admin/catalog/config\";}}}s:20:\"admin/catalog/manage\";a:3:{s:8:\"instance\";r:111;s:6:\"parent\";r:83;s:8:\"children\";a:0:{}}s:32:\"admin/catalog/product_attributes\";a:3:{s:8:\"instance\";r:113;s:6:\"parent\";r:83;s:8:\"children\";a:0:{}}s:23:\"admin/catalog/datafeeds\";a:3:{s:8:\"instance\";r:115;s:6:\"parent\";r:83;s:8:\"children\";a:0:{}}s:20:\"admin/catalog/config\";a:3:{s:8:\"instance\";r:117;s:6:\"parent\";r:83;s:8:\"children\";a:0:{}}s:13:\"admin/paygate\";a:3:{s:8:\"instance\";r:85;s:6:\"parent\";r:71;s:8:\"children\";a:3:{s:26:\"admin/paygate/authorizenet\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:26:\"admin/paygate/authorizenet\";}s:20:\"admin/paygate/paypal\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:20:\"admin/paygate/paypal\";}s:22:\"admin/paygate/verisign\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:22:\"admin/paygate/verisign\";}}}s:26:\"admin/paygate/authorizenet\";a:3:{s:8:\"instance\";r:139;s:6:\"parent\";r:85;s:8:\"children\";a:0:{}}s:20:\"admin/paygate/paypal\";a:3:{s:8:\"instance\";r:141;s:6:\"parent\";r:85;s:8:\"children\";a:0:{}}s:22:\"admin/paygate/verisign\";a:3:{s:8:\"instance\";r:143;s:6:\"parent\";r:85;s:8:\"children\";a:0:{}}s:11:\"admin/sales\";a:3:{s:8:\"instance\";r:87;s:6:\"parent\";r:71;s:8:\"children\";a:1:{s:18:\"admin/sales/orders\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:18:\"admin/sales/orders\";}}}s:18:\"admin/sales/orders\";a:3:{s:8:\"instance\";r:161;s:6:\"parent\";r:87;s:8:\"children\";a:0:{}}s:12:\"admin/system\";a:3:{s:8:\"instance\";r:89;s:6:\"parent\";r:71;s:8:\"children\";a:3:{s:24:\"admin/system/permissions\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:24:\"admin/system/permissions\";}s:20:\"admin/system/modules\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:20:\"admin/system/modules\";}s:19:\"admin/system/stores\";O:29:\"Mage_Admin_Model_Acl_Resource\":1:{s:14:\"\0*\0_resourceId\";s:19:\"admin/system/stores\";}}}s:24:\"admin/system/permissions\";a:3:{s:8:\"instance\";r:171;s:6:\"parent\";r:89;s:8:\"children\";a:0:{}}s:20:\"admin/system/modules\";a:3:{s:8:\"instance\";r:173;s:6:\"parent\";r:89;s:8:\"children\";a:0:{}}s:19:\"admin/system/stores\";a:3:{s:8:\"instance\";r:175;s:6:\"parent\";r:89;s:8:\"children\";a:0:{}}}s:9:\"\0*\0_rules\";a:2:{s:12:\"allResources\";a:2:{s:8:\"allRoles\";a:2:{s:13:\"allPrivileges\";a:2:{s:4:\"type\";s:9:\"TYPE_DENY\";s:6:\"assert\";N;}s:13:\"byPrivilegeId\";a:0:{}}s:8:\"byRoleId\";a:0:{}}s:12:\"byResourceId\";a:3:{s:5:\"admin\";a:1:{s:8:\"byRoleId\";a:1:{s:2:\"G1\";a:2:{s:13:\"byPrivilegeId\";a:0:{}s:13:\"allPrivileges\";a:2:{s:4:\"type\";s:10:\"TYPE_ALLOW\";s:6:\"assert\";N;}}}}s:13:\"admin/catalog\";a:1:{s:8:\"byRoleId\";a:1:{s:2:\"U1\";a:1:{s:13:\"byPrivilegeId\";a:2:{s:6:\"create\";a:2:{s:4:\"type\";s:10:\"TYPE_ALLOW\";s:6:\"assert\";N;}s:6:\"delete\";a:2:{s:4:\"type\";s:10:\"TYPE_ALLOW\";s:6:\"assert\";N;}}}}}s:19:\"admin/system/stores\";a:1:{s:8:\"byRoleId\";a:1:{s:2:\"U2\";a:1:{s:13:\"byPrivilegeId\";a:1:{s:6:\"delete\";a:2:{s:4:\"type\";s:9:\"TYPE_DENY\";s:6:\"assert\";N;}}}}}}}}}s:15:\"\0*\0_idFieldName\";N;}}adminhtml|a:2:{s:8:\"messages\";O:34:\"Mage_Core_Model_Message_Collection\":1:{s:12:\"\0*\0_messages\";a:0:{}}s:4:\"data\";O:13:\"Varien_Object\":2:{s:8:\"\0*\0_data\";a:3:{s:16:\"customerGridsort\";s:2:\"id\";s:15:\"customerGriddir\";s:3:\"asc\";s:18:\"customerGridfilter\";s:100:\"Y3VzdG9tZXJfc2luY2UlNUJmcm9tJTVEPTA3JTJGMTYlMkYwNyZjdXN0b21lcl9zaW5jZSU1QnRvJTVEPTA3JTJGMTYlMkYwNw==\";}s:15:\"\0*\0_idFieldName\";N;}}customer|a:1:{s:4:\"data\";O:13:\"Varien_Object\":2:{s:8:\"\0*\0_data\";a:1:{s:2:\"id\";N;}s:15:\"\0*\0_idFieldName\";N;}}review|a:2:{s:4:\"data\";O:13:\"Varien_Object\":2:{s:8:\"\0*\0_data\";a:0:{}s:15:\"\0*\0_idFieldName\";N;}s:8:\"messages\";O:34:\"Mage_Core_Model_Message_Collection\":1:{s:12:\"\0*\0_messages\";a:0:{}}}');

/*Table structure for table `core_store` */

DROP TABLE IF EXISTS `core_store`;

CREATE TABLE `core_store` (
  `store_id` smallint(5) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '',
  `language_code` varchar(2) default NULL,
  `website_id` smallint(5) unsigned default '0',
  `name` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`store_id`),
  UNIQUE KEY `code` (`code`),
  KEY `FK_STORE_LANGUAGE` (`language_code`),
  KEY `FK_STORE_WEBSITE` (`website_id`),
  CONSTRAINT `FK_STORE_LANGUAGE` FOREIGN KEY (`language_code`) REFERENCES `core_language` (`language_code`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `FK_STORE_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores';

/*Data for the table `core_store` */

insert into `core_store` (`store_id`,`code`,`language_code`,`website_id`,`name`) values (1,'base','en',1,'English'),(2,'russian','ru',1,'Russian');

/*Table structure for table `core_website` */

DROP TABLE IF EXISTS `core_website`;

CREATE TABLE `core_website` (
  `website_id` smallint(5) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '',
  `name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Websites';

/*Data for the table `core_website` */

insert into `core_website` (`website_id`,`code`,`name`) values (1,'base','Default');

/*Table structure for table `cron_schedule` */

DROP TABLE IF EXISTS `cron_schedule`;

CREATE TABLE `cron_schedule` (
  `schedule_id` int(10) unsigned NOT NULL auto_increment,
  `task_name` int(10) unsigned NOT NULL default '0',
  `schedule_status` tinyint(4) NOT NULL default '0',
  `schedule_type` tinyint(4) NOT NULL default '0',
  `schedule_cmd` text NOT NULL,
  `schedule_comments` text NOT NULL,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `scheduled_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `executed_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `finished_at` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`schedule_id`),
  KEY `task_name` (`task_name`),
  KEY `scheduled_at` (`scheduled_at`,`schedule_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `cron_schedule` */

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
