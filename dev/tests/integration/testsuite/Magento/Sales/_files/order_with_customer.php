<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

include (__DIR__ . '/order.php');
include (__DIR__ . '/../../../Magento/Customer/_files/customer.php');

$order->setCustomerId(1)
    ->setCustomerIsGuest(false);
$order->save();
