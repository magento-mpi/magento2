<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

include(__DIR__ . '/../../../Magento/Sales/_files/order.php');

/** @var $rma Magento_Rma_Model_Rma */
$rma = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Rma_Model_Rma');
$rma->setOrderId($order->getId());
$rma->setIncrementId(1);
$rma->save();

/** @var $trackingNumber Magento_Rma_Model_Shipping */
$trackingNumber = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Rma_Model_Shipping');
$trackingNumber
    ->setRmaEntityId($rma->getId())
    ->setCarrierTitle('CarrierTitle')
    ->setTrackNumber('TrackNumber')
;
$trackingNumber->save();
