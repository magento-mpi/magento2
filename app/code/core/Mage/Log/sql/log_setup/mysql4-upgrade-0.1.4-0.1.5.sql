SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


DROP TABLE IF EXISTS `log_customer`;
DROP TABLE IF EXISTS `log_quote`;
DROP TABLE IF EXISTS `log_summary`;
DROP TABLE IF EXISTS `log_summary_type`;
DROP TABLE IF EXISTS `log_url`;
DROP TABLE IF EXISTS `log_url_info`;
DROP TABLE IF EXISTS `log_visitor`;
DROP TABLE IF EXISTS `log_visitor_info`;

--
-- Table structure for table `log_customer`
--

CREATE TABLE `log_customer` (
  `log_id` int(10) unsigned NOT NULL auto_increment,
  `visitor_id` bigint(20) unsigned default NULL,
  `customer_id` int(11) NOT NULL default '0',
  `login_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `logout_at` datetime default NULL,
  PRIMARY KEY  (`log_id`),
  KEY `FK_LOG_CUSTOMER_VISITOR` (`visitor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Customers log information';

--
-- Dumping data for table `log_customer`
--

LOCK TABLES `log_customer` WRITE;
/*!40000 ALTER TABLE `log_customer` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_quote`
--

CREATE TABLE `log_quote` (
  `quote_id` int(10) unsigned NOT NULL default '0',
  `visitor_id` bigint(20) unsigned default NULL,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `deleted_at` datetime NOT NULL,
  PRIMARY KEY  (`quote_id`),
  KEY `FK_LOG_QUOTE_VISITOR` (`visitor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Quote log data';

--
-- Dumping data for table `log_quote`
--

LOCK TABLES `log_quote` WRITE;
/*!40000 ALTER TABLE `log_quote` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_quote` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_summary`
--

CREATE TABLE `log_summary` (
  `summary_id` bigint(20) unsigned NOT NULL auto_increment,
  `type_id` smallint(5) unsigned default NULL,
  `visitor_count` int(11) NOT NULL default '0',
  `customer_count` int(11) NOT NULL default '0',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`summary_id`),
  KEY `FK_LOG_SUMMARY_TYPE` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Summary log information';

--
-- Dumping data for table `log_summary`
--

LOCK TABLES `log_summary` WRITE;
/*!40000 ALTER TABLE `log_summary` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_summary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_summary_type`
--

CREATE TABLE `log_summary_type` (
  `type_id` smallint(5) unsigned NOT NULL auto_increment,
  `type_code` varchar(64) NOT NULL default '',
  `period` smallint(5) unsigned NOT NULL default '0',
  `period_type` enum('MINUTE','HOUR','DAY','WEEK','MONTH') NOT NULL default 'MINUTE',
  PRIMARY KEY  (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Type of summary information';

--
-- Dumping data for table `log_summary_type`
--

LOCK TABLES `log_summary_type` WRITE;
/*!40000 ALTER TABLE `log_summary_type` DISABLE KEYS */;
INSERT INTO `log_summary_type` VALUES (1,'live_5_mins',5,'MINUTE');
/*!40000 ALTER TABLE `log_summary_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_url`
--

CREATE TABLE `log_url` (
  `url_id` bigint(20) unsigned NOT NULL auto_increment,
  `visitor_id` bigint(20) unsigned default NULL,
  `visit_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`url_id`),
  KEY `FK_URL_VISIT_VISITOR` (`visitor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='URL visiting history';

--
-- Dumping data for table `log_url`
--

LOCK TABLES `log_url` WRITE;
/*!40000 ALTER TABLE `log_url` DISABLE KEYS */;
INSERT INTO `log_url` VALUES (1,1,'2007-06-25 20:58:20'),(2,1,'2007-06-25 20:58:20');
/*!40000 ALTER TABLE `log_url` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_url_info`
--

CREATE TABLE `log_url_info` (
  `url_id` bigint(20) unsigned NOT NULL default '0',
  `url` varchar(255) NOT NULL default '',
  `referer` varchar(255) default NULL,
  PRIMARY KEY  (`url_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Detale information about url visit';

--
-- Dumping data for table `log_url_info`
--

LOCK TABLES `log_url_info` WRITE;
/*!40000 ALTER TABLE `log_url_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_url_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_visitor`
--

CREATE TABLE `log_visitor` (
  `visitor_id` bigint(20) unsigned NOT NULL auto_increment,
  `session_id` char(64) NOT NULL default '',
  `first_visit_at` datetime default NULL,
  `last_visit_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `server_addr` bigint(19) default NULL,
  `remote_addr` bigint(19) default NULL,
  `last_url_id` bigint(20) unsigned NOT NULL default '0',
  PRIMARY KEY  (`visitor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='System visitors log';

--
-- Dumping data for table `log_visitor`
--

LOCK TABLES `log_visitor` WRITE;
/*!40000 ALTER TABLE `log_visitor` DISABLE KEYS */;
INSERT INTO `log_visitor` VALUES (1,'c7599c5332ebc9dbcdd13a60038393c5','2007-06-25 20:58:20','2007-06-25 20:58:20',NULL,NULL,2);
/*!40000 ALTER TABLE `log_visitor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_visitor_info`
--

CREATE TABLE `log_visitor_info` (
  `visitor_id` bigint(20) unsigned NOT NULL default '0',
  `http_referer` varchar(255) default NULL,
  `http_user_agent` varchar(255) default NULL,
  `http_accept_charset` varchar(255) default NULL,
  `http_accept_language` varchar(255) default NULL,
  `server_addr` bigint(20) default NULL,
  `remote_addr` bigint(20) default NULL,
  PRIMARY KEY  (`visitor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Additional information by visitor';

--
-- Dumping data for table `log_visitor_info`
--

LOCK TABLES `log_visitor_info` WRITE;
/*!40000 ALTER TABLE `log_visitor_info` DISABLE KEYS */;
INSERT INTO `log_visitor_info` VALUES (1,'http://magento.net/catalog/category/view/id/14/','Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.8.1.3) Gecko/20070525 Firefox/2.0.0.3','ISO-8859-1,utf-8;q=0.7,*;q=0.7','en-us,en;q=0.5',3232235528,3232235528);
/*!40000 ALTER TABLE `log_visitor_info` ENABLE KEYS */;
UNLOCK TABLES;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
