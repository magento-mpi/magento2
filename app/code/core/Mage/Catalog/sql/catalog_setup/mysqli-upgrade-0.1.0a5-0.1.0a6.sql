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

/*Table structure for table `catalog_attribute` */

DROP TABLE IF EXISTS `catalog_attribute`;

CREATE TABLE `catalog_attribute` (
  `attribute_id` int(10) unsigned NOT NULL auto_increment,
  `attribute_source_id` smallint(6) unsigned default NULL,
  `attribute_type_id` smallint(4) unsigned default NULL,
  `attribute_code` varchar(32) NOT NULL default '',
  `is_user_defined` tinyint(1) unsigned NOT NULL default '0',
  `is_required` tinyint(1) unsigned NOT NULL default '0',
  `sort_order` smallint(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`attribute_id`),
  KEY `FK_ATTRIBUTE_SOURCE` (`attribute_source_id`),
  KEY `FK_ATTRIBUTE_TYPE` (`attribute_type_id`),
  CONSTRAINT `FK_ATTRIBUTE_SOURCE` FOREIGN KEY (`attribute_source_id`) REFERENCES `catalog_attribute_source` (`attribute_source_id`),
  CONSTRAINT `FK_ATTRIBUTE_TYPE` FOREIGN KEY (`attribute_type_id`) REFERENCES `catalog_attribute_type` (`attribute_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='MySQL table';

/*Data for the table `catalog_attribute` */

insert into `catalog_attribute` (`attribute_id`,`attribute_source_id`,`attribute_type_id`,`attribute_code`,`is_user_defined`,`is_required`,`sort_order`) values (1,1,1,'title',0,1,1),(2,1,2,'description',0,1,2),(3,2,1,'name',0,1,1),(4,2,2,'description',0,1,2),(5,2,1,'price',0,1,3),(6,2,1,'weight',0,1,4),(7,2,1,'image',0,1,5),(8,2,1,'qty',0,1,6);

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

insert into `catalog_product_attribute` (`product_id`,`attribute_id`,`website_id`,`attribute_value`) values (1,3,1,'Product 1'),(1,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(1,5,1,'22'),(1,8,1,'10'),(2,3,1,'Product 2'),(2,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(2,5,1,'22'),(2,8,1,'12'),(3,3,1,'Product 3'),(3,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(3,5,1,'33'),(3,8,1,'13'),(4,3,1,'Product 4'),(4,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(4,5,1,'44'),(4,8,1,'22'),(5,3,1,'Product 5'),(5,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(5,5,1,'55'),(5,8,1,'43'),(6,3,1,'Product 6'),(6,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(6,5,1,'66'),(6,8,1,'21'),(7,3,1,'Product 7'),(7,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(7,5,1,'77'),(7,8,1,'0'),(8,3,1,'Product 8'),(8,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(8,5,1,'88'),(8,8,1,'54'),(9,3,1,'Product 9'),(9,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(9,5,1,'99'),(9,8,1,'22'),(10,3,1,'Product 10'),(10,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(10,5,1,'10'),(10,8,1,'87'),(11,3,1,'Product 11'),(11,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(11,5,1,'11'),(11,8,1,'33'),(12,3,1,'Product 12'),(12,4,1,'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam fringilla. In hac habitasse platea dictumst. Pellentesque erat sapien, condimentum ac, dictum in, faucibus sit amet, augue. Pellentesque convallis. Duis in mauris id metus ornare mattis. Vestibulum placerat. Pellentesque quam risus, venenatis fermentum, molestie eu, feugiat sit amet, metus. Suspendisse eleifend nulla eu elit. Vivamus eu mauris.'),(12,5,1,'12'),(12,8,1,'55');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
