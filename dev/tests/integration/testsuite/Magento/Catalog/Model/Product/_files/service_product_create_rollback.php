<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
use Magento\TestFramework\Helper\Bootstrap;

/** @var \Magento\Framework\Registry $registry */
$registry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\Registry');
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var \Magento\Catalog\Model\Product $productModel */
$productModel = Bootstrap::getObjectManager()->get('Magento\Catalog\Model\Product');
$productModel->load($productModel->getIdBySku('psku-test-1'));
if ($productModel->getId()) {
    $productModel->delete();
}

/** @var \Magento\Catalog\Model\Product $productModel */
$productModel = Bootstrap::getObjectManager()->get('Magento\Catalog\Model\Product');
$productModel->load($productModel->getIdBySku('psku-test-2'));
if ($productModel->getId()) {
    $productModel->delete();
}
