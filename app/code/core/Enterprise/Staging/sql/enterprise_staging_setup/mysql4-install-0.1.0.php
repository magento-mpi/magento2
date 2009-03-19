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
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 */

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer->startSetup();

$installer->getConnection()->addColumn($this->getTable('core/website'), 'is_staging', "TINYINT(1) NOT NULL DEFAULT '0'");
$installer->getConnection()->addColumn($this->getTable('core/website'), 'master_login', "VARCHAR(40) NOT NULL");
$installer->getConnection()->addColumn($this->getTable('core/website'), 'master_password', "VARCHAR(40) NOT NULL");
$installer->getConnection()->addColumn($this->getTable('core/website'), 'master_password_hash', "VARCHAR(40) NOT NULL");

$installer->getConnection()->addColumn($this->getTable('core/store'), 'is_staging', "TINYINT(1) NOT NULL DEFAULT '0'");
$installer->getConnection()->addColumn($this->getTable('core/store'), 'master_login', "VARCHAR(40) NOT NULL");
$installer->getConnection()->addColumn($this->getTable('core/store'), 'master_password', "VARCHAR(40) NOT NULL");
$installer->getConnection()->addColumn($this->getTable('core/store'), 'master_password_hash', "VARCHAR(40) NOT NULL");

$installer->run("
DROP TABLE IF EXISTS `{$this->getTable('enterprise_staging/dataset')}`;
DROP TABLE IF EXISTS `{$this->getTable('enterprise_staging/dataset_item')}`;

DROP TABLE IF EXISTS `{$this->getTable('enterprise_staging/staging_event')}`;
DROP TABLE IF EXISTS `{$this->getTable('enterprise_staging/staging_item')}`;
DROP TABLE IF EXISTS `{$this->getTable('enterprise_staging/staging_store')}`;
DROP TABLE IF EXISTS `{$this->getTable('enterprise_staging/staging_store_group')}`;
DROP TABLE IF EXISTS `{$this->getTable('enterprise_staging/staging_website')}`;
DROP TABLE IF EXISTS `{$this->getTable('enterprise_staging/staging')}`;
");

$installer->run("
CREATE TABLE `{$installer->getTable('enterprise_staging/dataset')}` (
  `dataset_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `is_active` tinyint(1) unsigned NOT NULL default '1',
  `sort_order` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`dataset_id`),
  UNIQUE KEY `UNQ_ENTERPRISE_STAGING_DATASET_NAME` (`name`),
  KEY `IDX_ENTERPRISE_STAGING_DATASET_IS_ACTIVE` (`is_active`),
  KEY `IDX_ENTERPRISE_STAGING_DATASET_SORT_ORDER` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Staging Data Set';
");
$installer->run("
INSERT INTO `{$this->getTable('enterprise_staging/dataset')}`
    (`dataset_id`,`name`,`sort_order`) VALUES
    (1,'Website','0');
");



$installer->run("
CREATE TABLE `{$this->getTable('enterprise_staging/dataset_item')}` (
  `dataset_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dataset_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `code` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `is_active` tinyint(1) unsigned NOT NULL default '1',
  `is_backend` tinyint(1) unsigned NOT NULL default '0',
  `sort_order` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`dataset_item_id`),
  KEY `IDX_ENTERPRISE_STAGING_DATASET_ID` (`dataset_id`),
  UNIQUE KEY `IDX_ENTERPRISE_STAGING_DATASET_ITEM_CODE` (`dataset_id`,`code`),
  KEY `IDX_ENTERPRISE_STAGING_DATASET_ITEM_IS_ACTIVE` (`is_active`),
  KEY `IDX_ENTERPRISE_STAGING_DATASET_ITEM_IS_BACKEND` (`is_backend`),
  KEY `IDX_ENTERPRISE_STAGING_DATASET_ITEM_SORT_ORDER` (`dataset_id`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Staging Data Set Items';
");
$installer->getConnection()->addConstraint(
    'FK_ENTERPRISE_STAGING_EAV_ENTITY_SET_ID',
    $this->getTable('enterprise_staging/dataset_item'),
    'dataset_id',
    $installer->getTable('enterprise_staging/dataset'),
    'dataset_id'
);
$installer->run("
INSERT INTO `{$this->getTable('enterprise_staging/dataset_item')}`
    (`dataset_item_id`,`dataset_id`,`code`,`name`,`is_active`,`is_backend`,`sort_order`) VALUES
    (1,1,'catalog_category','Catalog Category',1,0,'0'),
    (2,1,'catalog_product','Catalog Product',1,0,'1'),
    (3,1,'catalogrule','Catalog Rule',1,0,'2'),
    (4,1,'salesrule','Shopping Cart Rule',1,0,'3'),
    (5,1,'cms_page','Cms Page',1,0,'4'),
    (6,1,'cms_block','Cms Block',1,0,'5'),
    (7,1,'checkout','Conditions And Terms',1,0,'6'),
    (8,1,'poll','Poll',1,0,'7'),
    (9,1,'system_config','System Configuration',1,0,'8'),
    (10,1,'sales','Sale',0,1,'9'),
    (11,1,'customer','Customer',0,1,'10'),
    (12,1,'customer_address','Customer Address',0,1,'11');
");



$installer->run("
CREATE TABLE `{$installer->getTable('enterprise_staging/staging')}` (
  `staging_id` int(10) unsigned NOT NULL auto_increment,
  `dataset_id` smallint(5) unsigned NOT NULL default '0',
  `type` varchar(50) NOT NULL default '',
  `code` varchar(32) NOT NULL default '',
  `name` varchar(64) NOT NULL default '',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `apply_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `auto_apply_is_active` tinyint(1) unsigned NOT NULL default '0',
  `rollback_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `auto_rollback_is_active` tinyint(1) unsigned NOT NULL default '0',
  `state` varchar(20) NOT NULL default '',
  `status` varchar(10) NOT NULL default '',
  `is_active` tinyint(1) unsigned NOT NULL default '1',
  `visibility` varchar(40) NOT NULL default '',
  `master_login` varchar(40) NOT NULL default '',
  `master_password` varchar(40) NOT NULL default '',
  `master_password_hash` varchar(40) NOT NULL default '',
  `sort_order` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`staging_id`),
  KEY `IDX_ENTERPRISE_STAGING_STATE` (`state`),
  KEY `IDX_ENTERPRISE_STAGING_STATUS` (`status`),
  KEY `IDX_ENTERPRISE_STAGING_IS_ACTIVE` (`is_active`),
  KEY `IDX_ENTERPRISE_STAGING_MASTER_LOGIN` (`master_login`),
  KEY `IDX_ENTERPRISE_STAGING_SORT_ORDER` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Staging';
");
$installer->getConnection()->addConstraint(
    'FK_ENTERPRISE_STAGING_DATASET_ID',
    $this->getTable('enterprise_staging/dataset_item'),
    'dataset_id',
    $installer->getTable('enterprise_staging/dataset'),
    'dataset_id'
);



$installer->run("
CREATE TABLE `{$this->getTable('enterprise_staging/staging_item')}` (
  `staging_item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `staging_id` int(10) unsigned DEFAULT NULL,
  `staging_website_id` smallint(5) unsigned DEFAULT NULL,
  `staging_store_id` smallint(5) unsigned DEFAULT NULL,
  `dataset_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `dataset_item_id` int(10) unsigned NOT NULL DEFAULT '0',
  `code` varchar(50) NOT NULL DEFAULT '',
  `state` varchar(20) NOT NULL default '',
  `status` varchar(20) NOT NULL default '',
  `is_active` tinyint(1) unsigned NOT NULL default '1',
  `is_backend` tinyint(1) unsigned NOT NULL default '0',
  `sort_order` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`staging_item_id`),
  UNIQUE KEY `UNQ_ENTERPRISE_STAGING_ITEM` (`staging_id`,`dataset_item_id`),
  KEY `IDX_ENTERPRISE_STAGING_ITEM_DATASET_ID` (`dataset_id`),
  KEY `IDX_ENTERPRISE_STAGING_ITEM_DATASET_ITEM_ID` (`dataset_item_id`),
  KEY `IDX_ENTERPRISE_STAGING_ITEM_STATE` (`state`),
  KEY `IDX_ENTERPRISE_STAGING_ITEM_STATUS` (`status`),
  KEY `IDX_ENTERPRISE_STAGING_ITEM_IS_ACTIVE` (`is_active`),
  KEY `IDX_ENTERPRISE_STAGING_ITEM_IS_BACKEND` (`is_backend`),
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
$installer->getConnection()->addConstraint(
    'FK_ENTERPRISE_STAGING_ITEM_STAGING_WEBSITE_ID',
    $this->getTable('enterprise_staging/staging_item'),
    'staging_website_id',
    $installer->getTable('enterprise_staging/staging_website'),
    'staging_website_id'
);
$installer->getConnection()->addConstraint(
    'FK_ENTERPRISE_STAGING_ITEM_STAGING_STORE_ID',
    $this->getTable('enterprise_staging/staging_item'),
    'staging_store_id',
    $installer->getTable('enterprise_staging/staging_store'),
    'staging_store_id'
);
$installer->getConnection()->addConstraint(
    'FK_ENTERPRISE_STAGING_ITEM_DATASET_ID',
    $this->getTable('enterprise_staging/staging_item'),
    'dataset_id',
    $installer->getTable('enterprise_staging/dataset'),
    'dataset_id'
);
$installer->getConnection()->addConstraint(
    'FK_ENTERPRISE_STAGING_ITEM_DATASET_ITEM_ID',
    $this->getTable('enterprise_staging/staging_item'),
    'dataset_item_id',
    $installer->getTable('enterprise_staging/dataset_item'),
    'dataset_item_id'
);


$installer->run("
CREATE TABLE `{$installer->getTable('enterprise_staging/staging_website')}` (
  `staging_website_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `staging_id` int(10) unsigned NOT NULL default '0',
  `master_website_id` smallint(5) unsigned NOT NULL default '0',
  `master_website_code` varchar(32) NOT NULL DEFAULT '',
  `slave_website_id` smallint(5) unsigned NOT NULL default '0',
  `slave_website_code` varchar(32) NOT NULL DEFAULT '',
  `code` varchar(32) NOT NULL DEFAULT '',
  `name` varchar(64) NOT NULL DEFAULT '',
  `default_group_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `is_default` tinyint(1) unsigned DEFAULT '0',
  `state` varchar(20) NOT NULL default '',
  `status` varchar(20) NOT NULL default '',
  `is_active` tinyint(1) unsigned NOT NULL default '1',
  `sort_order` smallint(5) NOT NULL DEFAULT '0',
  `visibility` varchar(40) NOT NULL default '',
  `master_login` varchar(40) NOT NULL default '',
  `master_password` varchar(40) NOT NULL default '',
  `master_password_hash` varchar(40) NOT NULL default '',
  `base_url` varchar(255) NOT NULL DEFAULT '',
  `base_secure_url` varchar(255) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `apply_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `auto_apply_is_active` tinyint(1) unsigned NOT NULL default '0',
  `rollback_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `auto_rollback_is_active` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY (`staging_website_id`),
  UNIQUE KEY `UNQ_ENTERPRISE_STAGING_WEBSITE` (`staging_id`,`staging_website_id`),
  KEY `IDX_ENTERPRISE_STAGING_WEBSITE_STATE` (`state`),
  KEY `IDX_ENTERPRISE_STAGING_WEBSITE_STATUS` (`status`),
  KEY `IDX_ENTERPRISE_STAGING_WEBSITE_IS_ACTIVE` (`is_active`),
  KEY `IDX_ENTERPRISE_STAGING_WEBSITE_MASTER_LOGIN` (`master_login`),
  KEY `IDX_ENTERPRISE_STAGING_WEBSITE_SORT_ORDER` (`staging_id`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Staging Websites';
");
$installer->getConnection()->addConstraint(
    'FK_ENTERPRISE_STAGING_WEBSITE_MASTER_WEBSITE_ID',
    $this->getTable('enterprise_staging/staging_website'),
    'master_website_id',
    $installer->getTable('core/website'),
    'website_id'
);
$installer->getConnection()->addConstraint(
    'FK_ENTERPRISE_STAGING_WEBSITE_SLAVE_WEBSITE_ID',
    $this->getTable('enterprise_staging/staging_website'),
    'slave_website_id',
    $installer->getTable('core/website'),
    'website_id'
);

$installer->run("
CREATE TABLE `{$installer->getTable('enterprise_staging/staging_store_group')}` (
  `staging_group_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `staging_website_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `staging_id` int(10) unsigned NOT NULL default '0',
  `master_website_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `master_group_id` smallint(5) unsigned NOT NULL default '0',
  `slave_group_id` smallint(5) unsigned NOT NULL default '0',
  `name` varchar(64) NOT NULL DEFAULT '',
  `root_category_id` int(10) unsigned NOT NULL DEFAULT '0',
  `default_store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `state` varchar(20) NOT NULL default '',
  `status` varchar(20) NOT NULL default '',
  `use_specific_items` tinyint(1) unsigned DEFAULT '0',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`staging_group_id`),
  UNIQUE KEY `UNQ_ENTERPRISE_STAGING_WEBSITE_STORE_GROUP` (`staging_website_id`,`staging_group_id`),
  UNIQUE KEY `UNQ_ENTERPRISE_STAGING_STAGING_STORE_GROUP` (`staging_id`,`staging_group_id`),
  KEY `IDX_ENTERPRISE_STAGING_STORE_GROUP_STATE` (`state`),
  KEY `IDX_ENTERPRISE_STAGING_STORE_GROUP_STATUS` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Staging Store Group';
");
$installer->getConnection()->addConstraint(
    'FK_ENTERPRISE_STAGING_STORE_GROUP_STAGING_WEBSITE_ID',
    $this->getTable('enterprise_staging/staging_store_group'),
    'staging_website_id',
    $installer->getTable('enterprise_staging/staging_website'),
    'staging_website_id'
);
$installer->getConnection()->addConstraint(
    'FK_ENTERPRISE_STAGING_STORE_GROUP_STAGING_ID',
    $this->getTable('enterprise_staging/staging_store_group'),
    'staging_id',
    $installer->getTable('enterprise_staging/staging'),
    'staging_id'
);



$installer->run("
CREATE TABLE `{$installer->getTable('enterprise_staging/staging_store')}` (
  `staging_store_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `staging_group_id` smallint(5) unsigned DEFAULT NULL,
  `staging_website_id` smallint(5) unsigned DEFAULT NULL,
  `staging_id` int(10) unsigned DEFAULT NULL,
  `master_store_id` smallint(5) DEFAULT NULL,
  `master_store_code` varchar(32) NOT NULL DEFAULT '',
  `slave_store_id` smallint(5) unsigned DEFAULT NULL,
  `slave_store_code` varchar(32) NOT NULL DEFAULT '',
  `code` varchar(32) NOT NULL DEFAULT '',
  `name` varchar(64) NOT NULL DEFAULT '',
  `master_website_id` smallint(5) unsigned DEFAULT NULL,
  `master_group_id` smallint(5) unsigned DEFAULT NULL,
  `is_default` tinyint(1) unsigned DEFAULT '0',
  `state` varchar(20) NOT NULL DEFAULT '',
  `status` varchar(20) NOT NULL DEFAULT '',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `sort_order` smallint(5) NOT NULL DEFAULT '0',
  `base_url` varchar(255) NOT NULL DEFAULT '',
  `base_secure_url` varchar(255) NOT NULL DEFAULT '',
  `visibility` varchar(40) NOT NULL DEFAULT '',
  `use_specific_items` tinyint(1) NOT NULL DEFAULT '0',
  `master_login` varchar(40) NOT NULL default '',
  `master_password` varchar(40) NOT NULL default '',
  `master_password_hash` varchar(40) NOT NULL default '',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`staging_store_id`),
  UNIQUE KEY `UNQ_ENTERPRISE_STAGING_WEBSITE_STORE` (`staging_website_id`,`staging_store_id`),
  UNIQUE KEY `UNQ_ENTERPRISE_STAGING_GROUP_STORE` (`staging_group_id`,`staging_store_id`),
  KEY `IDX_ENTERPRISE_STAGING_STORE_STATE` (`state`),
  KEY `IDX_ENTERPRISE_STAGING_STORE_STATUS` (`status`),
  KEY `IDX_ENTERPRISE_STAGING_STORE_IS_ACTIVE` (`is_active`),
  KEY `IDX_ENTERPRISE_STAGING_STORE_MASTER_LOGIN` (`master_login`),
  KEY `IDX_ENTERPRISE_STAGING_STORE_SORT_ORDER` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Staging Store';
");
$installer->getConnection()->addConstraint(
    'FK_ENTERPRISE_STAGING_STORE_STAGING_GROUP_ID',
    $this->getTable('enterprise_staging/staging_store'),
    'staging_group_id',
    $installer->getTable('enterprise_staging/staging_store_group'),
    'staging_group_id'
);
$installer->getConnection()->addConstraint(
    'FK_ENTERPRISE_STAGING_STORE_STAGING_WEBSITE_ID',
    $this->getTable('enterprise_staging/staging_store'),
    'staging_website_id',
    $installer->getTable('enterprise_staging/staging_website'),
    'staging_website_id'
);
$installer->getConnection()->addConstraint(
    'FK_ENTERPRISE_STAGING_STORE_STAGING_ID',
    $this->getTable('enterprise_staging/staging_store'),
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
  `status` char(20) NOT NULL default '',
  `internal_status` char(20) NOT NULL default '',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `user_id` int(11) NOT NULL default '0',
  `username` varchar(255) NOT NULL default '',
  `action` char(20) NOT NULL default '',
  `attempt_count` smallint(5) NOT NULL default '0',
  `is_admin_notified` tinyint(1) unsigned NOT NULL default '0',
  `comment` text NOT NULL default '',
  `log` text NOT NULL default '',
  PRIMARY KEY  (`event_id`),
  KEY `IDX_ENTERPRISE_STAGING_EVENT_STAGING_ID` (`staging_id`),
  KEY `IDX_ENTERPRISE_STAGING_EVENT_PARENT_ID` (`parent_id`),
  KEY `IDX_ENTERPRISE_STAGING_EVENT_STATUS` (`status`),
  KEY `IDX_ENTERPRISE_STAGING_EVENT_INT_STATUS` (`internal_status`),
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

$installer->endSetup();