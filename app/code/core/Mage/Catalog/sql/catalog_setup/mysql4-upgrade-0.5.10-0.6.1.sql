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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/* Bundles db structure */

SET NAMES utf8 ;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `catalog_product_bundle_option` */

DROP TABLE IF EXISTS `catalog_product_bundle_option`;

CREATE TABLE `catalog_product_bundle_option` (
  `option_id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`option_id`),
  KEY `FK_catalog_product_bundle_option` (`product_id`),
  CONSTRAINT `FK_catalog_product_bundle_option` FOREIGN KEY (`product_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_product_bundle_option` */

/*Table structure for table `catalog_product_bundle_option_link` */

DROP TABLE IF EXISTS `catalog_product_bundle_option_link`;

CREATE TABLE `catalog_product_bundle_option_link` (
  `link_id` int(10) unsigned NOT NULL auto_increment,
  `option_id` int(10) unsigned NOT NULL default '0',
  `product_id` int(10) unsigned NOT NULL default '0',
  `discount` decimal(10,4) unsigned default NULL,
  PRIMARY KEY  (`link_id`),
  KEY `FK_catalog_product_bundle_option_link` (`option_id`),
  KEY `FK_catalog_product_bundle_option_link_entity` (`product_id`),
  CONSTRAINT `FK_catalog_product_bundle_option_link_entity` FOREIGN KEY (`product_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_catalog_product_bundle_option_link` FOREIGN KEY (`option_id`) REFERENCES `catalog_product_bundle_option` (`option_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_product_bundle_option_link` */

/*Table structure for table `catalog_product_bundle_option_value` */

DROP TABLE IF EXISTS `catalog_product_bundle_option_value`;

CREATE TABLE `catalog_product_bundle_option_value` (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `option_id` int(10) unsigned NOT NULL default '0',
  `label` varchar(255) default NULL,
  `position` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_catalog_product_bundle_option_label` (`option_id`),
  CONSTRAINT `FK_catalog_product_bundle_option_label` FOREIGN KEY (`option_id`) REFERENCES `catalog_product_bundle_option` (`option_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `catalog_product_bundle_option_value` */

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
