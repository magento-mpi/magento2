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
 * @package     Enterprise_Banner
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/* @var $installer Enterprise_Banner_Model_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$installer->getTable('enterprise_banner/banner')}` (
  `banner_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `is_enabled` INT(1) NOT NULL,
  PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Banners';

CREATE TABLE IF NOT EXISTS `{$installer->getTable('enterprise_banner/content')}` (
  `banner_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `store_id` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
  `banner_content` MEDIUMTEXT NOT NULL,
  PRIMARY KEY (`banner_id`,`store_id`),
  KEY `banner_id` (`banner_id`),
  KEY `store_id` (`store_id`),
  CONSTRAINT `FK_BANNER_CONTENT_BANNER` FOREIGN KEY (`banner_id`) REFERENCES `{$installer->getTable('enterprise_banner/banner')}` (`banner_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_BANNER_CONTENT_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Banners Content per Store';

CREATE TABLE IF NOT EXISTS `{$installer->getTable('enterprise_banner/catalogrule')}` (
  `banner_id` INT(10) UNSIGNED NOT NULL,
  `rule_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`banner_id`,`rule_id`),
  KEY `banner_id` (`banner_id`),
  KEY `rule_id` (`rule_id`),
  CONSTRAINT `FK_BANNER_CATALOGRULE_BANNER` FOREIGN KEY (`banner_id`) REFERENCES `{$installer->getTable('enterprise_banner/banner')}` (`banner_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_BANNER_CATALOGRULE_RULE` FOREIGN KEY (`rule_id`) REFERENCES `{$installer->getTable('catalogrule')}` (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Banners Relations to Catalog Rules';

CREATE TABLE IF NOT EXISTS `{$installer->getTable('enterprise_banner/salesrule')}` (
  `banner_id` INT(10) UNSIGNED NOT NULL,
  `rule_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`banner_id`,`rule_id`),
  KEY `banner_id` (`banner_id`),
  KEY `rule_id` (`rule_id`),
  CONSTRAINT `FK_BANNER_SALESRULE_BANNER` FOREIGN KEY (`banner_id`) REFERENCES `{$installer->getTable('enterprise_banner/banner')}` (`banner_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_BANNER_SALESRULE_RULE` FOREIGN KEY (`rule_id`) REFERENCES `{$installer->getTable('catalogrule/rule')}` (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Banners Relations to Sales Rules';

CREATE TABLE IF NOT EXISTS `{$installer->getTable('enterprise_banner/customersegment')}` (
  `banner_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `segment_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`banner_id`,`segment_id`),
  KEY `banner_id` (`banner_id`),
  KEY `segment_id` (`segment_id`),
  CONSTRAINT `FK_BANNER_CUSTOMER_SEGMENT_BANNER` FOREIGN KEY (`banner_id`) REFERENCES `{$installer->getTable('enterprise_banner/banner')}` (`banner_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_BANNER_CUSTOMER_SEGMENT_SEGMENT` FOREIGN KEY (`segment_id`) REFERENCES `{$installer->getTable('enterprise_customersegment/segment')}` (`segment_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation Banners with Customer Segments';
");

$installer->endSetup();
