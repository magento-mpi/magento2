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

/*Table structure for table `auth_assert` */

DROP TABLE IF EXISTS `auth_assert`;

CREATE TABLE `auth_assert` (
  `assert_id` int(10) unsigned NOT NULL auto_increment,
  `assert_type` varchar(20) NOT NULL default '',
  `assert_data` text,
  PRIMARY KEY  (`assert_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='ACL Asserts';

/*Data for the table `auth_assert` */

/*Table structure for table `auth_role` */

DROP TABLE IF EXISTS `auth_role`;

CREATE TABLE `auth_role` (
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

/*Data for the table `auth_role` */

insert into `auth_role` (`role_id`,`parent_id`,`tree_level`,`sort_order`,`role_type`,`user_id`,`role_name`) values (1,0,1,0,'G',0,'Developers'),(2,0,1,0,'G',0,'Administrators'),(3,0,1,0,'G',0,'Users'),(4,1,2,0,'U',1,'Moshe Gurvich'),(5,1,2,0,'U',2,'Andrey Korolyov'),(6,1,2,0,'U',3,'Dmitriy Soroka'),(7,1,2,0,'U',5,'Roy Rubin');

/*Table structure for table `auth_rule` */

DROP TABLE IF EXISTS `auth_rule`;

CREATE TABLE `auth_rule` (
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

/*Data for the table `auth_rule` */

insert into `auth_rule` (`rule_id`,`role_type`,`role_id`,`resource_id`,`privileges`,`permission`,`assert_id`) values (1,'G',1,'system','',2,0),(2,'U',1,'catalog','create,delete',2,0),(3,'U',2,'system/websites','delete',0,0);

/*Table structure for table `auth_user` */

DROP TABLE IF EXISTS `auth_user`;

CREATE TABLE `auth_user` (
  `user_id` mediumint(9) unsigned NOT NULL auto_increment,
  `firstname` varchar(32) NOT NULL default '',
  `lastname` varchar(32) NOT NULL default '',
  `email` varchar(128) NOT NULL default '',
  `username` varchar(40) NOT NULL default '',
  `password` varchar(40) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime default NULL,
  `logdate` datetime default NULL,
  `lognum` smallint(5) unsigned NOT NULL default '0',
  `reload_acl_flag` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Users';

/*Data for the table `auth_user` */

insert into `auth_user` (`user_id`,`firstname`,`lastname`,`email`,`username`,`password`,`created`,`modified`,`logdate`,`lognum`,`reload_acl_flag`) values (1,'Moshe','Gurvich','moshe@varien.com','moshe','4297f44b13955235245b2497399d7a93','0000-00-00 00:00:00',NULL,NULL,0,0),(2,'Andrey','Korolyov','andrey@varien.com','andrey','4297f44b13955235245b2497399d7a93','0000-00-00 00:00:00',NULL,NULL,0,0),(3,'Dmitriy','Soroka','dmitriy@varien.com','dmitriy','4297f44b13955235245b2497399d7a93','0000-00-00 00:00:00',NULL,NULL,0,0),(4,'Vincent','Maung','vincent@varien.com','vincent','a7461721eb9221fb6898dfe919ed1a17','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00',0,0),(5,'Roy','Rubin','roy@varien.com','roy','4297f44b13955235245b2497399d7a93','0000-00-00 00:00:00',NULL,NULL,0,0);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
