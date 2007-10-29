<?php
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
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$this->startSetup()->run("

drop table if exists `core_convert_profile`;
CREATE TABLE `core_convert_profile` (
  `profile_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `actions_xml` text,
  PRIMARY KEY  (`profile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

drop table if exists `core_convert_history`;
CREATE TABLE `core_convert_history` (
  `history_id` int(10) unsigned NOT NULL auto_increment,
  `profile_id` int(10) unsigned NOT NULL default '0',
  `action_code` varchar(64) default NULL,
  `user_id` int(10) unsigned NOT NULL default '0',
  `performed_at` datetime default NULL,
  PRIMARY KEY  (`history_id`),
  KEY `FK_core_convert_history` (`profile_id`),
  CONSTRAINT `FK_core_convert_history` FOREIGN KEY (`profile_id`) REFERENCES `core_convert_profile` (`profile_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

")->endSetup();