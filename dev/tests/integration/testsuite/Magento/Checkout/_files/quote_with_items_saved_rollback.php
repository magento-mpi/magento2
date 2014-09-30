<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$registry = $objectManager->get('Magento\Framework\Registry');
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);
$quote = $objectManager->create('Magento\Sales\Model\Quote');
$quote->load('test_order_item_with_items', 'reserved_order_id');
$product = $objectManager->create('Magento\Catalog\Model\Product');
foreach ($quote->getAllItems() as $item) {
    $sku = $item->getSku();
    $product->load($product->getIdBySku($sku));
    if ($product->getId()) {
        $product->delete();
    }
};
$quote->delete();
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
