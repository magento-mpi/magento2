<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

include __DIR__ . '/order.php';
include __DIR__ . '/../../../Magento/Customer/_files/customer.php';

$customerIdFromFixture = 1;
/** @var $order \Magento\Sales\Model\Order */
$order->setCustomerId($customerIdFromFixture)->setCustomerIsGuest(false)->save();
