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

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
