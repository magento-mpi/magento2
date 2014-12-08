<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $store \Magento\Store\Model\Store */
$store = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Store\Model\Store');
$store->setData(
    [
        'code' => 'test',
        'website_id' => '1',
        'group_id' => '1',
        'name' => 'Test Store',
        'sort_order' => '0',
        'is_active' => '1',
    ]
);
$store->save();
