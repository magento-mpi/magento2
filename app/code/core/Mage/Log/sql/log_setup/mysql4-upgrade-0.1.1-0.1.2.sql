SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';


DROP TABLE IF EXISTS `log_visitor`;
CREATE TABLE `log_visitor` (
  `session_id` varchar(255) NOT NULL default '',
  `first_visit_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_visit_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `server_addr` varchar(64) NOT NULL default '',
  `remote_addr` varchar(64) NOT NULL default '',
  `http_referer` text,
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
  PRIMARY KEY  (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `log_visitor` DROP `url_history` ;
ALTER TABLE `log_visitor` DROP `request_uri`;

ALTER TABLE `log_visitor` ADD `last_url_id` BIGINT UNSIGNED NOT NULL ;


ALTER TABLE `log_visitor` CHANGE `server_addr` `server_addr` BIGINT NULL,
    CHANGE `remote_addr` `remote_addr` BIGINT NULL;

ALTER TABLE `log_visitor` CHANGE `first_visit_at` `first_visit_at` TIMESTAMP NULL ,
    CHANGE `last_visit_at` `last_visit_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL;


CREATE TABLE `log_url_history` (
`url_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
`session_id` VARCHAR( 255 ) NOT NULL ,
`url_value` VARCHAR( 255 ) NOT NULL ,
INDEX ( `url_id` )
) ENGINE = innodb;

ALTER TABLE `log_url_history`
  ADD CONSTRAINT `FK_LOG_PARENT` FOREIGN KEY (`session_id`) REFERENCES `log_visitor` (`session_id`) ON DELETE CASCADE ON UPDATE CASCADE;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
