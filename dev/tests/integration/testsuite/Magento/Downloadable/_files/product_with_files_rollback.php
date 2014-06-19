<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var Magento\Catalog\Model\Product $product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
/** @var \Magento\Downloadable\Model\Product\Type $downloadableType */
$downloadableType = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Downloadable\Model\Product\Type');

$product->load(1);
$downloadableType->deleteTypeSpecificData($product);
$product->save();