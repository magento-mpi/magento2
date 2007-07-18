/*
SQLyog Enterprise - MySQL GUI v6.03
Host - 4.1.20 : Database - magento_moshe
*********************************************************************
Server version : 4.1.20
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `core_config_field` */

DROP TABLE IF EXISTS `core_config_field`;

CREATE TABLE `core_config_field` (
  `field_id` int(10) unsigned NOT NULL auto_increment,
  `path` varchar(255) NOT NULL default '',
  `frontend_label` varchar(255) NOT NULL default '',
  `frontend_type` varchar(64) NOT NULL default 'text',
  `frontend_class` varchar(255) NOT NULL default '',
  `frontend_model` varchar(255) NOT NULL default '',
  `backend_model` varchar(255) NOT NULL default '',
  `source_model` varchar(255) NOT NULL default '',
  `sort_order` smallint(5) unsigned NOT NULL default '0',
  `show_in_default` tinyint(4) NOT NULL default '1',
  `show_in_website` tinyint(4) NOT NULL default '1',
  `show_in_store` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`field_id`),
  KEY `path` (`path`,`sort_order`)
) ENGINE=InnoDb DEFAULT CHARSET=utf8;

/*Data for the table `core_config_field` */

insert  into `core_config_field`(`field_id`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`) values (1,'advanced/datashare/customer','Customer','text','','','','',0,1,1,1),(2,'advanced/datashare/customer_address','Customer Address','text','','','','',0,1,1,1),(3,'advanced/datashare/order','Order','text','','','','',0,1,1,1),(4,'advanced/datashare/order_address','Order Address','text','','','','',0,1,1,1),(5,'advanced/datashare/order_payment','Order Payment','text','','','','',0,1,1,1),(6,'advanced/datashare/quote','Shopping cart / Quote','text','','','','',0,1,1,1),(7,'advanced/datashare/quote_address','Shopping cart / Quote Address','text','','','','',0,1,1,1),(8,'advanced/datashare/wishlist','Wishlist','text','','','','',0,1,1,1),(9,'general/country/allow','Allow countries','text','','','','',0,1,1,1),(10,'general/country/default','Default country','text','','','','',0,1,1,1),(11,'general/currency/allow','Allow currencies','text','','','','',0,1,1,1),(12,'general/currency/base','Base currency','text','','','','',0,1,1,1),(13,'general/currency/default','Default currency','text','','','','',0,1,1,1),(14,'general/local/date_format_mysql','Date format (db/deprecated)','text','','','','',0,1,1,1),(15,'general/local/date_format_php','Date format','text','','','','',0,1,1,1),(16,'general/local/language','Language','text','','','','',0,1,1,1),(17,'system/filesystem/base','Base directory','text','','','','',0,1,1,1),(18,'system/filesystem/cache_config','Config cache directory','text','','','','',0,1,1,1),(19,'system/filesystem/cache_layout','Layout cache directory','text','','','','',0,1,1,1),(20,'system/filesystem/code','Code pools root directory','text','','','','',0,1,1,1),(21,'system/filesystem/etc','Configuration directory','text','','','','',0,1,1,1),(22,'system/filesystem/layout','Layout files directory','text','','','','',0,1,1,1),(23,'system/filesystem/media','Media files directory','text','','','','',0,1,1,1),(24,'system/filesystem/session','Session files directory','text','','','','',0,1,1,1),(25,'system/filesystem/skin','Skin directory','text','','','','',0,1,1,1),(26,'system/filesystem/template','Template directory','text','','','','',0,1,1,1),(27,'system/filesystem/translate','Translactions directory','text','','','','',0,1,1,1),(28,'system/filesystem/upload','Upload directory','text','','','','',0,1,1,1),(29,'system/filesystem/var','Var (temporary files) directory','text','','','','',0,1,1,1),(30,'web/default/front','Default web url','text','','','','',0,1,1,1),(31,'web/default/no_route','Default no-route url','text','','','','',0,1,1,1),(32,'web/secure/base_path','Base url','text','','','','',0,1,1,1),(33,'web/secure/host','Host','text','','','','',0,1,1,1),(34,'web/secure/port','Port','text','','','','',0,1,1,1),(35,'web/secure/protocol','Protocol','text','','','','',0,1,1,1),(36,'web/unsecure/base_path','Base url','text','','','','',0,1,1,1),(37,'web/unsecure/host','Host','text','','','','',0,1,1,1),(38,'web/unsecure/port','Port','text','','','','',0,1,1,1),(39,'web/unsecure/protocol','Protocol','text','','','','',0,1,1,1),(40,'web/url/js','Js base url','text','','','','',0,1,1,1),(41,'web/url/media','Media base url','text','','','','',0,1,1,1),(42,'web/url/skin','Skin base url','text','','','','',0,1,1,1),(43,'general','General','text','','','','',0,1,1,1),(44,'web','Web','text','','','','',0,1,1,1),(45,'system','System','text','','','','',0,1,1,1),(46,'advanced','Advanced','text','','','','',0,1,1,1),(47,'web/default','Default','text','','','','',0,1,1,1),(48,'web/secure','Secure','text','','','','',0,1,1,1),(49,'web/unsecure','Unsecure','text','','','','',0,1,1,1),(50,'web/url','URLs','text','','','','',0,1,1,1),(51,'system/filesystem','Filesystem','text','','','','',0,1,1,1),(52,'general/currency','Currency options','text','','','','',0,1,1,1),(53,'general/local','Local options','text','','','','',0,1,1,1),(54,'general/country','Countries options','text','','','','',0,1,1,1),(55,'advanced/datashare','Datasharing','text','','','','',0,1,1,1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
