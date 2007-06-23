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

/*Table structure for table `review` */

DROP TABLE IF EXISTS `review`;

CREATE TABLE `review` (
  `review_id` bigint(20) unsigned NOT NULL auto_increment,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `entity_id` smallint(5) unsigned NOT NULL default '0',
  `entity_pk_value` bigint(20) unsigned NOT NULL default '0',
  `status_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`review_id`),
  KEY `FK_REVIEW_ENTITY` (`entity_id`),
  KEY `FK_REVIEW_STATUS` (`status_id`),
  CONSTRAINT `FK_REVIEW_STATUS` FOREIGN KEY (`status_id`) REFERENCES `review_status` (`status_id`),
  CONSTRAINT `FK_REVIEW_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `review_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Review base information';

/*Data for the table `review` */

insert into `review` (`review_id`,`created_at`,`entity_id`,`entity_pk_value`,`status_id`) values (3,'2007-06-12 02:20:10',1,2650,2),(4,'2007-06-12 02:20:19',1,2650,2),(5,'2007-06-12 02:24:15',1,2650,2),(6,'2007-06-12 02:25:01',1,2650,2),(7,'2007-06-12 02:25:12',1,2650,2),(8,'2007-06-12 02:26:04',1,2650,2);

/*Table structure for table `review_detail` */

DROP TABLE IF EXISTS `review_detail`;

CREATE TABLE `review_detail` (
  `detail_id` bigint(20) unsigned NOT NULL auto_increment,
  `review_id` bigint(20) unsigned NOT NULL default '0',
  `store_id` smallint(6) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `detail` text NOT NULL,
  PRIMARY KEY  (`detail_id`),
  KEY `FK_REVIEW_DETAIL_REVIEW` (`review_id`),
  CONSTRAINT `FK_REVIEW_DETAIL_REVIEW` FOREIGN KEY (`review_id`) REFERENCES `review` (`review_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Review detail information';

/*Data for the table `review_detail` */

insert into `review_detail` (`detail_id`,`review_id`,`store_id`,`title`,`detail`) values (3,3,1,'test','test'),(4,4,1,'test','test'),(5,5,1,'test','test'),(6,6,1,'test','test'),(7,7,1,'test','test'),(8,8,1,'test','test');

/*Table structure for table `review_entity` */

DROP TABLE IF EXISTS `review_entity`;

CREATE TABLE `review_entity` (
  `entity_id` smallint(5) unsigned NOT NULL auto_increment,
  `entity_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Review entities';

/*Data for the table `review_entity` */

insert into `review_entity` (`entity_id`,`entity_code`) values (1,'product'),(2,'customer'),(3,'category');

/*Table structure for table `review_status` */

DROP TABLE IF EXISTS `review_status`;

CREATE TABLE `review_status` (
  `status_id` tinyint(3) unsigned NOT NULL auto_increment,
  `status_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Review statuses';

/*Data for the table `review_status` */

insert into `review_status` (`status_id`,`status_code`) values (1,'approved'),(2,'pending'),(3,'not approved');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
