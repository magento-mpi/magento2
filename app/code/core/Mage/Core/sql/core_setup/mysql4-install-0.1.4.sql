/*
SQLyog Enterprise - MySQL GUI v5.13
Host - 4.1.21-community-nt : Database - magenta
*********************************************************************
Server version : 4.1.21-community-nt
*/

SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';


drop table if exists core_language;

/*==============================================================*/
/* Table: core_language                                         */
/*==============================================================*/
create table core_language
(
   language_code                  char(2)                        not null,
   language_title                 varchar(32)                   not null,
   primary key (language_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 comment = "Languages";
insert into `core_language` (`language_code`,`language_title`) values ('en','English');

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

/*Table structure for table `core_domain` */

DROP TABLE IF EXISTS `core_domain`;

CREATE TABLE `core_domain` (
  `domain_id` smallint(4) unsigned NOT NULL auto_increment,
  `domain_name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`domain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Websites domain';

/*Data for the table `core_domain` */

insert into `core_domain` (`domain_id`,`domain_name`) values (1,'');

/*Table structure for table `core_domain_setting` */

DROP TABLE IF EXISTS `core_domain_setting`;

CREATE TABLE `core_domain_setting` (
  `domain_id` smallint(4) unsigned NOT NULL default '0',
  `setting_id` mediumint(9) unsigned NOT NULL default '0',
  `setting_value` text NOT NULL,
  PRIMARY KEY  (`domain_id`,`setting_id`),
  KEY `FK_DOMAIN_SETTING` (`setting_id`),
  CONSTRAINT `FK_DOMAIN_SETTING` FOREIGN KEY (`setting_id`) REFERENCES `core_setting` (`setting_id`),
  CONSTRAINT `FK_SETTING_DOMAIN` FOREIGN KEY (`domain_id`) REFERENCES `core_domain` (`domain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Domain settings';

/*Data for the table `core_domain_setting` */

/*Table structure for table `core_domain_website` */

DROP TABLE IF EXISTS `core_domain_website`;

CREATE TABLE `core_domain_website` (
  `domain_id` smallint(4) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`domain_id`,`website_id`),
  KEY `FK_DOMAIN_WEBSITE` (`website_id`),
  CONSTRAINT `FK_DOMAIN_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`),
  CONSTRAINT `FK_WEBSITE_DOMAIN` FOREIGN KEY (`domain_id`) REFERENCES `core_domain` (`domain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Domain websites';

/*Data for the table `core_domain_website` */

insert into `core_domain_website` (`domain_id`,`website_id`) values (1,1);

/*Table structure for table `core_module` */

DROP TABLE IF EXISTS `core_module`;

CREATE TABLE `core_module` (
  `module_id` int(10) unsigned NOT NULL auto_increment,
  `module_name` varchar(100) NOT NULL default '',
  `module_db_version` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Ecom Modules version';

/*Data for the table `core_module` */

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
  `language_code` char(2) NOT NULL,
  `website_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`website_id`),
  KEY `FK_WEBSITE_LANGUAGE` (`language_code`),
  CONSTRAINT `FK_WEBSITE_LANGUAGE` FOREIGN KEY (`language_code`) REFERENCES `core_language` (`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Websites';

/*Data for the table `core_website` */

insert into `core_website` (`website_id`,`language_code`,`website_code`) values (1,'en','Magenta'),(2,'en','WebSite 2'),(3,'en','WebSite 2'),(4,'en','WebSite 2'),(5,'en','WebSite 2');


drop table if exists core_config;
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

insert into core_config (config_module, config_key, config_value, value_input_type) 
values ('Mage_Sales', 'global/shipping/ups/active', 'True', 'checkbox');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
