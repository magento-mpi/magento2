<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$quote = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Sales\Model\Quote');
$quote->setData(['store_id' => 1, 'is_active' => 0, 'is_multi_shipping' => 0]);
$quote->save();
