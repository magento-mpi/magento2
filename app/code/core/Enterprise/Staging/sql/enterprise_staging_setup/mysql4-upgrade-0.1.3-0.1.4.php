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
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer->startSetup();

$installer->getConnection()->dropColumn($this->getTable('core/store'), 'is_staging');
$installer->getConnection()->dropColumn($this->getTable('core/store'), 'master_login');
$installer->getConnection()->dropColumn($this->getTable('core/store'), 'master_password');
$installer->getConnection()->dropColumn($this->getTable('core/store'), 'master_password_hash');

$installer->getConnection()->dropColumn($this->getTable('core/website'), 'master_password_hash');

$installer->getConnection()->addColumn($this->getTable('core/website'), 'is_staging', "TINYINT(1) NOT NULL DEFAULT '0'");
$installer->getConnection()->addColumn($this->getTable('core/website'), 'master_login', "VARCHAR(40) NOT NULL");
$installer->getConnection()->addColumn($this->getTable('core/website'), 'master_password', "VARCHAR(100) NOT NULL");

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('enterprise_staging/dataset')}`;
DROP TABLE IF EXISTS `{$this->getTable('enterprise_staging/dataset_item')}`;

DROP TABLE IF EXISTS `{$this->getTable('enterprise_staging/staging_store')}`;
DROP TABLE IF EXISTS `{$this->getTable('enterprise_staging/staging_store_group')}`;
DROP TABLE IF EXISTS `{$this->getTable('enterprise_staging/staging_website')}`;

DROP TABLE IF EXISTS `{$this->getTable('enterprise_staging/staging_rollback')}`;
DROP TABLE IF EXISTS `{$this->getTable('enterprise_staging/staging_backup')}`;
DROP TABLE IF EXISTS `{$this->getTable('enterprise_staging/staging_event')}`;
DROP TABLE IF EXISTS `{$this->getTable('enterprise_staging/staging_item')}`;
DROP TABLE IF EXISTS `{$this->getTable('enterprise_staging/staging')}`;
");

$installer->run("
CREATE TABLE `{$installer->getTable('enterprise_staging/staging')}` (
  `staging_id` int(10) unsigned NOT NULL auto_increment,
  `type` varchar(50) NOT NULL default '',
  `name` varchar(64) NOT NULL default '',
  `master_website_id` smallint(5) unsigned default NULL,
  `staging_website_id` smallint(5) unsigned NOT NULL default '0',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `state` varchar(20) NOT NULL default '',
  `status` varchar(10) NOT NULL default '',
  `sort_order` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY (`staging_id`),
  KEY `IDX_ENTERPRISE_STAGING_STATE` (`state`),
  KEY `IDX_ENTERPRISE_STAGING_STATUS` (`status`),
  KEY `IDX_ENTERPRISE_STAGING_SORT_ORDER` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Staging';
");
$installer->getConnection()->addConstraint(
    'FK_ENTERPRISE_STAGING_MASTER_WEBSITE_ID',
    $this->getTable('enterprise_staging/staging'),
    'master_website_id',
    $installer->getTable('core/website'),
    'website_id',
    'SET NULL'
);
$installer->getConnection()->addConstraint(
    'FK_ENTERPRISE_STAGING_STAGING_WEBSITE_ID',
    $this->getTable('enterprise_staging/staging'),
    'staging_website_id',
    $installer->getTable('core/website'),
    'website_id'
);

$installer->run("
CREATE TABLE `{$this->getTable('enterprise_staging/staging_item')}` (
  `staging_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staging_id` int(10) unsigned DEFAULT NULL,
  `code` varchar(50) NOT NULL DEFAULT '',
  `sort_order` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`staging_item_id`),
  UNIQUE KEY `UNQ_ENTERPRISE_STAGING_ITEM` (`staging_id`,`code`),
  KEY `IDX_ENTERPRISE_STAGING_ITEM_SORT_ORDER` (`staging_id`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Staging Items';
");
$installer->getConnection()->addConstraint(
    'FK_ENTERPRISE_STAGING_ITEM_STAGING_ID',
    $this->getTable('enterprise_staging/staging_item'),
    'staging_id',
    $installer->getTable('enterprise_staging/staging'),
    'staging_id'
);

$installer->run("
CREATE TABLE `".$this->getTable('enterprise_staging/staging_event')."` (
  `event_id` int(10) NOT NULL auto_increment,
  `staging_id` int(10) unsigned NOT NULL default '0',
  `parent_id` int(10) unsigned NOT NULL default '0',
  `ip` bigint(20) unsigned NOT NULL default '0',
  `code` char(20) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `state` char(20) NOT NULL default '',
  `status` char(20) NOT NULL default '',
  `is_backuped` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `user_id` int(11) NOT NULL default '0',
  `username` varchar(255) NOT NULL default '',
  `action` char(20) NOT NULL default '',
  `attempt_count` smallint(5) NOT NULL default '0',
  `is_admin_notified` tinyint(1) unsigned NOT NULL default '0',
  `comment` text NOT NULL default '',
  `log` text NOT NULL default '',
  `merge_map` text default '',
  `merge_schedule_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`event_id`),
  KEY `IDX_ENTERPRISE_STAGING_EVENT_STAGING_ID` (`staging_id`),
  KEY `IDX_ENTERPRISE_STAGING_EVENT_PARENT_ID` (`parent_id`),
  KEY `IDX_ENTERPRISE_STAGING_EVENT_STATE` (`state`),
  KEY `IDX_ENTERPRISE_STAGING_EVENT_STATUS` (`status`),
  KEY `IDX_ENTERPRISE_STAGING_EVENT_IS_BACKUPED` (`is_backuped`),
  KEY `IDX_ENTERPRISE_STAGING_EVENT_NOTIFY` (`is_admin_notified`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Staging Events';
");
$installer->getConnection()->addConstraint(
    'FK_ENTERPRISE_STAGING_EVENT_ID',
    $installer->getTable('enterprise_staging/staging_event'),
    'staging_id',
    $installer->getTable('enterprise_staging/staging'),
    'staging_id'
);

$installer->run("
CREATE TABLE `".$this->getTable('enterprise_staging/staging_backup')."` (
  `backup_id` int(10) NOT NULL auto_increment,
  `event_id` int(10) unsigned NOT NULL default '0',
  `staging_id` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `state` char(20) NOT NULL default '',
  `status` char(20) NOT NULL default '',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `staging_table_prefix` varchar(255) NOT NULL default '',
  `merge_map` text default '',
  `mage_version`  char(50) NOT NULL default '',
  `mage_modules_version` text NOT NULL default '',
  PRIMARY KEY  (`backup_id`),
  KEY `IDX_ENTERPRISE_STAGING_BACKUP_EVENT_ID` (`event_id`),
  KEY `IDX_ENTERPRISE_STAGING_BACKUP_STAGING_ID` (`staging_id`),
  KEY `IDX_ENTERPRISE_STAGING_BACKUP_STATE` (`state`),
  KEY `IDX_ENTERPRISE_STAGING_BACKUP_STATUS` (`status`),
  KEY `IDX_ENTERPRISE_STAGING_BACKUP_VERSION` (`mage_version`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Staging Backups';
");

$installer->run("
CREATE TABLE `".$this->getTable('enterprise_staging/staging_rollback')."` (
  `rollback_id` int(10) NOT NULL auto_increment,
  `backup_id` int(10) unsigned NOT NULL default '0',
  `event_id` int(10) unsigned NOT NULL default '0',
  `staging_id` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `state` char(20) NOT NULL default '',
  `status` char(20) NOT NULL default '',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `staging_table_prefix` varchar(255) NOT NULL default '',
  `mage_version`  char(50) NOT NULL default '',
  `mage_modules_version` text NOT NULL default '',
  PRIMARY KEY  (`rollback_id`),
  KEY `IDX_ENTERPRISE_STAGING_ROLLBACK_BACKUP_ID` (`backup_id`),
  KEY `IDX_ENTERPRISE_STAGING_ROLLBACK_EVENT_ID` (`event_id`),
  KEY `IDX_ENTERPRISE_STAGING_ROLLBACK_STAGING_ID` (`staging_id`),
  KEY `IDX_ENTERPRISE_STAGING_ROLLBACK_STATE` (`state`),
  KEY `IDX_ENTERPRISE_STAGING_ROLLBACK_STATUS` (`status`),
  KEY `IDX_ENTERPRISE_STAGING_ROLLBACK_VERSION` (`mage_version`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Staging Rollbacks';
");

$installer->endSetup();