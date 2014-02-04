<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
$categoryFirst = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Catalog\Model\Category');
$categoryFirst->setName(uniqid('Category First '))
    ->setParentId(2)
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1)
    ->save();
$categoryId =  $categoryFirst->getId();
$categoryFirst->setPath('1/2/' . $categoryId)->save();

$category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Catalog\Model\Category');
$category->setName(uniqid('Category First First '))
    ->setParentId($categoryFirst->getId())
    ->setLevel(3)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1)
    ->save();
$category->setPath($categoryFirst->getPath() . '/' . $category->getId())->save();


$categorySecond = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Catalog\Model\Category');
$categorySecond->setName(uniqid('Category Second '))
    ->setParentId(2)
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(2)
    ->save();
$categoryId =  $categorySecond->getId();
$categorySecond->setPath('1/2/' . $categoryId)->save();

$category = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Catalog\Model\Category');
$category->setName(uniqid('Category Second Second '))
    ->setParentId($categorySecond->getId())
    ->setLevel(3)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1)
    ->save();
$category->setPath($categorySecond->getPath() . '/' . $category->getId())->save();