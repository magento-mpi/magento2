<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

\Magento\TestFramework\Helper\Bootstrap::getInstance()
    ->loadArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);

/** @var $relatedProduct \Magento\Catalog\Model\Product */
$relatedProduct = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
$relatedProduct->load(1);
$relatedProduct->delete();

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
$product->load(2);
$product->delete();
