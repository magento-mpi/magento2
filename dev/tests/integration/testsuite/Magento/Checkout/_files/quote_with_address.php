<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../Customer/_files/customer.php';
require __DIR__ . '/../../Customer/_files/customer_address.php';
require __DIR__ . '/../../../Magento/Catalog/_files/products.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Sales\Model\Quote\Address $quoteShippingAddress */
$quoteShippingAddress = $objectManager->create('Magento\Sales\Model\Quote\Address');

/** @var \Magento\Customer\Service\V1\CustomerAddressServiceInterface $addressService */
$addressService = $objectManager->create('Magento\Customer\Service\V1\CustomerAddressServiceInterface');
$quoteShippingAddress->importCustomerAddressData($addressService->getAddress(1));

/** @var \Magento\Sales\Model\Quote $quote */
$quote = $objectManager->create('Magento\Sales\Model\Quote');
$quote->setStoreId(
    1
)->setIsActive(
    true
)->setIsMultiShipping(
    false
)->assignCustomerWithAddressChange(
    $customer
)->setShippingAddress(
    $quoteShippingAddress
)->setBillingAddress(
    $quoteShippingAddress
)->setCheckoutMethod(
    $customer->getMode()
)->setPasswordHash(
    $customer->encryptPassword($customer->getPassword())
)->setReservedOrderId(
    'test_order_1'
)->setEmail(
    'aaa@aaa.com'
)->addProduct(
    $product->load($product->getId()),
    2
);
