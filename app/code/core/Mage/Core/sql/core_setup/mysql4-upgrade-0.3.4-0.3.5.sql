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

/*Table structure for table `core_config_attribute` */


DROP TABLE IF EXISTS `core_store`;

CREATE TABLE `core_store` (
  `store_id` smallint(5) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '',
  `language_code` varchar(2) default NULL,
  `website_id` smallint(5) unsigned default '0',
  `name` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`store_id`),
  UNIQUE KEY `code` (`code`),
  KEY `FK_STORE_LANGUAGE` (`language_code`),
  KEY `FK_STORE_WEBSITE` (`website_id`),
  CONSTRAINT `FK_STORE_LANGUAGE` FOREIGN KEY (`language_code`) REFERENCES `core_language` (`language_code`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `FK_STORE_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores';

/*Data for the table `core_store` */

insert  into `core_store`(`store_id`,`code`,`language_code`,`website_id`,`name`) values (1,'base','en',1,'English'),(2,'russian','ru',1,'Russian');

/*Table structure for table `core_website` */

DROP TABLE IF EXISTS `core_website`;

CREATE TABLE `core_website` (
  `website_id` smallint(5) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '',
  `name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Websites';

/*Data for the table `core_website` */

insert  into `core_website`(`website_id`,`code`,`name`) values (1,'base','Default');

DROP TABLE IF EXISTS `core_config_data`;
CREATE TABLE `core_config_data` (
  `config_id` int(10) unsigned NOT NULL auto_increment,
  `scope` enum('default','website','store','config') NOT NULL default 'default',
  `scope_id` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default 'general',
  `data` varchar(255) NOT NULL default '',
  `inherit` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`config_id`),
  UNIQUE KEY `config_scope` (`scope`,`scope_id`,`path`)
) ENGINE=InnoDb DEFAULT CHARSET=utf8;

/*Data for the table `core_config_value` */

insert  into `core_config_data`(`config_id`,`scope`,`scope_id`,`path`,`data`,`inherit`) values 
(1,'default',0,'general/currency/base','USD',0),
(2,'default',0,'general/currency/allow','USD,CAD,UAH,RUB',0),
(3,'default',0,'general/currency/default','USD',0),
(4,'default',0,'general/local/language','en',0),
(6,'default',0,'system/filesystem/layout','/home/moshe/dev/magento/app/view/layout',0),
(7,'default',0,'system/filesystem/template','/home/moshe/dev/magento/app/view/template',0),
(8,'default',0,'system/filesystem/translate','/home/moshe/dev/magento/app/view/translate',0),
(9,'default',0,'system/filesystem/base','/home/moshe/dev/magento',0),
(10,'default',0,'system/filesystem/media','/home/moshe/dev/magento/media',0),
(11,'default',0,'system/filesystem/skin','/home/moshe/dev/magento/skins/default',0),
(12,'default',0,'web/unsecure/protocol','http',0),
(13,'default',0,'web/unsecure/host','var-dev.varien.com',0),
(14,'default',0,'web/unsecure/port','81',0),
(15,'default',0,'web/unsecure/base_path','/dev/moshe/magento/',0),
(16,'default',0,'web/secure/protocol','https',0),
(17,'default',0,'web/secure/host','var-dev.varien.com',0),
(18,'default',0,'web/secure/port','444',0),
(19,'default',0,'web/secure/base_path','/dev/moshe/magento/',0),
(20,'default',0,'web/url/media','/dev/moshe/magento/media/',0),
(21,'default',0,'web/url/skin','/dev/moshe/magento/skins/default/',0),
(22,'default',0,'web/url/js','/dev/moshe/magento/js/',0),
(23,'default',0,'system/filesystem/etc','/home/moshe/dev/magento/app/etc/',0),
(24,'default',0,'system/filesystem/code','/home/moshe/dev/magento/app/code/',0),
(25,'default',0,'system/filesystem/upload','/home/moshe/dev/magento/media/upload/',0),
(26,'default',0,'system/filesystem/var','/home/moshe/dev/magento/var/',0),
(27,'default',0,'system/filesystem/session','/home/moshe/dev/magento/var/session/',0),
(28,'default',0,'system/filesystem/cache_config','/home/moshe/dev/magento/var/cache/config/',0),
(29,'default',0,'system/filesystem/cache_layout','/home/moshe/dev/magento/var/cache/layout/',0),
(35,'default',0,'general/country/default','US',0),
(34,'default',0,'web/default/no_route','core/index/noRoute',0),
(32,'default',0,'web/default/front','catalog',0),
(36,'default',0,'general/country/allow','US,CA,UA',0),
(37,'default',0,'general/local/date_format_mysql','%a, %b %e %Y',0),
(38,'default',0,'general/local/date_format_php','a, b e Y',0),
(39,'default',0,'advanced/datashare/customer','1',0),
(40,'default',0,'advanced/datashare/customer_address','1',0),
(41,'default',0,'advanced/datashare/quote','1',0),
(42,'default',0,'advanced/datashare/quote_address','1',0),
(43,'default',0,'advanced/datashare/order','1',0),
(44,'default',0,'advanced/datashare/order_address','1',0),
(45,'default',0,'advanced/datashare/order_payment','1',0),
(46,'default',0,'advanced/datashare/wishlist','1',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
