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

$tablePage = $this->getTable('cms/page');

// add fancy homepage content and make it 1-column layout
$page = $installer->getConnection()->fetchRow($installer->getConnection()->select()
    ->from($tablePage, array('page_id', 'content'))
    ->where('identifier = ?', 'home')
    ->limit(1));
if ($page) {

    // but first create a few banners
    $banners = array(
        array('top.container', 'Free Shipping on All Handbags', '<a href="{{store direct_url="apparel/women/handbags"}}"> <img class="callout" title="Get Free Shipping on All Items under Handbags" src="{{skin url="images/callouts/home/free_shipping_all_handbags.jpg"}}" alt="Free Shipping on All Handbags" /></a>'),
        array('footer.before', '15% off Our New Evening Dresses', '<a href="{{store direct_url="apparel/women/evening-dresses"}}"> <img class="callout" title="15% off Our New Evening Dresses" src="{{skin url="images/callouts/home/15_off_new_evening_dresses.jpg"}}" alt="15% off Our New Evening Dresses" /></a>'),
    );
    $createdBanners = array();
    foreach ($banners as $bannerData) {
        list($reference, $title, $content) = $bannerData;
        $banner = Mage::getModel('enterprise_banner/banner')
            ->setName($title)
            ->setIsEnabled(1)
            ->setStoreContents(array(0 => $content))
            ->save()
        ;
        $createdBanners[] = array($banner->getId(), $title, $reference);
    }

    // and static blocks
    $blocks = array(
        array('Flaunt yourself', 'flaunt_yourself', '<img src="{{skin url="images/callouts/home/flaunt_yourself.jpg"}}" alt="Flaunt yourself" />'),
        array('Link to Private Sales Site', 'link_privatesales', '<a href="{{store direct_url="privatesales/"}}"><img src="{{skin url="images/callouts/home/link_private_sales.gif"}}" alt="Private Sales Exclusive Store" /></a>'),
        array('Link to Gift Cards Category', 'link_giftcards', '<a href="{{store direct_url="gift-cards"}}"><img src="{{skin url="images/callouts/home/link_gift_cards.gif"}}" alt="Gift Cards" /></a>'),
        array('Link to Apparel -> Women -> Handbags Category', 'link_apparel_women_handbags', '<a href="{{store direct_url="apparel/women/handbags"}}"><img style="margin-bottom:7px;" src="{{skin url="images/callouts/home/link_handbags.jpg"}}" alt="Handbags" /></a>'),
    );
    $createdBlocks = array();
    foreach ($blocks as $key => $blockData) {
        list($title, $identifier, $content) = $blockData;
        $block = Mage::getModel('cms/block')
            ->setTitle($title)
            ->setIdentifier($identifier)
            ->setContent($content)
            ->setStores(array(0))
            ->save()
        ;
        $createdBlocks[$identifier] = $block->getId();
    }

    $content = '<div class="col2-set">
<div class="col-1">
{{widget type="cms/widget_block" template="cms/widget/static_block/default.phtml" block_id="' . $createdBlocks['flaunt_yourself'] . '"}}
</div>
<div class="col-2">
{{widget type="cms/widget_block" template="cms/widget/static_block/default.phtml" block_id="' . $createdBlocks['link_privatesales'] . '"}}
{{widget type="cms/widget_block" template="cms/widget/static_block/default.phtml" block_id="' . $createdBlocks['link_giftcards'] . '"}}
{{widget type="cms/widget_block" template="cms/widget/static_block/default.phtml" block_id="' . $createdBlocks['link_apparel_women_handbags'] . '"}}
</div>
</div>
    ' . "\n\n\n\n<div style=\"display:none\"><!-- your previous content backup comes below -->\n\n\n " . $page['content'] . "\n\n\n</div>";
    $installer->getConnection()->update($tablePage, array('content' => $content, 'root_template' => 'one_column'), "page_id = {$page['page_id']}");

    // also add widget instances to home page
    foreach ($createdBanners as $i => $b) {
        list($bannerId, $title, $reference) = $b;
        $widgetInstance = Mage::getModel('widget/widget_instance')
            ->setData('page_groups', array(
                array(
                    'page_group' => 'pages',
                    'pages'      => array(
                        'page_id'       => 0,
                        'for'           => 'all',
                        'layout_handle' => 'cms_index_index',
                        'block'         => $reference,
                        'template'      => 'banner/widget.phtml',
                ))
            ))
            ->setData('store_ids', '0')
            ->setData('widget_parameters', array(
                'display_mode' => 'fixed',
                'types'        => array(''),
                'rotate'       => '',
                'banner_ids'   => $bannerId,
            ))
            ->addData(array(
                'type'          => 'enterprise_banner/widget_banner',
                'package_theme' => 'enterprise/default',
                'title'         => $title,
                'sort_order'    => $i,
            ))
            ->save()
        ;
    }
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
