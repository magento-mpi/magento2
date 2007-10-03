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
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

alter table `tag_relation` ,add column `active` tinyint (1) UNSIGNED  DEFAULT '1' NOT NULL  after `store_id`;

alter table  `tag`,   drop column `store_id`;

CREATE TABLE `tag_summary` (
   `tag_id` int(11) unsigned NOT NULL default '0',
   `store_id` smallint(5) unsigned NOT NULL default '0',
   `customers` int(11) unsigned NOT NULL default '0',
   `products` int(11) unsigned NOT NULL default '0',
   `uses` int(11) unsigned NOT NULL default '0',
   `historical_uses` int(11) unsigned NOT NULL default '0',
   `popularity` int(11) unsigned NOT NULL default '0',
   PRIMARY KEY  (`tag_id`,`store_id`),
   CONSTRAINT `TAG_SUMMARY_TAG` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`tag_id`) ON DELETE CASCADE
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
