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
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `cms_block` */

DROP TABLE IF EXISTS `cms_block`;
CREATE TABLE `cms_block` (
  `block_id` smallint(6) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `identifier` varchar(255) NOT NULL default '',
  `content` text,
  `creation_time` datetime default NULL,
  `update_time` datetime default NULL,
  `is_active` tinyint(1) NOT NULL default '1',
  `store_id` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`block_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMS Blocks';

/*Table structure for table `cms_page` */

DROP TABLE IF EXISTS `cms_page`;
CREATE TABLE `cms_page` (
  `page_id` smallint(6) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `meta_keywords` text NOT NULL,
  `meta_description` text NOT NULL,
  `identifier` varchar(100) NOT NULL default '',
  `content` text,
  `creation_time` datetime default NULL,
  `update_time` datetime default NULL,
  `is_active` tinyint(1) NOT NULL default '1',
  `store_id` tinyint(4) NOT NULL default '1',
  `sort_order` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='CMS pages';

/*Table data for table `cms_page` */

INSERT INTO `cms_page` (`page_id`, `title`, `meta_keywords`, `meta_description`, `identifier`, `content`, `creation_time`, `update_time`, `is_active`, `store_id`, `sort_order`) VALUES (1, '404 Not Found 1', 'Page keywords', 'Page description', 'no-route', '<h1 class="page-heading">404 Error</h1>\r\n<p>\r\nPage not found.<br />\r\n<em>by NoRoute Action :-)</em>\r\n</p>\r\n', '2007-06-20 18:38:32', '2007-07-27 10:45:35', 1, 0, 0);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
