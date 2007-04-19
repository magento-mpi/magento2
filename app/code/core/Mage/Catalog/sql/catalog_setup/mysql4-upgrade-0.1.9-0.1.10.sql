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

/*Table structure for table `catalog_category` */

DROP TABLE IF EXISTS `catalog_category`;

CREATE TABLE `catalog_category` (
  `category_id` mediumint(9) unsigned NOT NULL auto_increment,
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `pid` mediumint(9) unsigned NOT NULL default '0',
  `left_key` mediumint(9) unsigned NOT NULL default '0',
  `right_key` mediumint(9) unsigned NOT NULL default '0',
  `level` smallint(4) unsigned NOT NULL default '0',
  `attribute_set_id` smallint(6) unsigned NOT NULL default '1',
  PRIMARY KEY  (`category_id`),
  KEY `FK_CATALOG_WEBSITE` (`website_id`),
  KEY `FK_CATEGORY_ATTRIBUTE_SET` (`attribute_set_id`),
  CONSTRAINT `FK_CATALOG_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`),
  CONSTRAINT `FK_CATEGORY_ATTRIBUTE_SET` FOREIGN KEY (`attribute_set_id`) REFERENCES `catalog_category_attribute_set` (`category_attribute_set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categories tree';

/*Data for the table `catalog_category` */

insert into `catalog_category` (`category_id`,`website_id`,`pid`,`left_key`,`right_key`,`level`,`attribute_set_id`) values (1,1,0,1,46,0,1),(2,1,1,2,23,1,1),(3,1,2,3,4,2,1),(4,1,2,5,6,2,1),(5,1,2,7,8,2,1),(6,1,2,9,10,2,1),(7,1,2,11,12,2,1),(8,1,2,13,14,2,1),(9,1,2,15,16,2,1),(10,1,2,17,18,2,1),(11,1,2,19,20,2,1),(12,1,2,21,22,2,1),(13,1,1,24,45,1,1),(14,1,13,25,26,2,1),(15,1,13,27,28,2,1),(16,1,13,29,30,2,1),(17,1,13,31,32,2,1),(18,1,13,33,34,2,1),(19,1,13,35,36,2,1),(20,1,13,37,38,2,1),(21,1,13,39,40,2,1),(22,1,13,41,42,2,1),(23,1,13,43,44,2,1);

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
  `code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`category_attribute_set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Category attributes set';

/*Data for the table `catalog_category_attribute_set` */

insert into `catalog_category_attribute_set` (`category_attribute_set_id`,`code`) values (1,'Base category');

/*Table structure for table `catalog_category_attribute_value` */

DROP TABLE IF EXISTS `catalog_category_attribute_value`;

CREATE TABLE `catalog_category_attribute_value` (
  `category_id` mediumint(9) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `attribute_value` text NOT NULL,
  PRIMARY KEY  (`category_id`,`website_id`,`attribute_id`),
  KEY `FK_CATEGORY_EXTENSION_WEBSITE` (`website_id`),
  KEY `FK_CATEGORY_ATTRIBUTE_VALUE` (`attribute_id`),
  CONSTRAINT `FK_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES `catalog_category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATEGORY_ATTRIBUTE_VALUE` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_category_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATEGORY_EXTENSION_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Categories detale information';

/*Data for the table `catalog_category_attribute_value` */

insert into `catalog_category_attribute_value` (`category_id`,`website_id`,`attribute_id`,`attribute_value`) values (1,1,1,'Root'),(1,1,2,'Root'),(2,1,1,'BROWSE BY TOPIC'),(2,1,2,'BROWSE BY TOPIC'),(3,1,1,'9-1-1'),(3,1,2,'9-1-1'),(4,1,1,'Bicycle Safety'),(4,1,2,'Bicycle Safety'),(5,1,1,'Bullying'),(5,1,2,'Bullying'),(6,1,1,'Drug Abuse'),(6,1,2,'Drug Abuse'),(7,1,1,'Halloween Safety'),(7,1,2,'Halloween Safety'),(8,1,1,'Internet Safety'),(8,1,2,'Internet Safety'),(9,1,1,'Law Enforcement'),(9,1,2,'Law Enforcement'),(10,1,1,'School Safety'),(10,1,2,'School Safety'),(11,1,1,'Senior Safety'),(11,1,2,'Senior Safety'),(12,1,1,'Stranger Awareness'),(12,1,2,'Stranger Awareness'),(13,1,1,'BROWSE BY PRODUCT'),(13,1,2,'BROWSE BY PRODUCT'),(14,1,1,'Bookmarks Store'),(14,1,2,'Bookmarks Store'),(15,1,1,'Brochures'),(15,1,2,'Brochures'),(16,1,1,'Coloring Books'),(16,1,2,'Coloring Books'),(17,1,1,'Evidence Packaging'),(17,1,2,'Evidence Packaging'),(18,1,1,'Litter/Literature Bags'),(18,1,2,'Litter/Literature Bags'),(19,1,1,'Pencils'),(19,1,2,'Pencils'),(20,1,1,'Reflectives'),(20,1,2,'Reflectives'),(21,1,1,'Safety Kits'),(21,1,2,'Safety Kits'),(22,1,1,'Slide Guides'),(22,1,2,'Slide Guides'),(23,1,1,'Spanish Products'),(23,1,2,'Spanish Products');

/*Table structure for table `catalog_category_filter` */

DROP TABLE IF EXISTS `catalog_category_filter`;

CREATE TABLE `catalog_category_filter` (
  `filter_id` mediumint(9) unsigned NOT NULL auto_increment,
  `category_id` mediumint(9) unsigned NOT NULL default '0',
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `use_option` tinyint(1) unsigned NOT NULL default '0',
  `position` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`filter_id`),
  KEY `IDX_CATEGORY_ATTRIBUTE` (`category_id`,`attribute_id`),
  KEY `FK_CATEGORY_FILTER_ATTRIBUTE` (`attribute_id`),
  CONSTRAINT `FK_CATEGORY_FILTER_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATEGORY_FILTER_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES `catalog_category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

/*Data for the table `catalog_category_filter` */

insert into `catalog_category_filter` (`filter_id`,`category_id`,`attribute_id`,`use_option`,`position`) values (1,5,11,1,1),(2,6,10,1,1),(3,5,5,0,2);

/*Table structure for table `catalog_category_filter_value` */

DROP TABLE IF EXISTS `catalog_category_filter_value`;

CREATE TABLE `catalog_category_filter_value` (
  `value_id` int(11) unsigned NOT NULL auto_increment,
  `filter_id` mediumint(9) unsigned NOT NULL default '0',
  `value_from` int(11) default NULL,
  `value_to` int(11) default NULL,
  `position` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_CATALOG_CATEGORY_VALUE_FILTER` (`filter_id`),
  CONSTRAINT `FK_CATALOG_CATEGORY_VALUE_FILTER` FOREIGN KEY (`filter_id`) REFERENCES `catalog_category_filter` (`filter_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Category filter values';

/*Data for the table `catalog_category_filter_value` */

insert into `catalog_category_filter_value` (`value_id`,`filter_id`,`value_from`,`value_to`,`position`) values (1,3,10,50,1),(2,3,50,100,2),(3,3,100,-1,3);

/*Table structure for table `catalog_category_product` */

DROP TABLE IF EXISTS `catalog_category_product`;

CREATE TABLE `catalog_category_product` (
  `category_id` mediumint(9) unsigned NOT NULL default '0',
  `product_id` int(11) unsigned NOT NULL default '0',
  `position` mediumint(9) unsigned NOT NULL default '0',
  PRIMARY KEY  (`category_id`,`product_id`),
  KEY `FK_CATEGORY_PRODUCT` (`product_id`),
  CONSTRAINT `FK_CATEGORY_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES `catalog_category` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Products in categories';

/*Data for the table `catalog_category_product` */

insert into `catalog_category_product` (`category_id`,`product_id`,`position`) values (3,413,1),(3,483,1),(4,413,1),(4,444,1),(4,451,1),(4,480,1),(4,483,1),(5,409,1),(5,430,1),(5,434,1),(5,437,1),(5,444,1),(5,451,1),(5,457,1),(5,465,1),(5,467,1),(5,471,1),(5,480,1),(6,409,1),(6,427,1),(6,430,1),(6,434,1),(6,437,1),(6,456,1),(6,457,1),(6,463,1),(6,465,1),(6,467,1),(6,471,1),(6,498,1),(6,502,1),(7,412,1),(7,427,1),(7,438,1),(7,442,1),(7,452,1),(7,456,1),(7,463,1),(7,498,1),(7,502,1),(8,412,1),(8,414,1),(8,418,1),(8,425,1),(8,438,1),(8,441,1),(8,442,1),(8,452,1),(8,469,1),(8,494,1),(8,503,1),(9,411,1),(9,414,1),(9,418,1),(9,421,1),(9,425,1),(9,441,1),(9,460,1),(9,469,1),(9,481,1),(9,488,1),(9,491,1),(9,494,1),(9,503,1),(10,407,1),(10,411,1),(10,421,1),(10,429,1),(10,440,1),(10,460,1),(10,466,1),(10,474,1),(10,479,1),(10,481,1),(10,488,1),(10,491,1),(10,497,1),(11,407,1),(11,429,1),(11,431,1),(11,433,1),(11,440,1),(11,455,1),(11,466,1),(11,468,1),(11,474,1),(11,479,1),(11,495,1),(11,497,1),(11,500,1),(12,405,1),(12,408,1),(12,422,1),(12,426,1),(12,431,1),(12,433,1),(12,455,1),(12,462,1),(12,468,1),(12,470,1),(12,476,1),(12,484,1),(12,489,1),(12,492,1),(12,493,1),(12,495,1),(12,500,1),(13,405,1),(13,406,1),(13,408,1),(13,420,1),(13,422,1),(13,423,1),(13,424,1),(13,426,1),(13,439,1),(13,459,1),(13,462,1),(13,464,1),(13,470,1);
insert into `catalog_category_product` (`category_id`,`product_id`,`position`) values (13,476,1),(13,484,1),(13,489,1),(13,492,1),(13,493,1),(14,406,1),(14,410,1),(14,420,1),(14,423,1),(14,424,1),(14,439,1),(14,445,1),(14,459,1),(14,464,1),(14,496,1),(15,410,1),(15,445,1),(15,477,1),(15,496,1),(16,419,1),(16,461,1),(16,477,1),(16,485,1),(17,419,1),(17,428,1),(17,461,1),(17,485,1),(18,428,1),(18,453,1),(18,490,1),(18,504,1),(19,448,1),(19,450,1),(19,453,1),(19,472,1),(19,487,1),(19,490,1),(19,504,1),(20,415,1),(20,417,1),(20,446,1),(20,447,1),(20,448,1),(20,449,1),(20,450,1),(20,458,1),(20,472,1),(20,473,1),(20,482,1),(20,486,1),(20,487,1),(20,499,1),(21,415,1),(21,417,1),(21,443,1),(21,446,1),(21,447,1),(21,449,1),(21,454,1),(21,458,1),(21,473,1),(21,482,1),(21,486,1),(21,499,1),(22,416,1),(22,432,1),(22,435,1),(22,436,1),(22,443,1),(22,454,1),(22,475,1),(22,478,1),(22,501,1),(23,416,1),(23,432,1),(23,435,1),(23,436,1),(23,475,1),(23,478,1),(23,501,1);

/*Table structure for table `catalog_product` */

DROP TABLE IF EXISTS `catalog_product`;

CREATE TABLE `catalog_product` (
  `product_id` int(11) unsigned NOT NULL auto_increment,
  `create_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `set_id` smallint(6) unsigned NOT NULL default '1',
  PRIMARY KEY  (`product_id`),
  KEY `IDX_PRODUCT_ATTRIBUTE_SET_ID` USING BTREE (`set_id`),
  CONSTRAINT `FK_PRODUCT_ATTRIBUTE_SET` FOREIGN KEY (`set_id`) REFERENCES `catalog_product_attribute_set` (`set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Products';

/*Data for the table `catalog_product` */

insert into `catalog_product` (`product_id`,`create_date`,`set_id`) values (405,'2007-03-21 00:45:53',1),(406,'2007-03-21 00:45:53',1),(407,'2007-03-21 00:45:54',1),(408,'2007-03-21 00:45:54',1),(409,'2007-03-21 00:45:54',1),(410,'2007-03-21 00:45:54',1),(411,'2007-03-21 00:45:55',1),(412,'2007-03-21 00:45:55',1),(413,'2007-03-21 00:45:56',1),(414,'2007-03-21 00:45:56',1),(415,'2007-03-21 00:45:56',1),(416,'2007-03-21 00:45:56',1),(417,'2007-03-21 00:45:57',1),(418,'2007-03-21 00:45:57',1),(419,'2007-03-21 00:45:57',1),(420,'2007-03-21 00:45:58',1),(421,'2007-03-21 00:45:58',1),(422,'2007-03-21 00:45:58',1),(423,'2007-03-21 00:45:58',1),(424,'2007-03-21 00:45:59',1),(425,'2007-03-21 00:45:59',1),(426,'2007-03-21 00:45:59',1),(427,'2007-03-21 00:45:59',1),(428,'2007-03-21 00:46:00',1),(429,'2007-03-21 00:46:00',1),(430,'2007-03-21 00:46:00',1),(431,'2007-03-21 00:46:00',1),(432,'2007-03-21 00:46:01',1),(433,'2007-03-21 00:46:01',1),(434,'2007-03-21 00:46:01',1),(435,'2007-03-21 00:46:02',1),(436,'2007-03-21 00:46:02',1),(437,'2007-03-21 00:46:02',1),(438,'2007-03-21 00:46:03',1),(439,'2007-03-21 00:46:03',1),(440,'2007-03-21 00:46:03',1),(441,'2007-03-21 00:46:04',1),(442,'2007-03-21 00:46:04',1),(443,'2007-03-21 00:46:04',1);
insert into `catalog_product` (`product_id`,`create_date`,`set_id`) values (444,'2007-03-21 00:46:04',1),(445,'2007-03-21 00:46:05',1),(446,'2007-03-21 00:46:05',1),(447,'2007-03-21 00:46:06',1),(448,'2007-03-21 00:46:06',1),(449,'2007-03-21 00:46:07',1),(450,'2007-03-21 00:46:07',1),(451,'2007-03-21 00:46:08',1),(452,'2007-03-21 00:46:08',1),(453,'2007-03-21 00:46:09',1),(454,'2007-03-21 00:46:09',1),(455,'2007-03-21 00:46:10',1),(456,'2007-03-21 00:46:10',1),(457,'2007-03-21 00:46:11',1),(458,'2007-03-21 00:46:11',1),(459,'2007-03-21 00:46:12',1),(460,'2007-03-21 00:46:12',1),(461,'2007-03-21 00:46:13',1),(462,'2007-03-21 00:46:13',1),(463,'2007-03-21 00:46:14',1),(464,'2007-03-21 00:46:14',1),(465,'2007-03-21 00:46:15',1),(466,'2007-03-21 00:46:16',1),(467,'2007-03-21 00:46:16',1),(468,'2007-03-21 00:46:17',1),(469,'2007-03-21 00:46:17',1),(470,'2007-03-21 00:46:18',1),(471,'2007-03-21 00:46:18',1),(472,'2007-03-21 00:46:19',1),(473,'2007-03-21 00:46:19',1),(474,'2007-03-21 00:46:20',1),(475,'2007-03-21 00:46:20',1),(476,'2007-03-21 00:46:21',1),(477,'2007-03-21 00:46:21',1),(478,'2007-03-21 00:46:22',1),(479,'2007-03-21 00:46:22',1),(480,'2007-03-21 00:46:23',1),(481,'2007-03-21 00:46:24',1),(482,'2007-03-21 00:46:24',1);
insert into `catalog_product` (`product_id`,`create_date`,`set_id`) values (483,'2007-03-21 00:46:25',1),(484,'2007-03-21 00:46:25',1),(485,'2007-03-21 00:46:25',1),(486,'2007-03-21 00:46:26',1),(487,'2007-03-21 00:46:26',1),(488,'2007-03-21 00:46:27',1),(489,'2007-03-21 00:46:27',1),(490,'2007-03-21 00:46:27',1),(491,'2007-03-21 00:46:28',1),(492,'2007-03-21 00:46:28',1),(493,'2007-03-21 00:46:29',1),(494,'2007-03-21 00:46:29',1),(495,'2007-03-21 00:46:30',1),(496,'2007-03-21 00:46:30',1),(497,'2007-03-21 00:46:31',1),(498,'2007-03-21 00:46:31',1),(499,'2007-03-21 00:46:32',1),(500,'2007-03-21 00:46:32',1),(501,'2007-03-21 00:46:32',1),(502,'2007-03-21 00:46:33',1),(503,'2007-03-21 00:46:33',1),(504,'2007-03-21 00:46:33',1);

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
  `comparale` tinyint(1) unsigned NOT NULL default '1',
  `multiple` tinyint(1) unsigned NOT NULL default '0',
  `delitable` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attribute_id`),
  UNIQUE KEY `attribute_code` (`attribute_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product attributes defination';

/*Data for the table `catalog_product_attribute` */

insert into `catalog_product_attribute` (`attribute_id`,`attribute_code`,`data_input`,`data_saver`,`data_source`,`data_type`,`validation`,`input_format`,`output_format`,`required`,`searchable`,`comparale`,`multiple`,`delitable`) values (1,'name','text','',NULL,'varchar',NULL,NULL,NULL,1,1,0,0,0),(2,'description','textarea','',NULL,'text',NULL,NULL,NULL,1,1,0,0,0),(3,'image','imagefile','image',NULL,'varchar',NULL,NULL,NULL,0,0,0,0,0),(4,'model','text','',NULL,'varchar',NULL,NULL,NULL,1,1,0,0,0),(5,'price','text','',NULL,'decimal','decimal',NULL,NULL,1,0,1,1,0),(6,'cost','text','',NULL,'decimal','decimal',NULL,NULL,1,0,0,0,0),(7,'add_date','hidden','date',NULL,'date',NULL,NULL,NULL,1,0,0,0,0),(8,'weight','text','',NULL,'decimal','decimal',NULL,NULL,1,0,1,0,0),(9,'status','select','','product_status','int',NULL,NULL,NULL,1,1,1,0,0),(10,'manufacturer','select','','product_manufacturer','int',NULL,NULL,NULL,0,1,1,0,0),(11,'type','select','','product_type','int',NULL,NULL,NULL,0,1,1,0,0),(12,'default_category_id','select','','product_category','int',NULL,NULL,NULL,1,0,0,0,0);

/*Table structure for table `catalog_product_attribute_date` */

DROP TABLE IF EXISTS `catalog_product_attribute_date`;

CREATE TABLE `catalog_product_attribute_date` (
  `value_id` bigint(16) unsigned NOT NULL auto_increment,
  `product_id` int(11) unsigned NOT NULL default '0',
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `attribute_value` datetime NOT NULL default '0000-00-00 00:00:00',
  `parent_id` bigint(16) unsigned NOT NULL default '0',
  `is_inherit` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_DATE` (`attribute_id`),
  KEY `FK_WEBSITE_DATE` (`website_id`),
  KEY `IDX_VALUE_JOIN` (`product_id`,`attribute_id`,`website_id`),
  CONSTRAINT `FK_ATTRIBUTE_DATE` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_DATE` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_WEBSITE_DATE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Date attribute value';

/*Data for the table `catalog_product_attribute_date` */

insert into `catalog_product_attribute_date` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (83,405,7,1,'2007-03-21 00:45:53',0,1),(84,405,7,2,'2007-03-21 00:45:53',0,1),(85,406,7,1,'2007-03-21 00:45:53',0,1),(86,406,7,2,'2007-03-21 00:45:53',0,1),(87,407,7,1,'2007-03-21 00:45:54',0,1),(88,407,7,2,'2007-03-21 00:45:54',0,1),(89,408,7,1,'2007-03-21 00:45:54',0,1),(90,408,7,2,'2007-03-21 00:45:54',0,1),(91,409,7,1,'2007-03-21 00:45:54',0,1),(92,409,7,2,'2007-03-21 00:45:54',0,1),(93,410,7,1,'2007-03-21 00:45:55',0,1),(94,410,7,2,'2007-03-21 00:45:55',0,1),(95,411,7,1,'2007-03-21 00:45:55',0,1),(96,411,7,2,'2007-03-21 00:45:55',0,1),(97,412,7,1,'2007-03-21 00:45:55',0,1),(98,412,7,2,'2007-03-21 00:45:56',0,1),(99,413,7,1,'2007-03-21 00:45:56',0,1),(100,413,7,2,'2007-03-21 00:45:56',0,1),(101,414,7,1,'2007-03-21 00:45:56',0,1),(102,414,7,2,'2007-03-21 00:45:56',0,1),(103,415,7,1,'2007-03-21 00:45:56',0,1),(104,415,7,2,'2007-03-21 00:45:56',0,1),(105,416,7,1,'2007-03-21 00:45:57',0,1),(106,416,7,2,'2007-03-21 00:45:57',0,1),(107,417,7,1,'2007-03-21 00:45:57',0,1),(108,417,7,2,'2007-03-21 00:45:57',0,1),(109,418,7,1,'2007-03-21 00:45:57',0,1),(110,418,7,2,'2007-03-21 00:45:57',0,1),(111,419,7,1,'2007-03-21 00:45:57',0,1),(112,419,7,2,'2007-03-21 00:45:57',0,1),(113,420,7,1,'2007-03-21 00:45:58',0,1);
insert into `catalog_product_attribute_date` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (114,420,7,2,'2007-03-21 00:45:58',0,1),(115,421,7,1,'2007-03-21 00:45:58',0,1),(116,421,7,2,'2007-03-21 00:45:58',0,1),(117,422,7,1,'2007-03-21 00:45:58',0,1),(118,422,7,2,'2007-03-21 00:45:58',0,1),(119,423,7,1,'2007-03-21 00:45:58',0,1),(120,423,7,2,'2007-03-21 00:45:59',0,1),(121,424,7,1,'2007-03-21 00:45:59',0,1),(122,424,7,2,'2007-03-21 00:45:59',0,1),(123,425,7,1,'2007-03-21 00:45:59',0,1),(124,425,7,2,'2007-03-21 00:45:59',0,1),(125,426,7,1,'2007-03-21 00:45:59',0,1),(126,426,7,2,'2007-03-21 00:45:59',0,1),(127,427,7,1,'2007-03-21 00:46:00',0,1),(128,427,7,2,'2007-03-21 00:46:00',0,1),(129,428,7,1,'2007-03-21 00:46:00',0,1),(130,428,7,2,'2007-03-21 00:46:00',0,1),(131,429,7,1,'2007-03-21 00:46:00',0,1),(132,429,7,2,'2007-03-21 00:46:00',0,1),(133,430,7,1,'2007-03-21 00:46:00',0,1),(134,430,7,2,'2007-03-21 00:46:00',0,1),(135,431,7,1,'2007-03-21 00:46:01',0,1),(136,431,7,2,'2007-03-21 00:46:01',0,1),(137,432,7,1,'2007-03-21 00:46:01',0,1),(138,432,7,2,'2007-03-21 00:46:01',0,1),(139,433,7,1,'2007-03-21 00:46:01',0,1),(140,433,7,2,'2007-03-21 00:46:01',0,1),(141,434,7,1,'2007-03-21 00:46:01',0,1),(142,434,7,2,'2007-03-21 00:46:02',0,1),(143,435,7,1,'2007-03-21 00:46:02',0,1),(144,435,7,2,'2007-03-21 00:46:02',0,1),(145,436,7,1,'2007-03-21 00:46:02',0,1);
insert into `catalog_product_attribute_date` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (146,436,7,2,'2007-03-21 00:46:02',0,1),(147,437,7,1,'2007-03-21 00:46:02',0,1),(148,437,7,2,'2007-03-21 00:46:03',0,1),(149,438,7,1,'2007-03-21 00:46:03',0,1),(150,438,7,2,'2007-03-21 00:46:03',0,1),(151,439,7,1,'2007-03-21 00:46:03',0,1),(152,439,7,2,'2007-03-21 00:46:03',0,1),(153,440,7,1,'2007-03-21 00:46:03',0,1),(154,440,7,2,'2007-03-21 00:46:03',0,1),(155,441,7,1,'2007-03-21 00:46:04',0,1),(156,441,7,2,'2007-03-21 00:46:04',0,1),(157,442,7,1,'2007-03-21 00:46:04',0,1),(158,442,7,2,'2007-03-21 00:46:04',0,1),(159,443,7,1,'2007-03-21 00:46:04',0,1),(160,443,7,2,'2007-03-21 00:46:04',0,1),(161,444,7,1,'2007-03-21 00:46:05',0,1),(162,444,7,2,'2007-03-21 00:46:05',0,1),(163,445,7,1,'2007-03-21 00:46:05',0,1),(164,445,7,2,'2007-03-21 00:46:05',0,1),(165,446,7,1,'2007-03-21 00:46:06',0,1),(166,446,7,2,'2007-03-21 00:46:06',0,1),(167,447,7,1,'2007-03-21 00:46:06',0,1),(168,447,7,2,'2007-03-21 00:46:06',0,1),(169,448,7,1,'2007-03-21 00:46:07',0,1),(170,448,7,2,'2007-03-21 00:46:07',0,1),(171,449,7,1,'2007-03-21 00:46:07',0,1),(172,449,7,2,'2007-03-21 00:46:07',0,1),(173,450,7,1,'2007-03-21 00:46:08',0,1),(174,450,7,2,'2007-03-21 00:46:08',0,1),(175,451,7,1,'2007-03-21 00:46:08',0,1),(176,451,7,2,'2007-03-21 00:46:08',0,1),(177,452,7,1,'2007-03-21 00:46:09',0,1);
insert into `catalog_product_attribute_date` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (178,452,7,2,'2007-03-21 00:46:09',0,1),(179,453,7,1,'2007-03-21 00:46:09',0,1),(180,453,7,2,'2007-03-21 00:46:09',0,1),(181,454,7,1,'2007-03-21 00:46:09',0,1),(182,454,7,2,'2007-03-21 00:46:10',0,1),(183,455,7,1,'2007-03-21 00:46:10',0,1),(184,455,7,2,'2007-03-21 00:46:10',0,1),(185,456,7,1,'2007-03-21 00:46:11',0,1),(186,456,7,2,'2007-03-21 00:46:11',0,1),(187,457,7,1,'2007-03-21 00:46:11',0,1),(188,457,7,2,'2007-03-21 00:46:11',0,1),(189,458,7,1,'2007-03-21 00:46:11',0,1),(190,458,7,2,'2007-03-21 00:46:12',0,1),(191,459,7,1,'2007-03-21 00:46:12',0,1),(192,459,7,2,'2007-03-21 00:46:12',0,1),(193,460,7,1,'2007-03-21 00:46:12',0,1),(194,460,7,2,'2007-03-21 00:46:13',0,1),(195,461,7,1,'2007-03-21 00:46:13',0,1),(196,461,7,2,'2007-03-21 00:46:13',0,1),(197,462,7,1,'2007-03-21 00:46:13',0,1),(198,462,7,2,'2007-03-21 00:46:14',0,1),(199,463,7,1,'2007-03-21 00:46:14',0,1),(200,463,7,2,'2007-03-21 00:46:14',0,1),(201,464,7,1,'2007-03-21 00:46:15',0,1),(202,464,7,2,'2007-03-21 00:46:15',0,1),(203,465,7,1,'2007-03-21 00:46:15',0,1),(204,465,7,2,'2007-03-21 00:46:15',0,1),(205,466,7,1,'2007-03-21 00:46:16',0,1),(206,466,7,2,'2007-03-21 00:46:16',0,1),(207,467,7,1,'2007-03-21 00:46:16',0,1),(208,467,7,2,'2007-03-21 00:46:16',0,1),(209,468,7,1,'2007-03-21 00:46:17',0,1);
insert into `catalog_product_attribute_date` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (210,468,7,2,'2007-03-21 00:46:17',0,1),(211,469,7,1,'2007-03-21 00:46:17',0,1),(212,469,7,2,'2007-03-21 00:46:18',0,1),(213,470,7,1,'2007-03-21 00:46:18',0,1),(214,470,7,2,'2007-03-21 00:46:18',0,1),(215,471,7,1,'2007-03-21 00:46:18',0,1),(216,471,7,2,'2007-03-21 00:46:19',0,1),(217,472,7,1,'2007-03-21 00:46:19',0,1),(218,472,7,2,'2007-03-21 00:46:19',0,1),(219,473,7,1,'2007-03-21 00:46:19',0,1),(220,473,7,2,'2007-03-21 00:46:20',0,1),(221,474,7,1,'2007-03-21 00:46:20',0,1),(222,474,7,2,'2007-03-21 00:46:20',0,1),(223,475,7,1,'2007-03-21 00:46:20',0,1),(224,475,7,2,'2007-03-21 00:46:21',0,1),(225,476,7,1,'2007-03-21 00:46:21',0,1),(226,476,7,2,'2007-03-21 00:46:21',0,1),(227,477,7,1,'2007-03-21 00:46:22',0,1),(228,477,7,2,'2007-03-21 00:46:22',0,1),(229,478,7,1,'2007-03-21 00:46:22',0,1),(230,478,7,2,'2007-03-21 00:46:22',0,1),(231,479,7,1,'2007-03-21 00:46:23',0,1),(232,479,7,2,'2007-03-21 00:46:23',0,1),(233,480,7,1,'2007-03-21 00:46:23',0,1),(234,480,7,2,'2007-03-21 00:46:23',0,1),(235,481,7,1,'2007-03-21 00:46:24',0,1),(236,481,7,2,'2007-03-21 00:46:24',0,1),(237,482,7,1,'2007-03-21 00:46:24',0,1),(238,482,7,2,'2007-03-21 00:46:25',0,1),(239,483,7,1,'2007-03-21 00:46:25',0,1),(240,483,7,2,'2007-03-21 00:46:25',0,1),(241,484,7,1,'2007-03-21 00:46:25',0,1);
insert into `catalog_product_attribute_date` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (242,484,7,2,'2007-03-21 00:46:25',0,1),(243,485,7,1,'2007-03-21 00:46:25',0,1),(244,485,7,2,'2007-03-21 00:46:26',0,1),(245,486,7,1,'2007-03-21 00:46:26',0,1),(246,486,7,2,'2007-03-21 00:46:26',0,1),(247,487,7,1,'2007-03-21 00:46:26',0,1),(248,487,7,2,'2007-03-21 00:46:26',0,1),(249,488,7,1,'2007-03-21 00:46:27',0,1),(250,488,7,2,'2007-03-21 00:46:27',0,1),(251,489,7,1,'2007-03-21 00:46:27',0,1),(252,489,7,2,'2007-03-21 00:46:27',0,1),(253,490,7,1,'2007-03-21 00:46:28',0,1),(254,490,7,2,'2007-03-21 00:46:28',0,1),(255,491,7,1,'2007-03-21 00:46:28',0,1),(256,491,7,2,'2007-03-21 00:46:28',0,1),(257,492,7,1,'2007-03-21 00:46:28',0,1),(258,492,7,2,'2007-03-21 00:46:29',0,1),(259,493,7,1,'2007-03-21 00:46:29',0,1),(260,493,7,2,'2007-03-21 00:46:29',0,1),(261,494,7,1,'2007-03-21 00:46:29',0,1),(262,494,7,2,'2007-03-21 00:46:30',0,1),(263,495,7,1,'2007-03-21 00:46:30',0,1),(264,495,7,2,'2007-03-21 00:46:30',0,1),(265,496,7,1,'2007-03-21 00:46:31',0,1),(266,496,7,2,'2007-03-21 00:46:31',0,1),(267,497,7,1,'2007-03-21 00:46:31',0,1),(268,497,7,2,'2007-03-21 00:46:31',0,1),(269,498,7,1,'2007-03-21 00:46:32',0,1),(270,498,7,2,'2007-03-21 00:46:32',0,1),(271,499,7,1,'2007-03-21 00:46:32',0,1),(272,499,7,2,'2007-03-21 00:46:32',0,1),(273,500,7,1,'2007-03-21 00:46:32',0,1);
insert into `catalog_product_attribute_date` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (274,500,7,2,'2007-03-21 00:46:32',0,1),(275,501,7,1,'2007-03-21 00:46:32',0,1),(276,501,7,2,'2007-03-21 00:46:33',0,1),(277,502,7,1,'2007-03-21 00:46:33',0,1),(278,502,7,2,'2007-03-21 00:46:33',0,1),(279,503,7,1,'2007-03-21 00:46:33',0,1),(280,503,7,2,'2007-03-21 00:46:33',0,1),(281,504,7,1,'2007-03-21 00:46:33',0,1),(282,504,7,2,'2007-03-21 00:46:34',0,1);

/*Table structure for table `catalog_product_attribute_decimal` */

DROP TABLE IF EXISTS `catalog_product_attribute_decimal`;

CREATE TABLE `catalog_product_attribute_decimal` (
  `value_id` bigint(16) unsigned NOT NULL auto_increment,
  `product_id` int(11) unsigned NOT NULL default '0',
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `attribute_value` decimal(12,4) NOT NULL default '0.0000',
  `attribute_qty` int(11) NOT NULL default '0',
  `parent_id` bigint(16) unsigned NOT NULL default '0',
  `is_inherit` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_DECIMAL` (`attribute_id`),
  KEY `FK_WEBSITE_DECIMAL` (`website_id`),
  KEY `IDX_VALUE_JOIN` (`product_id`,`attribute_id`,`website_id`),
  CONSTRAINT `FK_ATTRIBUTE_DECIMAL` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_DECIMAL` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_WEBSITE_DECIMAL` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Decimal values for product attributes';

/*Data for the table `catalog_product_attribute_decimal` */

insert into `catalog_product_attribute_decimal` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`attribute_qty`,`parent_id`,`is_inherit`) values (247,405,5,1,58.0000,1,0,1),(248,405,6,1,98.0000,1,0,1),(249,405,8,1,77.0000,1,0,1),(250,405,5,2,95.0000,1,0,1),(251,405,6,2,35.0000,1,0,1),(252,405,8,2,80.0000,1,0,1),(253,406,5,1,48.0000,1,0,1),(254,406,6,1,37.0000,1,0,1),(255,406,8,1,14.0000,1,0,1),(256,406,5,2,65.0000,1,0,1),(257,406,6,2,42.0000,1,0,1),(258,406,8,2,65.0000,1,0,1),(259,407,5,1,73.0000,1,0,1),(260,407,6,1,49.0000,1,0,1),(261,407,8,1,33.0000,1,0,1),(262,407,5,2,97.0000,1,0,1),(263,407,6,2,3.0000,1,0,1),(264,407,8,2,3.0000,1,0,1),(265,408,5,1,59.0000,1,0,1),(266,408,6,1,87.0000,1,0,1),(267,408,8,1,81.0000,1,0,1),(268,408,5,2,37.0000,1,0,1),(269,408,6,2,14.0000,1,0,1),(270,408,8,2,10.0000,1,0,1),(271,409,5,1,93.0000,1,0,1),(272,409,6,1,76.0000,1,0,1),(273,409,8,1,42.0000,1,0,1),(274,409,5,2,39.0000,1,0,1),(275,409,6,2,92.0000,1,0,1),(276,409,8,2,88.0000,1,0,1),(277,410,5,1,93.0000,1,0,1),(278,410,6,1,86.0000,1,0,1),(279,410,8,1,7.0000,1,0,1),(280,410,5,2,41.0000,1,0,1),(281,410,6,2,55.0000,1,0,1),(282,410,8,2,98.0000,1,0,1),(283,411,5,1,90.0000,1,0,1),(284,411,6,1,11.0000,1,0,1),(285,411,8,1,52.0000,1,0,1),(286,411,5,2,42.0000,1,0,1),(287,411,6,2,88.0000,1,0,1),(288,411,8,2,34.0000,1,0,1),(289,412,5,1,97.0000,1,0,1),(290,412,6,1,42.0000,1,0,1),(291,412,8,1,13.0000,1,0,1),(292,412,5,2,74.0000,1,0,1),(293,412,6,2,26.0000,1,0,1);
insert into `catalog_product_attribute_decimal` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`attribute_qty`,`parent_id`,`is_inherit`) values (294,412,8,2,1.0000,1,0,1),(295,413,5,1,86.0000,1,0,1),(296,413,6,1,40.0000,1,0,1),(297,413,8,1,59.0000,1,0,1),(298,413,5,2,78.0000,1,0,1),(299,413,6,2,24.0000,1,0,1),(300,413,8,2,87.0000,1,0,1),(301,414,5,1,66.0000,1,0,1),(302,414,6,1,15.0000,1,0,1),(303,414,8,1,71.0000,1,0,1),(304,414,5,2,82.0000,1,0,1),(305,414,6,2,35.0000,1,0,1),(306,414,8,2,40.0000,1,0,1),(307,415,5,1,53.0000,1,0,1),(308,415,6,1,97.0000,1,0,1),(309,415,8,1,11.0000,1,0,1),(310,415,5,2,70.0000,1,0,1),(311,415,6,2,85.0000,1,0,1),(312,415,8,2,43.0000,1,0,1),(313,416,5,1,48.0000,1,0,1),(314,416,6,1,15.0000,1,0,1),(315,416,8,1,3.0000,1,0,1),(316,416,5,2,63.0000,1,0,1),(317,416,6,2,43.0000,1,0,1),(318,416,8,2,87.0000,1,0,1),(319,417,5,1,13.0000,1,0,1),(320,417,6,1,67.0000,1,0,1),(321,417,8,1,4.0000,1,0,1),(322,417,5,2,89.0000,1,0,1),(323,417,6,2,5.0000,1,0,1),(324,417,8,2,49.0000,1,0,1),(325,418,5,1,41.0000,1,0,1),(326,418,6,1,100.0000,1,0,1),(327,418,8,1,81.0000,1,0,1),(328,418,5,2,63.0000,1,0,1),(329,418,6,2,59.0000,1,0,1),(330,418,8,2,65.0000,1,0,1),(331,419,5,1,37.0000,1,0,1),(332,419,6,1,83.0000,1,0,1),(333,419,8,1,85.0000,1,0,1),(334,419,5,2,57.0000,1,0,1),(335,419,6,2,68.0000,1,0,1),(336,419,8,2,4.0000,1,0,1),(337,420,5,1,90.0000,1,0,1),(338,420,6,1,80.0000,1,0,1),(339,420,8,1,26.0000,1,0,1),(340,420,5,2,79.0000,1,0,1),(341,420,6,2,41.0000,1,0,1);
insert into `catalog_product_attribute_decimal` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`attribute_qty`,`parent_id`,`is_inherit`) values (342,420,8,2,45.0000,1,0,1),(343,421,5,1,48.0000,1,0,1),(344,421,6,1,30.0000,1,0,1),(345,421,8,1,51.0000,1,0,1),(346,421,5,2,46.0000,1,0,1),(347,421,6,2,10.0000,1,0,1),(348,421,8,2,52.0000,1,0,1),(349,422,5,1,93.0000,1,0,1),(350,422,6,1,15.0000,1,0,1),(351,422,8,1,12.0000,1,0,1),(352,422,5,2,58.0000,1,0,1),(353,422,6,2,100.0000,1,0,1),(354,422,8,2,49.0000,1,0,1),(355,423,5,1,17.0000,1,0,1),(356,423,6,1,42.0000,1,0,1),(357,423,8,1,48.0000,1,0,1),(358,423,5,2,77.0000,1,0,1),(359,423,6,2,13.0000,1,0,1),(360,423,8,2,92.0000,1,0,1),(361,424,5,1,97.0000,1,0,1),(362,424,6,1,14.0000,1,0,1),(363,424,8,1,59.0000,1,0,1),(364,424,5,2,98.0000,1,0,1),(365,424,6,2,95.0000,1,0,1),(366,424,8,2,48.0000,1,0,1),(367,425,5,1,67.0000,1,0,1),(368,425,6,1,5.0000,1,0,1),(369,425,8,1,75.0000,1,0,1),(370,425,5,2,24.0000,1,0,1),(371,425,6,2,14.0000,1,0,1),(372,425,8,2,70.0000,1,0,1),(373,426,5,1,98.0000,1,0,1),(374,426,6,1,36.0000,1,0,1),(375,426,8,1,39.0000,1,0,1),(376,426,5,2,44.0000,1,0,1),(377,426,6,2,35.0000,1,0,1),(378,426,8,2,66.0000,1,0,1),(379,427,5,1,69.0000,1,0,1),(380,427,6,1,51.0000,1,0,1),(381,427,8,1,77.0000,1,0,1),(382,427,5,2,6.0000,1,0,1),(383,427,6,2,95.0000,1,0,1),(384,427,8,2,82.0000,1,0,1),(385,428,5,1,43.0000,1,0,1),(386,428,6,1,91.0000,1,0,1),(387,428,8,1,74.0000,1,0,1),(388,428,5,2,94.0000,1,0,1),(389,428,6,2,79.0000,1,0,1);
insert into `catalog_product_attribute_decimal` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`attribute_qty`,`parent_id`,`is_inherit`) values (390,428,8,2,73.0000,1,0,1),(391,429,5,1,45.0000,1,0,1),(392,429,6,1,66.0000,1,0,1),(393,429,8,1,51.0000,1,0,1),(394,429,5,2,98.0000,1,0,1),(395,429,6,2,92.0000,1,0,1),(396,429,8,2,77.0000,1,0,1),(397,430,5,1,30.0000,1,0,1),(398,430,6,1,37.0000,1,0,1),(399,430,8,1,37.0000,1,0,1),(400,430,5,2,94.0000,1,0,1),(401,430,6,2,37.0000,1,0,1),(402,430,8,2,92.0000,1,0,1),(403,431,5,1,66.0000,1,0,1),(404,431,6,1,84.0000,1,0,1),(405,431,8,1,45.0000,1,0,1),(406,431,5,2,19.0000,1,0,1),(407,431,6,2,89.0000,1,0,1),(408,431,8,2,50.0000,1,0,1),(409,432,5,1,5.0000,1,0,1),(410,432,6,1,85.0000,1,0,1),(411,432,8,1,49.0000,1,0,1),(412,432,5,2,42.0000,1,0,1),(413,432,6,2,69.0000,1,0,1),(414,432,8,2,94.0000,1,0,1),(415,433,5,1,56.0000,1,0,1),(416,433,6,1,90.0000,1,0,1),(417,433,8,1,55.0000,1,0,1),(418,433,5,2,43.0000,1,0,1),(419,433,6,2,20.0000,1,0,1),(420,433,8,2,49.0000,1,0,1),(421,434,5,1,64.0000,1,0,1),(422,434,6,1,94.0000,1,0,1),(423,434,8,1,81.0000,1,0,1),(424,434,5,2,85.0000,1,0,1),(425,434,6,2,83.0000,1,0,1),(426,434,8,2,1.0000,1,0,1),(427,435,5,1,85.0000,1,0,1),(428,435,6,1,17.0000,1,0,1),(429,435,8,1,28.0000,1,0,1),(430,435,5,2,90.0000,1,0,1),(431,435,6,2,69.0000,1,0,1),(432,435,8,2,71.0000,1,0,1),(433,436,5,1,56.0000,1,0,1),(434,436,6,1,73.0000,1,0,1),(435,436,8,1,57.0000,1,0,1),(436,436,5,2,18.0000,1,0,1),(437,436,6,2,39.0000,1,0,1);
insert into `catalog_product_attribute_decimal` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`attribute_qty`,`parent_id`,`is_inherit`) values (438,436,8,2,86.0000,1,0,1),(439,437,5,1,75.0000,1,0,1),(440,437,6,1,50.0000,1,0,1),(441,437,8,1,61.0000,1,0,1),(442,437,5,2,34.0000,1,0,1),(443,437,6,2,72.0000,1,0,1),(444,437,8,2,61.0000,1,0,1),(445,438,5,1,76.0000,1,0,1),(446,438,6,1,81.0000,1,0,1),(447,438,8,1,47.0000,1,0,1),(448,438,5,2,90.0000,1,0,1),(449,438,6,2,46.0000,1,0,1),(450,438,8,2,70.0000,1,0,1),(451,439,5,1,99.0000,1,0,1),(452,439,6,1,23.0000,1,0,1),(453,439,8,1,1.0000,1,0,1),(454,439,5,2,95.0000,1,0,1),(455,439,6,2,11.0000,1,0,1),(456,439,8,2,18.0000,1,0,1),(457,440,5,1,72.0000,1,0,1),(458,440,6,1,28.0000,1,0,1),(459,440,8,1,73.0000,1,0,1),(460,440,5,2,96.0000,1,0,1),(461,440,6,2,64.0000,1,0,1),(462,440,8,2,24.0000,1,0,1),(463,441,5,1,79.0000,1,0,1),(464,441,6,1,21.0000,1,0,1),(465,441,8,1,45.0000,1,0,1),(466,441,5,2,46.0000,1,0,1),(467,441,6,2,23.0000,1,0,1),(468,441,8,2,88.0000,1,0,1),(469,442,5,1,40.0000,1,0,1),(470,442,6,1,74.0000,1,0,1),(471,442,8,1,8.0000,1,0,1),(472,442,5,2,84.0000,1,0,1),(473,442,6,2,2.0000,1,0,1),(474,442,8,2,71.0000,1,0,1),(475,443,5,1,85.0000,1,0,1),(476,443,6,1,79.0000,1,0,1),(477,443,8,1,39.0000,1,0,1),(478,443,5,2,9.0000,1,0,1),(479,443,6,2,90.0000,1,0,1),(480,443,8,2,68.0000,1,0,1),(481,444,5,1,28.0000,1,0,1),(482,444,6,1,27.0000,1,0,1),(483,444,8,1,73.0000,1,0,1),(484,444,5,2,52.0000,1,0,1),(485,444,6,2,20.0000,1,0,1);
insert into `catalog_product_attribute_decimal` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`attribute_qty`,`parent_id`,`is_inherit`) values (486,444,8,2,83.0000,1,0,1),(487,445,5,1,41.0000,1,0,1),(488,445,6,1,80.0000,1,0,1),(489,445,8,1,81.0000,1,0,1),(490,445,5,2,55.0000,1,0,1),(491,445,6,2,48.0000,1,0,1),(492,445,8,2,5.0000,1,0,1),(493,446,5,1,31.0000,1,0,1),(494,446,6,1,47.0000,1,0,1),(495,446,8,1,41.0000,1,0,1),(496,446,5,2,44.0000,1,0,1),(497,446,6,2,27.0000,1,0,1),(498,446,8,2,82.0000,1,0,1),(499,447,5,1,17.0000,1,0,1),(500,447,6,1,59.0000,1,0,1),(501,447,8,1,17.0000,1,0,1),(502,447,5,2,6.0000,1,0,1),(503,447,6,2,81.0000,1,0,1),(504,447,8,2,96.0000,1,0,1),(505,448,5,1,98.0000,1,0,1),(506,448,6,1,43.0000,1,0,1),(507,448,8,1,32.0000,1,0,1),(508,448,5,2,59.0000,1,0,1),(509,448,6,2,81.0000,1,0,1),(510,448,8,2,39.0000,1,0,1),(511,449,5,1,36.0000,1,0,1),(512,449,6,1,100.0000,1,0,1),(513,449,8,1,44.0000,1,0,1),(514,449,5,2,34.0000,1,0,1),(515,449,6,2,22.0000,1,0,1),(516,449,8,2,87.0000,1,0,1),(517,450,5,1,25.0000,1,0,1),(518,450,6,1,76.0000,1,0,1),(519,450,8,1,20.0000,1,0,1),(520,450,5,2,44.0000,1,0,1),(521,450,6,2,94.0000,1,0,1),(522,450,8,2,76.0000,1,0,1),(523,451,5,1,70.0000,1,0,1),(524,451,6,1,38.0000,1,0,1),(525,451,8,1,9.0000,1,0,1),(526,451,5,2,61.0000,1,0,1),(527,451,6,2,58.0000,1,0,1),(528,451,8,2,75.0000,1,0,1),(529,452,5,1,59.0000,1,0,1),(530,452,6,1,53.0000,1,0,1),(531,452,8,1,25.0000,1,0,1),(532,452,5,2,94.0000,1,0,1),(533,452,6,2,23.0000,1,0,1);
insert into `catalog_product_attribute_decimal` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`attribute_qty`,`parent_id`,`is_inherit`) values (534,452,8,2,65.0000,1,0,1),(535,453,5,1,40.0000,1,0,1),(536,453,6,1,57.0000,1,0,1),(537,453,8,1,10.0000,1,0,1),(538,453,5,2,60.0000,1,0,1),(539,453,6,2,21.0000,1,0,1),(540,453,8,2,8.0000,1,0,1),(541,454,5,1,96.0000,1,0,1),(542,454,6,1,35.0000,1,0,1),(543,454,8,1,19.0000,1,0,1),(544,454,5,2,58.0000,1,0,1),(545,454,6,2,79.0000,1,0,1),(546,454,8,2,28.0000,1,0,1),(547,455,5,1,19.0000,1,0,1),(548,455,6,1,91.0000,1,0,1),(549,455,8,1,92.0000,1,0,1),(550,455,5,2,51.0000,1,0,1),(551,455,6,2,97.0000,1,0,1),(552,455,8,2,82.0000,1,0,1),(553,456,5,1,84.0000,1,0,1),(554,456,6,1,30.0000,1,0,1),(555,456,8,1,26.0000,1,0,1),(556,456,5,2,32.0000,1,0,1),(557,456,6,2,21.0000,1,0,1),(558,456,8,2,37.0000,1,0,1),(559,457,5,1,28.0000,1,0,1),(560,457,6,1,26.0000,1,0,1),(561,457,8,1,53.0000,1,0,1),(562,457,5,2,6.0000,1,0,1),(563,457,6,2,19.0000,1,0,1),(564,457,8,2,44.0000,1,0,1),(565,458,5,1,20.0000,1,0,1),(566,458,6,1,99.0000,1,0,1),(567,458,8,1,15.0000,1,0,1),(568,458,5,2,62.0000,1,0,1),(569,458,6,2,57.0000,1,0,1),(570,458,8,2,14.0000,1,0,1),(571,459,5,1,39.0000,1,0,1),(572,459,6,1,94.0000,1,0,1),(573,459,8,1,39.0000,1,0,1),(574,459,5,2,48.0000,1,0,1),(575,459,6,2,72.0000,1,0,1),(576,459,8,2,92.0000,1,0,1),(577,460,5,1,49.0000,1,0,1),(578,460,6,1,51.0000,1,0,1),(579,460,8,1,10.0000,1,0,1),(580,460,5,2,47.0000,1,0,1),(581,460,6,2,48.0000,1,0,1);
insert into `catalog_product_attribute_decimal` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`attribute_qty`,`parent_id`,`is_inherit`) values (582,460,8,2,31.0000,1,0,1),(583,461,5,1,74.0000,1,0,1),(584,461,6,1,81.0000,1,0,1),(585,461,8,1,48.0000,1,0,1),(586,461,5,2,49.0000,1,0,1),(587,461,6,2,91.0000,1,0,1),(588,461,8,2,71.0000,1,0,1),(589,462,5,1,70.0000,1,0,1),(590,462,6,1,44.0000,1,0,1),(591,462,8,1,82.0000,1,0,1),(592,462,5,2,32.0000,1,0,1),(593,462,6,2,3.0000,1,0,1),(594,462,8,2,9.0000,1,0,1),(595,463,5,1,5.0000,1,0,1),(596,463,6,1,21.0000,1,0,1),(597,463,8,1,26.0000,1,0,1),(598,463,5,2,30.0000,1,0,1),(599,463,6,2,60.0000,1,0,1),(600,463,8,2,78.0000,1,0,1),(601,464,5,1,29.0000,1,0,1),(602,464,6,1,89.0000,1,0,1),(603,464,8,1,53.0000,1,0,1),(604,464,5,2,14.0000,1,0,1),(605,464,6,2,82.0000,1,0,1),(606,464,8,2,20.0000,1,0,1),(607,465,5,1,54.0000,1,0,1),(608,465,6,1,98.0000,1,0,1),(609,465,8,1,71.0000,1,0,1),(610,465,5,2,63.0000,1,0,1),(611,465,6,2,13.0000,1,0,1),(612,465,8,2,62.0000,1,0,1),(613,466,5,1,23.0000,1,0,1),(614,466,6,1,45.0000,1,0,1),(615,466,8,1,95.0000,1,0,1),(616,466,5,2,41.0000,1,0,1),(617,466,6,2,93.0000,1,0,1),(618,466,8,2,87.0000,1,0,1),(619,467,5,1,92.0000,1,0,1),(620,467,6,1,47.0000,1,0,1),(621,467,8,1,28.0000,1,0,1),(622,467,5,2,70.0000,1,0,1),(623,467,6,2,34.0000,1,0,1),(624,467,8,2,17.0000,1,0,1),(625,468,5,1,99.0000,1,0,1),(626,468,6,1,20.0000,1,0,1),(627,468,8,1,30.0000,1,0,1),(628,468,5,2,8.0000,1,0,1),(629,468,6,2,95.0000,1,0,1);
insert into `catalog_product_attribute_decimal` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`attribute_qty`,`parent_id`,`is_inherit`) values (630,468,8,2,81.0000,1,0,1),(631,469,5,1,42.0000,1,0,1),(632,469,6,1,52.0000,1,0,1),(633,469,8,1,96.0000,1,0,1),(634,469,5,2,23.0000,1,0,1),(635,469,6,2,58.0000,1,0,1),(636,469,8,2,92.0000,1,0,1),(637,470,5,1,54.0000,1,0,1),(638,470,6,1,75.0000,1,0,1),(639,470,8,1,29.0000,1,0,1),(640,470,5,2,64.0000,1,0,1),(641,470,6,2,100.0000,1,0,1),(642,470,8,2,24.0000,1,0,1),(643,471,5,1,76.0000,1,0,1),(644,471,6,1,47.0000,1,0,1),(645,471,8,1,20.0000,1,0,1),(646,471,5,2,44.0000,1,0,1),(647,471,6,2,70.0000,1,0,1),(648,471,8,2,84.0000,1,0,1),(649,472,5,1,34.0000,1,0,1),(650,472,6,1,20.0000,1,0,1),(651,472,8,1,15.0000,1,0,1),(652,472,5,2,5.0000,1,0,1),(653,472,6,2,65.0000,1,0,1),(654,472,8,2,88.0000,1,0,1),(655,473,5,1,14.0000,1,0,1),(656,473,6,1,17.0000,1,0,1),(657,473,8,1,98.0000,1,0,1),(658,473,5,2,4.0000,1,0,1),(659,473,6,2,3.0000,1,0,1),(660,473,8,2,38.0000,1,0,1),(661,474,5,1,37.0000,1,0,1),(662,474,6,1,12.0000,1,0,1),(663,474,8,1,60.0000,1,0,1),(664,474,5,2,78.0000,1,0,1),(665,474,6,2,100.0000,1,0,1),(666,474,8,2,95.0000,1,0,1),(667,475,5,1,30.0000,1,0,1),(668,475,6,1,97.0000,1,0,1),(669,475,8,1,77.0000,1,0,1),(670,475,5,2,26.0000,1,0,1),(671,475,6,2,42.0000,1,0,1),(672,475,8,2,52.0000,1,0,1),(673,476,5,1,9.0000,1,0,1),(674,476,6,1,62.0000,1,0,1),(675,476,8,1,84.0000,1,0,1),(676,476,5,2,79.0000,1,0,1),(677,476,6,2,64.0000,1,0,1);
insert into `catalog_product_attribute_decimal` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`attribute_qty`,`parent_id`,`is_inherit`) values (678,476,8,2,16.0000,1,0,1),(679,477,5,1,45.0000,1,0,1),(680,477,6,1,70.0000,1,0,1),(681,477,8,1,53.0000,1,0,1),(682,477,5,2,81.0000,1,0,1),(683,477,6,2,22.0000,1,0,1),(684,477,8,2,74.0000,1,0,1),(685,478,5,1,47.0000,1,0,1),(686,478,6,1,29.0000,1,0,1),(687,478,8,1,61.0000,1,0,1),(688,478,5,2,57.0000,1,0,1),(689,478,6,2,68.0000,1,0,1),(690,478,8,2,74.0000,1,0,1),(691,479,5,1,31.0000,1,0,1),(692,479,6,1,70.0000,1,0,1),(693,479,8,1,73.0000,1,0,1),(694,479,5,2,92.0000,1,0,1),(695,479,6,2,26.0000,1,0,1),(696,479,8,2,99.0000,1,0,1),(697,480,5,1,98.0000,1,0,1),(698,480,6,1,22.0000,1,0,1),(699,480,8,1,12.0000,1,0,1),(700,480,5,2,6.0000,1,0,1),(701,480,6,2,70.0000,1,0,1),(702,480,8,2,40.0000,1,0,1),(703,481,5,1,9.0000,1,0,1),(704,481,6,1,83.0000,1,0,1),(705,481,8,1,35.0000,1,0,1),(706,481,5,2,29.0000,1,0,1),(707,481,6,2,90.0000,1,0,1),(708,481,8,2,74.0000,1,0,1),(709,482,5,1,59.0000,1,0,1),(710,482,6,1,1.0000,1,0,1),(711,482,8,1,8.0000,1,0,1),(712,482,5,2,75.0000,1,0,1),(713,482,6,2,79.0000,1,0,1),(714,482,8,2,36.0000,1,0,1),(715,483,5,1,52.0000,1,0,1),(716,483,6,1,43.0000,1,0,1),(717,483,8,1,84.0000,1,0,1),(718,483,5,2,16.0000,1,0,1),(719,483,6,2,97.0000,1,0,1),(720,483,8,2,97.0000,1,0,1),(721,484,5,1,77.0000,1,0,1),(722,484,6,1,75.0000,1,0,1),(723,484,8,1,73.0000,1,0,1),(724,484,5,2,60.0000,1,0,1),(725,484,6,2,55.0000,1,0,1);
insert into `catalog_product_attribute_decimal` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`attribute_qty`,`parent_id`,`is_inherit`) values (726,484,8,2,35.0000,1,0,1),(727,485,5,1,82.0000,1,0,1),(728,485,6,1,34.0000,1,0,1),(729,485,8,1,19.0000,1,0,1),(730,485,5,2,24.0000,1,0,1),(731,485,6,2,83.0000,1,0,1),(732,485,8,2,14.0000,1,0,1),(733,486,5,1,49.0000,1,0,1),(734,486,6,1,4.0000,1,0,1),(735,486,8,1,77.0000,1,0,1),(736,486,5,2,8.0000,1,0,1),(737,486,6,2,8.0000,1,0,1),(738,486,8,2,58.0000,1,0,1),(739,487,5,1,70.0000,1,0,1),(740,487,6,1,90.0000,1,0,1),(741,487,8,1,85.0000,1,0,1),(742,487,5,2,74.0000,1,0,1),(743,487,6,2,30.0000,1,0,1),(744,487,8,2,23.0000,1,0,1),(745,488,5,1,21.0000,1,0,1),(746,488,6,1,96.0000,1,0,1),(747,488,8,1,43.0000,1,0,1),(748,488,5,2,16.0000,1,0,1),(749,488,6,2,96.0000,1,0,1),(750,488,8,2,76.0000,1,0,1),(751,489,5,1,39.0000,1,0,1),(752,489,6,1,97.0000,1,0,1),(753,489,8,1,81.0000,1,0,1),(754,489,5,2,39.0000,1,0,1),(755,489,6,2,74.0000,1,0,1),(756,489,8,2,69.0000,1,0,1),(757,490,5,1,91.0000,1,0,1),(758,490,6,1,12.0000,1,0,1),(759,490,8,1,42.0000,1,0,1),(760,490,5,2,31.0000,1,0,1),(761,490,6,2,30.0000,1,0,1),(762,490,8,2,12.0000,1,0,1),(763,491,5,1,59.0000,1,0,1),(764,491,6,1,87.0000,1,0,1),(765,491,8,1,51.0000,1,0,1),(766,491,5,2,40.0000,1,0,1),(767,491,6,2,100.0000,1,0,1),(768,491,8,2,51.0000,1,0,1),(769,492,5,1,5.0000,1,0,1),(770,492,6,1,61.0000,1,0,1),(771,492,8,1,96.0000,1,0,1),(772,492,5,2,50.0000,1,0,1),(773,492,6,2,68.0000,1,0,1);
insert into `catalog_product_attribute_decimal` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`attribute_qty`,`parent_id`,`is_inherit`) values (774,492,8,2,38.0000,1,0,1),(775,493,5,1,54.0000,1,0,1),(776,493,6,1,46.0000,1,0,1),(777,493,8,1,95.0000,1,0,1),(778,493,5,2,50.0000,1,0,1),(779,493,6,2,40.0000,1,0,1),(780,493,8,2,14.0000,1,0,1),(781,494,5,1,61.0000,1,0,1),(782,494,6,1,1.0000,1,0,1),(783,494,8,1,77.0000,1,0,1),(784,494,5,2,19.0000,1,0,1),(785,494,6,2,20.0000,1,0,1),(786,494,8,2,76.0000,1,0,1),(787,495,5,1,94.0000,1,0,1),(788,495,6,1,7.0000,1,0,1),(789,495,8,1,57.0000,1,0,1),(790,495,5,2,91.0000,1,0,1),(791,495,6,2,81.0000,1,0,1),(792,495,8,2,56.0000,1,0,1),(793,496,5,1,4.0000,1,0,1),(794,496,6,1,43.0000,1,0,1),(795,496,8,1,8.0000,1,0,1),(796,496,5,2,36.0000,1,0,1),(797,496,6,2,45.0000,1,0,1),(798,496,8,2,97.0000,1,0,1),(799,497,5,1,2.0000,1,0,1),(800,497,6,1,56.0000,1,0,1),(801,497,8,1,36.0000,1,0,1),(802,497,5,2,33.0000,1,0,1),(803,497,6,2,55.0000,1,0,1),(804,497,8,2,24.0000,1,0,1),(805,498,5,1,33.0000,1,0,1),(806,498,6,1,45.0000,1,0,1),(807,498,8,1,59.0000,1,0,1),(808,498,5,2,47.0000,1,0,1),(809,498,6,2,53.0000,1,0,1),(810,498,8,2,23.0000,1,0,1),(811,499,5,1,50.0000,1,0,1),(812,499,6,1,27.0000,1,0,1),(813,499,8,1,78.0000,1,0,1),(814,499,5,2,99.0000,1,0,1),(815,499,6,2,49.0000,1,0,1),(816,499,8,2,14.0000,1,0,1),(817,500,5,1,93.0000,1,0,1),(818,500,6,1,18.0000,1,0,1),(819,500,8,1,53.0000,1,0,1),(820,500,5,2,49.0000,1,0,1),(821,500,6,2,2.0000,1,0,1);
insert into `catalog_product_attribute_decimal` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`attribute_qty`,`parent_id`,`is_inherit`) values (822,500,8,2,26.0000,1,0,1),(823,501,5,1,59.0000,1,0,1),(824,501,6,1,4.0000,1,0,1),(825,501,8,1,80.0000,1,0,1),(826,501,5,2,62.0000,1,0,1),(827,501,6,2,95.0000,1,0,1),(828,501,8,2,73.0000,1,0,1),(829,502,5,1,82.0000,1,0,1),(830,502,6,1,20.0000,1,0,1),(831,502,8,1,62.0000,1,0,1),(832,502,5,2,89.0000,1,0,1),(833,502,6,2,3.0000,1,0,1),(834,502,8,2,29.0000,1,0,1),(835,503,5,1,2.0000,1,0,1),(836,503,6,1,21.0000,1,0,1),(837,503,8,1,88.0000,1,0,1),(838,503,5,2,42.0000,1,0,1),(839,503,6,2,78.0000,1,0,1),(840,503,8,2,99.0000,1,0,1),(841,504,5,1,46.0000,1,0,1),(842,504,6,1,61.0000,1,0,1),(843,504,8,1,7.0000,1,0,1),(844,504,5,2,64.0000,1,0,1),(845,504,6,2,15.0000,1,0,1),(846,504,8,2,2.0000,1,0,1),(847,434,5,1,234.1100,23,0,1);

