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

/*
insert into `core_resource` (`code`,`version`) values ('admin_setup','0.2.3'),('catalog_setup','0.3.0'),('cms_setup','0.1.2'),('core_setup','0.3.7'),('cron_setup','0.2.0'),('customer_setup','0.2.5'),('directory_setup','0.2.2'),('eav_setup','0.1.3'),('log_setup','0.1.6'),('newsletter_setup','0.1.5'),('permissions_setup','0.0.2'),('poll_setup','0.2.0'),('rating_setup','0.1.3'),('review_setup','0.1.1'),('sales_setup','0.2.2'),('shiptable_setup','0.2.0'),('tag_setup','0.0.5'),('tax_setup','0.1.2'),('usa_setup','0.2.0'),('wishlist_setup','0.1.0');
*/

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
