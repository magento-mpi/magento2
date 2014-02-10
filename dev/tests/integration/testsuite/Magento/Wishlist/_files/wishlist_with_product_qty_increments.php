<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Customer/_files/customer.php';
require __DIR__ . '/../../../Magento/Catalog/_files/product_special_price.php';

/** @var \Magento\ObjectManager $objectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Catalog\Model\Product $product */
$product->load(1);
$product->setStockData(array(
    'enable_qty_increments' => 1,
    'qty_increments' => 5,
))->save();

/** @var \Magento\Wishlist\Model\Wishlist $wishlist */
$wishlist = $objectManager->create('Magento\Wishlist\Model\Wishlist');
$wishlist->loadByCustomer($customer->getId(), true);
$wishlist->addNewItem($product);
$wishlist->save();
