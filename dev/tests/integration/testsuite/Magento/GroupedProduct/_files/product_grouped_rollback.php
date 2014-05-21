<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

\Magento\TestFramework\Helper\Bootstrap::getInstance()
    ->loadArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);

/** @var $simpleProduct \Magento\Catalog\Model\Product */
$simpleProduct = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
$simpleProduct->load(2);
$simpleProduct->delete();

/** @var $virtualProduct \Magento\Catalog\Model\Product */
$virtualProduct = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
$virtualProduct->load(21);
$virtualProduct->delete();

/** @var $groupedProduct \Magento\Catalog\Model\Product */
$groupedProduct = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
$groupedProduct->load(9);
$groupedProduct->delete();
