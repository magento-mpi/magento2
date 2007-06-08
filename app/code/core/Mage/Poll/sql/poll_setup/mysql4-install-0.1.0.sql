SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';


DROP TABLE IF EXISTS `poll`;
CREATE TABLE `poll` (
  `poll_id` int(11) NOT NULL auto_increment,
  `poll_title` varchar(255) NOT NULL,
  `votes_count` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`poll_id`)
) TYPE=InnoDB AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `poll_answer`;
CREATE TABLE `poll_answer` (
  `answer_id` int(11) NOT NULL auto_increment,
  `poll_id` int(11) NOT NULL,
  `answer_title` varchar(255) NOT NULL,
  `votes_count` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`answer_id`),
  CONSTRAINT `FK_POLL_PARENT` FOREIGN KEY (poll_id) REFERENCES poll(poll_id) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `poll_vote`;
CREATE TABLE `poll_vote` (
  `vote_id` int(10) unsigned NOT NULL auto_increment,
  `poll_id` int(11) NOT NULL,
  `poll_answer_id` int(11) NOT NULL,
  PRIMARY KEY  (`vote_id`),
  CONSTRAINT `FK_POLL_ANSWER` FOREIGN KEY (poll_answer_id) REFERENCES poll_answer(answer_id) ON DELETE CASCADE ON UPDATE CASCADE
) TYPE=InnoDB AUTO_INCREMENT=1 ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
