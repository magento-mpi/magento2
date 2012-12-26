<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

define('CATEGORIES_IN_TREE', 3);
$categoryTree = array();
$parentPath = '1/' . Mage::app()->getDefaultStoreView()->getRootCategoryId();

for ($i = 0; $i < CATEGORIES_IN_TREE; $i++) {
    /* @var $categoryFixture Mage_Catalog_Model_Category */
    $categoryFixture = require 'API/_fixture/_block/Catalog/Category.php';
    $categoryFixture->setPath($parentPath);
    $categoryFixture->save();

    $parentPath .= '/' . $categoryFixture->getId();
    $categoryTree[] = $categoryFixture;
}

Magento_Test_TestCase_ApiAbstract::setFixture('category_tree', $categoryTree);
