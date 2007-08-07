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
insert into `catalog_product_link_attribute` (`product_link_attribute_id`,`link_type_id`,`product_link_attribute_code`,`data_type`) values (7,3,'position','int');
insert into `catalog_product_link_attribute` (`product_link_attribute_id`,`link_type_id`,`product_link_attribute_code`,`data_type`) values (8,3,'qty','decimal');

/*Table structure for table `catalog_product_type` */

DROP TABLE IF EXISTS `catalog_product_type`;

CREATE TABLE `catalog_product_type` (
  `type_id` tinyint(3) unsigned NOT NULL auto_increment,
  `code` varchar(32) character set cp1251 NOT NULL default '',
  PRIMARY KEY  (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_product_type` */

insert into `catalog_product_type` (`type_id`,`code`) values (1,'simple');
insert into `catalog_product_type` (`type_id`,`code`) values (2,'bundle');
insert into `catalog_product_type` (`type_id`,`code`) values (3,'super config');
insert into `catalog_product_type` (`type_id`,`code`) values (4,'super group');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
