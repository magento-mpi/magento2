/*
SQLyog Enterprise - MySQL GUI v6.03
Host - 4.1.20 : Database - magento_moshe
*********************************************************************
Server version : 4.1.20
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

create database if not exists `magento_moshe`;

USE `magento_moshe`;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `core_config_attribute` */

DROP TABLE IF EXISTS `core_config_attribute`;

CREATE TABLE `core_config_attribute` (
  `attribute_id` int(10) unsigned NOT NULL auto_increment,
  `section_id` int(10) unsigned NOT NULL default '0',
  `group_id` int(10) unsigned NOT NULL default '0',
  `attribute_name` varchar(255) NOT NULL default '',
  `backend_model` varchar(255) NOT NULL default '',
  `frontend_model` varchar(255) NOT NULL default '',
  `frontend_type` varchar(255) NOT NULL default '',
  `frontend_label` varchar(255) NOT NULL default '',
  `source_model` varchar(255) NOT NULL default '',
  `in_global` tinyint(4) NOT NULL default '0',
  `in_website` tinyint(4) NOT NULL default '0',
  `in_store` tinyint(4) NOT NULL default '0',
  `is_required` tinyint(4) NOT NULL default '0',
  `sort_order` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attribute_id`),
  UNIQUE KEY `section_id` (`section_id`,`attribute_name`),
  UNIQUE KEY `group_id` (`group_id`,`attribute_name`),
  KEY `group_id_2` (`group_id`,`sort_order`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `core_config_group` */

DROP TABLE IF EXISTS `core_config_group`;

CREATE TABLE `core_config_group` (
  `group_id` int(10) unsigned NOT NULL auto_increment,
  `section_id` int(10) unsigned NOT NULL default '0',
  `group_name` varchar(255) NOT NULL default '',
  `group_label` varchar(255) NOT NULL default '',
  `sort_order` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`group_id`),
  UNIQUE KEY `section_id` (`section_id`,`group_name`),
  KEY `section_id_2` (`section_id`,`sort_order`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `core_config_section` */

DROP TABLE IF EXISTS `core_config_section`;

CREATE TABLE `core_config_section` (
  `section_id` int(10) unsigned NOT NULL auto_increment,
  `section_name` varchar(255) NOT NULL default '',
  `section_label` varchar(255) NOT NULL default '',
  `sort_order` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`section_id`),
  UNIQUE KEY `section_name` (`section_name`),
  KEY `sort_order` (`sort_order`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Table structure for table `core_config_value` */

DROP TABLE IF EXISTS `core_config_value`;

CREATE TABLE `core_config_value` (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `section_id` int(10) unsigned NOT NULL default '0',
  `group_id` int(10) unsigned NOT NULL default '0',
  `attribute_id` int(10) unsigned NOT NULL default '0',
  `entity_type` enum('global','website','store') NOT NULL default 'global',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text,
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `entity_type` (`entity_type`,`entity_id`,`section_id`,`attribute_id`),
  UNIQUE KEY `group_id` (`group_id`,`attribute_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
