SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';


DROP TABLE IF EXISTS `cms_page`;
CREATE TABLE `cms_page` (
  `page_id` smallint(6) NOT NULL auto_increment,
  `page_title` varchar(255) NOT NULL,
  `page_meta_keywords` text NOT NULL,
  `page_meta_description` text NOT NULL,
  `page_identifier` varchar(100) NOT NULL,
  `page_content` text,
  `page_creation_time` datetime default NULL,
  `page_update_time` datetime default NULL,
  `page_active` tinyint(1) NOT NULL default '1',
  `page_store_id` tinyint(4) NOT NULL default '1',
  `page_order` tinyint(4) NOT NULL,
  PRIMARY KEY  (`page_id`)
) TYPE=InnoDB COMMENT='CMS pages';


INSERT INTO `cms_page` (`page_id`, `page_title`, `page_meta_keywords`, `page_meta_description`, `page_identifier`, `page_content`, `page_creation_time`, `page_update_time`, `page_active`, `page_store_id`, `page_order`) VALUES 
(1, '404 Not Found', 'Page keywords', 'Page description', 'no-route', '<h1 class="page-heading">404 Error</h1>\r\n<p>\r\nPage not found.<br />\r\n<small><i>by NoRoute Action :-)</i></small>\r\n</p>', '2007-06-20 18:38:32', '2007-06-20 18:38:32', 0, 0, 0);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
