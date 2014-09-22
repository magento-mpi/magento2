<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

include __DIR__ . '/quote.php';
include __DIR__ . '/../../../Magento/Customer/_files/customer.php';

/** @var $quote \Magento\Sales\Model\Quote */
$quote = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Sales\Model\Quote');
$quote->load('test01', 'reserved_order_id');
/** @var $customer \Magento\Customer\Model\Customer */
$customer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Customer\Model\Customer');
$customer->load(1);
$quote->setCustomer($customer)->setCustomerIsGuest(false)->save();
