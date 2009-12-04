<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();
$installer->run("
CREATE TABLE IF NOT EXISTS `{$installer->getTable('enterprise_reward')}` (
  `reward_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `customer_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `website_id` smallINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `points_balance` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `currency_amount` decimal(12,4) UNSIGNED NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`reward_id`),
  KEY `IDX_CUSTOMER_ID` (`customer_id`),
  KEY `IDX_WEBSITE_ID` (`website_id`),
  CONSTRAINT `FK_REWARD_CUSTOMER_ID` FOREIGN KEY (`customer_id`) REFERENCES `{$installer->getTable('customer_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_REWARD_WEBSITE_ID` FOREIGN KEY (`website_id`) REFERENCES `{$installer->getTable('core_website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$installer->getTable('enterprise_reward_history')}` (
  `history_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `reward_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `website_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `action` SMALLINT(2) UNSIGNED NOT NULL DEFAULT '0',
  `points_balance` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `points_delta` INT(11) NOT NULL DEFAULT '0',
  `currency_amount` DECIMAL(12,4) UNSIGNED NOT NULL DEFAULT '0.0000',
  `currency_delta` DECIMAL(12,4) NOT NULL DEFAULT '0.0000',
  `base_currency_code` VARCHAR(5) NOT NULL,
  `additional_info` TEXT,
  `created_at` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`history_id`),
  KEY `IDX_REWARD_ID` (`reward_id`),
  KEY `IDX_WEBSITE_ID` (`reward_id`),
  CONSTRAINT `FK_REWARD_HISTORY_REWARD_ID` FOREIGN KEY (`reward_id`) REFERENCES `{$installer->getTable('enterprise_reward')}` (`reward_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$installer->getTable('enterprise_reward_rate')}` (
  `rate_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `website_id` smallINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `customer_group_id` smallINT(5) UNSIGNED DEFAULT NULL,
  `points_count` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `points_currency_value` DECIMAL(12,4) UNSIGNED NOT NULL DEFAULT '0.0000',
  `currency_amount` DECIMAL(12,4) UNSIGNED NOT NULL DEFAULT '0.0000',
  `currency_points_value` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`rate_id`),
  UNIQUE KEY `IDX_WEBSITE_GROUP` (`website_id`,`customer_group_id`),
  KEY `IDX_WEBSITE_ID` (`website_id`),
  KEY `IDX_CUSTOMER_GROUP_ID` (`customer_group_id`),
  CONSTRAINT `FK_REWARD_RATE_WEBSITE_ID` FOREIGN KEY (`website_id`) REFERENCES `{$installer->getTable('core_website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");
$installer->addAttribute('customer', 'reward_update_notification', array('type' => 'int', 'visible' => 0, 'visible_on_front' => 1));
$installer->addAttribute('customer', 'reward_warning_notification', array('type' => 'int', 'visible' => 0, 'visible_on_front' => 1));
$installer->endSetup();