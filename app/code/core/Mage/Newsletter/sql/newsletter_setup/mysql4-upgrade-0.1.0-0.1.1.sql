SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `newsletter_template` */

DROP TABLE IF EXISTS `newsletter_template`;

CREATE TABLE `newsletter_template` (
  `template_id` int(7) unsigned NOT NULL auto_increment,
  `template_code` varchar(150) character set latin1 collate latin1_general_ci default NULL,
  `template_text` text,
  `template_text_preprocessed` text,
  `template_type` int(3) unsigned default NULL,
  `template_subject` varchar(200) default NULL,
  `template_sender_name` varchar(200) default NULL,
  `template_sender_email` varchar(200) character set latin1 collate latin1_general_ci default NULL,
  `template_actual` enum('true','false') default NULL,
  PRIMARY KEY  (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Newsletter templates';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
