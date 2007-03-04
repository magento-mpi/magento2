/*
SQLyog Enterprise - MySQL GUI v5.13
Host - 4.1.21-community-nt : Database - pepper
*********************************************************************
Server version : 4.1.21-community-nt
*/

SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `catalog_product_attribute` */

DROP TABLE IF EXISTS `catalog_product_attribute`;

CREATE TABLE `catalog_product_attribute` (
  `product_id` int(11) unsigned NOT NULL default '0',
  `attribute_id` int(10) unsigned NOT NULL default '0',
  `website_id` smallint(6) unsigned default '0',
  `attribute_value` text,
  PRIMARY KEY  (`product_id`,`attribute_id`),
  KEY `FK_PRODUCT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_PRODUCT_ATTRIBUTE_WEBSITE` (`website_id`),
  CONSTRAINT `FK_ATTRIBUTE_PRODUCT` FOREIGN KEY (`product_id`) REFERENCES `catalog_product` (`product_id`),
  CONSTRAINT `FK_PRODUCT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `catalog_attribute` (`attribute_id`),
  CONSTRAINT `FK_PRODUCT_ATTRIBUTE_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `core_website` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Products attributes';

/*Data for the table `catalog_product_attribute` */

insert into `catalog_product_attribute` (`product_id`,`attribute_id`,`website_id`,`attribute_value`) values (1,3,1,'Product 1'),(1,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(1,5,1,'22'),(2,3,1,'Product 2'),(2,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(2,5,1,'22'),(3,3,1,'Product 3'),(3,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(3,5,1,'33'),(4,3,1,'Product 4'),(4,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(4,5,1,'44'),(5,3,1,'Product 5'),(5,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(5,5,1,'55'),(6,3,1,'Product 6'),(6,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(6,5,1,'66'),(7,3,1,'Product 7'),(7,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(7,5,1,'77'),(8,3,1,'Product 8'),(8,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(8,5,1,'88'),(9,3,1,'Product 9'),(9,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(9,5,1,'99'),(10,3,1,'Product 10'),(10,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(10,5,1,'10'),(11,3,1,'Product 11'),(11,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(11,5,1,'11'),(12,3,1,'Product 12'),(12,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(12,5,1,'12');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
