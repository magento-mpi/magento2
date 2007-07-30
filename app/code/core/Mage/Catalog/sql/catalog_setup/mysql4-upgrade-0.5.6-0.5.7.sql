SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `catalog_product_link_attribute` */

DROP TABLE IF EXISTS `catalog_product_link_attribute`;

CREATE TABLE `catalog_product_link_attribute` (
  `product_link_attribute_id` smallint(6) unsigned NOT NULL auto_increment,
  `link_type_id` tinyint(3) unsigned NOT NULL default '0',
  `product_link_attribute_code` varchar(32) NOT NULL default '',
  `data_type` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`product_link_attribute_id`),
  KEY `FK_ATTRIBUTE_PRODUCT_LINK_TYPE` (`link_type_id`),
  CONSTRAINT `FK_ATTRIBUTE_PRODUCT_LINK_TYPE` FOREIGN KEY (`link_type_id`) REFERENCES `catalog_product_link_type` (`link_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Attributes for product link';

/*Data for the table `catalog_product_link_attribute` */

insert into `catalog_product_link_attribute` (`product_link_attribute_id`,`link_type_id`,`product_link_attribute_code`,`data_type`) values (1,2,'qty','decimal');
insert into `catalog_product_link_attribute` (`product_link_attribute_id`,`link_type_id`,`product_link_attribute_code`,`data_type`) values (2,1,'position','int');
insert into `catalog_product_link_attribute` (`product_link_attribute_id`,`link_type_id`,`product_link_attribute_code`,`data_type`) values (3,4,'position','int');
insert into `catalog_product_link_attribute` (`product_link_attribute_id`,`link_type_id`,`product_link_attribute_code`,`data_type`) values (4,5,'position','int');
insert into `catalog_product_link_attribute` (`product_link_attribute_id`,`link_type_id`,`product_link_attribute_code`,`data_type`) values (6,1,'qty','decimal');

/*Table structure for table `catalog_product_link_attribute_int` */

DROP TABLE IF EXISTS `catalog_product_link_attribute_int`;

CREATE TABLE `catalog_product_link_attribute_int` (
  `value_id` int(11) unsigned NOT NULL auto_increment,
  `product_link_attribute_id` smallint(6) unsigned default NULL,
  `link_id` int(11) unsigned default NULL,
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_INT_PRODUCT_LINK_ATTRIBUTE` (`product_link_attribute_id`),
  KEY `FK_INT_PRODUCT_LINK` (`link_id`),
  CONSTRAINT `FK_INT_PRODUCT_LINK_ATTRIBUTE` FOREIGN KEY (`product_link_attribute_id`) REFERENCES `catalog_product_link_attribute` (`product_link_attribute_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_INT_PRODUCT_LINK` FOREIGN KEY (`link_id`) REFERENCES `catalog_product_link` (`link_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
