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
 * @package     Enterprise_TargetRule
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/** @var Enterprise_TargetRule_Model_Mysql4_Setup */
$installer = $this;

$installer->getConnection()->modifyColumn(
    $installer->getTable('catalog/eav_attribute'), 'is_used_for_target_rules',
    "TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'deprecated since 1.7.1.0'"
);

$installer->run("UPDATE {$installer->getTable('catalog/eav_attribute')}
    SET is_used_for_promo_rules = is_used_for_promo_rules || is_used_for_target_rules;"
);
