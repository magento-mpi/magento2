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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

// alter table `xmlconnect_application` add column `configuration` blob default null;
// alter table `xmlconnect_application` add column `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';

$installer->run("
CREATE TABLE `{$installer->getTable('xmlconnect_application')}` (
  `application_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(32) NOT NULL,
  `type` varchar(32) DEFAULT NULL,
  `store_id` smallint(5) unsigned DEFAULT NULL,
  `active_from` date DEFAULT NULL,
  `active_to` date DEFAULT NULL,
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `configuration` blob DEFAULT NULL,
  PRIMARY KEY (`application_id`),
  KEY `FK_XMLCONNECT_APPLICAION_STORE` (`store_id`),
  CONSTRAINT `FK_XMLCONNECT_APPLICAION_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`)
    ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8

");
