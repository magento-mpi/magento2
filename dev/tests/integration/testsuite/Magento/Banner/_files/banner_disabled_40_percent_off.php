<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/SalesRule/_files/cart_rule_40_percent_off.php';

/** @var \Magento\SalesRule\Model\Rule $rule */
$rule = Mage::getModel('Magento\SalesRule\Model\Rule');
$rule->load('40% Off on Large Orders', 'name');

/** @var \Magento\Banner\Model\Banner $banner */
$banner = Mage::getModel('Magento\Banner\Model\Banner');
$banner->setData(array(
    'name' => 'Get 40% Off on Large Orders',
    'is_enabled' => \Magento\Banner\Model\Banner::STATUS_DISABLED,
    'types' => array()/*Any Banner Type*/,
    'store_contents' => array('<img src="http://example.com/banner_40_percent_off.png" />'),
    'banner_sales_rules' => array($rule->getId()),
));
$banner->save();
