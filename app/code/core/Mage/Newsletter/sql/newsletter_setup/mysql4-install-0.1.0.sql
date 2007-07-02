SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `newsletter_problem` */

DROP TABLE IF EXISTS `newsletter_problem`;

CREATE TABLE `newsletter_problem` (
  `problem_id` int(7) unsigned NOT NULL auto_increment,
  `subscriber_id` int(7) unsigned default NULL,
  `queue_id` int(7) unsigned NOT NULL,
  `problem_error_code` int(3) unsigned default '0',
  `problem_error_text` varchar(200) default NULL,
  PRIMARY KEY  (`problem_id`),
  KEY `FK_PROBLEM_SUBSCRIBER` (`subscriber_id`),
  KEY `FK_PROBLEM_QUEUE` (`queue_id`),
  CONSTRAINT `FK_PROBLEM_QUEUE` FOREIGN KEY (`queue_id`) REFERENCES `newsletter_queue` (`queue_id`),
  CONSTRAINT `FK_PROBLEM_SUBSCRIBER` FOREIGN KEY (`subscriber_id`) REFERENCES `newsletter_subscriber` (`subscriber_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8  COMMENT='Newsletter problems';

/*Data for the table `newsletter_problem` */

/*Table structure for table `newsletter_queue` */

DROP TABLE IF EXISTS `newsletter_queue`;

CREATE TABLE `newsletter_queue` (
  `queue_id` int(7) unsigned NOT NULL auto_increment,
  `template_id` int(7) unsigned NOT NULL,
  `queue_status` int(3) unsigned NOT NULL,
  `queue_start_at` datetime default NULL,
  `queue_finish_at` datetime default NULL,
  `website_id` int(3) unsigned NOT NULL,
  PRIMARY KEY  (`queue_id`),
  KEY `FK_QUEUE_TEMPLATE` (`template_id`),
  KEY `FK_QUEUE_WEBSITE` (`website_id`),
  CONSTRAINT `FK_QUEUE_TEMPLATE` FOREIGN KEY (`template_id`) REFERENCES `newsletter_template` (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Newsletter templates';

/*Data for the table `newsletter_queue` */

/*Table structure for table `newsletter_queue_link` */

DROP TABLE IF EXISTS `newsletter_queue_link`;

CREATE TABLE `newsletter_queue_link` (
  `queue_link_id` int(9) unsigned NOT NULL auto_increment,
  `queue_id` int(7) unsigned NOT NULL,
  `subscriber_id` int(7) unsigned NOT NULL,
  `letter_sent_at` datetime default NULL,
  PRIMARY KEY  (`queue_link_id`),
  KEY `FK_QUEUE_LINK_SUBSCRIBER` (`subscriber_id`),
  KEY `FK_QUEUE_LINK_QUEUE` (`queue_id`),
  CONSTRAINT `FK_QUEUE_LINK_SUBSCRIBER` FOREIGN KEY (`subscriber_id`) REFERENCES `newsletter_subscriber` (`subscriber_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_QUEUE_LINK_QUEUE` FOREIGN KEY (`queue_id`) REFERENCES `newsletter_queue` (`queue_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Newsletter queue to subscriber link';

/*Data for the table `newsletter_queue_link` */

/*Table structure for table `newsletter_subscriber` */

DROP TABLE IF EXISTS `newsletter_subscriber`;

CREATE TABLE `newsletter_subscriber` (
  `subscriber_id` int(7) unsigned NOT NULL auto_increment,
  `website_id` int(3) unsigned default '0',
  `customer_id` int(7) unsigned NOT NULL default '0',
  `subscriber_email` varchar(150) character set latin1 collate latin1_general_ci NOT NULL,
  `subscriber_status` int(3) NOT NULL,
  `subscriber_confirm_code` varchar(32) default 'NULL',
  PRIMARY KEY  (`subscriber_id`),
  KEY `FK_SUBSCRIBER_WEBSITE` (`website_id`),
  KEY `FK_SUBSCRIBER_CUSTOMER` (`customer_id`),
  CONSTRAINT `newsletter_subscriber_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Newsletter subscribers';

/*Data for the table `newsletter_subscriber` */

/*Table structure for table `newsletter_template` */

DROP TABLE IF EXISTS `newsletter_template`;

CREATE TABLE `newsletter_template` (
  `template_id` int(7) unsigned NOT NULL auto_increment,
  `template_code` varchar(150) character set latin1 collate latin1_general_ci default NULL,
  `template_text` text,
  `template_type` int(3) unsigned default NULL,
  `template_subject` varchar(200) default NULL,
  `template_sender_name` varchar(200) default NULL,
  `template_sender_email` varchar(200) character set latin1 collate latin1_general_ci default NULL,
  `template_actual` enum('true','false') default NULL,
  PRIMARY KEY  (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Newsletter templates';

/*Data for the table `newsletter_template` */

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
