<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/SalesRule/_files/cart_rule_50_percent_off.php';
require __DIR__ . '/../../../Magento/CustomerSegment/_files/segment_developers.php';

/** @var Magento_SalesRule_Model_Rule $rule */
$rule = Mage::getModel('Magento_SalesRule_Model_Rule');
$rule->load('50% Off on Large Orders', 'name');

/** @var $segment Magento_CustomerSegment_Model_Segment */
$segment = Mage::getModel('Magento_CustomerSegment_Model_Segment');
$segment->load('Developers', 'name');

/** @var Magento_Banner_Model_Banner $banner */
$banner = Mage::getModel('Magento_Banner_Model_Banner');
$banner->setData(array(
    'name' => 'Get 50% Off on Development IDEs',
    'is_enabled' => Magento_Banner_Model_Banner::STATUS_ENABLED,
    'types' => array()/*Any Banner Type*/,
    'store_contents' => array('<img src="http://example.com/banner_50_percent_off_on_ide.png" />'),
    'banner_sales_rules' => array($rule->getId()),
    'customer_segment_ids' => array($segment->getId()),
));
$banner->save();
