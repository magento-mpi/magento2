<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

require __DIR__ . '/../../../Magento/SalesRule/_files/cart_rule_40_percent_off.php';
require __DIR__ . '/../../../Magento/SalesRule/_files/cart_rule_50_percent_off.php';

/** @var \Magento\SalesRule\Model\Rule $ruleFrom */
$ruleFrom = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\SalesRule\Model\Rule');
$ruleFrom->load('40% Off on Large Orders', 'name');

/** @var \Magento\SalesRule\Model\Rule $ruleTo */
$ruleTo = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\SalesRule\Model\Rule');
$ruleTo->load('50% Off on Large Orders', 'name');

/** @var \Magento\Banner\Model\Banner $banner */
$banner = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Banner\Model\Banner');
$banner->setData(
    [
        'name' => 'Get from 40% to 50% Off on Large Orders',
        'is_enabled' => \Magento\Banner\Model\Banner::STATUS_ENABLED,
        'types' => [], /*Any Banner Type*/
        'store_contents' => ['<img src="http://example.com/banner_40_to_50_percent_off.png" />'],
        'banner_sales_rules' => [$ruleFrom->getId(), $ruleTo->getId()],
    ]
);
$banner->save();
