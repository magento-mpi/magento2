<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Sales/_files/order.php';

/** @var \Magento\Sales\Model\Order $order */
$order = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Sales\Model\Order');

$order->loadByIncrementId('100000001');
$order->setIncrementId('M00000001');
$order->save();
