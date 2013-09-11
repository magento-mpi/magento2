<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/SalesRule/_files/cart_rule_50_percent_off.php';
require __DIR__ . '/../../../Magento/CustomerSegment/_files/segment_developers.php';

/** @var \Magento\SalesRule\Model\Rule $rule */
$rule = Mage::getModel('\Magento\SalesRule\Model\Rule');
$rule->load('50% Off on Large Orders', 'name');

/** @var $segment \Magento\CustomerSegment\Model\Segment */
$segment = Mage::getModel('\Magento\CustomerSegment\Model\Segment');
$segment->load('Developers', 'name');

/** @var \Magento\Banner\Model\Banner $banner */
$banner = Mage::getModel('\Magento\Banner\Model\Banner');
$banner->setData(array(
    'name' => 'Get 50% Off on Development IDEs',
    'is_enabled' => \Magento\Banner\Model\Banner::STATUS_ENABLED,
    'types' => array()/*Any Banner Type*/,
    'store_contents' => array('<img src="http://example.com/banner_50_percent_off_on_ide.png" />'),
    'banner_sales_rules' => array($rule->getId()),
    'customer_segment_ids' => array($segment->getId()),
));
$banner->save();
