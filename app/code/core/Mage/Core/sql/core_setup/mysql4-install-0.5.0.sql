/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
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
  `value` varchar(255) NOT NULL default '',
  `old_value` varchar(255) NOT NULL default '',
  `inherit` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`config_id`),
  UNIQUE KEY `config_scope` (`scope`,`scope_id`,`path`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `core_config_data` */

insert into `core_config_data` (`config_id`,`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values (1,'default',0,'general/currency/base','USD','',1),(2,'default',0,'general/currency/allow','USD,CAD,UAH,RUB','',1),(3,'default',0,'general/currency/default','USD','',1),(4,'default',0,'general/local/language','en','',1),(6,'default',0,'system/filesystem/layout','{{root_dir}}/app/view/layout','',1),(7,'default',0,'system/filesystem/template','{{root_dir}}/app/view/template','',1),(8,'default',0,'system/filesystem/translate','{{root_dir}}/app/view/translate','',1),(9,'default',0,'system/filesystem/base','{{root_dir}}','',1),(10,'default',0,'system/filesystem/media','{{root_dir}}/media','',1),(11,'default',0,'system/filesystem/skin','{{root_dir}}/skins/default','',1),(12,'default',0,'web/unsecure/protocol','{{protocol}}','',1),(13,'default',0,'web/unsecure/host','{{host}}','',1),(14,'default',0,'web/unsecure/port','{{port}}','',1),(15,'default',0,'web/unsecure/base_path','{{base_path}}','',1),(16,'default',0,'web/secure/protocol','{{protocol}}','',1),(17,'default',0,'web/secure/host','{{host}}','',1),(18,'default',0,'web/secure/port','{{port}}','',1),(19,'default',0,'web/secure/base_path','{{base_path}}','',1);
insert into `core_config_data` (`config_id`,`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values (20,'default',0,'web/url/media','{{base_path}}media/','',1),(21,'default',0,'web/url/skin','{{base_path}}skins/default/','',1),(22,'default',0,'web/url/js','{{base_path}}js/','',1),(23,'default',0,'system/filesystem/etc','{{root_dir}}/app/etc/','',1),(24,'default',0,'system/filesystem/code','{{root_dir}}/app/code/','',1),(25,'default',0,'system/filesystem/upload','{{root_dir}}/media/upload/','',1),(26,'default',0,'system/filesystem/var','{{var_dir}}','',1),(27,'default',0,'system/filesystem/session','{{var_dir}}/session/','',1),(28,'default',0,'system/filesystem/cache_config','{{var_dir}}/cache/config/','',1),(29,'default',0,'system/filesystem/cache_layout','{{var_dir}}/cache/layout/','',1),(32,'default',0,'web/default/front','catalog','',1),(34,'default',0,'web/default/no_route','core/index/noRoute','',1),(35,'default',0,'general/country/default','US','CA',1),(36,'default',0,'general/country/allow','US,CA,UA','',1),(39,'default',0,'advanced/datashare/customer','1','',1),(40,'default',0,'advanced/datashare/customer_address','1','',1),(41,'default',0,'advanced/datashare/quote','1','',1),(42,'default',0,'advanced/datashare/quote_address','1','',1),(43,'default',0,'advanced/datashare/order','1','',1),(44,'default',0,'advanced/datashare/order_address','1','',1),(45,'default',0,'advanced/datashare/order_payment','1','',1);
insert into `core_config_data` (`config_id`,`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values (46,'default',0,'advanced/datashare/wishlist','1','',1),(47,'default',0,'general/local/date_format_short','%m/%d/%y','',1),(48,'default',0,'general/local/date_format_medium','%a, %b %e %Y','',1),(49,'default',0,'general/local/date_format_long','%A, %B %e %Y','',1),(50,'default',0,'general/local/datetime_format_short','%m/%d/%y [%I:%M %p]','',1),(51,'default',0,'general/local/datetime_format_medium','%a, %b %e %Y [%I:%M %p]','',1),(52,'default',0,'general/local/datetime_format_long','%A, %B %e %Y [%I:%M %p]','',1),(53,'store',1,'general/country/allow','US,CA,UA','',0),(54,'store',1,'general/country/default','US','',1),(55,'store',1,'general/currency/allow','USD,CAD,UAH,RUB','',1),(56,'store',1,'general/currency/base','USD','',1),(57,'store',1,'general/currency/default','USD','',1),(58,'store',1,'general/local/datetime_format_long','%A, %B %e %Y [%I:%M %p]','',1),(59,'store',1,'general/local/datetime_format_medium','%a, %b %e %Y [%I:%M %p]','',1),(60,'store',1,'general/local/datetime_format_short','%m/%d/%y [%I:%M %p]','',1),(61,'store',1,'general/local/date_format_long','%A, %B %e %Y','',1),(62,'store',1,'general/local/date_format_medium','%a, %b %e %Y','',1),(63,'store',1,'general/local/date_format_short','%m/%d/%y','',1),(64,'store',1,'general/local/language','en','',1);
insert into `core_config_data` (`config_id`,`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values (77,'website',1,'general/country/allow','US,CA,UA','',1),(78,'website',1,'general/country/default','US','',1),(79,'website',1,'general/currency/allow','USD,CAD,UAH,RUB','',1),(80,'website',1,'general/currency/base','USD','',1),(81,'website',1,'general/currency/default','USD','',1),(82,'website',1,'general/local/datetime_format_long','%A, %B %e %Y [%I:%M %p]','',1),(83,'website',1,'general/local/datetime_format_medium','%a, %b %e %Y [%I:%M %p]','',1),(84,'website',1,'general/local/datetime_format_short','%m/%d/%y [%I:%M %p]','',1),(85,'website',1,'general/local/date_format_long','%A, %B %e %Y','',1),(86,'website',1,'general/local/date_format_medium','%a, %b %e %Y','',1),(87,'website',1,'general/local/date_format_short','%m/%d/%y','',1),(88,'website',1,'general/local/language','en','',1),(89,'store',2,'paygate/authorizenet/test','','',0),(90,'default',0,'paygate/authorizenet/test','1','0',0),(91,'store',1,'paygate/authorizenet/test','1','',0);

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
  `module_name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`field_id`),
  KEY `path` (`path`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `core_config_field` */

insert into `core_config_field` (`field_id`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (1,'advanced/datashare/customer','Customer','text','','','','',0,1,1,1,''),(2,'advanced/datashare/customer_address','Customer Address','text','','','','',0,1,1,1,''),(3,'advanced/datashare/order','Order','text','','','','',0,1,1,1,''),(4,'advanced/datashare/order_address','Order Address','text','','','','',0,1,1,1,''),(5,'advanced/datashare/order_payment','Order Payment','text','','','','',0,1,1,1,''),(6,'advanced/datashare/quote','Shopping cart / Quote','text','','','','',0,1,1,1,''),(7,'advanced/datashare/quote_address','Shopping cart / Quote Address','text','','','','',0,1,1,1,''),(8,'advanced/datashare/wishlist','Wishlist','text','','','','',0,1,1,1,''),(9,'general/country/allow','Allow countries','text','','','','',0,1,1,1,''),(10,'general/country/default','Default country','text','','','','',0,1,1,1,''),(11,'general/currency/allow','Allow currencies','text','','','','',0,1,1,1,''),(12,'general/currency/base','Base currency','text','','','','',0,1,1,1,''),(13,'general/currency/default','Default currency','text','','','','',0,1,1,1,''),(14,'general/local/date_format_short','Date format (short)','text','','','','',0,1,1,1,''),(15,'general/local/date_format_medium','Date format (medium)','text','','','','',0,1,1,1,''),(16,'general/local/language','Language','text','','','','',0,1,1,1,''),(17,'system/filesystem/base','Base directory','text','','','','',0,1,1,1,''),(18,'system/filesystem/cache_config','Config cache directory','text','','','','',0,1,1,1,'');
insert into `core_config_field` (`field_id`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (19,'system/filesystem/cache_layout','Layout cache directory','text','','','','',0,1,1,1,''),(20,'system/filesystem/code','Code pools root directory','text','','','','',0,1,1,1,''),(21,'system/filesystem/etc','Configuration directory','text','','','','',0,1,1,1,''),(22,'system/filesystem/layout','Layout files directory','text','','','','',0,1,1,1,''),(23,'system/filesystem/media','Media files directory','text','','','','',0,1,1,1,''),(24,'system/filesystem/session','Session files directory','text','','','','',0,1,1,1,''),(25,'system/filesystem/skin','Skin directory','text','','','','',0,1,1,1,''),(26,'system/filesystem/template','Template directory','text','','','','',0,1,1,1,''),(27,'system/filesystem/translate','Translactions directory','text','','','','',0,1,1,1,''),(28,'system/filesystem/upload','Upload directory','text','','','','',0,1,1,1,''),(29,'system/filesystem/var','Var (temporary files) directory','text','','','','',0,1,1,1,''),(30,'web/default/front','Default web url','text','','','','',0,1,1,1,''),(31,'web/default/no_route','Default no-route url','text','','','','',0,1,1,1,''),(32,'web/secure/base_path','Base url','text','','','','',0,1,1,1,''),(33,'web/secure/host','Host','text','','','','',0,1,1,1,''),(34,'web/secure/port','Port','text','','','','',0,1,1,1,''),(35,'web/secure/protocol','Protocol','text','','','','',0,1,1,1,''),(36,'web/unsecure/base_path','Base url','text','','','','',0,1,1,1,''),(37,'web/unsecure/host','Host','text','','','','',0,1,1,1,''),(38,'web/unsecure/port','Port','text','','','','',0,1,1,1,''),(39,'web/unsecure/protocol','Protocol','text','','','','',0,1,1,1,'');
insert into `core_config_field` (`field_id`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values (40,'web/url/js','Js base url','text','','','','',0,1,1,1,''),(41,'web/url/media','Media base url','text','','','','',0,1,1,1,''),(42,'web/url/skin','Skin base url','text','','','','',0,1,1,1,''),(43,'general','General','text','','','','',0,1,1,1,''),(44,'web','Web','text','','','','',0,1,1,1,''),(45,'system','System','text','','','','',0,1,1,1,''),(46,'advanced','Advanced','text','','','','',0,1,1,1,''),(47,'web/default','Default','text','','','','',0,1,1,1,''),(48,'web/secure','Secure','text','','','','',0,1,1,1,''),(49,'web/unsecure','Unsecure','text','','','','',0,1,1,1,''),(50,'web/url','URLs','text','','','','',0,1,1,1,''),(51,'system/filesystem','Filesystem','text','','','','',0,1,1,1,''),(52,'general/currency','Currency options','text','','','','',0,1,1,1,''),(53,'general/local','Local options','text','','','','',0,1,1,1,''),(54,'general/country','Countries options','text','','','','',0,1,1,1,''),(55,'advanced/datashare','Datasharing','text','','','','',0,1,1,1,''),(56,'general/local/date_format_long','Date format (long)','text','','','','',0,1,1,1,''),(57,'general/local/datetime_format_short','Date format (short with time)','text','','','','',0,1,1,1,''),(58,'general/local/datetime_format_medium','Date format (medium with time)','text','','','','',0,1,1,1,''),(59,'general/local/datetime_format_long','Date format (long with time)','text','','','','',0,1,1,1,''),(60,'paygate','Payment gateways','text','','','','',0,1,1,1,''),(61,'paygate/authorizenet','Authorize.net','text','','','','',0,1,1,1,''),(62,'paygate/authorizenet/test','Test mode','select','','','','adminhtml/system_config_source_yesno',0,1,1,1,'');

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

insert into `core_store` (`store_id`,`code`,`language_code`,`website_id`,`name`) values (0,'default','en',0,'Default'),(1,'base','en',1,'Store 1 (English)'),(2,'russian','ru',1,'Store 2 (Russian)');

/*Table structure for table `core_website` */

DROP TABLE IF EXISTS `core_website`;

CREATE TABLE `core_website` (
  `website_id` smallint(5) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '',
  `name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Websites';

/*Data for the table `core_website` */

insert into `core_website` (`website_id`,`code`,`name`) values (0,'default','Default'),(1,'base','Main website');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
