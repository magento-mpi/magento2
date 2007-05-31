
SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';


DROP TABLE IF EXISTS `sales_quote_rule`;
CREATE TABLE `sales_quote_rule` (
  `quote_rule_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `is_active` tinyint(4) NOT NULL default '0',
  `start_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `expire_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `coupon_code` varchar(50) NOT NULL default '',
  `customer_registered` tinyint(1) NOT NULL default '2',
  `customer_new_buyer` tinyint(1) NOT NULL default '2',
  `show_in_catalog` tinyint(1) NOT NULL default '0',
  `sort_order` smallint(6) NOT NULL default '0',
  `conditions_serialized` text NOT NULL,
  `actions_serialized` text NOT NULL,
  PRIMARY KEY  (`quote_rule_id`),
  KEY `rule_name` (`name`),
  KEY `is_active` (`is_active`,`start_at`,`expire_at`,`coupon_code`,`customer_registered`,`customer_new_buyer`,`show_in_catalog`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
