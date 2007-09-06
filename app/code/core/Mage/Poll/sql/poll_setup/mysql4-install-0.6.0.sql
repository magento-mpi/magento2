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
 * @package    Mage_Poll
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*Table structure for table `poll` */

DROP TABLE IF EXISTS `poll`;

CREATE TABLE `poll` (
  `poll_id` int(11) NOT NULL auto_increment,
  `poll_title` varchar(255) NOT NULL default '',
  `votes_count` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(6) NOT NULL default '0',
  `date_posted` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_closed` datetime default NULL,
  `active` smallint(6) NOT NULL default '1',
  `closed` tinyint(1) NOT NULL default '0',
  `answers_display` smallint(6) default NULL,
  PRIMARY KEY  (`poll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `poll` */

insert  into `poll`(`poll_id`,`poll_title`,`votes_count`,`store_id`,`date_posted`,`date_closed`,`active`,`closed`,`answers_display`) values (1,'What is your favorite color',0,1,'2007-06-15 19:17:49',NULL,1,0,NULL);

/*Table structure for table `poll_answer` */

DROP TABLE IF EXISTS `poll_answer`;

CREATE TABLE `poll_answer` (
  `answer_id` int(11) NOT NULL auto_increment,
  `poll_id` int(11) NOT NULL default '0',
  `answer_title` varchar(255) NOT NULL default '',
  `votes_count` int(10) unsigned NOT NULL default '0',
  `answer_order` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`answer_id`),
  KEY `FK_POLL_PARENT` (`poll_id`),
  CONSTRAINT `FK_POLL_PARENT` FOREIGN KEY (`poll_id`) REFERENCES `poll` (`poll_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `poll_answer` */

insert  into `poll_answer`(`answer_id`,`poll_id`,`answer_title`,`votes_count`,`answer_order`) values (5,1,'Green',0,0),(6,1,'Red',0,0),(7,1,'Black',0,0),(8,1,'Magenta',0,0);

/*Table structure for table `poll_vote` */

DROP TABLE IF EXISTS `poll_vote`;

CREATE TABLE `poll_vote` (
  `vote_id` int(10) unsigned NOT NULL auto_increment,
  `poll_id` int(11) NOT NULL default '0',
  `poll_answer_id` int(11) NOT NULL default '0',
  `ip_address` bigint(20) default NULL,
  `customer_id` int(11) default NULL,
  `vote_time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`vote_id`),
  KEY `FK_POLL_ANSWER` (`poll_answer_id`),
  CONSTRAINT `FK_POLL_ANSWER` FOREIGN KEY (`poll_answer_id`) REFERENCES `poll_answer` (`answer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `poll_vote` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
