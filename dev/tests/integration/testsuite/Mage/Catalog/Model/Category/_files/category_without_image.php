<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

Mage::app()->getStore()->setConfig('dev/log/active', 1);
Mage::app()->getStore()->setConfig('dev/log/exception_file', 'save_category_without_image.log');
Mage::getConfig()->setNode('global/log/core/writer_model',
    'Stub_Mage_Catalog_Model_CategoryTest_Zend_Log_Writer_Stream'
);

/**
 * After installation system has two categories: root one with ID:1 and Default category with ID:2
 */
$category = new Mage_Catalog_Model_Category();
$category->setName('Category Without Image 1')
    ->setParentId(2)
    ->setLevel(2)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->save();

Mage::register('_fixture/Mage_Catalog_Model_Category', $category);
