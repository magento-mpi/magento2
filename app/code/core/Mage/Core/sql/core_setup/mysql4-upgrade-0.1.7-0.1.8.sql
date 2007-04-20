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

/*Table structure for table `core_website` */

DROP TABLE IF EXISTS `core_website`;

CREATE TABLE `core_website` (
  `website_id` smallint(6) unsigned NOT NULL auto_increment,
  `language_code` varchar(2) NOT NULL default '',
  `website_code` varchar(32) NOT NULL default '',
  `root_category_id` mediumint(9) unsigned NOT NULL default '0',
  PRIMARY KEY  (`website_id`),
  KEY `FK_WEBSITE_LANGUAGE` (`language_code`),
  CONSTRAINT `FK_WEBSITE_LANGUAGE` FOREIGN KEY (`language_code`) REFERENCES `core_language` (`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Websites';

/*Data for the table `core_website` */

insert into `core_website` (`website_id`,`language_code`,`website_code`,`root_category_id`) values (1,'en','Magenta',2),(2,'en','WebSite 2',2),(3,'en','WebSite 3',13),(4,'en','WebSite 4',13),(5,'en','WebSite 5',2);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
