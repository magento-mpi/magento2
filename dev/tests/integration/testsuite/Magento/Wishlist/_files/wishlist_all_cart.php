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

/** @var \Magento\ObjectManager $objectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Catalog\Model\Product $product */
$product = $objectManager->create('Magento\Catalog\Model\Product');
$product->setTypeId('simple')
    ->setId(1)
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(1))
    ->setName('Simple Product')
    ->setSku('simple')
    ->setPrice(10)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Status::STATUS_ENABLED)
    ->setStockData(array(
        'use_config_manage_stock'   => 0,
        'use_config_enable_qty_inc' => 1,
    ))->save();

/** @var \Magento\Wishlist\Model\Wishlist $wishlist */
$wishlist = $objectManager->create('Magento\Wishlist\Model\Wishlist');
$wishlist->loadByCustomer($customer->getId(), true);
$item = $wishlist->addNewItem($product);
$wishlist->save();

/** @var \Magento\Core\Model\Registry $registry */
$registry = $objectManager->get('Magento\Core\Model\Registry');
$registry->register('wishlist', $wishlist);
