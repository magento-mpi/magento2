SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `wishlist_item`;

CREATE TABLE `wishlist_item` (
  `wishlist_item_id` int(10) unsigned NOT NULL auto_increment,
  `wishlist_id` int(10) unsigned NOT NULL default '0',
  `product_id` int(10) unsigned NOT NULL default '0',
  `store_id` int(10) unsigned NOT NULL default '0',
  `added_at` datetime default NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`wishlist_item_id`),
  KEY `FK_ITEM_WISHLIST` (`wishlist_id`),
  KEY `FK_WISHLIST_PRODUCT` (`product_id`),
  KEY `FK_WISHLIST_STORE` (`store_id`),
  CONSTRAINT `FK_ITEM_WISHLIST` FOREIGN KEY (`wishlist_id`) REFERENCES `wishlist` (`wishlist_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Wishlist items';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;