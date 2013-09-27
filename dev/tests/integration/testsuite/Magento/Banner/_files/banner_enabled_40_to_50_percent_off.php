<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/SalesRule/_files/cart_rule_40_percent_off.php';
require __DIR__ . '/../../../Magento/SalesRule/_files/cart_rule_50_percent_off.php';

/** @var Magento_SalesRule_Model_Rule $ruleFrom */
$ruleFrom = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_SalesRule_Model_Rule');
$ruleFrom->load('40% Off on Large Orders', 'name');

/** @var Magento_SalesRule_Model_Rule $ruleTo */
$ruleTo = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_SalesRule_Model_Rule');
$ruleTo->load('50% Off on Large Orders', 'name');

/** @var Magento_Banner_Model_Banner $banner */
$banner = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Banner_Model_Banner');
$banner->setData(array(
    'name' => 'Get from 40% to 50% Off on Large Orders',
    'is_enabled' => Magento_Banner_Model_Banner::STATUS_ENABLED,
    'types' => array()/*Any Banner Type*/,
    'store_contents' => array('<img src="http://example.com/banner_40_to_50_percent_off.png" />'),
    'banner_sales_rules' => array($ruleFrom->getId(), $ruleTo->getId()),
));
$banner->save();
