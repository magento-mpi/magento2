<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

$data = ['name' => 'Customer Segment 1', 'website_ids' => [1], 'is_active' => '1'];
/** @var $segment \Magento\CustomerSegment\Model\Segment */
$segment = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\CustomerSegment\Model\Segment'
);
$segment->loadPost($data);
$segment->save();
