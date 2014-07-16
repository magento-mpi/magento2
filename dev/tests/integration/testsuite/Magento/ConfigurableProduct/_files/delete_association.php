<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\Eav\Model\Config $eavConfig */
$eavConfig = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Eav\Model\Config');
$attribute = $eavConfig->getAttribute('catalog_product', 'test_configurable');

/** @var $options \Magento\Eav\Model\Resource\Entity\Attribute\Option\Collection */
$options = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Eav\Model\Resource\Entity\Attribute\Option\Collection'
);
$options->setAttributeFilter($attribute->getId());
$option = $options->getFirstItem();

/** @var $product \Magento\Catalog\Model\Product */
$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
$product->load(1);

$childrenIds = array_values($product->getTypeInstance()->getChildrenIds($product->getId())[0]);
$childrenIds = array_diff($childrenIds, [$option->getId() * 10]);
$product->setAssociatedProductIds($childrenIds);
$product->save();
