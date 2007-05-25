/*
SQLyog Enterprise - MySQL GUI v5.13
Host - 4.1.20 : Database - magenta
*********************************************************************
Server version : 4.1.20
*/

SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';


/*Table structure for table `core_block` */

DROP TABLE IF EXISTS `core_block`;

CREATE TABLE `core_block` (
  `block_id` mediumint(9) unsigned NOT NULL auto_increment,
  `group_id` smallint(6) unsigned NOT NULL default '0',
  `block_type` varchar(64) NOT NULL default '',
  `block_name` varchar(64) NOT NULL default '',
  `data_serialized` text,
  PRIMARY KEY  (`block_id`),
  KEY `FK_BLOCK_GROUP` (`group_id`),
  CONSTRAINT `FK_BLOCK_GROUP` FOREIGN KEY (`group_id`) REFERENCES `core_block_group` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='System blocks';

/*Data for the table `core_block` */

/*Table structure for table `core_block_group` */

DROP TABLE IF EXISTS `core_block_group`;

CREATE TABLE `core_block_group` (
  `group_id` smallint(6) unsigned NOT NULL auto_increment,
  `group_name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Group of blocks';

/*Data for the table `core_block_group` */

/*Table structure for table `core_config` */

DROP TABLE IF EXISTS `core_config`;

CREATE TABLE `core_config` (
  `config_id` int(10) unsigned NOT NULL auto_increment,
  `config_module` varchar(255) NOT NULL default '',
  `config_key` varchar(255) NOT NULL default '',
  `config_value` text,
  `value_input_type` varchar(50) default NULL,
  `value_source` varchar(255) default NULL,
  PRIMARY KEY  (`config_id`),
  UNIQUE KEY `config_key` (`config_key`),
  KEY `config_module` (`config_module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `core_config` */

insert into `core_config` (`config_id`,`config_module`,`config_key`,`config_value`,`value_input_type`,`value_source`) values (1,'Mage_Sales','global/shipping/ups/active','True','checkbox',NULL);

/*Table structure for table `core_custom_field` */

DROP TABLE IF EXISTS `core_custom_field`;

CREATE TABLE `core_custom_field` (
  `custom_field_id` int(10) unsigned NOT NULL auto_increment,
  `table_id` int(10) unsigned default '0',
  `custom_field_name` varchar(100) NOT NULL default '',
  `custom_field_type` varchar(7) NOT NULL default 'text',
  `custom_field_title` varchar(255) NOT NULL default '',
  `custom_field_description` text,
  `custom_field_default_value` text,
  PRIMARY KEY  (`custom_field_id`),
  KEY `FK_CUSTOM_FIELD_TABLE` (`table_id`),
  CONSTRAINT `FK_CUSTOM_FIELD_TABLE` FOREIGN KEY (`table_id`) REFERENCES `core_table` (`table_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Custom table fields';

/*Data for the table `core_custom_field` */

/*Table structure for table `core_custom_field_data` */

DROP TABLE IF EXISTS `core_custom_field_data`;

CREATE TABLE `core_custom_field_data` (
  `custom_field_data_id` int(10) unsigned NOT NULL auto_increment,
  `custom_field_id` int(10) unsigned NOT NULL default '0',
  `table_row_id` int(10) unsigned NOT NULL default '0',
  `value_int` int(10) unsigned default NULL,
  `value_decimal` decimal(12,4) default NULL,
  `value_text` text,
  PRIMARY KEY  (`custom_field_data_id`),
  KEY `FK_CUSTOM_FIELD_DATA_FIELD` (`custom_field_id`),
  CONSTRAINT `FK_CUSTOM_FIELD_DATA_FIELD` FOREIGN KEY (`custom_field_id`) REFERENCES `core_custom_field` (`custom_field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Data from custom fields';

/*Data for the table `core_custom_field_data` */

/*Table structure for table `core_data_change` */

DROP TABLE IF EXISTS `core_data_change`;

CREATE TABLE `core_data_change` (
  `change_id` int(11) unsigned NOT NULL auto_increment,
  `user_id` mediumint(9) unsigned NOT NULL default '0',
  `change_code` varchar(64) default NULL,
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`change_id`),
  KEY `FK_DATA_CHANGE_USER` (`user_id`),
  CONSTRAINT `FK_DATA_CHANGE_USER` FOREIGN KEY (`user_id`) REFERENCES `acl_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tables data change history';

/*Data for the table `core_data_change` */

/*Table structure for table `core_data_change_info` */

DROP TABLE IF EXISTS `core_data_change_info`;

CREATE TABLE `core_data_change_info` (
  `change_info_id` int(11) unsigned NOT NULL default '0',
  `change_id` int(11) unsigned NOT NULL default '0',
  `change_type_id` tinyint(3) unsigned NOT NULL default '0',
  `table_name` varchar(64) NOT NULL default '',
  `table_pk_value` varchar(64) NOT NULL default '',
  `data_before` text,
  `data_after` text,
  PRIMARY KEY  (`change_info_id`),
  KEY `FK_DATA_INFO_CHANGE` (`change_id`),
  KEY `FK_DATE_INFO_CHANGE_TYPE` (`change_type_id`),
  CONSTRAINT `FK_DATA_INFO_CHANGE` FOREIGN KEY (`change_id`) REFERENCES `core_data_change` (`change_id`),
  CONSTRAINT `FK_DATE_INFO_CHANGE_TYPE` FOREIGN KEY (`change_type_id`) REFERENCES `core_data_change_type` (`change_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Information about data changes';

/*Data for the table `core_data_change_info` */

/*Table structure for table `core_data_change_type` */

DROP TABLE IF EXISTS `core_data_change_type`;

CREATE TABLE `core_data_change_type` (
  `change_type_id` tinyint(3) unsigned NOT NULL default '0',
  `change_type_code` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`change_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Changes types(insert,update,delete)';

/*Data for the table `core_data_change_type` */

insert into `core_data_change_type` (`change_type_id`,`change_type_code`) values (1,'insert'),(2,'update'),(3,'delete');

/*Table structure for table `core_language` */

DROP TABLE IF EXISTS `core_language`;

CREATE TABLE `core_language` (
  `language_code` varchar(2) NOT NULL default '',
  `language_title` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Languages';

/*Data for the table `core_language` */

insert into `core_language` (`language_code`,`language_title`) values ('en','English');

/*Table structure for table `core_module` */

DROP TABLE IF EXISTS `core_module`;

CREATE TABLE `core_module` (
  `module_id` int(10) unsigned NOT NULL auto_increment,
  `module_name` varchar(100) NOT NULL default '',
  `module_db_version` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Ecom Modules version';

/*Data for the table `core_module` */

/*Table structure for table `core_resource` */

DROP TABLE IF EXISTS `core_resource`;

CREATE TABLE `core_resource` (
  `resource_name` varchar(50) NOT NULL default '',
  `resource_db_version` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`resource_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `core_resource` */


/*Table structure for table `core_session` */

DROP TABLE IF EXISTS `core_session`;

CREATE TABLE `core_session` (
  `session_id` varchar(255) NOT NULL default '',
  `session_expires` int(10) unsigned NOT NULL default '0',
  `session_data` text NOT NULL,
  PRIMARY KEY  (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `core_session` */

insert into `core_session` (`session_id`,`session_expires`,`session_data`) values ('923158a1c0f146dd9f5250640a938f49',1180072423,'customer|a:1:{s:8:\"customer\";O:28:\"Mage_Customer_Model_Customer\":3:{s:8:\"\0*\0_data\";a:0:{}s:13:\"\0*\0_isChanged\";b:0;s:13:\"\0*\0_isDeleted\";b:0;}}checkout|a:1:{s:4:\"data\";O:13:\"Varien_Object\":3:{s:8:\"\0*\0_data\";a:1:{s:8:\"quote_id\";s:3:\"103\";}s:13:\"\0*\0_isChanged\";b:0;s:13:\"\0*\0_isDeleted\";b:0;}}');

/*Table structure for table `core_session_visitor` */

DROP TABLE IF EXISTS `core_session_visitor`;

CREATE TABLE `core_session_visitor` (
  `session_id` varchar(255) NOT NULL default '',
  `first_visit_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_visit_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `server_addr` varchar(64) NOT NULL default '',
  `remote_addr` varchar(64) NOT NULL default '',
  `http_referer` text NOT NULL,
  `http_secure` tinyint(4) NOT NULL default '0',
  `http_host` varchar(255) NOT NULL default '',
  `http_user_agent` varchar(255) NOT NULL default '',
  `http_accept_language` varchar(255) NOT NULL default '',
  `http_accept_charset` varchar(255) NOT NULL default '',
  `request_uri` text NOT NULL,
  `website_id` int(10) unsigned NOT NULL default '0',
  `customer_id` int(10) unsigned NOT NULL default '0',
  `quote_id` int(10) unsigned NOT NULL default '0',
  `url_history` text NOT NULL,
  PRIMARY KEY  (`session_id`),
  CONSTRAINT `FK_core_session_visitor` FOREIGN KEY (`session_id`) REFERENCES `core_session` (`session_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `core_session_visitor` */

insert into `core_session_visitor` (`session_id`,`first_visit_at`,`last_visit_at`,`server_addr`,`remote_addr`,`http_referer`,`http_secure`,`http_host`,`http_user_agent`,`http_accept_language`,`http_accept_charset`,`request_uri`,`website_id`,`customer_id`,`quote_id`,`url_history`) values ('923158a1c0f146dd9f5250640a938f49','2007-05-24 22:20:51','2007-05-24 22:29:43','10.0.5.201','10.0.5.249','http://var-dev.varien.com:81/dev/moshe/magenta/checkout/cart/',0,'var-dev.varien.com:81','Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3','en-us,en;q=0.5','ISO-8859-1,utf-8;q=0.7,*;q=0.7','/dev/moshe/magenta/skins/default/page/images/loading.gif',1,0,103,'http://var-dev.varien.com:81/dev/moshe/magenta/catalog/category/view/id/12/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/catalog/category/view/id/12/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/skins/default/page/images/loading.gif\nhttp://var-dev.varien.com:81/dev/moshe/magenta/catalog/category/view/id/12/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/catalog/category/view/id/12/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/catalog/category/view/id/12/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/catalog/category/view/id/12/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/catalog/category/view/id/12/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/catalog/category/view/id/12/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/catalog/category/view/id/12/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/catalog/category/view/id/12/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/catalog/category/view/id/12/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/catalog/category/view/id/12/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/catalog/category/view/id/12/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/catalog/category/view/id/12/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/catalog/category/view/id/12/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/catalog/category/view/id/12/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/skins/default/page/images/loading.gif\nhttp://var-dev.varien.com:81/dev/moshe/magenta/checkout/cart/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/skins/default/page/images/loading.gif\nhttp://var-dev.varien.com:81/dev/moshe/magenta/catalog/category/view/id/7/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/media/2073esm.jpg\nhttp://var-dev.varien.com:81/dev/moshe/magenta/media/N\nhttp://var-dev.varien.com:81/dev/moshe/magenta/catalog/product/view/id/2493/category/7/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/skins/default/page/images/loading.gif\nhttp://var-dev.varien.com:81/dev/moshe/magenta/skins/default/catalog/js/product.js\nhttp://var-dev.varien.com:81/dev/moshe/magenta/skins/default/catalog/images/magnifier_zoom_out.gif\nhttp://var-dev.varien.com:81/dev/moshe/magenta/skins/default/catalog/images/icon_made_in_america.gif\nhttp://var-dev.varien.com:81/dev/moshe/magenta/checkout/cart/add/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/checkout/cart/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/skins/default/page/images/loading.gif\nhttp://var-dev.varien.com:81/dev/moshe/magenta/checkout/cart/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/skins/default/page/images/loading.gif\nhttp://var-dev.varien.com:81/dev/moshe/magenta/checkout/cart/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/skins/default/page/images/loading.gif\nhttp://var-dev.varien.com:81/dev/moshe/magenta/checkout/cart/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/skins/default/page/images/loading.gif\nhttp://var-dev.varien.com:81/dev/moshe/magenta/checkout/cart/\nhttp://var-dev.varien.com:81/dev/moshe/magenta/skins/default/page/images/loading.gif');

/*Table structure for table `core_setting` */

DROP TABLE IF EXISTS `core_setting`;

CREATE TABLE `core_setting` (
  `setting_id` mediumint(9) unsigned NOT NULL auto_increment,
  `setting_name` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Settings';

/*Data for the table `core_setting` */

/*Table structure for table `core_table` */

DROP TABLE IF EXISTS `core_table`;

CREATE TABLE `core_table` (
  `table_id` int(10) unsigned NOT NULL auto_increment,
  `module_id` int(10) unsigned default '0',
  `table_name` varchar(100) NOT NULL default '',
  `table_pk` varchar(100) NOT NULL default '',
  `table_version` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`table_id`),
  KEY `FK_TABLE_MODULE` (`module_id`),
  CONSTRAINT `FK_TABLE_MODULE` FOREIGN KEY (`module_id`) REFERENCES `core_module` (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tables configuration';

/*Data for the table `core_table` */

/*Table structure for table `core_website` */

DROP TABLE IF EXISTS `core_website`;

CREATE TABLE `core_website` (
  `website_id` smallint(6) unsigned NOT NULL auto_increment,
  `language_code` varchar(2) NOT NULL default '',
  `website_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`website_id`),
  KEY `FK_WEBSITE_LANGUAGE` (`language_code`),
  CONSTRAINT `FK_WEBSITE_LANGUAGE` FOREIGN KEY (`language_code`) REFERENCES `core_language` (`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Websites';

/*Data for the table `core_website` */

insert into `core_website` (`website_id`,`language_code`,`website_code`) values (1,'en','base'),(2,'en','site2'),(3,'en','site3'),(4,'en','site4'),(5,'en','site5');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;