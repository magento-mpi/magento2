<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $category \Magento\Catalog\Model\Category */
$objectManager =\Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$categoryFirst = $objectManager->create('Magento\Catalog\Model\Category');
$categoryFirst->setName('Category 1')
    ->setPath('1/2')
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1)
    ->save();

$categorySecond = $objectManager->create('Magento\Catalog\Model\Category');
$categorySecond->setName('Category 2')
    ->setPath('1/2')
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(2)
    ->save();

$categoryThird = $objectManager->create('Magento\Catalog\Model\Category');
$categoryThird->setName('Category 3')
    ->setPath('1/2/' . $categoryFirst->getId())
    ->setLevel(3)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(2)
    ->save();


$categoryFourth = $objectManager->create('Magento\Catalog\Model\Category');
$categoryFourth->setName('Category 4')
    ->setPath('1/2/' . $categorySecond->getId())
    ->setLevel(3)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(2)
    ->save();


/** @var $productFirst \Magento\Catalog\Model\Product */
$productFirst = $objectManager->create('Magento\Catalog\Model\Product');
$productFirst->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(1))
    ->setName('Simple Product 01')
    ->setSku('simple 01')
    ->setPrice(10)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')

    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Status::STATUS_ENABLED)

    ->setStockData(array(
            'use_config_manage_stock' => 0,
        ))
    ->save();

/** @var $productSecond \Magento\Catalog\Model\Product */
$productSecond = $objectManager->create('Magento\Catalog\Model\Product');
$productSecond->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(1))
    ->setName('Simple Product 02')
    ->setSku('simple 02')
    ->setPrice(10)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')

    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Status::STATUS_ENABLED)

    ->setStockData(array(
            'use_config_manage_stock' => 0,
        ))
    ->save();

/** @var $productThird \Magento\Catalog\Model\Product */
$productThird = $objectManager->create('Magento\Catalog\Model\Product');
$productThird->setTypeId('simple')
    ->setAttributeSetId(4)
    ->setWebsiteIds(array(1))
    ->setName('Simple Product 03')
    ->setSku('simple 02')
    ->setPrice(10)
    ->setMetaTitle('meta title')
    ->setMetaKeyword('meta keyword')
    ->setMetaDescription('meta description')

    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Status::STATUS_ENABLED)

    ->setStockData(array(
            'use_config_manage_stock' => 0,
        ))
    ->save();