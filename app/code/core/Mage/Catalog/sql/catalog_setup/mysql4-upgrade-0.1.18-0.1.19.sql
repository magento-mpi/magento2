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

/*Table structure for table `catalog_product_attribute` */

DROP TABLE IF EXISTS `catalog_product_attribute`;

CREATE TABLE `catalog_product_attribute` (
  `attribute_id` smallint(6) unsigned NOT NULL auto_increment,
  `attribute_code` varchar(32) NOT NULL default '',
  `data_input` varchar(32) NOT NULL default '',
  `data_saver` varchar(32) default NULL,
  `data_source` varchar(32) default NULL,
  `data_type` varchar(32) NOT NULL default '',
  `validation` varchar(64) default NULL,
  `input_format` varchar(32) default NULL,
  `output_format` varchar(32) default NULL,
  `required` tinyint(1) unsigned NOT NULL default '1',
  `searchable` tinyint(1) unsigned NOT NULL default '0',
  `comparable` tinyint(1) unsigned NOT NULL default '1',
  `multiple` tinyint(1) unsigned NOT NULL default '0',
  `deletable` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attribute_id`),
  UNIQUE KEY `IDX_CODE` USING BTREE (`attribute_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product attributes defination';

/*Data for the table `catalog_product_attribute` */

insert into `catalog_product_attribute` (`attribute_id`,`attribute_code`,`data_input`,`data_saver`,`data_source`,`data_type`,`validation`,`input_format`,`output_format`,`required`,`searchable`,`comparable`,`multiple`,`deletable`) values (1,'name','text','',NULL,'varchar',NULL,NULL,NULL,1,1,0,0,0),(2,'description','textarea','',NULL,'text',NULL,NULL,NULL,1,1,0,0,0),(3,'image','imagefile','image',NULL,'varchar',NULL,NULL,NULL,0,0,0,0,0),(4,'model','text','',NULL,'varchar',NULL,NULL,NULL,1,1,0,0,0),(5,'price','text','',NULL,'decimal','decimal',NULL,NULL,1,0,1,0,0),(6,'cost','text','',NULL,'decimal','decimal',NULL,NULL,1,0,0,0,0),(7,'add_date','hidden','date',NULL,'date',NULL,NULL,NULL,1,0,0,0,0),(8,'weight','text','',NULL,'decimal','decimal',NULL,NULL,1,0,1,0,0),(9,'status','select','','status','int',NULL,NULL,NULL,1,1,1,0,0),(10,'manufacturer','select','','manufacturer','int',NULL,NULL,NULL,0,1,1,0,0),(11,'type','select','','type','int',NULL,NULL,NULL,0,1,1,0,0),(12,'default_category','select','','category','int',NULL,NULL,NULL,1,0,0,0,0);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
