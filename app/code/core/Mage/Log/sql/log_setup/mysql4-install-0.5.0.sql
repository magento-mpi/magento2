/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Log
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
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

/*Table structure for table `log_customer` */

DROP TABLE IF EXISTS `log_customer`;

CREATE TABLE `log_customer` (
  `log_id` int(10) unsigned NOT NULL auto_increment,
  `visitor_id` bigint(20) unsigned default NULL,
  `customer_id` int(11) NOT NULL default '0',
  `login_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `logout_at` datetime default NULL,
  PRIMARY KEY  (`log_id`),
  KEY `FK_LOG_CUSTOMER_VISITOR` (`visitor_id`),
  CONSTRAINT `FK_CUSTOMER_PARENT` FOREIGN KEY (`visitor_id`) REFERENCES `log_visitor` (`visitor_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Customers log information';

/*Data for the table `log_customer` */

/*Table structure for table `log_quote` */

DROP TABLE IF EXISTS `log_quote`;

CREATE TABLE `log_quote` (
  `quote_id` int(10) unsigned NOT NULL default '0',
  `visitor_id` bigint(20) unsigned default NULL,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `deleted_at` datetime default NULL,
  PRIMARY KEY  (`quote_id`),
  KEY `FK_LOG_QUOTE_VISITOR` (`visitor_id`),
  CONSTRAINT `FK_QUOTE_PARENT` FOREIGN KEY (`visitor_id`) REFERENCES `log_visitor` (`visitor_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Quote log data';

/*Data for the table `log_quote` */

/*Table structure for table `log_summary` */

DROP TABLE IF EXISTS `log_summary`;

CREATE TABLE `log_summary` (
  `summary_id` bigint(20) unsigned NOT NULL auto_increment,
  `type_id` smallint(5) unsigned default NULL,
  `visitor_count` int(11) NOT NULL default '0',
  `customer_count` int(11) NOT NULL default '0',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`summary_id`),
  KEY `FK_LOG_SUMMARY_TYPE` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Summary log information';

/*Data for the table `log_summary` */

/*Table structure for table `log_summary_type` */

DROP TABLE IF EXISTS `log_summary_type`;

CREATE TABLE `log_summary_type` (
  `type_id` smallint(5) unsigned NOT NULL auto_increment,
  `type_code` varchar(64) NOT NULL default '',
  `period` smallint(5) unsigned NOT NULL default '0',
  `period_type` enum('MINUTE','HOUR','DAY','WEEK','MONTH') NOT NULL default 'MINUTE',
  PRIMARY KEY  (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Type of summary information';

/*Data for the table `log_summary_type` */

/*Table structure for table `log_url` */

DROP TABLE IF EXISTS `log_url`;

CREATE TABLE `log_url` (
  `url_id` bigint(20) unsigned NOT NULL auto_increment,
  `visitor_id` bigint(20) unsigned default NULL,
  `visit_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`url_id`),
  KEY `FK_URL_VISIT_VISITOR` (`visitor_id`),
  CONSTRAINT `FK_URL_PARENT` FOREIGN KEY (`visitor_id`) REFERENCES `log_visitor` (`visitor_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='URL visiting history';

/*Data for the table `log_url` */

/*Table structure for table `log_url_info` */

DROP TABLE IF EXISTS `log_url_info`;

CREATE TABLE `log_url_info` (
  `url_id` bigint(20) unsigned NOT NULL default '0',
  `url` varchar(255) NOT NULL default '',
  `referer` varchar(255) default NULL,
  PRIMARY KEY  (`url_id`),
  CONSTRAINT `FK_URL_INFO_PARENT` FOREIGN KEY (`url_id`) REFERENCES `log_url` (`url_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Detale information about url visit';

/*Data for the table `log_url_info` */

/*Table structure for table `log_visitor` */

DROP TABLE IF EXISTS `log_visitor`;

CREATE TABLE `log_visitor` (
  `visitor_id` bigint(20) unsigned NOT NULL auto_increment,
  `session_id` char(64) NOT NULL default '',
  `first_visit_at` datetime default NULL,
  `last_visit_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_url_id` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`visitor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='System visitors log';

/*Data for the table `log_visitor` */

/*Table structure for table `log_visitor_info` */

DROP TABLE IF EXISTS `log_visitor_info`;

CREATE TABLE `log_visitor_info` (
  `visitor_id` bigint(20) unsigned NOT NULL default '0',
  `http_referer` varchar(255) default NULL,
  `http_user_agent` varchar(255) default NULL,
  `http_accept_charset` varchar(255) default NULL,
  `http_accept_language` varchar(255) default NULL,
  `server_addr` bigint(20) default NULL,
  `remote_addr` bigint(20) default NULL,
  PRIMARY KEY  (`visitor_id`),
  CONSTRAINT `FK_VISITOR_PARENT` FOREIGN KEY (`visitor_id`) REFERENCES `log_visitor` (`visitor_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Additional information by visitor';

/*Data for the table `log_visitor_info` */

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
