<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/product_image.php';
require __DIR__ . '/product_simple.php';

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
$product->load(1)
    ->setStoreId(0)
    ->setImage('/m/a/magento_image.jpg')
    ->setSmallImage('/m/a/magento_image.jpg')
    ->setThumbnail('/m/a/magento_image.jpg')
    ->setData('media_gallery', array('images' => array(
        array(
            'file' => '/m/a/magento_image.jpg',
            'position' => 1,
            'label' => 'Image Alt Text',
            'disabled' => 0,
        ),
    )))->save();
