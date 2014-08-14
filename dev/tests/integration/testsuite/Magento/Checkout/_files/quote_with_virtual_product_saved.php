<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Catalog/_files/product_virtual.php';

/** @var \Magento\Sales\Model\Quote $quote */
$quote = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Sales\Model\Quote');
$quote->setStoreId(1)
    ->setIsActive(true)
    ->setIsMultiShipping(false)
    ->setReservedOrderId('test_order_with_virtual_product_without_address')
    ->setEmail('store@example.com')
    ->addProduct(
        $product->load($product->getId()),
        1
    );

$quote->collectTotals()->save();
