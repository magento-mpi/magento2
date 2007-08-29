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
 * @package    Mage_Rating
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `rating`;
DROP TABLE IF EXISTS `rating_entity`;
DROP TABLE IF EXISTS `rating_option`;
DROP TABLE IF EXISTS `rating_option_vote`;
DROP TABLE IF EXISTS `rating_option_vote_aggregated`;

CREATE TABLE `rating` (
  `rating_id` smallint(6) unsigned NOT NULL auto_increment,
  `entity_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `rating_code` varchar(64) NOT NULL default '',
  `position` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`rating_id`),
  UNIQUE KEY `IDX_CODE` (`rating_code`),
  KEY `FK_RATING_ENTITY` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ratings' AUTO_INCREMENT=10 ;

-- 
-- Dumping data for table `rating`
-- 

INSERT INTO `rating` (`rating_id`, `entity_id`, `store_id`, `rating_code`, `position`) VALUES 
(1, 2, 1, 'product_review_quality', 1),
(2, 2, 1, 'product_review_use', 2),
(3, 2, 1, 'product_review_value', 3),
(4, 3, 1, 'review_quality', 0),
(8, 1, 0, 'Product Value', 0),
(9, 1, 0, 'Product Price', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `rating_entity`
-- 

CREATE TABLE `rating_entity` (
  `entity_id` smallint(6) unsigned NOT NULL auto_increment,
  `entity_code` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`entity_id`),
  UNIQUE KEY `IDX_CODE` (`entity_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Rating entities' AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `rating_entity`
-- 

INSERT INTO `rating_entity` (`entity_id`, `entity_code`) VALUES 
(1, 'product'),
(2, 'product_review'),
(3, 'review');

-- --------------------------------------------------------

-- 
-- Table structure for table `rating_option`
-- 

CREATE TABLE `rating_option` (
  `option_id` int(10) unsigned NOT NULL auto_increment,
  `rating_id` smallint(6) unsigned NOT NULL default '0',
  `code` varchar(32) NOT NULL default '',
  `value` tinyint(3) unsigned NOT NULL default '0',
  `position` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`option_id`),
  KEY `FK_RATING_OPTION_RATING` (`rating_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Rating options' AUTO_INCREMENT=11 ;

-- 
-- Dumping data for table `rating_option`
-- 

INSERT INTO `rating_option` (`option_id`, `rating_id`, `code`, `value`, `position`) VALUES 
(1, 8, 'Very Bad', 1, 1),
(2, 8, 'Bad', 2, 2),
(3, 8, 'Good', 3, 3),
(4, 8, 'Very Good', 4, 4),
(5, 8, 'Perfect!', 5, 5),
(6, 9, '1', 1, 1),
(7, 9, '2', 2, 2),
(8, 9, '3', 3, 3),
(9, 9, '4', 4, 4),
(10, 9, '5', 5, 5);

-- --------------------------------------------------------

-- 
-- Table structure for table `rating_option_vote`
-- 

CREATE TABLE `rating_option_vote` (
  `vote_id` bigint(20) unsigned NOT NULL auto_increment,
  `option_id` int(10) unsigned NOT NULL default '0',
  `remote_ip` varchar(16) NOT NULL default '',
  `remote_ip_long` int(11) NOT NULL default '0',
  `customer_id` int(11) unsigned default '0',
  `entity_pk_value` bigint(20) unsigned NOT NULL default '0',
  `rating_id` smallint(6) unsigned NOT NULL default '0',
  `review_id` bigint(20) unsigned default NULL,
  `percent` tinyint(3) NOT NULL,
  PRIMARY KEY  (`vote_id`),
  KEY `FK_RATING_OPTION_VALUE_OPTION` (`option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Rating option values' AUTO_INCREMENT=21 ;

-- 
-- Dumping data for table `rating_option_vote`
-- 

INSERT INTO `rating_option_vote` (`vote_id`, `option_id`, `remote_ip`, `remote_ip_long`, `customer_id`, `entity_pk_value`, `rating_id`, `review_id`, `percent`) VALUES 
(3, 5, '192.168.0.8', 2147483647, 3, 2493, 8, 2, 100),
(4, 6, '192.168.0.8', 2147483647, 3, 2493, 9, 2, 20),
(11, 1, '192.168.0.8', 2147483647, 3, 2493, 8, 6, 20),
(12, 8, '192.168.0.8', 2147483647, 3, 2493, 9, 6, 60),
(13, 1, '192.168.0.8', 2147483647, 3, 2493, 8, 7, 20),
(14, 6, '192.168.0.8', 2147483647, 3, 2493, 9, 7, 20);

-- --------------------------------------------------------

-- 
-- Table structure for table `rating_option_vote_aggregated`
-- 

CREATE TABLE `rating_option_vote_aggregated` (
  `primary_id` int(11) NOT NULL auto_increment,
  `rating_id` smallint(6) unsigned NOT NULL,
  `entity_pk_value` bigint(20) unsigned NOT NULL,
  `vote_count` int(10) unsigned NOT NULL,
  `vote_value_sum` int(10) unsigned NOT NULL,
  `percent` tinyint(3) NOT NULL,
  PRIMARY KEY  (`primary_id`),
  KEY `FK_RATING_OPTION_VALUE_AGGREGATE` (`rating_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `rating_option_vote_aggregated`
-- 

-- 
-- Constraints for table `rating`
-- 
ALTER TABLE `rating`
  ADD CONSTRAINT `FK_RATING_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `rating_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `rating_option`
-- 
ALTER TABLE `rating_option`
  ADD CONSTRAINT `FK_RATING_OPTION_RATING` FOREIGN KEY (`rating_id`) REFERENCES `rating` (`rating_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `rating_option_vote`
-- 
ALTER TABLE `rating_option_vote`
  ADD CONSTRAINT `FK_RATING_OPTION_VALUE_OPTION` FOREIGN KEY (`option_id`) REFERENCES `rating_option` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `rating_option_vote_aggregated`
-- 
ALTER TABLE `rating_option_vote_aggregated`
  ADD CONSTRAINT `FK_RATING_OPTION_VALUE_AGGREGATE` FOREIGN KEY (`rating_id`) REFERENCES `rating` (`rating_id`) ON DELETE CASCADE ON UPDATE CASCADE;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
