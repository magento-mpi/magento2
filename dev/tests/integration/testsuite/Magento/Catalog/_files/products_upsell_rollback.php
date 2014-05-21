<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
\Magento\TestFramework\Helper\Bootstrap::getInstance()
    ->loadArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);

/** @var $upSellProduct \Magento\Catalog\Model\Product */
$upSellProduct = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
$upSellProduct->load(1);
$upSellProduct->delete();

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
$product->load(2);
$product->delete();
