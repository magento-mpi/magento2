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
 * @package     Enterprise_Reward
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/* @var $installer Mage_Customer_Model_Entity_Setup */
$installer = $this;
$installer->startSetup();

$installer->getConnection()->dropColumn($installer->getTable('enterprise_reward'), 'currency_amount');
$installer->getConnection()->addColumn($installer->getTable('enterprise_reward_history'), 'rate_description', "VARCHAR(255) NOT NULL DEFAULT '' AFTER `currency_delta`");
$installer->getConnection()->addColumn($installer->getTable('enterprise_reward_history'), 'comment', "TEXT NOT NULL DEFAULT '' AFTER `additional_info`");
$installer->getConnection()->addColumn($installer->getTable('enterprise_reward_history'), 'additional_data', "TEXT NOT NULL DEFAULT '' AFTER `additional_info`");
$installer->getConnection()->dropColumn($installer->getTable('enterprise_reward_history'), 'additional_info');
$installer->getConnection()->changeColumn($installer->getTable('enterprise_reward_history'), 'action', 'action', "TINYINT(3) NOT NULL DEFAULT '0'");
$installer->getConnection()->addColumn($installer->getTable('enterprise_reward_rate'), 'direction', "TINYINT(3) NOT NULL DEFAULT '1' AFTER `customer_group_id`");
$installer->getConnection()->changeColumn($installer->getTable('enterprise_reward_rate'), 'points_count', 'points', "INT(11) NOT NULL DEFAULT '0'");
$installer->getConnection()->dropColumn($installer->getTable('enterprise_reward_rate'), 'points_currency_value');
$installer->getConnection()->dropColumn($installer->getTable('enterprise_reward_rate'), 'currency_points_value');
$installer->getConnection()->dropKey($installer->getTable('enterprise_reward_rate'), 'IDX_WEBSITE_GROUP');
$installer->getConnection()->addKey($installer->getTable('enterprise_reward_rate'), 'IDX_WEBSITE_GROUP_DIRECTION', array('website_id', 'customer_group_id', 'direction'), 'unique');

$installer->endSetup();
