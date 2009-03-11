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
 * @package    Enterprise_CatalogPermissions
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Enterprise_CatalogPermissions_Model_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

$tableName = $installer->getTable('enterprise_catalogpermissions/permission');
$fkPrefix = strtoupper($tableName);

$installer->run("
    CREATE TABLE `{$tableName}` (
        `permission_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `category_id` INT(10) UNSIGNED NOT NULL,
        `website_id` SMALLINT(5) UNSIGNED NOT NULL,
        `customer_group_id` SMALLINT(3) UNSIGNED NOT NULL,
        `grant_catalog_category_view` TINYINT(1) NOT NULL,
        `grant_catalog_product_price` TINYINT(1) NOT NULL,
        `grant_checkout_items` TINYINT(1) NOT NULL
        PRIMARY KEY (`permission_id`),
        UNIQUE KEY `UNQ_PERMISSION_SCOPE` (`category_id`, `website_id`, `customer_group_id`)
    ) ENGINE=InnoDB;
");

$installer->getConnection()->addConstraint($fkPrefix . '_CATEGORY', $tableName, 'category_id',
                                           $installer->getTable('catalog/category'), 'entity_id');

$installer->getConnection()->addConstraint($fkPrefix . '_WEBSITE', $tableName, 'website_id',
                                           $installer->getTable('core/website'), 'website_id');

$installer->getConnection()->addConstraint($fkPrefix . '_CUSTGROUP', $tableName, 'customer_group_id',
                                           $installer->getTable('customer/customer_group'), 'customer_group_id');

$installer->endSetup();