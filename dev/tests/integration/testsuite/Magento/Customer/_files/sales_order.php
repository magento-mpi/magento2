<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\Customer\Model\Customer $customer */
$customer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Customer\Model\Customer')
    ->load(1);

/** @var \Magento\Sales\Model\Order $order */
$order = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Sales\Model\Order')
    ->loadByIncrementId('100000001');
$order->setCustomerIsGuest(false)
    ->setCustomerId($customer->getId())
    ->setCustomerEmail($customer->getEmail());
$order->save();

