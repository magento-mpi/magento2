<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\Framework\Registry $registry */
$registry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\Registry');
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var \Magento\Sales\Model\Order $order */
$order = $objectManager->create('Magento\Sales\Model\Order');
$order->loadByIncrementId('100000001')
    ->delete();
$creditmemo = $objectManager->create('Magento\Sales\Model\Order\Creditmemo');
$creditmemo->load('100000001', 'increment_id')
    ->delete();

$product = $objectManager->create('Magento\Catalog\Model\Product');
$product->load(1)->delete();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
