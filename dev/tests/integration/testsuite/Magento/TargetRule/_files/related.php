<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

if (!isset($type)) {
    $type = 'related';
}
$applyTo = $type == 'related' ? '1' : '2';

/** @var $rule \Magento\TargetRule\Model\Rule */
$rule = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\TargetRule\Model\Rule');
$data = [
    'name' => $type,
    'is_active' => '1',
    'apply_to' => $applyTo,
    'use_customer_segment' => '0',
    'customer_segment_ids' => ['0' => ''],
];
$rule->loadPost($data);
$rule->save();
