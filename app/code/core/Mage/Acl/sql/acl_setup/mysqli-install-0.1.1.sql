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
  `group_id` smallint(6) unsigned NOT NULL auto_increment,
  `group_name` varchar(32) NOT NULL default '',
  `group_code` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='MySQL table';

/*Data for the table `acl_group` */

insert into `acl_group` (`group_id`,`group_name`,`group_code`) values (1,'Administrators','admin');

/*Table structure for table `acl_group_privilege` */

DROP TABLE IF EXISTS `acl_group_privilege`;

CREATE TABLE `acl_group_privilege` (
  `group_id` smallint(5) unsigned NOT NULL default '0',
  `resource_id` mediumint(8) unsigned NOT NULL default '0',
  `privilege` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`resource_id`),
  KEY `FK_GROUP_RESOURCE` (`resource_id`),
  CONSTRAINT `FK_RESOURCE_GROUP` FOREIGN KEY (`group_id`) REFERENCES `acl_group` (`group_id`),
  CONSTRAINT `FK_GROUP_RESOURCE` FOREIGN KEY (`resource_id`) REFERENCES `acl_resource` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='MySQL table';

/*Data for the table `acl_group_privilege` */

/*Table structure for table `acl_resource` */

DROP TABLE IF EXISTS `acl_resource`;

CREATE TABLE `acl_resource` (
  `resource_id` mediumint(8) unsigned NOT NULL auto_increment,
  `resource_left` mediumint(8) unsigned NOT NULL default '0',
  `resource_right` mediumint(8) unsigned NOT NULL default '0',
  `resource_level` tinyint(3) unsigned NOT NULL default '0',
  `resource_parent` mediumint(8) unsigned NOT NULL default '0',
  `resource_name` varchar(64) NOT NULL default '',
  `resource_map` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='MySQL table';

/*Data for the table `acl_resource` */

/*Table structure for table `acl_user` */

DROP TABLE IF EXISTS `acl_user`;

CREATE TABLE `acl_user` (
  `user_id` mediumint(9) unsigned NOT NULL auto_increment,
  `firstname` varchar(32) NOT NULL default '',
  `lastname` varchar(32) NOT NULL default '',
  `email` varchar(128) NOT NULL default '',
  `password` varchar(40) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime default NULL,
  `logdate` datetime default NULL,
  `lognum` smallint(5) unsigned NOT NULL default '0',
  `reload_acl_flag` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='MySQL table';

/*Data for the table `acl_user` */

/*Table structure for table `acl_user_group` */

DROP TABLE IF EXISTS `acl_user_group`;

CREATE TABLE `acl_user_group` (
  `group_id` smallint(5) unsigned NOT NULL default '0',
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`group_id`,`user_id`),
  KEY `FK_GROUP_USER` (`user_id`),
  CONSTRAINT `FK_USER_GROUP` FOREIGN KEY (`group_id`) REFERENCES `acl_group` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_GROUP_USER` FOREIGN KEY (`user_id`) REFERENCES `acl_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='MySQL table';

/*Data for the table `acl_user_group` */

/*Table structure for table `acl_user_privilege` */

DROP TABLE IF EXISTS `acl_user_privilege`;

CREATE TABLE `acl_user_privilege` (
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `resource_id` mediumint(8) unsigned NOT NULL default '0',
  `privilege` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`resource_id`),
  KEY `FK_USER_RESOURCE` (`resource_id`),
  CONSTRAINT `FK_USER_RESOURCE` FOREIGN KEY (`resource_id`) REFERENCES `acl_resource` (`resource_id`),
  CONSTRAINT `FK_RESOURCE_USER` FOREIGN KEY (`user_id`) REFERENCES `acl_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='MySQL table';

/*Data for the table `acl_user_privilege` */

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
