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

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var $category \Magento\Catalog\Model\Category */
$category = $objectManager->create('Magento\Catalog\Model\Category');
$category->setId(
    3
)->setName(
    'Category 1'
)->setParentId(
    2
)->setPath(
    '1/2/3'
)->setLevel(
    2
)->setAvailableSortBy(
    'name'
)->setDefaultSortBy(
    'name'
)->setIsActive(
    true
)->setPosition(
    1
)->save();

$product = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Product');
$product->setTypeId(
    \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
)->setId(
    1
)->setAttributeSetId(
    4
)->setWebsiteIds(
    array(1)
)->setName(
    'Simple Product'
)->setSku(
    'simple'
)->setPrice(
    10
)->setCategoryIds(
    array(3)
)->setVisibility(
    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
)->setStatus(
    \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
)->save();

$category = $objectManager->create('Magento\Catalog\Model\Category');
$category->setId(
    4
)->setName(
    'Category 2'
)->setParentId(
    2
)->setPath(
    '1/2/4'
)->setLevel(
    2
)->setAvailableSortBy(
    'name'
)->setDefaultSortBy(
    'name'
)->setIsActive(
    true
)->setPosition(
    2
)->save();

$category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Category');
$category->setId(
    5
)->setName(
    'Old Root'
)->setParentId(
    1
)->setPath(
    '1/5'
)->setLevel(
    1
)->setAvailableSortBy(
    'name'
)->setDefaultSortBy(
    'name'
)->setIsActive(
    true
)->setPosition(
    3
)->save();
