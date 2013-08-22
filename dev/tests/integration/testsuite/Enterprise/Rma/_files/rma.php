<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Rma
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

include(__DIR__ . '/../../../Magento/Sales/_files/order.php');

/** @var $rma Enterprise_Rma_Model_Rma */
$rma = Mage::getModel('Enterprise_Rma_Model_Rma');
$rma->setOrderId($order->getId());
$rma->setIncrementId(1);
$rma->save();

/** @var $trackingNumber Enterprise_Rma_Model_Shipping */
$trackingNumber = Mage::getModel('Enterprise_Rma_Model_Shipping');
$trackingNumber
    ->setRmaEntityId($rma->getId())
    ->setCarrierTitle('CarrierTitle')
    ->setTrackNumber('TrackNumber')
;
$trackingNumber->save();
