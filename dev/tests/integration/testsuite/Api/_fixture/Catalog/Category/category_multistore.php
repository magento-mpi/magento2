<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $categoryFixture Mage_Catalog_Model_Category */
$categoryFixture = require '_fixture/_block/Catalog/Category.php';
$defaultWebsite = Mage::app()->getWebsite();
$parentCategory = Mage::getModel('Mage_Catalog_Model_Category')->load(
    $defaultWebsite->getDefaultGroup()->getRootCategoryId()
);
$categoryFixture->setPath($parentCategory->getPath());
$categoryFixture->setStoreId(0);
$categoryFixture->save();

// create new store fixture
require '_fixture/Core/Store/store.php';
/** @var $storeFixture Mage_Core_Model_Store */
$storeFixture = PHPUnit_Framework_TestCase::getFixture('store');
$categoryDataOnStore = require '_fixture/_data/Catalog/Category/category_store_data.php';
$categoryFixture->setStoreId($storeFixture->getId())->addData($categoryDataOnStore)->save();

PHPUnit_Framework_TestCase::setFixture(
    'category',
    $categoryFixture,
    PHPUnit_Framework_TestCase::AUTO_TEAR_DOWN_DISABLED
);
