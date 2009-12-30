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

$now = new Zend_Date(time());
$now = $now->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

$installer->getConnection()->insert($installer->getTable('cms/page'), array(
        'title' => 'Reward Points',
        'identifier' => 'reward-points',
        'content' => '<div class="box">
<div class="content">
<p>Gain Reward Points and spend them as you wish!</p>
<p>If you are a registered member, please <a href="{{store url="customer/account/login"}}">log in here</a>.</p>
</div>
</div>',
        'creation_time' => $now,
        'update_time' => $now,
        'is_active' => '1'
));

$pageId = $installer->getConnection()->lastInsertId();
$installer->getConnection()->insert(
    $installer->getTable('cms/page_store'),
    array('page_id'=>$pageId, 'store_id'=>0)
);

$installer->endSetup();
