<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Mage/SalesRule/_files/cart_rule_40_percent_off.php';

/** @var Mage_SalesRule_Model_Rule $rule */
$rule = Mage::getModel('Mage_SalesRule_Model_Rule');
$rule->load('40% Off on Large Orders', 'name');

/** @var Enterprise_Banner_Model_Banner $banner */
$banner = Mage::getModel('Enterprise_Banner_Model_Banner');
$banner->setData(array(
    'name' => 'Get 40% Off on Large Orders',
    'is_enabled' => Enterprise_Banner_Model_Banner::STATUS_DISABLED,
    'types' => array()/*Any Banner Type*/,
    'store_contents' => array('<img src="http://example.com/banner_40_percent_off.png" />'),
    'banner_sales_rules' => array($rule->getId()),
));
$banner->save();
