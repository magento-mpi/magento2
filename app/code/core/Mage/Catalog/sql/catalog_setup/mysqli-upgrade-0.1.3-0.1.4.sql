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
  `input_html_param` varchar(255) default NULL,
  `data_saver` varchar(32) NOT NULL default '',
  `data_source` varchar(32) default NULL,
  `validation` varchar(64) default NULL,
  `input_format` varchar(32) default NULL,
  `output_format` varchar(32) default NULL,
  `required` tinyint(1) unsigned default NULL,
  `inheritable` tinyint(1) unsigned default NULL,
  `searchable` tinyint(1) unsigned default NULL,
  `filterable` tinyint(1) unsigned default NULL,
  `multiple` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product attributes defination';

/*Data for the table `catalog_product_attribute` */

insert into `catalog_product_attribute` (`attribute_id`,`attribute_code`,`data_input`,`input_html_param`,`data_saver`,`data_source`,`validation`,`input_format`,`output_format`,`required`,`inheritable`,`searchable`,`filterable`,`multiple`) values (1,'name','text',NULL,'varchar',NULL,NULL,NULL,NULL,1,1,1,0,0),(2,'description','textarea',NULL,'text',NULL,NULL,NULL,NULL,1,1,1,0,0),(3,'image','imagefile',NULL,'image',NULL,NULL,NULL,NULL,0,1,0,0,0),(4,'model','text',NULL,'attribute_varchar',NULL,NULL,NULL,NULL,1,1,1,0,0),(5,'price','text',NULL,'attribute_decimal',NULL,'decimal',NULL,NULL,1,1,0,1,0),(6,'cost','text',NULL,'attribute_decimal',NULL,'decimal',NULL,NULL,1,1,0,0,0),(7,'add_date','hidden',NULL,'attribute_date',NULL,NULL,NULL,NULL,1,0,0,0,0),(8,'weight','text',NULL,'attribute_decimal',NULL,'decimal',NULL,NULL,1,1,0,1,0),(9,'status','select',NULL,'attribute_int','product_status',NULL,NULL,NULL,1,0,1,1,0),(10,'manufacturers','select',NULL,'attribute_int','product_manufacturer',NULL,NULL,NULL,0,1,1,1,0);

/*Table structure for table `catalog_product_attribute_option` */

DROP TABLE IF EXISTS `catalog_product_attribute_option`;

CREATE TABLE `catalog_product_attribute_option` (
  `option_id` int(11) unsigned NOT NULL auto_increment,
  `website_id` smallint(6) unsigned default NULL,
  `option_type_id` smallint(6) unsigned default NULL,
  `option_value` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`option_id`),
  KEY `FK_ATTRIBUTE_OPTION_VALUE_WEBSITE` (`website_id`),
  KEY `FK_ATTRIBUTE_OPTION_VALUE_TYPE` (`option_type_id`),
  CONSTRAINT `FK_ATTRIBUTE_OPTION_VALUE_TYPE` FOREIGN KEY (`option_type_id`) REFERENCES `catalog_product_attribute_option_type` (`option_type_id`),
  CONSTRAINT `FK_ATTRIBUTE_OPTION_VALUE_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Attribute option values';

/*Data for the table `catalog_product_attribute_option` */

insert into `catalog_product_attribute_option` (`option_id`,`website_id`,`option_type_id`,`option_value`) values (1,1,1,'In stock'),(2,1,1,'Out of stock'),(3,1,1,'Disabled'),(4,1,2,'Videos'),(5,1,2,'Audios'),(6,1,3,'Man 1'),(7,1,3,'Man 2'),(8,1,3,'Man 3'),(9,1,3,'Man 4');

/*Table structure for table `catalog_product_attribute_option_type` */

DROP TABLE IF EXISTS `catalog_product_attribute_option_type`;

CREATE TABLE `catalog_product_attribute_option_type` (
  `option_type_id` smallint(6) unsigned NOT NULL auto_increment,
  `option_type_code` varchar(16) NOT NULL default '',
  PRIMARY KEY  (`option_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Types of attributes option';

/*Data for the table `catalog_product_attribute_option_type` */

insert into `catalog_product_attribute_option_type` (`option_type_id`,`option_type_code`) values (1,'status'),(2,'type'),(3,'manufacturer');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
