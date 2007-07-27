SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `catalog_product_link_type` */

DROP TABLE IF EXISTS `catalog_product_link_type`;

CREATE TABLE `catalog_product_link_type` (
  `link_type_id` tinyint(3) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`link_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Types of product link(Related, superproduct, bundles)';

/*Data for the table `catalog_product_link_type` */

insert into `catalog_product_link_type` (`link_type_id`,`code`) values (1,'relation');
insert into `catalog_product_link_type` (`link_type_id`,`code`) values (2,'bundle');
insert into `catalog_product_link_type` (`link_type_id`,`code`) values (3,'super');
insert into `catalog_product_link_type` (`link_type_id`,`code`) values (4,'up_sell');
insert into `catalog_product_link_type` (`link_type_id`,`code`) values (5,'cross_sell');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;