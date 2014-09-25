<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

include __DIR__ . '/../../../Magento/Sales/_files/order.php';

/** @var $rma \Magento\Rma\Model\Rma */
$rma = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Rma\Model\Rma');
$rma->setOrderId($order->getId());
$rma->setIncrementId(1);
$rma->save();

$history = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Rma\Model\Rma\Status\History');
$history->setRma($rma);
$history->saveComment('Test comment', true, true);

/** @var $trackingNumber \Magento\Rma\Model\Shipping */
$trackingNumber = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Rma\Model\Shipping');
$trackingNumber->setRmaEntityId($rma->getId())->setCarrierTitle('CarrierTitle')->setTrackNumber('TrackNumber');
$trackingNumber->save();
