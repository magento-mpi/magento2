/*
SQLyog Enterprise - MySQL GUI v6.03
Host - 4.1.20 : Database - magento_moshe
*********************************************************************
Server version : 4.1.20
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `core_config_attribute` */

DROP TABLE IF EXISTS `core_config`;

CREATE TABLE `core_config` (
  `config_id` int(10) unsigned NOT NULL auto_increment,
  `config_scope` enum('global','website','store') NOT NULL default 'global',
  `config_scope_id` int(11) NOT NULL default '0',
  `config_section` varchar(64) NOT NULL default 'general',
  `config_group` varchar(64) NOT NULL default 'general',
  `config_field` varchar(64) NOT NULL default '',
  `config_data` varchar(255) default NULL,
  `use_default` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`config_id`),
  UNIQUE KEY `config_scope` (`config_scope`,`config_scope_id`,`config_section`,`config_group`,`config_field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
