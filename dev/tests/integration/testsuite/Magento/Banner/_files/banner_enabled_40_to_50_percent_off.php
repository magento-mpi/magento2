<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/SalesRule/_files/cart_rule_40_percent_off.php';
require __DIR__ . '/../../../Magento/SalesRule/_files/cart_rule_50_percent_off.php';

/** @var \Magento\SalesRule\Model\Rule $ruleFrom */
$ruleFrom = Mage::getModel('\Magento\SalesRule\Model\Rule');
$ruleFrom->load('40% Off on Large Orders', 'name');

/** @var \Magento\SalesRule\Model\Rule $ruleTo */
$ruleTo = Mage::getModel('\Magento\SalesRule\Model\Rule');
$ruleTo->load('50% Off on Large Orders', 'name');

/** @var \Magento\Banner\Model\Banner $banner */
$banner = Mage::getModel('\Magento\Banner\Model\Banner');
$banner->setData(array(
    'name' => 'Get from 40% to 50% Off on Large Orders',
    'is_enabled' => \Magento\Banner\Model\Banner::STATUS_ENABLED,
    'types' => array()/*Any Banner Type*/,
    'store_contents' => array('<img src="http://example.com/banner_40_to_50_percent_off.png" />'),
    'banner_sales_rules' => array($ruleFrom->getId(), $ruleTo->getId()),
));
$banner->save();
