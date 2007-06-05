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

/*Table structure for table `core_language` */

DROP TABLE IF EXISTS `core_language`;

CREATE TABLE `core_language` (
  `language_code` varchar(2) NOT NULL default '',
  `language_title` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Languages';

/*Data for the table `core_language` */

insert into `core_language` (`language_code`,`language_title`) values ('en','English'),('ru','Russian');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
