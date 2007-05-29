/*
SQLyog Enterprise - MySQL GUI v5.13
Host - 4.1.21-community-nt : Database - magento
*********************************************************************
Server version : 4.1.21-community-nt
*/

SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `log_visitor` */

DROP TABLE IF EXISTS `log_visitor`;

CREATE TABLE `log_visitor` (
  `session_id` varchar(255) NOT NULL default '',
  `first_visit_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_visit_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `server_addr` varchar(64) NOT NULL default '',
  `remote_addr` varchar(64) NOT NULL default '',
  `http_referer` text NOT NULL,
  `http_secure` tinyint(4) NOT NULL default '0',
  `http_host` varchar(255) NOT NULL default '',
  `http_user_agent` varchar(255) NOT NULL default '',
  `http_accept_language` varchar(255) NOT NULL default '',
  `http_accept_charset` varchar(255) NOT NULL default '',
  `request_uri` text NOT NULL,
  `website_id` int(10) unsigned NOT NULL default '0',
  `customer_id` int(10) unsigned NOT NULL default '0',
  `quote_id` int(10) unsigned NOT NULL default '0',
  `url_history` text NOT NULL,
  PRIMARY KEY  (`session_id`),
  CONSTRAINT `FK_core_session_visitor` FOREIGN KEY (`session_id`) REFERENCES `core_session` (`session_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `log_visitor` */

insert into `log_visitor` (`session_id`,`first_visit_at`,`last_visit_at`,`server_addr`,`remote_addr`,`http_referer`,`http_secure`,`http_host`,`http_user_agent`,`http_accept_language`,`http_accept_charset`,`request_uri`,`website_id`,`customer_id`,`quote_id`,`url_history`) values ('agdu1rdncarigmo99h4ca771e1','2007-05-29 07:29:34','2007-05-29 07:30:22','127.0.0.1','127.0.0.1','http://magento/install/wizard/config/',0,'magento','Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3','ru-ru,ru;q=0.8,en-us;q=0.5,en;q=0.3','windows-1251,utf-8;q=0.7,*;q=0.7','/customer/account/login/',1,0,0,'http://magento/install/wizard/configPost/\nhttp://magento/install/wizard/administrator/\nhttp://magento/\nhttp://magento/catalog/category/view/id/6/\nhttp://magento/customer/account\nhttp://magento/customer/account/login/\nhttp://magento/customer/account/loginPost/\nhttp://magento/customer/account\nhttp://magento/customer/account/login/');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
