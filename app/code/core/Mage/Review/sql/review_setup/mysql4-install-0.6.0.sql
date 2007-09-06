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


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `review` */

DROP TABLE IF EXISTS `review`;

CREATE TABLE `review` (
  `review_id` bigint(20) unsigned NOT NULL auto_increment,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `entity_id` smallint(5) unsigned NOT NULL default '0',
  `entity_pk_value` int(10) unsigned NOT NULL default '0',
  `status_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`review_id`),
  KEY `FK_REVIEW_ENTITY` (`entity_id`),
  KEY `FK_REVIEW_STATUS` (`status_id`),
  KEY `FK_REVIEW_PARENT_PRODUCT` (`entity_pk_value`),
  CONSTRAINT `FK_REVIEW_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `review_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_REVIEW_PARENT_PRODUCT` FOREIGN KEY (`entity_pk_value`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_REVIEW_STATUS` FOREIGN KEY (`status_id`) REFERENCES `review_status` (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Review base information';

/*Data for the table `review` */

/*Table structure for table `review_detail` */

DROP TABLE IF EXISTS `review_detail`;

CREATE TABLE `review_detail` (
  `detail_id` bigint(20) unsigned NOT NULL auto_increment,
  `review_id` bigint(20) unsigned NOT NULL default '0',
  `store_id` smallint(6) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `detail` text NOT NULL,
  `nickname` varchar(128) NOT NULL default '',
  `customer_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`detail_id`),
  KEY `FK_REVIEW_DETAIL_REVIEW` (`review_id`),
  CONSTRAINT `FK_REVIEW_DETAIL_REVIEW` FOREIGN KEY (`review_id`) REFERENCES `review` (`review_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Review detail information';

/*Data for the table `review_detail` */

/*Table structure for table `review_entity` */

DROP TABLE IF EXISTS `review_entity`;

CREATE TABLE `review_entity` (
  `entity_id` smallint(5) unsigned NOT NULL auto_increment,
  `entity_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Review entities';

/*Data for the table `review_entity` */

insert  into `review_entity`(`entity_id`,`entity_code`) values (1,'product'),(2,'customer'),(3,'category');

/*Table structure for table `review_entity_summary` */

DROP TABLE IF EXISTS `review_entity_summary`;

CREATE TABLE `review_entity_summary` (
  `primary_id` bigint(20) NOT NULL auto_increment,
  `entity_pk_value` bigint(20) NOT NULL default '0',
  `entity_type` tinyint(4) NOT NULL default '0',
  `reviews_count` smallint(6) NOT NULL default '0',
  `rating_summary` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`primary_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `review_entity_summary` */

insert  into `review_entity_summary`(`primary_id`,`entity_pk_value`,`entity_type`,`reviews_count`,`rating_summary`) values (1,44,1,2,83),(2,49,1,2,80),(3,20,1,3,67),(4,17,1,1,73),(5,18,1,3,62),(6,25,1,2,80),(7,27,1,2,60),(8,28,1,1,80),(9,51,1,1,80),(10,42,1,2,80),(11,32,1,1,60),(12,34,1,2,63),(13,29,1,2,60),(14,37,1,3,60),(15,38,1,1,67),(16,19,1,2,63),(17,35,1,2,65),(18,36,1,2,64),(19,30,1,2,62),(20,31,1,2,67),(21,33,1,3,62),(22,39,1,1,80),(23,40,1,2,70),(24,41,1,2,67),(25,16,1,1,73),(26,48,1,1,60),(27,47,1,1,93),(28,46,1,1,80),(29,53,1,1,73),(30,119,1,1,73),(31,120,1,1,80),(32,126,1,1,100),(33,83,1,1,80),(34,76,1,1,93),(35,112,1,1,67),(36,98,1,1,67),(37,103,1,1,80),(38,26,1,2,60);

/*Table structure for table `review_status` */

DROP TABLE IF EXISTS `review_status`;

CREATE TABLE `review_status` (
  `status_id` tinyint(3) unsigned NOT NULL auto_increment,
  `status_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Review statuses';

/*Data for the table `review_status` */

insert  into `review_status`(`status_id`,`status_code`) values (1,'Approved'),(2,'Pending'),(3,'Not Approved');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
