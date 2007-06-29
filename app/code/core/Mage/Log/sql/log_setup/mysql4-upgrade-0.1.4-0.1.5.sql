SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';


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


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
