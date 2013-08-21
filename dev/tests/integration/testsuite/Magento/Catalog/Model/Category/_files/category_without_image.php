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
/** @var $category Magento_Catalog_Model_Category */
$category = Mage::getModel('Magento_Catalog_Model_Category');
$category->setName('Category Without Image 1')
    ->setParentId(2)
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->save();

Mage::register('_fixture/Magento_Catalog_Model_Category', $category);
