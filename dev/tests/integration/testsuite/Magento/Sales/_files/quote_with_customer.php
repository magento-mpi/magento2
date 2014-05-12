<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

include __DIR__ . '/quote.php';
include __DIR__ . '/../../../Magento/Customer/_files/customer.php';

$customerIdFromFixture = 1;
$quote->setCustomerId($customerIdFromFixture)->setCustomerIsGuest(false)->save();
