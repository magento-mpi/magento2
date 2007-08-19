SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `review_entity_summary` (
  `primary_id` bigint(20) NOT NULL auto_increment,
  `entity_pk_value` bigint(20) NOT NULL,
  `entity_type` tinyint(4) NOT NULL,
  `reviews_count` smallint(6) NOT NULL,
  `rating_summary` tinyint(4) NOT NULL,
  PRIMARY KEY  (`primary_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
