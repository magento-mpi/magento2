<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/SalesRule/_files/cart_rule_40_percent_off.php';
require __DIR__ . '/../../../Magento/CustomerSegment/_files/segment_designers.php';

/** @var \Magento\SalesRule\Model\Rule $rule */
$rule = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\SalesRule\Model\Rule');
$rule->load('40% Off on Large Orders', 'name');

/** @var $segment \Magento\CustomerSegment\Model\Segment */
$segment = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\CustomerSegment\Model\Segment'
);
$segment->load('Designers', 'name');

/** @var \Magento\Banner\Model\Banner $banner */
$banner = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Banner\Model\Banner');
$banner->setData(
    array(
        'name' => 'Get 40% Off on Graphic Editors',
        'is_enabled' => \Magento\Banner\Model\Banner::STATUS_ENABLED,
        'types' => array(),
        'store_contents' => array('<img src="http://example.com/banner_40_percent_off_on_graphic_editor.png" />'),
        'banner_sales_rules' => array($rule->getId()),
        'customer_segment_ids' => array($segment->getId())
    )
);
$banner->save();
