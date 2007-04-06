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

/*Table structure for table `directory_country` */

DROP TABLE IF EXISTS `directory_country`;

CREATE TABLE `directory_country` (
  `country_id` smallint(6) NOT NULL auto_increment,
  `currency_id` smallint(6) unsigned default NULL,
  `country_iso_code` char(2) NOT NULL default '',
  PRIMARY KEY  (`country_id`),
  KEY `FK_COUNTRY_DEFAULT_CURRENCY` (`currency_id`),
  CONSTRAINT `FK_COUNTRY_DEFAULT_CURRENCY` FOREIGN KEY (`currency_id`) REFERENCES `directory_currency` (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Countries';

/*Data for the table `directory_country` */

/*Table structure for table `directory_country_currency` */

DROP TABLE IF EXISTS `directory_country_currency`;

CREATE TABLE `directory_country_currency` (
  `country_id` smallint(6) NOT NULL default '0',
  `currency_id` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`country_id`,`currency_id`),
  KEY `FK_COUNTRY_CURRENCY` (`currency_id`),
  CONSTRAINT `FK_CURRENCY_COUNTRY` FOREIGN KEY (`country_id`) REFERENCES `directory_country` (`country_id`),
  CONSTRAINT `FK_COUNTRY_CURRENCY` FOREIGN KEY (`currency_id`) REFERENCES `directory_currency` (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Currency per country';

/*Data for the table `directory_country_currency` */

/*Table structure for table `directory_country_name` */

DROP TABLE IF EXISTS `directory_country_name`;

CREATE TABLE `directory_country_name` (
  `language_code` char(2) NOT NULL default '',
  `country_id` smallint(6) NOT NULL default '0',
  `country_name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`language_code`,`country_id`),
  KEY `FK_COUNTRY_NAME_COUNTRY` (`country_id`),
  CONSTRAINT `FK_COUNTRY_NAME_LANG` FOREIGN KEY (`language_code`) REFERENCES `core_language` (`language_code`),
  CONSTRAINT `FK_COUNTRY_NAME_COUNTRY` FOREIGN KEY (`country_id`) REFERENCES `directory_country` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Country names';

/*Data for the table `directory_country_name` */

/*Table structure for table `directory_country_region` */

DROP TABLE IF EXISTS `directory_country_region`;

CREATE TABLE `directory_country_region` (
  `region_id` mediumint(8) unsigned NOT NULL auto_increment,
  `country_id` smallint(6) NOT NULL default '0',
  `region_code` char(2) NOT NULL default '',
  PRIMARY KEY  (`region_id`),
  KEY `FK_REGION_COUNTRY` (`country_id`),
  CONSTRAINT `FK_REGION_COUNTRY` FOREIGN KEY (`country_id`) REFERENCES `directory_country` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Country regions';

/*Data for the table `directory_country_region` */

/*Table structure for table `directory_currency` */

DROP TABLE IF EXISTS `directory_currency`;

CREATE TABLE `directory_currency` (
  `currency_id` smallint(6) unsigned NOT NULL auto_increment,
  `currency_code` char(3) NOT NULL default '',
  `currency_symbol` char(1) default NULL,
  PRIMARY KEY  (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Currency';

/*Data for the table `directory_currency` */

/*Table structure for table `directory_currency_name` */

DROP TABLE IF EXISTS `directory_currency_name`;

CREATE TABLE `directory_currency_name` (
  `language_code` char(2) NOT NULL default '',
  `currency_id` smallint(6) unsigned NOT NULL default '0',
  `currency_name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`language_code`,`currency_id`),
  KEY `FK_CURRENCY_NAME_CURRENCY` (`currency_id`),
  CONSTRAINT `FK_CURRENCY_NAME_CURRENCY` FOREIGN KEY (`currency_id`) REFERENCES `directory_currency` (`currency_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CURENCY_NAME_LANG` FOREIGN KEY (`language_code`) REFERENCES `core_language` (`language_code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Currency names';

/*Data for the table `directory_currency_name` */

/*Table structure for table `directory_region_name` */

DROP TABLE IF EXISTS `directory_region_name`;

CREATE TABLE `directory_region_name` (
  `language_code` char(2) NOT NULL default '',
  `region_id` mediumint(8) unsigned NOT NULL default '0',
  `region_name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`language_code`,`region_id`),
  KEY `FK_REGION_NAME_REGION` (`region_id`),
  CONSTRAINT `FK_REGION_NAME_REGION` FOREIGN KEY (`region_id`) REFERENCES `directory_country_region` (`region_id`),
  CONSTRAINT `FK_REGION_NAME_LANG` FOREIGN KEY (`language_code`) REFERENCES `core_language` (`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Regions names';

/*Data for the table `directory_region_name` */

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
