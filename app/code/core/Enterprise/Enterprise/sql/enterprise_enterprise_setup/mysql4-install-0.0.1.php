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
 * @package     Enterprise_Enterprise
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$tablePage = $this->getTable('cms/page');

// add fancy homepage content and make it 1-column layout
$page = $installer->getConnection()->fetchRow($installer->getConnection()->select()
    ->from($tablePage, array('page_id', 'content'))
    ->where('identifier = ?', 'home')
    ->limit(1));
if ($page) {
    $content = '<a href="{{store url=""}}">
<img class="callout" title="Get 10% off - All items under Apparel" alt="Get 10% off - All items under Apparel" src="{{skin url="images/callouts/apparel-sale.gif"}}"/>
</a>
<div class="col2-set">
    <div class="col-1">
        <img src="{{skin url="images/callouts/home/main.jpg"}}" alt="" />
    </div>
    <div class="col-2">
        <a href="{{store url=""}}"><img src="{{skin url="images/callouts/home/gift_cards.jpg"}}" alt=""/></a>
        <a href="{{store url=""}}"><img src="{{skin url="images/callouts/home/rediscover_identity.jpg"}}" alt=""/></a>
        <a href="{{store url=""}}"><img src="{{skin url="images/callouts/home/refund_policy.jpg"}}" alt="" style="margin-bottom:7px;"/></a>
    </div>
</div>' . "\n\n<!-- " . $page['content'] . ' -->';
    $installer->getConnection()->update($tablePage, array('content' => $content, 'root_template' => 'one_column'), "page_id = {$page['page_id']}");
}

// add fancy 404 page content
$page = $installer->getConnection()->fetchRow($installer->getConnection()->select()
    ->from($tablePage, array('page_id', 'content'))
    ->where('identifier = ?', 'no-route')
    ->limit(1));
if ($page) {
    $content = '<div class="page-head-alt"><h3>We’re sorry, the page you’re looking for can not be found.</h3></div>
<div>
    <ul class="disc">
        <li>If you typed the URL directly, please make sure the spelling is correct.</li>
        <li>If you clicked on a link to get here, we must have moved the content.<br/>Please try our store search box above to search for an item.</li>
        <li>If you are not sure how you got here, <a href="#" onclick="history.go(-1);">go back</a> to the previous page</a> or return to our <a href="{{store url=""}}">store homepage</a>.</li>
    </ul>
</div>' . "\n\n<!-- " . $page['content'] . ' -->';
    $installer->getConnection()->update($tablePage, array('content' => $content), "page_id = {$page['page_id']}");
}

$installer->endSetup();
