/*
SQLyog Enterprise - MySQL GUI v5.13
Host - 4.1.21-community-nt : Database - pepper
*********************************************************************
Server version : 4.1.21-community-nt
*/

SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `acl_group` */

DROP TABLE IF EXISTS `acl_group`;

CREATE TABLE `acl_group` (
  `group_id` smallint(6) unsigned NOT NULL auto_increment COMMENT 'Groups',
  `group_name` varchar(32) NOT NULL default '',
  `group_code` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Users Group';

/*Data for the table `acl_group` */

/*Table structure for table `acl_group_privilege` */

DROP TABLE IF EXISTS `acl_group_privilege`;

CREATE TABLE `acl_group_privilege` (
  `group_id` smallint(6) unsigned NOT NULL default '0',
  `resource_id` mediumint(9) unsigned NOT NULL default '0',
  `privilege` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`resource_id`),
  CONSTRAINT `acl_group_privilege_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `acl_group` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Groups privileges';

/*Data for the table `acl_group_privilege` */

/*Table structure for table `acl_resource` */

DROP TABLE IF EXISTS `acl_resource`;

CREATE TABLE `acl_resource` (
  `resource_id` mediumint(9) unsigned NOT NULL auto_increment,
  `resource_left` mediumint(9) unsigned NOT NULL default '0',
  `resource_right` mediumint(9) unsigned NOT NULL default '0',
  `resource_level` tinyint(3) unsigned NOT NULL default '0',
  `resource_parent` mediumint(9) unsigned NOT NULL default '0',
  `resource_name` varchar(64) NOT NULL default '',
  `resource_map` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='ACL Resources Tree';

/*Data for the table `acl_resource` */

/*Table structure for table `acl_user` */

DROP TABLE IF EXISTS `acl_user`;

CREATE TABLE `acl_user` (
  `user_id` mediumint(9) unsigned NOT NULL auto_increment,
  `user_firstname` varchar(32) NOT NULL default '',
  `user_lastname` varchar(32) NOT NULL default '',
  `user_email` varchar(128) NOT NULL default '',
  `user_password` varchar(40) NOT NULL default '',
  `user_created` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `user_modified` timestamp NULL default NULL,
  `user_logdate` timestamp NULL default NULL,
  `user_lognum` smallint(6) unsigned NOT NULL default '0',
  `reload_acl_flag` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `IDX_USER_EMAIL` (`user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Users';

/*Data for the table `acl_user` */

/*Table structure for table `acl_user_group` */

DROP TABLE IF EXISTS `acl_user_group`;

CREATE TABLE `acl_user_group` (
  `user_id` mediumint(9) unsigned NOT NULL default '0',
  `group_id` smallint(6) unsigned NOT NULL default '0',
  KEY `IDX_USER_ID` (`user_id`),
  KEY `IDX_GROUP_ID` (`group_id`),
  CONSTRAINT `acl_user_group_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `acl_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `acl_user_group_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `acl_group` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Users to Groups; InnoDB free: 9216 kB';

/*Data for the table `acl_user_group` */

/*Table structure for table `acl_user_privilege` */

DROP TABLE IF EXISTS `acl_user_privilege`;

CREATE TABLE `acl_user_privilege` (
  `user_id` mediumint(9) unsigned NOT NULL default '0',
  `resource_id` mediumint(9) unsigned NOT NULL default '0',
  `privilege` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`resource_id`),
  CONSTRAINT `acl_user_privilege_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `acl_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Users privileges; InnoDB free: 9216 kB';

/*Data for the table `acl_user_privilege` */

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
