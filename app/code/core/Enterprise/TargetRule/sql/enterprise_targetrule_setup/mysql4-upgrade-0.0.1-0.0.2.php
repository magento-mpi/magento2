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
 * @package    Enterprise_TargetRule
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('catalog/eav_attribute'),
    "is_used_for_target_rules", "TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0'");


$installer->run("CREATE TABLE `{$this->getTable('enterprise_targetrule')}` (
    `rule_id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL DEFAULT '',
    `from_date` date DEFAULT NULL,
    `to_date` date DEFAULT NULL,
    `is_active` tinyint(1) NOT NULL DEFAULT '0',
    `conditions_serialized` blob NOT NULL,
    `positions_limit` int(5) NOT NULL DEFAULT '0',
    `apply_to` varchar(255) NOT NULL DEFAULT '',
    `sort_order` int(10) DEFAULT NULL,
    PRIMARY KEY (`rule_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$installer->endSetup();