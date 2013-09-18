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

/**
 * After installation system has two categories: root one with ID:1 and Default category with ID:2
 */
/** @var $category \Magento\Catalog\Model\Category */
$category = Mage::getModel('Magento\Catalog\Model\Category');
$category->setName('Category Without Image 1')
    ->setParentId(2)
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->save();

/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
$objectManager->get('Magento\Core\Model\Registry')->register('_fixture/Magento\Catalog\Model\Category', $category);
