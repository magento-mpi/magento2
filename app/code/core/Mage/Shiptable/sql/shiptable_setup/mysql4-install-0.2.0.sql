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

/*Table structure for table `shiptable_data` */

DROP TABLE IF EXISTS `shiptable_data`;

CREATE TABLE `shiptable_data` (
  `pk` int(10) unsigned NOT NULL auto_increment,
  `dest_country_id` int(10) NOT NULL default '0',
  `dest_region_id` int(10) NOT NULL default '0',
  `dest_zip` varchar(10) NOT NULL default '',
  `condition_name` varchar(20) NOT NULL default '',
  `condition_value` decimal(12,4) NOT NULL default '0.0000',
  `price` decimal(12,4) NOT NULL default '0.0000',
  `cost` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`pk`),
  UNIQUE KEY `dest_country` (`dest_country_id`,`dest_region_id`,`condition_name`,`condition_value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `shiptable_data` */

insert into `shiptable_data` (`pk`,`dest_country_id`,`dest_region_id`,`dest_zip`,`condition_name`,`condition_value`,`price`,`cost`) values (1,223,1,'','package_weight',100.0000,10.0000,5.0000),(2,223,1,'','package_weight',1000.0000,20.0000,10.0000);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
