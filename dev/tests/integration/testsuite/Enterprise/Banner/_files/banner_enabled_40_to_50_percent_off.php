<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Mage/SalesRule/_files/cart_rule_40_percent_off.php';
require __DIR__ . '/../../../Mage/SalesRule/_files/cart_rule_50_percent_off.php';

/** @var Mage_SalesRule_Model_Rule $ruleFrom */
$ruleFrom = Mage::getModel('Mage_SalesRule_Model_Rule');
$ruleFrom->load('40% Off on Large Orders', 'name');

/** @var Mage_SalesRule_Model_Rule $ruleTo */
$ruleTo = Mage::getModel('Mage_SalesRule_Model_Rule');
$ruleTo->load('50% Off on Large Orders', 'name');

/** @var Enterprise_Banner_Model_Banner $banner */
$banner = Mage::getModel('Enterprise_Banner_Model_Banner');
$banner->setData(array(
    'name' => 'Get from 40% to 50% Off on Large Orders',
    'is_enabled' => 1,
    'types' => array()/*Any Banner Type*/,
    'store_contents' => array('<img src="http://example.com/banner_40_to_50_percent_off.png" />'),
    'banner_sales_rules' => array($ruleFrom->getId(), $ruleTo->getId()),
));
$banner->save();
