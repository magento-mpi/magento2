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
 * @package    Mage_Review
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `review`;
DROP TABLE IF EXISTS `review_detail`;
DROP TABLE IF EXISTS `review_entity`;
DROP TABLE IF EXISTS `review_status`;


-- --------------------------------------------------------

-- 
-- Table structure for table `review`
-- 

CREATE TABLE `review` (
  `review_id` bigint(20) unsigned NOT NULL auto_increment,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `entity_id` smallint(5) unsigned NOT NULL default '0',
  `entity_pk_value` bigint(20) unsigned NOT NULL default '0',
  `status_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`review_id`),
  KEY `FK_REVIEW_ENTITY` (`entity_id`),
  KEY `FK_REVIEW_STATUS` (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Review base information' AUTO_INCREMENT=11 ;

-- 
-- Dumping data for table `review`
-- 

INSERT INTO `review` (`review_id`, `created_at`, `entity_id`, `entity_pk_value`, `status_id`) VALUES 
(2, '2007-08-10 10:59:33', 1, 2493, 1),
(6, '2007-08-10 12:58:37', 1, 2493, 1),
(7, '2007-08-10 13:01:04', 1, 2493, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `review_detail`
-- 

CREATE TABLE `review_detail` (
  `detail_id` bigint(20) unsigned NOT NULL auto_increment,
  `review_id` bigint(20) unsigned NOT NULL default '0',
  `store_id` smallint(6) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `detail` text NOT NULL,
  `nickname` varchar(128) NOT NULL default '',
  `customer_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`detail_id`),
  KEY `FK_REVIEW_DETAIL_REVIEW` (`review_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Review detail information' AUTO_INCREMENT=110 ;

-- 
-- Dumping data for table `review_detail`
-- 

INSERT INTO `review_detail` (`detail_id`, `review_id`, `store_id`, `title`, `detail`, `nickname`, `customer_id`) VALUES 
(101, 2, 1, 'Test Review', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet  asdasdq dasd ', 'Alexander qweq ', 3),
(105, 6, 1, 'One More Test Review', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet ', 'Alexander', 3),
(106, 7, 1, 'This is a test review', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet ', 'Alexander', 3);

-- --------------------------------------------------------

-- 
-- Table structure for table `review_entity`
-- 

CREATE TABLE `review_entity` (
  `entity_id` smallint(5) unsigned NOT NULL auto_increment,
  `entity_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Review entities' AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `review_entity`
-- 

INSERT INTO `review_entity` (`entity_id`, `entity_code`) VALUES 
(1, 'product'),
(2, 'customer'),
(3, 'category');

-- --------------------------------------------------------

-- 
-- Table structure for table `review_status`
-- 

CREATE TABLE `review_status` (
  `status_id` tinyint(3) unsigned NOT NULL auto_increment,
  `status_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Review statuses' AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `review_status`
-- 

INSERT INTO `review_status` (`status_id`, `status_code`) VALUES 
(1, 'Approved'),
(3, 'Not Approved');

-- 
-- Constraints for dumped tables
-- 

-- 
-- Constraints for table `review`
-- 
ALTER TABLE `review`
  ADD CONSTRAINT `FK_REVIEW_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `review_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_REVIEW_STATUS` FOREIGN KEY (`status_id`) REFERENCES `review_status` (`status_id`);

-- 
-- Constraints for table `review_detail`
-- 
ALTER TABLE `review_detail`
  ADD CONSTRAINT `FK_REVIEW_DETAIL_REVIEW` FOREIGN KEY (`review_id`) REFERENCES `review` (`review_id`) ON DELETE CASCADE ON UPDATE CASCADE;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
