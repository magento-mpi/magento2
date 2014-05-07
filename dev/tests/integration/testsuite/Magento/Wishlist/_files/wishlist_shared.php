<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Customer/_files/customer.php';
require __DIR__ . '/../../../Magento/Catalog/_files/product_simple.php';

/* @var $wishlist \Magento\Wishlist\Model\Wishlist */
$wishlist = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Wishlist\Model\Wishlist');
$wishlist->loadByCustomerId($customer->getId(), true);

$item = $wishlist->addNewItem(
    $product,
    new \Magento\Framework\Object(
        [
            'options' => [
                1 => '1-text',
                2 => ['month' => 1, 'day' => 1, 'year' => 2001, 'hour' => 1, 'minute' => 1],
                3 => '1',
                4 => '1',
            ]
        ])
);

$wishlist->setSharingCode('fixture_unique_code')
    ->setShared(1)
    ->save();
