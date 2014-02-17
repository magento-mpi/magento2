<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')
    ->loadAreaPart(
        \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
        \Magento\Core\Model\App\Area::PART_CONFIG
    );
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')
    ->setAreaCode(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);

/** @var $category \Magento\Catalog\Model\Category */
$category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Catalog\Model\Category');
$category->load(9);
$category->delete();

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Catalog\Model\Product');
$product->load(1);
$product->delete();

$product->load(2);
$product->delete();
