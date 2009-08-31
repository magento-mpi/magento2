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
 * @package    Enterprise_Banner
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

$installer = $this;
/* @var $installer Enterprise_Banner_Model_Mysql4_Setup */
$installer->startSetup();

$bannerTable            = $this->getTable('enterprise_banner/banner');
$bannerContentTable     = $this->getTable('enterprise_banner/content');
$bannerCatalogRuleTable = $this->getTable('enterprise_banner/catalogrule');
$bannerSalesRuleTable   = $this->getTable('enterprise_banner/salesrule');

$installer->run("CREATE TABLE `{$bannerTable}` (
 `banner_id` int(10) unsigned NOT NULL auto_increment,
 `name` varchar(255) NOT NULL,
 `is_enabled` int(1) NOT NULL,
 PRIMARY KEY  (`banner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Banners'");

$installer->run("CREATE TABLE `{$bannerContentTable}` (
 `banner_id` int(10) unsigned NOT NULL default '0',
 `store_id` smallint(5) unsigned default NULL,
 `banner_content` mediumtext collate utf8_bin NOT NULL,
 KEY `banner_id` (`banner_id`),
 KEY `store_id` (`store_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Banners Content per Store'");

$installer->getConnection()->addConstraint(
    'FK_BANNER_CONTENT_BANNER', $bannerContentTable, 'banner_id',
    $bannerTable, 'banner_id', 'CASCADE', 'CASCADE'
);
$installer->getConnection()->addConstraint(
    'FK_BANNER_CONTENT_STORE', $bannerContentTable, 'store_id',
    $installer->getTable('core/store'), 'store_id', 'CASCADE', 'CASCADE'
);

$installer->run("CREATE TABLE `{$bannerCatalogRuleTable}` (
 `banner_id` int(10) unsigned NOT NULL,
 `rule_id` int(10) unsigned NOT NULL,
 KEY `banner_id` (`banner_id`),
 KEY `rule_id` (`rule_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Banners Relations to Catalog Rules'");

$installer->getConnection()->addConstraint(
    'FK_BANNER_CATALOGRULE_BANNER', $bannerCatalogRuleTable, 'banner_id',
    $bannerTable, 'banner_id', 'CASCADE', 'CASCADE'
);
$installer->getConnection()->addConstraint(
    'FK_BANNER_CATALOGRULE_RULE', $bannerCatalogRuleTable, 'rule_id',
    $installer->getTable('catalogrule'), 'rule_id', 'CASCADE', 'CASCADE'
);

$installer->run("CREATE TABLE `{$bannerSalesRuleTable}` (
 `banner_id` int(10) unsigned NOT NULL,
 `rule_id` int(10) unsigned NOT NULL,
 KEY `banner_id` (`banner_id`),
 KEY `rule_id` (`rule_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Banners Relations to Sales Rules'");

$installer->getConnection()->addConstraint(
    'FK_BANNER_SALESRULE_BANNER', $bannerSalesRuleTable, 'banner_id',
    $bannerTable, 'banner_id', 'CASCADE', 'CASCADE'
);
$installer->getConnection()->addConstraint(
    'FK_BANNER_SALESRULE_RULE', $bannerSalesRuleTable, 'rule_id',
    $installer->getTable('catalogrule/rule'), 'rule_id', 'CASCADE', 'CASCADE'
);

$installer->endSetup();