/*Table structure for table `catalog_product_attribute_group` */

DROP TABLE IF EXISTS `catalog_product_attribute_group`;

CREATE TABLE `catalog_product_attribute_group` (
  `group_id` smallint(6) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '',
  PRIMARY KEY  USING BTREE (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product attributes groups';

/*Data for the table `catalog_product_attribute_group` */

insert into `catalog_product_attribute_group` (`group_id`,`code`) values (1,'base'),(2,'info'),(3,'gallery');

/*Table structure for table `catalog_product_attribute_in_set` */

DROP TABLE IF EXISTS `catalog_product_attribute_in_set`;

CREATE TABLE `catalog_product_attribute_in_set` (
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `set_id` smallint(6) unsigned NOT NULL default '0',
  `group_id` smallint(6) unsigned NOT NULL default '1',
  `position` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attribute_id`),
  KEY `FK_PRODUCT_ATTRIBUTE_SET` USING BTREE (`set_id`),
  KEY `FK_PRODUCT_SET_GROUP` USING BTREE (`group_id`),
  CONSTRAINT `FK_PRODUCT_ATTRIBUTE_IN_GROUP` FOREIGN KEY (`group_id`) REFERENCES `catalog_product_attribute_group` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_ATTRIBUTE_IN_SET` FOREIGN KEY (`set_id`) REFERENCES `catalog_product_attribute_set` (`set_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_SET_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product attributes in set';

/*Data for the table `catalog_product_attribute_in_set` */

insert into `catalog_product_attribute_in_set` (`attribute_id`,`set_id`,`group_id`,`position`) values (1,1,1,1),(2,1,2,2),(3,1,3,3),(4,1,1,4),(5,1,1,5),(6,1,1,6),(7,1,1,7),(8,1,1,8),(9,1,2,9),(10,1,1,10),(11,1,1,11),(12,1,1,12);

/*Table structure for table `catalog_product_attribute_int` */

DROP TABLE IF EXISTS `catalog_product_attribute_int`;

CREATE TABLE `catalog_product_attribute_int` (
  `value_id` bigint(16) unsigned NOT NULL auto_increment,
  `product_id` int(11) unsigned NOT NULL default '0',
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `attribute_value` int(11) NOT NULL default '0',
  `parent_id` bigint(16) unsigned NOT NULL default '0',
  `is_inherit` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_INT` (`attribute_id`),
  KEY `FK_WEBSITE_INT` (`website_id`),
  KEY `IDX_VALUE_JOIN` (`product_id`,`attribute_id`,`website_id`),
  CONSTRAINT `FK_ATTRIBUTE_INT` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_INT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_WEBSITE_INT` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Int values for product attribute';

/*Data for the table `catalog_product_attribute_int` */

insert into `catalog_product_attribute_int` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (165,405,9,1,1,0,1),(166,405,10,1,6,0,1),(167,405,9,2,1,0,1),(168,405,10,2,8,0,1),(169,406,9,1,1,0,1),(170,406,10,1,8,0,1),(171,406,9,2,1,0,1),(172,406,10,2,7,0,1),(173,407,9,1,1,0,1),(174,407,10,1,3,0,1),(175,407,9,2,1,0,1),(176,407,10,2,4,0,1),(177,408,9,1,1,0,1),(178,408,10,1,5,0,1),(179,408,9,2,1,0,1),(180,408,10,2,4,0,1),(181,409,9,1,1,0,1),(182,409,10,1,4,0,1),(183,409,9,2,1,0,1),(184,409,10,2,8,0,1),(185,410,9,1,1,0,1),(186,410,10,1,10,0,1),(187,410,9,2,1,0,1),(188,410,10,2,7,0,1),(189,411,9,1,1,0,1),(190,411,10,1,2,0,1),(191,411,9,2,1,0,1),(192,411,10,2,6,0,1),(193,412,9,1,1,0,1),(194,412,10,1,9,0,1),(195,412,9,2,1,0,1),(196,412,10,2,1,0,1),(197,413,9,1,1,0,1),(198,413,10,1,3,0,1),(199,413,9,2,1,0,1),(200,413,10,2,8,0,1),(201,414,9,1,1,0,1),(202,414,10,1,7,0,1),(203,414,9,2,1,0,1),(204,414,10,2,2,0,1),(205,415,9,1,1,0,1),(206,415,10,1,6,0,1),(207,415,9,2,1,0,1),(208,415,10,2,1,0,1),(209,416,9,1,1,0,1),(210,416,10,1,1,0,1),(211,416,9,2,1,0,1),(212,416,10,2,5,0,1),(213,417,9,1,1,0,1),(214,417,10,1,9,0,1),(215,417,9,2,1,0,1),(216,417,10,2,5,0,1),(217,418,9,1,1,0,1),(218,418,10,1,6,0,1),(219,418,9,2,1,0,1),(220,418,10,2,8,0,1),(221,419,9,1,1,0,1),(222,419,10,1,2,0,1),(223,419,9,2,1,0,1),(224,419,10,2,6,0,1),(225,420,9,1,1,0,1),(226,420,10,1,10,0,1),(227,420,9,2,1,0,1),(228,420,10,2,4,0,1),(229,421,9,1,1,0,1),(230,421,10,1,1,0,1),(231,421,9,2,1,0,1),(232,421,10,2,5,0,1),(233,422,9,1,1,0,1);
insert into `catalog_product_attribute_int` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (234,422,10,1,6,0,1),(235,422,9,2,1,0,1),(236,422,10,2,2,0,1),(237,423,9,1,1,0,1),(238,423,10,1,7,0,1),(239,423,9,2,1,0,1),(240,423,10,2,9,0,1),(241,424,9,1,1,0,1),(242,424,10,1,2,0,1),(243,424,9,2,1,0,1),(244,424,10,2,5,0,1),(245,425,9,1,1,0,1),(246,425,10,1,6,0,1),(247,425,9,2,1,0,1),(248,425,10,2,8,0,1),(249,426,9,1,1,0,1),(250,426,10,1,3,0,1),(251,426,9,2,1,0,1),(252,426,10,2,2,0,1),(253,427,9,1,1,0,1),(254,427,10,1,9,0,1),(255,427,9,2,1,0,1),(256,427,10,2,8,0,1),(257,428,9,1,1,0,1),(258,428,10,1,6,0,1),(259,428,9,2,1,0,1),(260,428,10,2,7,0,1),(261,429,9,1,1,0,1),(262,429,10,1,1,0,1),(263,429,9,2,1,0,1),(264,429,10,2,10,0,1),(265,430,9,1,1,0,1),(266,430,10,1,5,0,1),(267,430,9,2,1,0,1),(268,430,10,2,6,0,1),(269,431,9,1,1,0,1),(270,431,10,1,5,0,1),(271,431,9,2,1,0,1),(272,431,10,2,10,0,1),(273,432,9,1,1,0,1),(274,432,10,1,8,0,1),(275,432,9,2,1,0,1),(276,432,10,2,5,0,1),(277,433,9,1,1,0,1),(278,433,10,1,5,0,1),(279,433,9,2,1,0,1),(280,433,10,2,8,0,1),(281,434,9,1,1,0,1),(282,434,10,1,9,0,1),(283,434,9,2,1,0,1),(284,434,10,2,2,0,1),(285,435,9,1,1,0,1),(286,435,10,1,3,0,1),(287,435,9,2,1,0,1),(288,435,10,2,2,0,1),(289,436,9,1,1,0,1),(290,436,10,1,6,0,1),(291,436,9,2,1,0,1),(292,436,10,2,9,0,1),(293,437,9,1,1,0,1),(294,437,10,1,4,0,1),(295,437,9,2,1,0,1),(296,437,10,2,1,0,1),(297,438,9,1,1,0,1),(298,438,10,1,3,0,1),(299,438,9,2,1,0,1),(300,438,10,2,6,0,1),(301,439,9,1,1,0,1),(302,439,10,1,10,0,1);
insert into `catalog_product_attribute_int` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (303,439,9,2,1,0,1),(304,439,10,2,3,0,1),(305,440,9,1,1,0,1),(306,440,10,1,8,0,1),(307,440,9,2,1,0,1),(308,440,10,2,5,0,1),(309,441,9,1,1,0,1),(310,441,10,1,6,0,1),(311,441,9,2,1,0,1),(312,441,10,2,6,0,1),(313,442,9,1,1,0,1),(314,442,10,1,5,0,1),(315,442,9,2,1,0,1),(316,442,10,2,6,0,1),(317,443,9,1,1,0,1),(318,443,10,1,4,0,1),(319,443,9,2,1,0,1),(320,443,10,2,8,0,1),(321,444,9,1,1,0,1),(322,444,10,1,1,0,1),(323,444,9,2,1,0,1),(324,444,10,2,1,0,1),(325,445,9,1,1,0,1),(326,445,10,1,9,0,1),(327,445,9,2,1,0,1),(328,445,10,2,10,0,1),(329,446,9,1,1,0,1),(330,446,10,1,1,0,1),(331,446,9,2,1,0,1),(332,446,10,2,10,0,1),(333,447,9,1,1,0,1),(334,447,10,1,2,0,1),(335,447,9,2,1,0,1),(336,447,10,2,8,0,1),(337,448,9,1,1,0,1),(338,448,10,1,3,0,1),(339,448,9,2,1,0,1),(340,448,10,2,5,0,1),(341,449,9,1,1,0,1),(342,449,10,1,10,0,1),(343,449,9,2,1,0,1),(344,449,10,2,1,0,1),(345,450,9,1,1,0,1),(346,450,10,1,10,0,1),(347,450,9,2,1,0,1),(348,450,10,2,5,0,1),(349,451,9,1,1,0,1),(350,451,10,1,2,0,1),(351,451,9,2,1,0,1),(352,451,10,2,7,0,1),(353,452,9,1,1,0,1),(354,452,10,1,1,0,1),(355,452,9,2,1,0,1),(356,452,10,2,3,0,1),(357,453,9,1,1,0,1),(358,453,10,1,6,0,1),(359,453,9,2,1,0,1),(360,453,10,2,6,0,1),(361,454,9,1,1,0,1),(362,454,10,1,9,0,1),(363,454,9,2,1,0,1),(364,454,10,2,8,0,1),(365,455,9,1,1,0,1),(366,455,10,1,2,0,1),(367,455,9,2,1,0,1),(368,455,10,2,5,0,1),(369,456,9,1,1,0,1),(370,456,10,1,3,0,1),(371,456,9,2,1,0,1);
insert into `catalog_product_attribute_int` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (372,456,10,2,4,0,1),(373,457,9,1,1,0,1),(374,457,10,1,5,0,1),(375,457,9,2,1,0,1),(376,457,10,2,3,0,1),(377,458,9,1,1,0,1),(378,458,10,1,5,0,1),(379,458,9,2,1,0,1),(380,458,10,2,8,0,1),(381,459,9,1,1,0,1),(382,459,10,1,8,0,1),(383,459,9,2,1,0,1),(384,459,10,2,8,0,1),(385,460,9,1,1,0,1),(386,460,10,1,5,0,1),(387,460,9,2,1,0,1),(388,460,10,2,5,0,1),(389,461,9,1,1,0,1),(390,461,10,1,5,0,1),(391,461,9,2,1,0,1),(392,461,10,2,10,0,1),(393,462,9,1,1,0,1),(394,462,10,1,6,0,1),(395,462,9,2,1,0,1),(396,462,10,2,2,0,1),(397,463,9,1,1,0,1),(398,463,10,1,8,0,1),(399,463,9,2,1,0,1),(400,463,10,2,4,0,1),(401,464,9,1,1,0,1),(402,464,10,1,7,0,1),(403,464,9,2,1,0,1),(404,464,10,2,3,0,1),(405,465,9,1,1,0,1),(406,465,10,1,3,0,1),(407,465,9,2,1,0,1),(408,465,10,2,3,0,1),(409,466,9,1,1,0,1),(410,466,10,1,10,0,1),(411,466,9,2,1,0,1),(412,466,10,2,6,0,1),(413,467,9,1,1,0,1),(414,467,10,1,1,0,1),(415,467,9,2,1,0,1),(416,467,10,2,1,0,1),(417,468,9,1,1,0,1),(418,468,10,1,4,0,1),(419,468,9,2,1,0,1),(420,468,10,2,6,0,1),(421,469,9,1,1,0,1),(422,469,10,1,7,0,1),(423,469,9,2,1,0,1),(424,469,10,2,10,0,1),(425,470,9,1,1,0,1),(426,470,10,1,4,0,1),(427,470,9,2,1,0,1),(428,470,10,2,10,0,1),(429,471,9,1,1,0,1),(430,471,10,1,2,0,1),(431,471,9,2,1,0,1),(432,471,10,2,6,0,1),(433,472,9,1,1,0,1),(434,472,10,1,6,0,1),(435,472,9,2,1,0,1),(436,472,10,2,1,0,1),(437,473,9,1,1,0,1),(438,473,10,1,3,0,1),(439,473,9,2,1,0,1),(440,473,10,2,9,0,1);
insert into `catalog_product_attribute_int` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (441,474,9,1,1,0,1),(442,474,10,1,5,0,1),(443,474,9,2,1,0,1),(444,474,10,2,9,0,1),(445,475,9,1,1,0,1),(446,475,10,1,10,0,1),(447,475,9,2,1,0,1),(448,475,10,2,6,0,1),(449,476,9,1,1,0,1),(450,476,10,1,8,0,1),(451,476,9,2,1,0,1),(452,476,10,2,7,0,1),(453,477,9,1,1,0,1),(454,477,10,1,10,0,1),(455,477,9,2,1,0,1),(456,477,10,2,8,0,1),(457,478,9,1,1,0,1),(458,478,10,1,10,0,1),(459,478,9,2,1,0,1),(460,478,10,2,2,0,1),(461,479,9,1,1,0,1),(462,479,10,1,3,0,1),(463,479,9,2,1,0,1),(464,479,10,2,9,0,1),(465,480,9,1,1,0,1),(466,480,10,1,9,0,1),(467,480,9,2,1,0,1),(468,480,10,2,10,0,1),(469,481,9,1,1,0,1),(470,481,10,1,5,0,1),(471,481,9,2,1,0,1),(472,481,10,2,3,0,1),(473,482,9,1,1,0,1),(474,482,10,1,9,0,1),(475,482,9,2,1,0,1),(476,482,10,2,7,0,1),(477,483,9,1,1,0,1),(478,483,10,1,8,0,1),(479,483,9,2,1,0,1),(480,483,10,2,3,0,1),(481,484,9,1,1,0,1),(482,484,10,1,7,0,1),(483,484,9,2,1,0,1),(484,484,10,2,7,0,1),(485,485,9,1,1,0,1),(486,485,10,1,6,0,1),(487,485,9,2,1,0,1),(488,485,10,2,2,0,1),(489,486,9,1,1,0,1),(490,486,10,1,7,0,1),(491,486,9,2,1,0,1),(492,486,10,2,10,0,1),(493,487,9,1,1,0,1),(494,487,10,1,1,0,1),(495,487,9,2,1,0,1),(496,487,10,2,5,0,1),(497,488,9,1,1,0,1),(498,488,10,1,8,0,1),(499,488,9,2,1,0,1),(500,488,10,2,7,0,1),(501,489,9,1,1,0,1),(502,489,10,1,10,0,1),(503,489,9,2,1,0,1),(504,489,10,2,4,0,1),(505,490,9,1,1,0,1),(506,490,10,1,3,0,1),(507,490,9,2,1,0,1),(508,490,10,2,9,0,1),(509,491,9,1,1,0,1);
insert into `catalog_product_attribute_int` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (510,491,10,1,2,0,1),(511,491,9,2,1,0,1),(512,491,10,2,3,0,1),(513,492,9,1,1,0,1),(514,492,10,1,10,0,1),(515,492,9,2,1,0,1),(516,492,10,2,8,0,1),(517,493,9,1,1,0,1),(518,493,10,1,4,0,1),(519,493,9,2,1,0,1),(520,493,10,2,4,0,1),(521,494,9,1,1,0,1),(522,494,10,1,3,0,1),(523,494,9,2,1,0,1),(524,494,10,2,2,0,1),(525,495,9,1,1,0,1),(526,495,10,1,7,0,1),(527,495,9,2,1,0,1),(528,495,10,2,4,0,1),(529,496,9,1,1,0,1),(530,496,10,1,1,0,1),(531,496,9,2,1,0,1),(532,496,10,2,5,0,1),(533,497,9,1,1,0,1),(534,497,10,1,6,0,1),(535,497,9,2,1,0,1),(536,497,10,2,2,0,1),(537,498,9,1,1,0,1),(538,498,10,1,6,0,1),(539,498,9,2,1,0,1),(540,498,10,2,6,0,1),(541,499,9,1,1,0,1),(542,499,10,1,3,0,1),(543,499,9,2,1,0,1),(544,499,10,2,5,0,1),(545,500,9,1,1,0,1),(546,500,10,1,8,0,1),(547,500,9,2,1,0,1),(548,500,10,2,8,0,1),(549,501,9,1,1,0,1),(550,501,10,1,4,0,1),(551,501,9,2,1,0,1),(552,501,10,2,3,0,1),(553,502,9,1,1,0,1),(554,502,10,1,9,0,1),(555,502,9,2,1,0,1),(556,502,10,2,9,0,1),(557,503,9,1,1,0,1),(558,503,10,1,9,0,1),(559,503,9,2,1,0,1),(560,503,10,2,4,0,1),(561,504,9,1,1,0,1),(562,504,10,1,8,0,1),(563,504,9,2,1,0,1),(564,504,10,2,2,0,1),(565,405,11,1,4,0,1),(566,406,11,1,4,0,1),(567,407,11,1,4,0,1),(568,408,11,1,4,0,1),(569,409,11,1,4,0,1),(570,410,11,1,4,0,1),(571,411,11,1,4,0,1),(572,412,11,1,4,0,1),(573,413,11,1,4,0,1),(574,414,11,1,4,0,1),(575,415,11,1,4,0,1),(576,416,11,1,4,0,1),(577,417,11,1,4,0,1),(578,418,11,1,4,0,1);
insert into `catalog_product_attribute_int` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (579,419,11,1,4,0,1),(580,420,11,1,4,0,1),(581,421,11,1,4,0,1),(582,422,11,1,4,0,1),(583,423,11,1,4,0,1),(584,424,11,1,4,0,1),(585,425,11,1,4,0,1),(586,426,11,1,4,0,1),(587,427,11,1,4,0,1),(588,428,11,1,4,0,1),(589,429,11,1,4,0,1),(590,430,11,1,4,0,1),(591,431,11,1,4,0,1),(592,432,11,1,4,0,1),(593,433,11,1,4,0,1),(594,434,11,1,4,0,1),(595,435,11,1,4,0,1),(596,436,11,1,4,0,1),(597,437,11,1,4,0,1),(598,438,11,1,4,0,1),(599,439,11,1,4,0,1),(600,440,11,1,4,0,1),(601,441,11,1,4,0,1),(602,442,11,1,4,0,1),(603,443,11,1,4,0,1),(604,444,11,1,4,0,1),(605,445,11,1,4,0,1),(606,446,11,1,4,0,1),(607,447,11,1,4,0,1),(608,448,11,1,4,0,1),(609,449,11,1,4,0,1),(610,450,11,1,4,0,1),(611,451,11,1,4,0,1),(612,452,11,1,4,0,1),(613,453,11,1,4,0,1),(614,454,11,1,4,0,1),(615,455,11,1,4,0,1),(616,456,11,1,4,0,1),(617,457,11,1,4,0,1),(618,458,11,1,4,0,1),(619,459,11,1,4,0,1),(620,460,11,1,4,0,1),(621,461,11,1,4,0,1),(622,462,11,1,4,0,1),(623,463,11,1,4,0,1),(624,464,11,1,4,0,1),(625,465,11,1,4,0,1),(626,466,11,1,4,0,1),(627,467,11,1,4,0,1),(628,468,11,1,4,0,1),(629,469,11,1,4,0,1),(630,470,11,1,4,0,1),(631,471,11,1,4,0,1),(632,472,11,1,4,0,1),(633,473,11,1,4,0,1),(634,474,11,1,4,0,1),(635,475,11,1,4,0,1),(636,476,11,1,4,0,1),(637,477,11,1,4,0,1),(638,478,11,1,4,0,1),(639,479,11,1,4,0,1),(640,480,11,1,4,0,1),(641,481,11,1,4,0,1),(642,482,11,1,4,0,1),(643,483,11,1,4,0,1),(644,484,11,1,4,0,1),(645,485,11,1,4,0,1);
insert into `catalog_product_attribute_int` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (646,486,11,1,4,0,1),(647,487,11,1,4,0,1),(648,488,11,1,4,0,1),(649,489,11,1,4,0,1),(650,490,11,1,4,0,1),(651,491,11,1,4,0,1),(652,492,11,1,4,0,1),(653,493,11,1,4,0,1),(654,494,11,1,4,0,1),(655,495,11,1,4,0,1),(656,496,11,1,4,0,1),(657,497,11,1,4,0,1),(658,498,11,1,4,0,1),(659,499,11,1,4,0,1),(660,405,11,2,4,0,1),(661,406,11,2,4,0,1),(662,407,11,2,4,0,1),(663,408,11,2,4,0,1),(664,409,11,2,4,0,1),(665,410,11,2,4,0,1),(666,411,11,2,4,0,1),(667,412,11,2,4,0,1),(668,413,11,2,4,0,1),(669,414,11,2,4,0,1),(670,415,11,2,4,0,1),(671,416,11,2,4,0,1),(672,417,11,2,4,0,1),(673,418,11,2,4,0,1),(674,419,11,2,4,0,1),(675,420,11,2,4,0,1),(676,421,11,2,4,0,1),(677,422,11,2,4,0,1),(678,423,11,2,4,0,1),(679,424,11,2,4,0,1),(680,425,11,2,4,0,1),(681,426,11,2,4,0,1),(682,427,11,2,4,0,1),(683,428,11,2,4,0,1),(684,429,11,2,4,0,1),(685,430,11,2,4,0,1),(686,431,11,2,4,0,1),(687,432,11,2,4,0,1),(688,433,11,2,4,0,1),(689,434,11,2,4,0,1),(690,435,11,2,4,0,1),(691,436,11,2,4,0,1),(692,437,11,2,4,0,1),(693,438,11,2,4,0,1),(694,439,11,2,4,0,1),(695,440,11,2,4,0,1),(696,441,11,2,4,0,1),(697,442,11,2,4,0,1),(698,443,11,2,4,0,1),(699,444,11,2,4,0,1),(700,445,11,2,4,0,1),(701,446,11,2,4,0,1),(702,447,11,2,4,0,1),(703,448,11,2,4,0,1),(704,449,11,2,4,0,1),(705,450,11,2,4,0,1),(706,451,11,2,4,0,1),(707,452,11,2,4,0,1),(708,453,11,2,4,0,1),(709,454,11,2,4,0,1),(710,455,11,2,4,0,1),(711,456,11,2,4,0,1),(712,457,11,2,4,0,1);
insert into `catalog_product_attribute_int` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (713,458,11,2,4,0,1),(714,459,11,2,4,0,1),(715,460,11,2,4,0,1),(716,461,11,2,4,0,1),(717,462,11,2,4,0,1),(718,463,11,2,4,0,1),(719,464,11,2,4,0,1),(720,465,11,2,4,0,1),(721,466,11,2,4,0,1),(722,467,11,2,4,0,1),(723,468,11,2,4,0,1),(724,469,11,2,4,0,1),(725,470,11,2,4,0,1),(726,471,11,2,4,0,1),(727,472,11,2,4,0,1),(728,473,11,2,4,0,1),(729,474,11,2,4,0,1),(730,475,11,2,4,0,1),(731,476,11,2,4,0,1),(732,477,11,2,4,0,1),(733,478,11,2,4,0,1),(734,479,11,2,4,0,1),(735,480,11,2,4,0,1),(736,481,11,2,4,0,1),(737,482,11,2,4,0,1),(738,483,11,2,4,0,1),(739,484,11,2,4,0,1),(740,485,11,2,4,0,1),(741,486,11,2,4,0,1),(742,487,11,2,4,0,1),(743,488,11,2,4,0,1),(744,489,11,2,4,0,1),(745,490,11,2,4,0,1),(746,491,11,2,4,0,1),(747,492,11,2,4,0,1),(748,493,11,2,4,0,1),(749,494,11,2,4,0,1),(750,495,11,2,4,0,1),(751,496,11,2,4,0,1),(752,497,11,2,4,0,1),(753,498,11,2,4,0,1),(754,499,11,2,4,0,1),(755,500,11,2,5,0,1),(756,501,11,2,5,0,1),(757,502,11,2,5,0,1),(758,503,11,2,5,0,1),(759,504,11,2,5,0,1),(760,500,11,1,5,0,1),(761,501,11,1,5,0,1),(762,502,11,1,5,0,1),(763,503,11,1,5,0,1),(764,504,11,1,5,0,1),(765,405,12,1,3,0,1),(766,406,12,1,3,0,1),(767,407,12,1,3,0,1),(768,408,12,1,3,0,1),(769,409,12,1,3,0,1),(770,410,12,1,3,0,1),(771,411,12,1,3,0,1),(772,412,12,1,3,0,1),(773,413,12,1,3,0,1),(774,414,12,1,3,0,1),(775,415,12,1,3,0,1),(776,416,12,1,3,0,1),(777,417,12,1,3,0,1),(778,418,12,1,3,0,1),(779,419,12,1,3,0,1);
insert into `catalog_product_attribute_int` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (780,420,12,1,4,0,1),(781,421,12,1,4,0,1),(782,422,12,1,4,0,1),(783,423,12,1,4,0,1),(784,424,12,1,4,0,1),(785,425,12,1,4,0,1),(786,426,12,1,4,0,1),(787,427,12,1,4,0,1),(788,428,12,1,4,0,1),(789,429,12,1,4,0,1),(790,430,12,1,5,0,1),(791,431,12,1,5,0,1),(792,432,12,1,5,0,1),(793,433,12,1,5,0,1),(794,434,12,1,5,0,1),(795,435,12,1,5,0,1),(796,436,12,1,5,0,1),(797,437,12,1,5,0,1),(798,438,12,1,5,0,1),(799,439,12,1,5,0,1),(800,440,12,1,7,0,1),(801,441,12,1,7,0,1),(802,442,12,1,7,0,1),(803,443,12,1,7,0,1),(804,444,12,1,7,0,1),(805,445,12,1,7,0,1),(806,446,12,1,7,0,1),(807,447,12,1,7,0,1),(808,448,12,1,7,0,1),(809,449,12,1,7,0,1),(810,450,12,1,10,0,1),(811,451,12,1,10,0,1),(812,452,12,1,10,0,1),(813,453,12,1,10,0,1),(814,454,12,1,10,0,1),(815,455,12,1,10,0,1),(816,456,12,1,10,0,1),(817,457,12,1,10,0,1),(818,458,12,1,10,0,1),(819,459,12,1,10,0,1),(820,460,12,1,10,0,1),(821,461,12,1,10,0,1),(822,462,12,1,10,0,1),(823,463,12,1,10,0,1),(824,464,12,1,10,0,1),(825,465,12,1,10,0,1),(826,466,12,1,10,0,1),(827,467,12,1,10,0,1),(828,468,12,1,10,0,1),(829,469,12,1,10,0,1),(830,470,12,1,10,0,1),(831,471,12,1,10,0,1),(832,472,12,1,10,0,1),(833,473,12,1,10,0,1),(834,474,12,1,10,0,1),(835,475,12,1,10,0,1),(836,476,12,1,10,0,1),(837,477,12,1,10,0,1),(838,478,12,1,10,0,1),(839,479,12,1,10,0,1),(840,480,12,1,10,0,1),(841,481,12,1,10,0,1),(842,482,12,1,10,0,1),(843,483,12,1,10,0,1),(844,484,12,1,10,0,1);
insert into `catalog_product_attribute_int` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (845,485,12,1,10,0,1),(846,486,12,1,10,0,1),(847,487,12,1,10,0,1),(848,488,12,1,10,0,1),(849,489,12,1,10,0,1),(850,490,12,1,10,0,1),(851,491,12,1,10,0,1),(852,492,12,1,10,0,1),(853,493,12,1,10,0,1),(854,494,12,1,10,0,1),(855,495,12,1,10,0,1),(856,496,12,1,10,0,1),(857,497,12,1,10,0,1),(858,498,12,1,10,0,1),(859,499,12,1,10,0,1),(860,500,12,1,10,0,1),(861,501,12,1,10,0,1),(862,502,12,1,10,0,1),(863,503,12,1,10,0,1),(864,504,12,1,10,0,1);

/*Table structure for table `catalog_product_attribute_option` */

DROP TABLE IF EXISTS `catalog_product_attribute_option`;

CREATE TABLE `catalog_product_attribute_option` (
  `option_id` int(11) unsigned NOT NULL auto_increment,
  `website_id` smallint(6) unsigned default NULL,
  `attribute_id` smallint(6) unsigned default NULL,
  `value` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`option_id`),
  KEY `FK_ATTRIBUTE_OPTION_VALUE_WEBSITE` (`website_id`),
  KEY `FK_ATTRIBUTE_OPTION_VALUE_TYPE` USING BTREE (`attribute_id`),
  CONSTRAINT `FK_ATTRIBUTE_OPTION_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ATTRIBUTE_OPTION_VALUE_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Attribute option values';

/*Data for the table `catalog_product_attribute_option` */

insert into `catalog_product_attribute_option` (`option_id`,`website_id`,`attribute_id`,`value`) values (1,1,9,'In stock'),(2,1,9,'Out of stock'),(3,1,9,'Disabled'),(4,1,11,'Clogs'),(5,1,11,'Sandals'),(6,1,10,'Man 1'),(7,1,10,'Man 2'),(8,1,10,'Man 3'),(9,1,10,'Man 4');

/*Table structure for table `catalog_product_attribute_set` */

DROP TABLE IF EXISTS `catalog_product_attribute_set`;

CREATE TABLE `catalog_product_attribute_set` (
  `set_id` smallint(6) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '',
  PRIMARY KEY  USING BTREE (`set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Product attributes set';

/*Data for the table `catalog_product_attribute_set` */

insert into `catalog_product_attribute_set` (`set_id`,`code`) values (1,'Simple product'),(2,'Base product'),(3,'Auto');

/*Table structure for table `catalog_product_attribute_text` */

DROP TABLE IF EXISTS `catalog_product_attribute_text`;

CREATE TABLE `catalog_product_attribute_text` (
  `value_id` bigint(16) unsigned NOT NULL auto_increment,
  `product_id` int(11) unsigned NOT NULL default '0',
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `attribute_value` text NOT NULL,
  `parent_id` bigint(16) unsigned NOT NULL default '0',
  `is_inherit` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_TEXT` (`attribute_id`),
  KEY `FK_WEBSITE_TEXT` (`website_id`),
  KEY `IDX_VALUE_JOIN` (`product_id`,`attribute_id`,`website_id`),
  CONSTRAINT `FK_ATTRIBUTE_TEXT` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_TEXT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_WEBSITE_TEXT` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Text attributes values';

/*Data for the table `catalog_product_attribute_text` */

insert into `catalog_product_attribute_text` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (83,405,2,1,'Product #405 description',0,1),(84,405,2,2,'Product #405 description',0,1),(85,406,2,1,'Product #406 description',0,1),(86,406,2,2,'Product #406 description',0,1),(87,407,2,1,'Product #407 description',0,1),(88,407,2,2,'Product #407 description',0,1),(89,408,2,1,'Product #408 description',0,1),(90,408,2,2,'Product #408 description',0,1),(91,409,2,1,'Product #409 description',0,1),(92,409,2,2,'Product #409 description',0,1),(93,410,2,1,'Product #410 description',0,1),(94,410,2,2,'Product #410 description',0,1),(95,411,2,1,'Product #411 description',0,1),(96,411,2,2,'Product #411 description',0,1),(97,412,2,1,'Product #412 description',0,1),(98,412,2,2,'Product #412 description',0,1),(99,413,2,1,'Product #413 description',0,1),(100,413,2,2,'Product #413 description',0,1),(101,414,2,1,'Product #414 description',0,1),(102,414,2,2,'Product #414 description',0,1),(103,415,2,1,'Product #415 description',0,1),(104,415,2,2,'Product #415 description',0,1),(105,416,2,1,'Product #416 description',0,1),(106,416,2,2,'Product #416 description',0,1),(107,417,2,1,'Product #417 description',0,1),(108,417,2,2,'Product #417 description',0,1),(109,418,2,1,'Product #418 description',0,1);
insert into `catalog_product_attribute_text` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (110,418,2,2,'Product #418 description',0,1),(111,419,2,1,'Product #419 description',0,1),(112,419,2,2,'Product #419 description',0,1),(113,420,2,1,'Product #420 description',0,1),(114,420,2,2,'Product #420 description',0,1),(115,421,2,1,'Product #421 description',0,1),(116,421,2,2,'Product #421 description',0,1),(117,422,2,1,'Product #422 description',0,1),(118,422,2,2,'Product #422 description',0,1),(119,423,2,1,'Product #423 description',0,1),(120,423,2,2,'Product #423 description',0,1),(121,424,2,1,'Product #424 description',0,1),(122,424,2,2,'Product #424 description',0,1),(123,425,2,1,'Product #425 description',0,1),(124,425,2,2,'Product #425 description',0,1),(125,426,2,1,'Product #426 description',0,1),(126,426,2,2,'Product #426 description',0,1),(127,427,2,1,'Product #427 description',0,1),(128,427,2,2,'Product #427 description',0,1),(129,428,2,1,'Product #428 description',0,1),(130,428,2,2,'Product #428 description',0,1),(131,429,2,1,'Product #429 description',0,1),(132,429,2,2,'Product #429 description',0,1),(133,430,2,1,'Product #430 description',0,1),(134,430,2,2,'Product #430 description',0,1),(135,431,2,1,'Product #431 description',0,1),(136,431,2,2,'Product #431 description',0,1),(137,432,2,1,'Product #432 description',0,1);
insert into `catalog_product_attribute_text` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (138,432,2,2,'Product #432 description',0,1),(139,433,2,1,'Product #433 description',0,1),(140,433,2,2,'Product #433 description',0,1),(141,434,2,1,'Product #434 description',0,1),(142,434,2,2,'Product #434 description',0,1),(143,435,2,1,'Product #435 description',0,1),(144,435,2,2,'Product #435 description',0,1),(145,436,2,1,'Product #436 description',0,1),(146,436,2,2,'Product #436 description',0,1),(147,437,2,1,'Product #437 description',0,1),(148,437,2,2,'Product #437 description',0,1),(149,438,2,1,'Product #438 description',0,1),(150,438,2,2,'Product #438 description',0,1),(151,439,2,1,'Product #439 description',0,1),(152,439,2,2,'Product #439 description',0,1),(153,440,2,1,'Product #440 description',0,1),(154,440,2,2,'Product #440 description',0,1),(155,441,2,1,'Product #441 description',0,1),(156,441,2,2,'Product #441 description',0,1),(157,442,2,1,'Product #442 description',0,1),(158,442,2,2,'Product #442 description',0,1),(159,443,2,1,'Product #443 description',0,1),(160,443,2,2,'Product #443 description',0,1),(161,444,2,1,'Product #444 description',0,1),(162,444,2,2,'Product #444 description',0,1),(163,445,2,1,'Product #445 description',0,1),(164,445,2,2,'Product #445 description',0,1),(165,446,2,1,'Product #446 description',0,1);
insert into `catalog_product_attribute_text` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (166,446,2,2,'Product #446 description',0,1),(167,447,2,1,'Product #447 description',0,1),(168,447,2,2,'Product #447 description',0,1),(169,448,2,1,'Product #448 description',0,1),(170,448,2,2,'Product #448 description',0,1),(171,449,2,1,'Product #449 description',0,1),(172,449,2,2,'Product #449 description',0,1),(173,450,2,1,'Product #450 description',0,1),(174,450,2,2,'Product #450 description',0,1),(175,451,2,1,'Product #451 description',0,1),(176,451,2,2,'Product #451 description',0,1),(177,452,2,1,'Product #452 description',0,1),(178,452,2,2,'Product #452 description',0,1),(179,453,2,1,'Product #453 description',0,1),(180,453,2,2,'Product #453 description',0,1),(181,454,2,1,'Product #454 description',0,1),(182,454,2,2,'Product #454 description',0,1),(183,455,2,1,'Product #455 description',0,1),(184,455,2,2,'Product #455 description',0,1),(185,456,2,1,'Product #456 description',0,1),(186,456,2,2,'Product #456 description',0,1),(187,457,2,1,'Product #457 description',0,1),(188,457,2,2,'Product #457 description',0,1),(189,458,2,1,'Product #458 description',0,1),(190,458,2,2,'Product #458 description',0,1),(191,459,2,1,'Product #459 description',0,1),(192,459,2,2,'Product #459 description',0,1),(193,460,2,1,'Product #460 description',0,1);
insert into `catalog_product_attribute_text` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (194,460,2,2,'Product #460 description',0,1),(195,461,2,1,'Product #461 description',0,1),(196,461,2,2,'Product #461 description',0,1),(197,462,2,1,'Product #462 description',0,1),(198,462,2,2,'Product #462 description',0,1),(199,463,2,1,'Product #463 description',0,1),(200,463,2,2,'Product #463 description',0,1),(201,464,2,1,'Product #464 description',0,1),(202,464,2,2,'Product #464 description',0,1),(203,465,2,1,'Product #465 description',0,1),(204,465,2,2,'Product #465 description',0,1),(205,466,2,1,'Product #466 description',0,1),(206,466,2,2,'Product #466 description',0,1),(207,467,2,1,'Product #467 description',0,1),(208,467,2,2,'Product #467 description',0,1),(209,468,2,1,'Product #468 description',0,1),(210,468,2,2,'Product #468 description',0,1),(211,469,2,1,'Product #469 description',0,1),(212,469,2,2,'Product #469 description',0,1),(213,470,2,1,'Product #470 description',0,1),(214,470,2,2,'Product #470 description',0,1),(215,471,2,1,'Product #471 description',0,1),(216,471,2,2,'Product #471 description',0,1),(217,472,2,1,'Product #472 description',0,1),(218,472,2,2,'Product #472 description',0,1),(219,473,2,1,'Product #473 description',0,1),(220,473,2,2,'Product #473 description',0,1),(221,474,2,1,'Product #474 description',0,1);
insert into `catalog_product_attribute_text` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (222,474,2,2,'Product #474 description',0,1),(223,475,2,1,'Product #475 description',0,1),(224,475,2,2,'Product #475 description',0,1),(225,476,2,1,'Product #476 description',0,1),(226,476,2,2,'Product #476 description',0,1),(227,477,2,1,'Product #477 description',0,1),(228,477,2,2,'Product #477 description',0,1),(229,478,2,1,'Product #478 description',0,1),(230,478,2,2,'Product #478 description',0,1),(231,479,2,1,'Product #479 description',0,1),(232,479,2,2,'Product #479 description',0,1),(233,480,2,1,'Product #480 description',0,1),(234,480,2,2,'Product #480 description',0,1),(235,481,2,1,'Product #481 description',0,1),(236,481,2,2,'Product #481 description',0,1),(237,482,2,1,'Product #482 description',0,1),(238,482,2,2,'Product #482 description',0,1),(239,483,2,1,'Product #483 description',0,1),(240,483,2,2,'Product #483 description',0,1),(241,484,2,1,'Product #484 description',0,1),(242,484,2,2,'Product #484 description',0,1),(243,485,2,1,'Product #485 description',0,1),(244,485,2,2,'Product #485 description',0,1),(245,486,2,1,'Product #486 description',0,1),(246,486,2,2,'Product #486 description',0,1),(247,487,2,1,'Product #487 description',0,1),(248,487,2,2,'Product #487 description',0,1),(249,488,2,1,'Product #488 description',0,1);
insert into `catalog_product_attribute_text` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (250,488,2,2,'Product #488 description',0,1),(251,489,2,1,'Product #489 description',0,1),(252,489,2,2,'Product #489 description',0,1),(253,490,2,1,'Product #490 description',0,1),(254,490,2,2,'Product #490 description',0,1),(255,491,2,1,'Product #491 description',0,1),(256,491,2,2,'Product #491 description',0,1),(257,492,2,1,'Product #492 description',0,1),(258,492,2,2,'Product #492 description',0,1),(259,493,2,1,'Product #493 description',0,1),(260,493,2,2,'Product #493 description',0,1),(261,494,2,1,'Product #494 description',0,1),(262,494,2,2,'Product #494 description',0,1),(263,495,2,1,'Product #495 description',0,1),(264,495,2,2,'Product #495 description',0,1),(265,496,2,1,'Product #496 description',0,1),(266,496,2,2,'Product #496 description',0,1),(267,497,2,1,'Product #497 description',0,1),(268,497,2,2,'Product #497 description',0,1),(269,498,2,1,'Product #498 description',0,1),(270,498,2,2,'Product #498 description',0,1),(271,499,2,1,'Product #499 description',0,1),(272,499,2,2,'Product #499 description',0,1),(273,500,2,1,'Product #500 description',0,1),(274,500,2,2,'Product #500 description',0,1),(275,501,2,1,'Product #501 description',0,1),(276,501,2,2,'Product #501 description',0,1),(277,502,2,1,'Product #502 description',0,1);
insert into `catalog_product_attribute_text` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (278,502,2,2,'Product #502 description',0,1),(279,503,2,1,'Product #503 description',0,1),(280,503,2,2,'Product #503 description',0,1),(281,504,2,1,'Product #504 description',0,1),(282,504,2,2,'Product #504 description',0,1);

/*Table structure for table `catalog_product_attribute_varchar` */

DROP TABLE IF EXISTS `catalog_product_attribute_varchar`;

CREATE TABLE `catalog_product_attribute_varchar` (
  `value_id` bigint(16) unsigned NOT NULL auto_increment,
  `product_id` int(11) unsigned NOT NULL default '0',
  `attribute_id` smallint(6) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `attribute_value` varchar(255) NOT NULL default '',
  `parent_id` bigint(16) unsigned NOT NULL default '0',
  `is_inherit` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_VARCHAR` (`attribute_id`),
  KEY `FK_WEBSITE_VARCHAR` (`website_id`),
  KEY `IDX_VALUE_JOIN` (`product_id`,`attribute_id`,`website_id`),
  CONSTRAINT `FK_ATTRIBUTE_VARCHAR` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_product_attribute` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_VARCHAR` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_WEBSITE_VARCHAR` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Varchar attributes value';

/*Data for the table `catalog_product_attribute_varchar` */

insert into `catalog_product_attribute_varchar` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (247,405,1,1,'Product #405',0,1),(248,405,3,1,'product_small_image.jpg',0,1),(249,405,4,1,'MDL405',0,1),(250,405,1,2,'Product #405',0,1),(251,405,3,2,'product_small_image.jpg',0,1),(252,405,4,2,'MDL405',0,1),(253,406,1,1,'Product #406',0,1),(254,406,3,1,'product_small_image.jpg',0,1),(255,406,4,1,'MDL406',0,1),(256,406,1,2,'Product #406',0,1),(257,406,3,2,'product_small_image.jpg',0,1),(258,406,4,2,'MDL406',0,1),(259,407,1,1,'Product #407',0,1),(260,407,3,1,'product_small_image.jpg',0,1),(261,407,4,1,'MDL407',0,1),(262,407,1,2,'Product #407',0,1),(263,407,3,2,'product_small_image.jpg',0,1),(264,407,4,2,'MDL407',0,1),(265,408,1,1,'Product #408',0,1),(266,408,3,1,'product_small_image.jpg',0,1),(267,408,4,1,'MDL408',0,1),(268,408,1,2,'Product #408',0,1),(269,408,3,2,'product_small_image.jpg',0,1),(270,408,4,2,'MDL408',0,1),(271,409,1,1,'Product #409',0,1),(272,409,3,1,'product_small_image.jpg',0,1),(273,409,4,1,'MDL409',0,1),(274,409,1,2,'Product #409',0,1),(275,409,3,2,'product_small_image.jpg',0,1),(276,409,4,2,'MDL409',0,1),(277,410,1,1,'Product #410',0,1),(278,410,3,1,'product_small_image.jpg',0,1),(279,410,4,1,'MDL410',0,1),(280,410,1,2,'Product #410',0,1),(281,410,3,2,'product_small_image.jpg',0,1),(282,410,4,2,'MDL410',0,1),(283,411,1,1,'Product #411',0,1);
insert into `catalog_product_attribute_varchar` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (284,411,3,1,'product_small_image.jpg',0,1),(285,411,4,1,'MDL411',0,1),(286,411,1,2,'Product #411',0,1),(287,411,3,2,'product_small_image.jpg',0,1),(288,411,4,2,'MDL411',0,1),(289,412,1,1,'Product #412',0,1),(290,412,3,1,'product_small_image.jpg',0,1),(291,412,4,1,'MDL412',0,1),(292,412,1,2,'Product #412',0,1),(293,412,3,2,'product_small_image.jpg',0,1),(294,412,4,2,'MDL412',0,1),(295,413,1,1,'Product #413',0,1),(296,413,3,1,'product_small_image.jpg',0,1),(297,413,4,1,'MDL413',0,1),(298,413,1,2,'Product #413',0,1),(299,413,3,2,'product_small_image.jpg',0,1),(300,413,4,2,'MDL413',0,1),(301,414,1,1,'Product #414',0,1),(302,414,3,1,'product_small_image.jpg',0,1),(303,414,4,1,'MDL414',0,1),(304,414,1,2,'Product #414',0,1),(305,414,3,2,'product_small_image.jpg',0,1),(306,414,4,2,'MDL414',0,1),(307,415,1,1,'Product #415',0,1),(308,415,3,1,'product_small_image.jpg',0,1),(309,415,4,1,'MDL415',0,1),(310,415,1,2,'Product #415',0,1),(311,415,3,2,'product_small_image.jpg',0,1),(312,415,4,2,'MDL415',0,1),(313,416,1,1,'Product #416',0,1),(314,416,3,1,'product_small_image.jpg',0,1),(315,416,4,1,'MDL416',0,1),(316,416,1,2,'Product #416',0,1),(317,416,3,2,'product_small_image.jpg',0,1),(318,416,4,2,'MDL416',0,1),(319,417,1,1,'Product #417',0,1),(320,417,3,1,'product_small_image.jpg',0,1),(321,417,4,1,'MDL417',0,1);
insert into `catalog_product_attribute_varchar` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (322,417,1,2,'Product #417',0,1),(323,417,3,2,'product_small_image.jpg',0,1),(324,417,4,2,'MDL417',0,1),(325,418,1,1,'Product #418',0,1),(326,418,3,1,'product_small_image.jpg',0,1),(327,418,4,1,'MDL418',0,1),(328,418,1,2,'Product #418',0,1),(329,418,3,2,'product_small_image.jpg',0,1),(330,418,4,2,'MDL418',0,1),(331,419,1,1,'Product #419',0,1),(332,419,3,1,'product_small_image.jpg',0,1),(333,419,4,1,'MDL419',0,1),(334,419,1,2,'Product #419',0,1),(335,419,3,2,'product_small_image.jpg',0,1),(336,419,4,2,'MDL419',0,1),(337,420,1,1,'Product #420',0,1),(338,420,3,1,'product_small_image.jpg',0,1),(339,420,4,1,'MDL420',0,1),(340,420,1,2,'Product #420',0,1),(341,420,3,2,'product_small_image.jpg',0,1),(342,420,4,2,'MDL420',0,1),(343,421,1,1,'Product #421',0,1),(344,421,3,1,'product_small_image.jpg',0,1),(345,421,4,1,'MDL421',0,1),(346,421,1,2,'Product #421',0,1),(347,421,3,2,'product_small_image.jpg',0,1),(348,421,4,2,'MDL421',0,1),(349,422,1,1,'Product #422',0,1),(350,422,3,1,'product_small_image.jpg',0,1),(351,422,4,1,'MDL422',0,1),(352,422,1,2,'Product #422',0,1),(353,422,3,2,'product_small_image.jpg',0,1),(354,422,4,2,'MDL422',0,1),(355,423,1,1,'Product #423',0,1),(356,423,3,1,'product_small_image.jpg',0,1),(357,423,4,1,'MDL423',0,1),(358,423,1,2,'Product #423',0,1),(359,423,3,2,'product_small_image.jpg',0,1);
insert into `catalog_product_attribute_varchar` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (360,423,4,2,'MDL423',0,1),(361,424,1,1,'Product #424',0,1),(362,424,3,1,'product_small_image.jpg',0,1),(363,424,4,1,'MDL424',0,1),(364,424,1,2,'Product #424',0,1),(365,424,3,2,'product_small_image.jpg',0,1),(366,424,4,2,'MDL424',0,1),(367,425,1,1,'Product #425',0,1),(368,425,3,1,'product_small_image.jpg',0,1),(369,425,4,1,'MDL425',0,1),(370,425,1,2,'Product #425',0,1),(371,425,3,2,'product_small_image.jpg',0,1),(372,425,4,2,'MDL425',0,1),(373,426,1,1,'Product #426',0,1),(374,426,3,1,'product_small_image.jpg',0,1),(375,426,4,1,'MDL426',0,1),(376,426,1,2,'Product #426',0,1),(377,426,3,2,'product_small_image.jpg',0,1),(378,426,4,2,'MDL426',0,1),(379,427,1,1,'Product #427',0,1),(380,427,3,1,'product_small_image.jpg',0,1),(381,427,4,1,'MDL427',0,1),(382,427,1,2,'Product #427',0,1),(383,427,3,2,'product_small_image.jpg',0,1),(384,427,4,2,'MDL427',0,1),(385,428,1,1,'Product #428',0,1),(386,428,3,1,'product_small_image.jpg',0,1),(387,428,4,1,'MDL428',0,1),(388,428,1,2,'Product #428',0,1),(389,428,3,2,'product_small_image.jpg',0,1),(390,428,4,2,'MDL428',0,1),(391,429,1,1,'Product #429',0,1),(392,429,3,1,'product_small_image.jpg',0,1),(393,429,4,1,'MDL429',0,1),(394,429,1,2,'Product #429',0,1),(395,429,3,2,'product_small_image.jpg',0,1),(396,429,4,2,'MDL429',0,1),(397,430,1,1,'Product #430',0,1);
insert into `catalog_product_attribute_varchar` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (398,430,3,1,'product_small_image.jpg',0,1),(399,430,4,1,'MDL430',0,1),(400,430,1,2,'Product #430',0,1),(401,430,3,2,'product_small_image.jpg',0,1),(402,430,4,2,'MDL430',0,1),(403,431,1,1,'Product #431',0,1),(404,431,3,1,'product_small_image.jpg',0,1),(405,431,4,1,'MDL431',0,1),(406,431,1,2,'Product #431',0,1),(407,431,3,2,'product_small_image.jpg',0,1),(408,431,4,2,'MDL431',0,1),(409,432,1,1,'Product #432',0,1),(410,432,3,1,'product_small_image.jpg',0,1),(411,432,4,1,'MDL432',0,1),(412,432,1,2,'Product #432',0,1),(413,432,3,2,'product_small_image.jpg',0,1),(414,432,4,2,'MDL432',0,1),(415,433,1,1,'Product #433',0,1),(416,433,3,1,'product_small_image.jpg',0,1),(417,433,4,1,'MDL433',0,1),(418,433,1,2,'Product #433',0,1),(419,433,3,2,'product_small_image.jpg',0,1),(420,433,4,2,'MDL433',0,1),(421,434,1,1,'Product #434',0,1),(422,434,3,1,'product_small_image.jpg',0,1),(423,434,4,1,'MDL434',0,1),(424,434,1,2,'Product #434',0,1),(425,434,3,2,'product_small_image.jpg',0,1),(426,434,4,2,'MDL434',0,1),(427,435,1,1,'Product #435',0,1),(428,435,3,1,'product_small_image.jpg',0,1),(429,435,4,1,'MDL435',0,1),(430,435,1,2,'Product #435',0,1),(431,435,3,2,'product_small_image.jpg',0,1),(432,435,4,2,'MDL435',0,1),(433,436,1,1,'Product #436',0,1),(434,436,3,1,'product_small_image.jpg',0,1),(435,436,4,1,'MDL436',0,1);
insert into `catalog_product_attribute_varchar` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (436,436,1,2,'Product #436',0,1),(437,436,3,2,'product_small_image.jpg',0,1),(438,436,4,2,'MDL436',0,1),(439,437,1,1,'Product #437',0,1),(440,437,3,1,'product_small_image.jpg',0,1),(441,437,4,1,'MDL437',0,1),(442,437,1,2,'Product #437',0,1),(443,437,3,2,'product_small_image.jpg',0,1),(444,437,4,2,'MDL437',0,1),(445,438,1,1,'Product #438',0,1),(446,438,3,1,'product_small_image.jpg',0,1),(447,438,4,1,'MDL438',0,1),(448,438,1,2,'Product #438',0,1),(449,438,3,2,'product_small_image.jpg',0,1),(450,438,4,2,'MDL438',0,1),(451,439,1,1,'Product #439',0,1),(452,439,3,1,'product_small_image.jpg',0,1),(453,439,4,1,'MDL439',0,1),(454,439,1,2,'Product #439',0,1),(455,439,3,2,'product_small_image.jpg',0,1),(456,439,4,2,'MDL439',0,1),(457,440,1,1,'Product #440',0,1),(458,440,3,1,'product_small_image.jpg',0,1),(459,440,4,1,'MDL440',0,1),(460,440,1,2,'Product #440',0,1),(461,440,3,2,'product_small_image.jpg',0,1),(462,440,4,2,'MDL440',0,1),(463,441,1,1,'Product #441',0,1),(464,441,3,1,'product_small_image.jpg',0,1),(465,441,4,1,'MDL441',0,1),(466,441,1,2,'Product #441',0,1),(467,441,3,2,'product_small_image.jpg',0,1),(468,441,4,2,'MDL441',0,1),(469,442,1,1,'Product #442',0,1),(470,442,3,1,'product_small_image.jpg',0,1),(471,442,4,1,'MDL442',0,1),(472,442,1,2,'Product #442',0,1),(473,442,3,2,'product_small_image.jpg',0,1);
insert into `catalog_product_attribute_varchar` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (474,442,4,2,'MDL442',0,1),(475,443,1,1,'Product #443',0,1),(476,443,3,1,'product_small_image.jpg',0,1),(477,443,4,1,'MDL443',0,1),(478,443,1,2,'Product #443',0,1),(479,443,3,2,'product_small_image.jpg',0,1),(480,443,4,2,'MDL443',0,1),(481,444,1,1,'Product #444',0,1),(482,444,3,1,'product_small_image.jpg',0,1),(483,444,4,1,'MDL444',0,1),(484,444,1,2,'Product #444',0,1),(485,444,3,2,'product_small_image.jpg',0,1),(486,444,4,2,'MDL444',0,1),(487,445,1,1,'Product #445',0,1),(488,445,3,1,'product_small_image.jpg',0,1),(489,445,4,1,'MDL445',0,1),(490,445,1,2,'Product #445',0,1),(491,445,3,2,'product_small_image.jpg',0,1),(492,445,4,2,'MDL445',0,1),(493,446,1,1,'Product #446',0,1),(494,446,3,1,'product_small_image.jpg',0,1),(495,446,4,1,'MDL446',0,1),(496,446,1,2,'Product #446',0,1),(497,446,3,2,'product_small_image.jpg',0,1),(498,446,4,2,'MDL446',0,1),(499,447,1,1,'Product #447',0,1),(500,447,3,1,'product_small_image.jpg',0,1),(501,447,4,1,'MDL447',0,1),(502,447,1,2,'Product #447',0,1),(503,447,3,2,'product_small_image.jpg',0,1),(504,447,4,2,'MDL447',0,1),(505,448,1,1,'Product #448',0,1),(506,448,3,1,'product_small_image.jpg',0,1),(507,448,4,1,'MDL448',0,1),(508,448,1,2,'Product #448',0,1),(509,448,3,2,'product_small_image.jpg',0,1),(510,448,4,2,'MDL448',0,1),(511,449,1,1,'Product #449',0,1);
insert into `catalog_product_attribute_varchar` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (512,449,3,1,'product_small_image.jpg',0,1),(513,449,4,1,'MDL449',0,1),(514,449,1,2,'Product #449',0,1),(515,449,3,2,'product_small_image.jpg',0,1),(516,449,4,2,'MDL449',0,1),(517,450,1,1,'Product #450',0,1),(518,450,3,1,'product_small_image.jpg',0,1),(519,450,4,1,'MDL450',0,1),(520,450,1,2,'Product #450',0,1),(521,450,3,2,'product_small_image.jpg',0,1),(522,450,4,2,'MDL450',0,1),(523,451,1,1,'Product #451',0,1),(524,451,3,1,'product_small_image.jpg',0,1),(525,451,4,1,'MDL451',0,1),(526,451,1,2,'Product #451',0,1),(527,451,3,2,'product_small_image.jpg',0,1),(528,451,4,2,'MDL451',0,1),(529,452,1,1,'Product #452',0,1),(530,452,3,1,'product_small_image.jpg',0,1),(531,452,4,1,'MDL452',0,1),(532,452,1,2,'Product #452',0,1),(533,452,3,2,'product_small_image.jpg',0,1),(534,452,4,2,'MDL452',0,1),(535,453,1,1,'Product #453',0,1),(536,453,3,1,'product_small_image.jpg',0,1),(537,453,4,1,'MDL453',0,1),(538,453,1,2,'Product #453',0,1),(539,453,3,2,'product_small_image.jpg',0,1),(540,453,4,2,'MDL453',0,1),(541,454,1,1,'Product #454',0,1),(542,454,3,1,'product_small_image.jpg',0,1),(543,454,4,1,'MDL454',0,1),(544,454,1,2,'Product #454',0,1),(545,454,3,2,'product_small_image.jpg',0,1),(546,454,4,2,'MDL454',0,1),(547,455,1,1,'Product #455',0,1),(548,455,3,1,'product_small_image.jpg',0,1),(549,455,4,1,'MDL455',0,1);
insert into `catalog_product_attribute_varchar` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (550,455,1,2,'Product #455',0,1),(551,455,3,2,'product_small_image.jpg',0,1),(552,455,4,2,'MDL455',0,1),(553,456,1,1,'Product #456',0,1),(554,456,3,1,'product_small_image.jpg',0,1),(555,456,4,1,'MDL456',0,1),(556,456,1,2,'Product #456',0,1),(557,456,3,2,'product_small_image.jpg',0,1),(558,456,4,2,'MDL456',0,1),(559,457,1,1,'Product #457',0,1),(560,457,3,1,'product_small_image.jpg',0,1),(561,457,4,1,'MDL457',0,1),(562,457,1,2,'Product #457',0,1),(563,457,3,2,'product_small_image.jpg',0,1),(564,457,4,2,'MDL457',0,1),(565,458,1,1,'Product #458',0,1),(566,458,3,1,'product_small_image.jpg',0,1),(567,458,4,1,'MDL458',0,1),(568,458,1,2,'Product #458',0,1),(569,458,3,2,'product_small_image.jpg',0,1),(570,458,4,2,'MDL458',0,1),(571,459,1,1,'Product #459',0,1),(572,459,3,1,'product_small_image.jpg',0,1),(573,459,4,1,'MDL459',0,1),(574,459,1,2,'Product #459',0,1),(575,459,3,2,'product_small_image.jpg',0,1),(576,459,4,2,'MDL459',0,1),(577,460,1,1,'Product #460',0,1),(578,460,3,1,'product_small_image.jpg',0,1),(579,460,4,1,'MDL460',0,1),(580,460,1,2,'Product #460',0,1),(581,460,3,2,'product_small_image.jpg',0,1),(582,460,4,2,'MDL460',0,1),(583,461,1,1,'Product #461',0,1),(584,461,3,1,'product_small_image.jpg',0,1),(585,461,4,1,'MDL461',0,1),(586,461,1,2,'Product #461',0,1),(587,461,3,2,'product_small_image.jpg',0,1);
insert into `catalog_product_attribute_varchar` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (588,461,4,2,'MDL461',0,1),(589,462,1,1,'Product #462',0,1),(590,462,3,1,'product_small_image.jpg',0,1),(591,462,4,1,'MDL462',0,1),(592,462,1,2,'Product #462',0,1),(593,462,3,2,'product_small_image.jpg',0,1),(594,462,4,2,'MDL462',0,1),(595,463,1,1,'Product #463',0,1),(596,463,3,1,'product_small_image.jpg',0,1),(597,463,4,1,'MDL463',0,1),(598,463,1,2,'Product #463',0,1),(599,463,3,2,'product_small_image.jpg',0,1),(600,463,4,2,'MDL463',0,1),(601,464,1,1,'Product #464',0,1),(602,464,3,1,'product_small_image.jpg',0,1),(603,464,4,1,'MDL464',0,1),(604,464,1,2,'Product #464',0,1),(605,464,3,2,'product_small_image.jpg',0,1),(606,464,4,2,'MDL464',0,1),(607,465,1,1,'Product #465',0,1),(608,465,3,1,'product_small_image.jpg',0,1),(609,465,4,1,'MDL465',0,1),(610,465,1,2,'Product #465',0,1),(611,465,3,2,'product_small_image.jpg',0,1),(612,465,4,2,'MDL465',0,1),(613,466,1,1,'Product #466',0,1),(614,466,3,1,'product_small_image.jpg',0,1),(615,466,4,1,'MDL466',0,1),(616,466,1,2,'Product #466',0,1),(617,466,3,2,'product_small_image.jpg',0,1),(618,466,4,2,'MDL466',0,1),(619,467,1,1,'Product #467',0,1),(620,467,3,1,'product_small_image.jpg',0,1),(621,467,4,1,'MDL467',0,1),(622,467,1,2,'Product #467',0,1),(623,467,3,2,'product_small_image.jpg',0,1),(624,467,4,2,'MDL467',0,1),(625,468,1,1,'Product #468',0,1);
insert into `catalog_product_attribute_varchar` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (626,468,3,1,'product_small_image.jpg',0,1),(627,468,4,1,'MDL468',0,1),(628,468,1,2,'Product #468',0,1),(629,468,3,2,'product_small_image.jpg',0,1),(630,468,4,2,'MDL468',0,1),(631,469,1,1,'Product #469',0,1),(632,469,3,1,'product_small_image.jpg',0,1),(633,469,4,1,'MDL469',0,1),(634,469,1,2,'Product #469',0,1),(635,469,3,2,'product_small_image.jpg',0,1),(636,469,4,2,'MDL469',0,1),(637,470,1,1,'Product #470',0,1),(638,470,3,1,'product_small_image.jpg',0,1),(639,470,4,1,'MDL470',0,1),(640,470,1,2,'Product #470',0,1),(641,470,3,2,'product_small_image.jpg',0,1),(642,470,4,2,'MDL470',0,1),(643,471,1,1,'Product #471',0,1),(644,471,3,1,'product_small_image.jpg',0,1),(645,471,4,1,'MDL471',0,1),(646,471,1,2,'Product #471',0,1),(647,471,3,2,'product_small_image.jpg',0,1),(648,471,4,2,'MDL471',0,1),(649,472,1,1,'Product #472',0,1),(650,472,3,1,'product_small_image.jpg',0,1),(651,472,4,1,'MDL472',0,1),(652,472,1,2,'Product #472',0,1),(653,472,3,2,'product_small_image.jpg',0,1),(654,472,4,2,'MDL472',0,1),(655,473,1,1,'Product #473',0,1),(656,473,3,1,'product_small_image.jpg',0,1),(657,473,4,1,'MDL473',0,1),(658,473,1,2,'Product #473',0,1),(659,473,3,2,'product_small_image.jpg',0,1),(660,473,4,2,'MDL473',0,1),(661,474,1,1,'Product #474',0,1),(662,474,3,1,'product_small_image.jpg',0,1),(663,474,4,1,'MDL474',0,1);
insert into `catalog_product_attribute_varchar` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (664,474,1,2,'Product #474',0,1),(665,474,3,2,'product_small_image.jpg',0,1),(666,474,4,2,'MDL474',0,1),(667,475,1,1,'Product #475',0,1),(668,475,3,1,'product_small_image.jpg',0,1),(669,475,4,1,'MDL475',0,1),(670,475,1,2,'Product #475',0,1),(671,475,3,2,'product_small_image.jpg',0,1),(672,475,4,2,'MDL475',0,1),(673,476,1,1,'Product #476',0,1),(674,476,3,1,'product_small_image.jpg',0,1),(675,476,4,1,'MDL476',0,1),(676,476,1,2,'Product #476',0,1),(677,476,3,2,'product_small_image.jpg',0,1),(678,476,4,2,'MDL476',0,1),(679,477,1,1,'Product #477',0,1),(680,477,3,1,'product_small_image.jpg',0,1),(681,477,4,1,'MDL477',0,1),(682,477,1,2,'Product #477',0,1),(683,477,3,2,'product_small_image.jpg',0,1),(684,477,4,2,'MDL477',0,1),(685,478,1,1,'Product #478',0,1),(686,478,3,1,'product_small_image.jpg',0,1),(687,478,4,1,'MDL478',0,1),(688,478,1,2,'Product #478',0,1),(689,478,3,2,'product_small_image.jpg',0,1),(690,478,4,2,'MDL478',0,1),(691,479,1,1,'Product #479',0,1),(692,479,3,1,'product_small_image.jpg',0,1),(693,479,4,1,'MDL479',0,1),(694,479,1,2,'Product #479',0,1),(695,479,3,2,'product_small_image.jpg',0,1),(696,479,4,2,'MDL479',0,1),(697,480,1,1,'Product #480',0,1),(698,480,3,1,'product_small_image.jpg',0,1),(699,480,4,1,'MDL480',0,1),(700,480,1,2,'Product #480',0,1),(701,480,3,2,'product_small_image.jpg',0,1);
insert into `catalog_product_attribute_varchar` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (702,480,4,2,'MDL480',0,1),(703,481,1,1,'Product #481',0,1),(704,481,3,1,'product_small_image.jpg',0,1),(705,481,4,1,'MDL481',0,1),(706,481,1,2,'Product #481',0,1),(707,481,3,2,'product_small_image.jpg',0,1),(708,481,4,2,'MDL481',0,1),(709,482,1,1,'Product #482',0,1),(710,482,3,1,'product_small_image.jpg',0,1),(711,482,4,1,'MDL482',0,1),(712,482,1,2,'Product #482',0,1),(713,482,3,2,'product_small_image.jpg',0,1),(714,482,4,2,'MDL482',0,1),(715,483,1,1,'Product #483',0,1),(716,483,3,1,'product_small_image.jpg',0,1),(717,483,4,1,'MDL483',0,1),(718,483,1,2,'Product #483',0,1),(719,483,3,2,'product_small_image.jpg',0,1),(720,483,4,2,'MDL483',0,1),(721,484,1,1,'Product #484',0,1),(722,484,3,1,'product_small_image.jpg',0,1),(723,484,4,1,'MDL484',0,1),(724,484,1,2,'Product #484',0,1),(725,484,3,2,'product_small_image.jpg',0,1),(726,484,4,2,'MDL484',0,1),(727,485,1,1,'Product #485',0,1),(728,485,3,1,'product_small_image.jpg',0,1),(729,485,4,1,'MDL485',0,1),(730,485,1,2,'Product #485',0,1),(731,485,3,2,'product_small_image.jpg',0,1),(732,485,4,2,'MDL485',0,1),(733,486,1,1,'Product #486',0,1),(734,486,3,1,'product_small_image.jpg',0,1),(735,486,4,1,'MDL486',0,1),(736,486,1,2,'Product #486',0,1),(737,486,3,2,'product_small_image.jpg',0,1),(738,486,4,2,'MDL486',0,1),(739,487,1,1,'Product #487',0,1);
insert into `catalog_product_attribute_varchar` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (740,487,3,1,'product_small_image.jpg',0,1),(741,487,4,1,'MDL487',0,1),(742,487,1,2,'Product #487',0,1),(743,487,3,2,'product_small_image.jpg',0,1),(744,487,4,2,'MDL487',0,1),(745,488,1,1,'Product #488',0,1),(746,488,3,1,'product_small_image.jpg',0,1),(747,488,4,1,'MDL488',0,1),(748,488,1,2,'Product #488',0,1),(749,488,3,2,'product_small_image.jpg',0,1),(750,488,4,2,'MDL488',0,1),(751,489,1,1,'Product #489',0,1),(752,489,3,1,'product_small_image.jpg',0,1),(753,489,4,1,'MDL489',0,1),(754,489,1,2,'Product #489',0,1),(755,489,3,2,'product_small_image.jpg',0,1),(756,489,4,2,'MDL489',0,1),(757,490,1,1,'Product #490',0,1),(758,490,3,1,'product_small_image.jpg',0,1),(759,490,4,1,'MDL490',0,1),(760,490,1,2,'Product #490',0,1),(761,490,3,2,'product_small_image.jpg',0,1),(762,490,4,2,'MDL490',0,1),(763,491,1,1,'Product #491',0,1),(764,491,3,1,'product_small_image.jpg',0,1),(765,491,4,1,'MDL491',0,1),(766,491,1,2,'Product #491',0,1),(767,491,3,2,'product_small_image.jpg',0,1),(768,491,4,2,'MDL491',0,1),(769,492,1,1,'Product #492',0,1),(770,492,3,1,'product_small_image.jpg',0,1),(771,492,4,1,'MDL492',0,1),(772,492,1,2,'Product #492',0,1),(773,492,3,2,'product_small_image.jpg',0,1),(774,492,4,2,'MDL492',0,1),(775,493,1,1,'Product #493',0,1),(776,493,3,1,'product_small_image.jpg',0,1),(777,493,4,1,'MDL493',0,1);
insert into `catalog_product_attribute_varchar` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (778,493,1,2,'Product #493',0,1),(779,493,3,2,'product_small_image.jpg',0,1),(780,493,4,2,'MDL493',0,1),(781,494,1,1,'Product #494',0,1),(782,494,3,1,'product_small_image.jpg',0,1),(783,494,4,1,'MDL494',0,1),(784,494,1,2,'Product #494',0,1),(785,494,3,2,'product_small_image.jpg',0,1),(786,494,4,2,'MDL494',0,1),(787,495,1,1,'Product #495',0,1),(788,495,3,1,'product_small_image.jpg',0,1),(789,495,4,1,'MDL495',0,1),(790,495,1,2,'Product #495',0,1),(791,495,3,2,'product_small_image.jpg',0,1),(792,495,4,2,'MDL495',0,1),(793,496,1,1,'Product #496',0,1),(794,496,3,1,'product_small_image.jpg',0,1),(795,496,4,1,'MDL496',0,1),(796,496,1,2,'Product #496',0,1),(797,496,3,2,'product_small_image.jpg',0,1),(798,496,4,2,'MDL496',0,1),(799,497,1,1,'Product #497',0,1),(800,497,3,1,'product_small_image.jpg',0,1),(801,497,4,1,'MDL497',0,1),(802,497,1,2,'Product #497',0,1),(803,497,3,2,'product_small_image.jpg',0,1),(804,497,4,2,'MDL497',0,1),(805,498,1,1,'Product #498',0,1),(806,498,3,1,'product_small_image.jpg',0,1),(807,498,4,1,'MDL498',0,1),(808,498,1,2,'Product #498',0,1),(809,498,3,2,'product_small_image.jpg',0,1),(810,498,4,2,'MDL498',0,1),(811,499,1,1,'Product #499',0,1),(812,499,3,1,'product_small_image.jpg',0,1),(813,499,4,1,'MDL499',0,1),(814,499,1,2,'Product #499',0,1),(815,499,3,2,'product_small_image.jpg',0,1);
insert into `catalog_product_attribute_varchar` (`value_id`,`product_id`,`attribute_id`,`website_id`,`attribute_value`,`parent_id`,`is_inherit`) values (816,499,4,2,'MDL499',0,1),(817,500,1,1,'Product #500',0,1),(818,500,3,1,'product_small_image.jpg',0,1),(819,500,4,1,'MDL500',0,1),(820,500,1,2,'Product #500',0,1),(821,500,3,2,'product_small_image.jpg',0,1),(822,500,4,2,'MDL500',0,1),(823,501,1,1,'Product #501',0,1),(824,501,3,1,'product_small_image.jpg',0,1),(825,501,4,1,'MDL501',0,1),(826,501,1,2,'Product #501',0,1),(827,501,3,2,'product_small_image.jpg',0,1),(828,501,4,2,'MDL501',0,1),(829,502,1,1,'Product #502',0,1),(830,502,3,1,'product_small_image.jpg',0,1),(831,502,4,1,'MDL502',0,1),(832,502,1,2,'Product #502',0,1),(833,502,3,2,'product_small_image.jpg',0,1),(834,502,4,2,'MDL502',0,1),(835,503,1,1,'Product #503',0,1),(836,503,3,1,'product_small_image.jpg',0,1),(837,503,4,1,'MDL503',0,1),(838,503,1,2,'Product #503',0,1),(839,503,3,2,'product_small_image.jpg',0,1),(840,503,4,2,'MDL503',0,1),(841,504,1,1,'Product #504',0,1),(842,504,3,1,'product_small_image.jpg',0,1),(843,504,4,1,'MDL504',0,1),(844,504,1,2,'Product #504',0,1),(845,504,3,2,'product_small_image.jpg',0,1),(846,504,4,2,'MDL504',0,1),(847,434,3,1,'test.jpg',0,1),(848,434,3,1,'test1.jpg',0,1);

/*Table structure for table `catalog_product_link` */

DROP TABLE IF EXISTS `catalog_product_link`;

CREATE TABLE `catalog_product_link` (
  `link_id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(11) unsigned NOT NULL default '0',
  `linked_product_id` int(11) unsigned NOT NULL default '0',
  `link_type_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`link_id`),
  KEY `FK_LINK_PRODUCT` (`product_id`),
  KEY `FK_LINKED_PRODUCT` (`linked_product_id`),
  KEY `FK_PRODUCT_LINK_TYPE` (`link_type_id`),
  CONSTRAINT `FK_LINKED_PRODUCT` FOREIGN KEY (`linked_product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_LINK_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_LINK_TYPE` FOREIGN KEY (`link_type_id`) REFERENCES `catalog_product_link_type` (`link_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Related products';

/*Data for the table `catalog_product_link` */

/*Table structure for table `catalog_product_link_attribute` */

DROP TABLE IF EXISTS `catalog_product_link_attribute`;

CREATE TABLE `catalog_product_link_attribute` (
  `product_link_attribute_id` smallint(6) unsigned NOT NULL auto_increment,
  `link_type_id` tinyint(3) unsigned NOT NULL default '0',
  `product_link_attribute_code` varchar(32) NOT NULL default '',
  `data_type` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`product_link_attribute_id`),
  KEY `FK_ATTRIBUTE_PRODUCT_LINK_TYPE` (`link_type_id`),
  CONSTRAINT `FK_ATTRIBUTE_PRODUCT_LINK_TYPE` FOREIGN KEY (`link_type_id`) REFERENCES `catalog_product_link_type` (`link_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Attributes for product link';

/*Data for the table `catalog_product_link_attribute` */

insert into `catalog_product_link_attribute` (`product_link_attribute_id`,`link_type_id`,`product_link_attribute_code`,`data_type`) values (1,2,'qty','decimal');

/*Table structure for table `catalog_product_link_attribute_decimal` */

DROP TABLE IF EXISTS `catalog_product_link_attribute_decimal`;

CREATE TABLE `catalog_product_link_attribute_decimal` (
  `value_id` int(11) unsigned NOT NULL auto_increment,
  `product_link_attribute_id` smallint(6) unsigned default NULL,
  `link_id` int(11) unsigned default NULL,
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_DECIMAL_PRODUCT_LINK_ATTRIBUTE` (`product_link_attribute_id`),
  KEY `FK_DECIMAL_LINK` (`link_id`),
  CONSTRAINT `FK_DECIMAL_LINK` FOREIGN KEY (`link_id`) REFERENCES `catalog_product_link` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_DECIMAL_PRODUCT_LINK_ATTRIBUTE` FOREIGN KEY (`product_link_attribute_id`) REFERENCES `catalog_product_link_attribute` (`product_link_attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Decimal attributes values';

/*Data for the table `catalog_product_link_attribute_decimal` */

/*Table structure for table `catalog_product_link_attribute_varchar` */

DROP TABLE IF EXISTS `catalog_product_link_attribute_varchar`;

CREATE TABLE `catalog_product_link_attribute_varchar` (
  `value_id` int(11) unsigned NOT NULL auto_increment,
  `product_link_attribute_id` smallint(6) unsigned NOT NULL default '0',
  `link_id` int(11) unsigned default NULL,
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_VARCHAR_PRODUCT_LINK_ATTRIBUTE` (`product_link_attribute_id`),
  KEY `FK_VARCHAR_LINK` (`link_id`),
  CONSTRAINT `FK_VARCHAR_LINK` FOREIGN KEY (`link_id`) REFERENCES `catalog_product_link` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_VARCHAR_PRODUCT_LINK_ATTRIBUTE` FOREIGN KEY (`product_link_attribute_id`) REFERENCES `catalog_product_link_attribute` (`product_link_attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Varchar attributes values';

/*Data for the table `catalog_product_link_attribute_varchar` */

/*Table structure for table `catalog_product_link_type` */

DROP TABLE IF EXISTS `catalog_product_link_type`;

CREATE TABLE `catalog_product_link_type` (
  `link_type_id` tinyint(3) unsigned NOT NULL auto_increment,
  `link_type_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`link_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Types of product link(Related, superproduct, bundles)';

/*Data for the table `catalog_product_link_type` */

insert into `catalog_product_link_type` (`link_type_id`,`link_type_code`) values (1,'relation'),(2,'bundles'),(3,'superproduct');

/*Table structure for table `catalog_product_status` */

DROP TABLE IF EXISTS `catalog_product_status`;

CREATE TABLE `catalog_product_status` (
  `status_id` tinyint(3) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '',
  PRIMARY KEY  USING BTREE (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='System product statuses (active, no active, deleted, etc.)';

/*Data for the table `catalog_product_status` */

insert into `catalog_product_status` (`status_id`,`code`) values (1,'Active');

/*Table structure for table `catalog_product_website` */

DROP TABLE IF EXISTS `catalog_product_website`;

CREATE TABLE `catalog_product_website` (
  `product_id` int(10) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned NOT NULL default '0',
  `status_id` tinyint(3) unsigned default NULL,
  PRIMARY KEY  (`product_id`,`website_id`),
  KEY `FK_PRODUCT_WEBSITE` (`website_id`),
  KEY `FK_PEODUCT_WEBSITE_STATUS` (`status_id`),
  CONSTRAINT `FK_PEODUCT_WEBSITE_STATUS` FOREIGN KEY (`status_id`) REFERENCES `catalog_product_status` (`status_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_PRODUCT_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_WEBSITE_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

/*Data for the table `catalog_product_website` */

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
