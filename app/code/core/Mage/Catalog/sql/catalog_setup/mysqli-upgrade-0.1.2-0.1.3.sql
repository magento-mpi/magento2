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

/*Table structure for table `catalog_category_attribute` */

DROP TABLE IF EXISTS `catalog_category_attribute`;

CREATE TABLE `catalog_category_attribute` (
  `attribute_id` smallint(6) unsigned NOT NULL auto_increment,
  `attribute_code` varchar(32) NOT NULL default '',
  `data_input` varchar(32) NOT NULL default '',
  `data_saver` varchar(32) NOT NULL default '',
  `data_source` varchar(32) default NULL,
  `validation` varchar(64) default NULL,
  `required` tinyint(1) unsigned default NULL,
  `inheritable` tinyint(1) unsigned default NULL,
  `multiple` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Category attributes defination';

/*Data for the table `catalog_category_attribute` */

insert into `catalog_category_attribute` (`attribute_id`,`attribute_code`,`data_input`,`data_saver`,`data_source`,`validation`,`required`,`inheritable`,`multiple`) values (1,'name','text','attribute_value',NULL,NULL,1,0,0),(2,'description','textarea','attribute_value',NULL,NULL,1,1,0),(3,'main_image','imagefile','attribute_image',NULL,NULL,0,0,0),(4,'category_set','select','attribute_value','category_set',NULL,1,1,1),(5,'meta_title','text','attribute_value',NULL,NULL,0,1,0),(6,'meta_keywords','text','attribute_value',NULL,NULL,0,1,0),(7,'meta_description','text','attribute_value',NULL,NULL,0,1,0);

/*Table structure for table `catalog_category_attribute_in_set` */

DROP TABLE IF EXISTS `catalog_category_attribute_in_set`;

CREATE TABLE `catalog_category_attribute_in_set` (
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `category_attribute_set_id` smallint(6) unsigned NOT NULL default '0',
  `position` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attribute_id`,`category_attribute_set_id`),
  KEY `FK_CATEGORY_SET` (`category_attribute_set_id`),
  CONSTRAINT `catalog_category_attribute_in_set_ibfk_1` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_category_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Category attributes set';

/*Data for the table `catalog_category_attribute_in_set` */

insert into `catalog_category_attribute_in_set` (`attribute_id`,`category_attribute_set_id`,`position`) values (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,1,5),(6,1,6),(7,1,7);

/*Table structure for table `catalog_category_attribute_set` */

DROP TABLE IF EXISTS `catalog_category_attribute_set`;

CREATE TABLE `catalog_category_attribute_set` (
  `category_attribute_set_id` smallint(6) unsigned NOT NULL auto_increment,
  `category_attribute_set_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`category_attribute_set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Category attributes set';

/*Data for the table `catalog_category_attribute_set` */

insert into `catalog_category_attribute_set` (`category_attribute_set_id`,`category_attribute_set_code`) values (1,'Base category');

/*Table structure for table `catalog_product_attribute` */

DROP TABLE IF EXISTS `catalog_product_attribute`;

CREATE TABLE `catalog_product_attribute` (
  `attribute_id` smallint(6) unsigned NOT NULL auto_increment,
  `attribute_code` varchar(32) NOT NULL default '',
  `data_input` varchar(32) NOT NULL default '',
  `data_saver` varchar(32) NOT NULL default '',
  `data_source` varchar(32) default NULL,
  `validation` varchar(64) default NULL,
  `required` tinyint(1) unsigned default NULL,
  `inheritable` tinyint(1) unsigned default NULL,
  `searchable` tinyint(1) unsigned default NULL,
  `filterable` tinyint(1) unsigned default NULL,
  `multiple` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product attributes defination';

/*Data for the table `catalog_product_attribute` */

insert into `catalog_product_attribute` (`attribute_id`,`attribute_code`,`data_input`,`data_saver`,`data_source`,`validation`,`required`,`inheritable`,`searchable`,`filterable`,`multiple`) values (1,'name','text','attribute_varchar',NULL,NULL,1,1,1,0,0),(2,'description','textarea','attribute_text',NULL,NULL,1,1,1,0,0),(3,'image','imagefile','attribute_image',NULL,NULL,0,1,0,0,0),(4,'model','text','attribute_varchar',NULL,NULL,1,1,1,0,0),(5,'price','text','attribute_decimal',NULL,'decimal',1,1,0,1,0),(6,'cost','text','attribute_decimal',NULL,'decimal',1,1,0,0,0),(7,'add_date','hidden','attribute_date',NULL,NULL,1,0,0,0,0),(8,'weight','text','attribute_decimal',NULL,'decimal',1,1,0,1,0),(9,'status','select','attribute_int','product_status',NULL,1,0,1,1,0),(10,'manufacturers','select','attribute_int','product_manufacturer',NULL,0,1,1,1,0);

/*Table structure for table `catalog_product_attribute_group` */

DROP TABLE IF EXISTS `catalog_product_attribute_group`;

CREATE TABLE `catalog_product_attribute_group` (
  `product_attribute_group_id` smallint(6) unsigned NOT NULL auto_increment,
  `product_attribute_group_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`product_attribute_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product attributes groups';

/*Data for the table `catalog_product_attribute_group` */

insert into `catalog_product_attribute_group` (`product_attribute_group_id`,`product_attribute_group_code`) values (1,'base'),(2,'info'),(3,'gallery');

/*Table structure for table `catalog_product_attribute_in_group` */

DROP TABLE IF EXISTS `catalog_product_attribute_in_group`;

CREATE TABLE `catalog_product_attribute_in_group` (
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `product_attribute_group_id` smallint(6) unsigned NOT NULL default '0',
  `position` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attribute_id`,`product_attribute_group_id`),
  KEY `FK_ATTRIBUTE_GROUP_IN` (`product_attribute_group_id`),
  CONSTRAINT `FK_ATTRIBUTE_GROUP_IN` FOREIGN KEY (`product_attribute_group_id`) REFERENCES `catalog_product_attribute_group` (`product_attribute_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_GROUP_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_product_attribute_in_group` */

insert into `catalog_product_attribute_in_group` (`attribute_id`,`product_attribute_group_id`,`position`) values (1,1,1),(2,2,1),(3,3,1),(4,1,2),(5,1,3),(6,1,4),(8,1,5),(9,1,6),(10,2,2);

/*Table structure for table `catalog_product_attribute_in_set` */

DROP TABLE IF EXISTS `catalog_product_attribute_in_set`;

CREATE TABLE `catalog_product_attribute_in_set` (
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `product_attribute_set_id` smallint(6) unsigned NOT NULL default '0',
  `position` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attribute_id`),
  KEY `FK_PRODUCT_ATTRIBUTE_SET` USING BTREE (`product_attribute_set_id`),
  CONSTRAINT `FK_PRODUCT_ATTRIBUTE_SET` FOREIGN KEY (`product_attribute_set_id`) REFERENCES `catalog_product_attribute_set` (`product_attribute_set_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_SET_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product attributes in set';

/*Data for the table `catalog_product_attribute_in_set` */

insert into `catalog_product_attribute_in_set` (`attribute_id`,`product_attribute_set_id`,`position`) values (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,1,5),(6,1,6),(7,1,7),(8,1,8),(9,1,9),(10,1,10);


/*Table structure for table `catalog_product_attribute_set` */

DROP TABLE IF EXISTS `catalog_product_attribute_set`;

CREATE TABLE `catalog_product_attribute_set` (
  `product_attribute_set_id` smallint(6) unsigned NOT NULL auto_increment,
  `product_set_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  USING BTREE (`product_attribute_set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product attributes set';

/*Data for the table `catalog_product_attribute_set` */

insert into `catalog_product_attribute_set` (`product_attribute_set_id`,`product_set_code`) values (1,'Base product');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
