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
 * @package     Enterprise_Reminder
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

$installer = $this;
/* @var $installer Enterprise_Reminder_Model_Mysql4_Setup */

$installer->getConnection()->dropForeignKey(
    $this->getTable('enterprise_reminder/rule'),
    'FK_IDX_EE_REMINDER_SALESRULE'
);

$installer->getConnection()->changeColumn(
    $this->getTable('enterprise_reminder/rule'),
    'salesrule_id',
    'salesrule_id',
    'int(10) unsigned DEFAULT NULL'
);

$installer->getConnection()->addConstraint(
    'IDX_EE_REMINDER_SALESRULE',
    $this->getTable('enterprise_reminder/rule'),
    'salesrule_id',
    $this->getTable('salesrule'),
    'rule_id',
    'SET NULL'
);

$installer->getConnection()->addColumn(
    $this->getTable('enterprise_reminder/coupon'),
    'emails_failed',
    'smallint(5) unsigned NOT NULL default "0"'
);
