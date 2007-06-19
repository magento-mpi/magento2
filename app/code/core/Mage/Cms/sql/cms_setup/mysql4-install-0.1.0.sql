SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';


DROP TABLE IF EXISTS `page_static`;
CREATE TABLE `page_static` (
  `page_id` smallint(6) NOT NULL auto_increment,
  `page_title` varchar(255) NOT NULL,
  `page_meta_keywords` text NOT NULL,
  `page_meta_description` text NOT NULL,
  `page_identifier` varchar(100) NOT NULL,
  `page_creation_time` timestamp NULL default NULL,
  `page_update_time` timestamp NOT NULL default '0000-00-00 00:00:00',
  `page_active` smallint(1) NOT NULL,
  PRIMARY KEY  (`page_id`)
) TYPE=InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
