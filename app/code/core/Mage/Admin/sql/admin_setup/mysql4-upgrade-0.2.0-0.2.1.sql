/*
SQLyog Enterprise - MySQL GUI v5.13
Host - 4.1.21-community-nt : Database - magento
*********************************************************************
Server version : 4.1.21-community-nt
*/

SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `admin_assert` */

DROP TABLE IF EXISTS `admin_assert`;

CREATE TABLE `admin_assert` (
  `assert_id` int(10) unsigned NOT NULL auto_increment,
  `assert_type` varchar(20) NOT NULL default '',
  `assert_data` text,
  PRIMARY KEY  (`assert_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='ACL Asserts';

/*Data for the table `admin_assert` */

/*Table structure for table `admin_role` */

DROP TABLE IF EXISTS `admin_role`;

CREATE TABLE `admin_role` (
  `role_id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned NOT NULL default '0',
  `tree_level` tinyint(3) unsigned NOT NULL default '0',
  `sort_order` tinyint(3) unsigned NOT NULL default '0',
  `role_type` char(1) NOT NULL default '0',
  `user_id` int(11) unsigned NOT NULL default '0',
  `role_name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`role_id`),
  KEY `parent_id` (`parent_id`,`sort_order`),
  KEY `tree_level` (`tree_level`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='ACL Roles';

/*Data for the table `admin_role` */

insert into `admin_role` (`role_id`,`parent_id`,`tree_level`,`sort_order`,`role_type`,`user_id`,`role_name`) values (1,0,1,0,'G',0,'Developers'),(2,0,1,0,'G',0,'Administrators'),(3,0,1,0,'G',0,'Users'),(4,1,2,0,'U',1,'Moshe Gurvich'),(5,1,2,0,'U',2,'Andrey Korolyov'),(6,1,2,0,'U',3,'Dmitriy Soroka'),(7,1,2,0,'U',5,'Roy Rubin');

/*Table structure for table `admin_rule` */

DROP TABLE IF EXISTS `admin_rule`;

CREATE TABLE `admin_rule` (
  `rule_id` int(10) unsigned NOT NULL auto_increment,
  `role_type` char(1) NOT NULL default '',
  `role_id` int(10) unsigned NOT NULL default '0',
  `resource_id` varchar(255) NOT NULL default '',
  `privileges` varchar(20) NOT NULL default '',
  `permission` tinyint(1) unsigned NOT NULL default '1',
  `assert_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`rule_id`),
  KEY `resource` (`resource_id`,`role_id`),
  KEY `role_id` (`role_type`,`role_id`,`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='ACL Rules';

/*Data for the table `admin_rule` */

insert into `admin_rule` (`rule_id`,`role_type`,`role_id`,`resource_id`,`privileges`,`permission`,`assert_id`) values (1,'G',1,'admin','',2,0),(2,'U',1,'admin/catalog','create,delete',2,0),(3,'U',2,'admin/system/websites','delete',0,0);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
