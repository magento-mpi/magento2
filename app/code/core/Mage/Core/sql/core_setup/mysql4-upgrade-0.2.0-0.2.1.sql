SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';


drop table if exists core_session;
CREATE TABLE `core_session` (
  `session_id` varchar(255) NOT NULL default '',
  `session_expires` int(10) unsigned NOT NULL default '0',
  `session_data` text not null,
  PRIMARY KEY  (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table if exists core_session_visitor;
CREATE TABLE `core_session_visitor` (
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
  `url_history` text not null,
  PRIMARY KEY  (`session_id`),
  CONSTRAINT `FK_core_session_visitor` FOREIGN KEY (`session_id`) REFERENCES `core_session` (`session_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
