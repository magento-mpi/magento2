/*
SQLyog Enterprise - MySQL GUI v5.13
Host - 4.1.21-community-nt : Database - magenta
*********************************************************************
Server version : 4.1.21-community-nt
*/

SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `cart` */

DROP TABLE IF EXISTS `cart`;

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL auto_increment,
  `customer_id` int(11) unsigned NOT NULL default '0',
  `create_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `uniq_code` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`cart_id`),
  UNIQUE KEY `uniq_code` (`uniq_code`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `FK_CART_CUSTOMER` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `cart` */

/*Table structure for table `cart_product` */

DROP TABLE IF EXISTS `cart_product`;

CREATE TABLE `cart_product` (
  `card_product_id` int(11) NOT NULL auto_increment,
  `card_id` int(11) unsigned NOT NULL default '0',
  `product_id` int(11) unsigned NOT NULL default '0',
  `product_qty` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`card_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Shopping card products';

/*Data for the table `cart_product` */

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
