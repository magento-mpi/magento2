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

/*Table structure for table `rating` */

DROP TABLE IF EXISTS `rating`;

CREATE TABLE `rating` (
  `rating_id` smallint(6) unsigned NOT NULL auto_increment,
  `entity_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `rating_code` varchar(64) NOT NULL default '',
  `position` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`rating_id`),
  UNIQUE KEY `IDX_CODE` (`rating_code`),
  KEY `FK_RATING_ENTITY` (`entity_id`),
  CONSTRAINT `FK_RATING_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `rating_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ratings';

/*Data for the table `rating` */

insert into `rating` (`rating_id`,`entity_id`,`store_id`,`rating_code`,`position`) values (1,2,1,'product_review_quality',1),(2,2,1,'product_review_use',2),(3,2,1,'product_review_value',3),(4,3,1,'review_quality',0);

/*Table structure for table `rating_entity` */

DROP TABLE IF EXISTS `rating_entity`;

CREATE TABLE `rating_entity` (
  `entity_id` smallint(6) unsigned NOT NULL auto_increment,
  `entity_code` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`entity_id`),
  UNIQUE KEY `IDX_CODE` (`entity_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Rating entities';

/*Data for the table `rating_entity` */

insert into `rating_entity` (`entity_id`,`entity_code`) values (1,'product'),(2,'product_review'),(3,'review');

/*Table structure for table `rating_option` */

DROP TABLE IF EXISTS `rating_option`;

CREATE TABLE `rating_option` (
  `option_id` int(10) unsigned NOT NULL auto_increment,
  `rating_id` smallint(6) unsigned NOT NULL default '0',
  `code` varchar(32) NOT NULL default '',
  `value` tinyint(3) unsigned NOT NULL default '0',
  `position` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`option_id`),
  KEY `FK_RATING_OPTION_RATING` (`rating_id`),
  CONSTRAINT `FK_RATING_OPTION_RATING` FOREIGN KEY (`rating_id`) REFERENCES `rating` (`rating_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Rating options';

/*Data for the table `rating_option` */

insert into `rating_option` (`option_id`,`rating_id`,`code`,`value`,`position`) values (1,1,'',1,1),(2,1,'',2,2),(3,1,'',3,3),(4,1,'',4,4),(5,1,'',5,5),(6,2,'',1,1),(7,2,'',2,2),(8,2,'',3,3),(9,2,'',4,4),(10,2,'',5,5),(11,3,'',1,1),(12,3,'',2,2),(13,3,'',3,3),(14,4,'',1,1),(15,4,'',2,2);

/*Table structure for table `rating_option_vote` */

DROP TABLE IF EXISTS `rating_option_vote`;

CREATE TABLE `rating_option_vote` (
  `vote_id` bigint(20) unsigned NOT NULL auto_increment,
  `option_id` int(10) unsigned NOT NULL default '0',
  `remote_ip` varchar(16) NOT NULL default '',
  `remote_ip_long` int(11) NOT NULL default '0',
  `customer_id` int(11) unsigned default '0',
  `entity_pk_value` bigint(20) unsigned NOT NULL default '0',
  `rating_id` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`vote_id`),
  KEY `FK_RATING_OPTION_VALUE_OPTION` (`option_id`),
  CONSTRAINT `FK_RATING_OPTION_VALUE_OPTION` FOREIGN KEY (`option_id`) REFERENCES `rating_option` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Rating option values';

/*Data for the table `rating_option_vote` */

insert into `rating_option_vote` (`vote_id`,`option_id`,`remote_ip`,`remote_ip_long`,`customer_id`,`entity_pk_value`,`rating_id`) values (6,3,'192.168.0.20',-1062731756,NULL,19,1),(7,8,'192.168.0.20',-1062731756,NULL,19,2),(8,11,'192.168.0.20',-1062731756,NULL,19,3);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
