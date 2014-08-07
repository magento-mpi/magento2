<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Catalog/_files/products.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Sales\Model\Quote $quote */
$quote = $objectManager->create('Magento\Sales\Model\Quote');
$quote->setStoreId(
        1
    )->setIsActive(
        false
    )->setIsMultiShipping(
        false
    )->setReservedOrderId(
        'test_order_1'
    )->setEmail(
        'aaa@aaa.com'
    )->addProduct(
        $product->load($product->getId()),
        2
    );
