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

CREATE TABLE `rating_option_vote_aggregated` (
`primary_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`rating_id` SMALLINT( 6 ) UNSIGNED NOT NULL ,
`entity_pk_value` bigint(20) UNSIGNED NOT NULL ,
`vote_count` INT UNSIGNED NOT NULL ,
`vote_value_sum` INT UNSIGNED NOT NULL ,
`percent` TINYINT( 3 ) NOT NULL,
CONSTRAINT `FK_RATING_OPTION_VALUE_AGGREGATE` FOREIGN KEY (`rating_id`) REFERENCES `rating` (`rating_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = innodb;

ALTER TABLE `rating_option_vote` ADD `review_id` BIGINT UNSIGNED NULL ;
ALTER TABLE `rating_option_vote` ADD `percent` TINYINT(3) NOT NULL ;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
