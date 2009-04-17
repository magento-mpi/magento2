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
 * @package    Enterprise_CatalogEvent
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

// add fancy homepage content

/* @var $installer Enterprise_CatalogEvent_Model_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

$tablePage = $this->getTable('cms/page');
$pageId = $installer->getConnection()->fetchOne($installer->getConnection()->select()
    ->from($tablePage, 'page_id')
    ->where('identifier = ?', 'home'));

if ($pageId) {
    $installer->getConnection()->update($tablePage, array('content' => '<a href="{{store url=""}}apparel.html">
<img class="callout" title="Get 10% off - All items under Apparel" alt="Get 10% off - All items under Apparel" src="{{skin url="images/callouts/apparel-sale.gif"}}"/>
</a>
<div class="col2-set">
    <div class="col-1">
        <img src="{{skin url="images/callouts/home/main.jpg"}}" alt=""/>
    </div>
    <div class="col-2">
        <a href="{{store url=""}}gift-cards.html"><img src="{{skin url="images/callouts/home/gift_cards.jpg"}}" alt=""/></a>
        <a href="{{store url=""}}apparel.html"><img src="{{skin url="images/callouts/home/rediscover_identity.jpg"}}" alt=""/></a>
        <a href="{{store url=""}}refund-policy"><img src="{{skin url="images/callouts/home/refund_policy.jpg"}}" alt="" style="margin-bottom:7px;"/></a>
    </div>
</div>'), "page_id = {$pageId}");
}

$installer->endSetup();
