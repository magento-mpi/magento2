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

/*Table structure for table `admin_assert` */

DROP TABLE IF EXISTS `admin_assert`;

CREATE TABLE `admin_assert` (
  `assert_id` int(10) unsigned NOT NULL auto_increment,
  `assert_type` varchar(20) character set latin1 NOT NULL default '',
  `assert_data` text character set latin1,
  PRIMARY KEY  (`assert_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ACL Asserts';

/*Data for the table `admin_assert` */

/*Table structure for table `admin_role` */

DROP TABLE IF EXISTS `admin_role`;

CREATE TABLE `admin_role` (
  `role_id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned NOT NULL default '0',
  `tree_level` tinyint(3) unsigned NOT NULL default '0',
  `sort_order` tinyint(3) unsigned NOT NULL default '0',
  `role_type` char(1) character set latin1 NOT NULL default '0',
  `user_id` int(11) unsigned NOT NULL default '0',
  `role_name` varchar(50) character set latin1 NOT NULL default '',
  PRIMARY KEY  (`role_id`),
  KEY `parent_id` (`parent_id`,`sort_order`),
  KEY `tree_level` (`tree_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ACL Roles';

/*Data for the table `admin_role` */

insert into `admin_role` (`role_id`,`parent_id`,`tree_level`,`sort_order`,`role_type`,`user_id`,`role_name`) values (1,0,1,0,'G',0,'Developers'),(2,0,1,0,'G',0,'Administrators'),(3,0,1,0,'G',0,'Users'),(4,1,2,0,'U',1,'Moshe Gurvich'),(5,1,2,0,'U',2,'Andrey Korolyov'),(6,1,2,0,'U',3,'Dmitriy Soroka'),(7,1,2,0,'U',5,'Roy Rubin');

/*Table structure for table `admin_rule` */

DROP TABLE IF EXISTS `admin_rule`;

CREATE TABLE `admin_rule` (
  `rule_id` int(10) unsigned NOT NULL auto_increment,
  `role_id` int(10) unsigned NOT NULL default '0',
  `resource_id` varchar(255) character set latin1 NOT NULL default '',
  `privileges` varchar(20) character set latin1 NOT NULL default '',
  `assert_id` int(10) unsigned NOT NULL default '0',
  `role_type` char(1) default NULL,
  PRIMARY KEY  (`rule_id`),
  KEY `resource` (`resource_id`,`role_id`),
  KEY `role_id` (`role_id`,`resource_id`),
  constraint `FK_admin_rule` foreign key(`role_id`)references `admin_role` (`role_id`) on delete cascade  on update cascade
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ACL Rules';

/*Data for the table `admin_rule` */

insert into `admin_rule` (`rule_id`,`role_id`,`resource_id`,`privileges`,`assert_id`,`role_type`) values (1,1,'admin','',0,NULL),(2,1,'admin/catalog','create,delete',0,NULL),(3,2,'admin/system/stores','delete',0,NULL);

/*Table structure for table `admin_user` */

DROP TABLE IF EXISTS `admin_user`;

CREATE TABLE `admin_user` (
  `user_id` mediumint(9) unsigned NOT NULL auto_increment,
  `firstname` varchar(32) character set latin1 NOT NULL default '',
  `lastname` varchar(32) character set latin1 NOT NULL default '',
  `email` varchar(128) character set latin1 NOT NULL default '',
  `username` varchar(40) character set latin1 NOT NULL default '',
  `password` varchar(40) character set latin1 NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime default NULL,
  `logdate` datetime default NULL,
  `lognum` smallint(5) unsigned NOT NULL default '0',
  `reload_acl_flag` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Users';

/*Data for the table `admin_user` */

insert into `admin_user` (`user_id`,`firstname`,`lastname`,`email`,`username`,`password`,`created`,`modified`,`logdate`,`lognum`,`reload_acl_flag`) values (1,'Admin','User','admin@varien.com','admin','4297f44b13955235245b2497399d7a93','2007-07-21 00:00:00','2007-07-21 00:00:00','2007-07-21 00:00:00',1,0),(2,'QA','User','qa@varien.com','qa','4297f44b13955235245b2497399d7a93','2007-07-21 00:00:00','2007-07-21 00:00:00','2007-07-21 00:00:00',0,0);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
