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

/* @var $installer Mage_Sales_Model_Mysql4_Setup */
$installer = $this;
$installer->startSetup();

$installer->updateAttribute('quote', 'reward_points_currency_amount', 'attribute_code', 'reward_currency_amount');
$installer->updateAttribute('quote', 'base_reward_points_currency_amount', 'attribute_code', 'base_reward_currency_amount');

$installer->getConnection()->changeColumn($installer->getTable('sales_flat_quote'), 'reward_points_currency_amount', 'reward_currency_amount', 'DECIMAL(12,4) DEFAULT NULL');
$installer->getConnection()->changeColumn($installer->getTable('sales_flat_quote'), 'base_reward_points_currency_amount', 'base_reward_currency_amount', 'DECIMAL(12,4) DEFAULT NULL');

$installer->updateAttribute('quote_address', 'reward_points_currency_amount', 'attribute_code', 'reward_currency_amount');
$installer->updateAttribute('quote_address', 'base_reward_points_currency_amount', 'attribute_code', 'base_reward_currency_amount');

$installer->getConnection()->changeColumn($installer->getTable('sales_flat_quote_address'), 'reward_points_currency_amount', 'reward_currency_amount', 'DECIMAL(12,4) DEFAULT NULL');
$installer->getConnection()->changeColumn($installer->getTable('sales_flat_quote_address'), 'base_reward_points_currency_amount', 'base_reward_currency_amount', 'DECIMAL(12,4) DEFAULT NULL');

$installer->updateAttribute('order', 'reward_points_currency_amount', 'attribute_code', 'reward_currency_amount');
$installer->updateAttribute('order', 'base_reward_points_currency_amount', 'attribute_code', 'base_reward_currency_amount');

$installer->addAttribute('order', 'base_reward_currency_amount_invoiced', array('type' => 'decimal'));
$installer->addAttribute('order', 'reward_currency_amount_invoiced', array('type' => 'decimal'));

$installer->addAttribute('order', 'base_reward_currency_amount_refunded', array('type' => 'decimal'));
$installer->addAttribute('order', 'reward_currency_amount_refunded', array('type' => 'decimal'));

$installer->addAttribute('invoice', 'base_reward_currency_amount', array('type' => 'decimal'));
$installer->addAttribute('invoice', 'reward_currency_amount', array('type' => 'decimal'));

$installer->addAttribute('creditmemo', 'base_reward_currency_amount', array('type' => 'decimal'));
$installer->addAttribute('creditmemo', 'reward_currency_amount', array('type' => 'decimal'));

$installer->endSetup();
