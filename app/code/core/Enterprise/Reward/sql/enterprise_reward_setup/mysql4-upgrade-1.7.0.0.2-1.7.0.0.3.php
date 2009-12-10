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

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$installer->getConnection()->dropForeignKey($this->getTable('enterprise_reward'), 'FK_REWARD_CUSTOMER_ID');
$installer->getConnection()->dropForeignKey($this->getTable('enterprise_reward'), 'FK_REWARD_WEBSITE_ID');
$installer->getConnection()->dropKey($this->getTable('enterprise_reward'), 'IDX_CUSTOMER_ID');
$installer->getConnection()->dropKey($this->getTable('enterprise_reward'), 'IDX_WEBSITE_ID');
$installer->getConnection()->addKey($this->getTable('enterprise_reward'), 'UNQ_CUSTOMER_WEBSITE', array('customer_id', 'website_id'), 'unique');
$installer->getConnection()->addConstraint('REWARD_CUSTOMER_ID', $this->getTable('enterprise_reward'), 'customer_id', $this->getTable('customer_entity'), 'entity_id');
$installer->getConnection()->addConstraint('REWARD_WEBSITE_ID', $this->getTable('enterprise_reward'), 'website_id', $this->getTable('core_website'), 'website_id');
$installer->endSetup();
