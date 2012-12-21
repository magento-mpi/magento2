<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $categoryFixture Mage_Catalog_Model_Category */
$categoryFixture = require 'API/fixture/_block/Catalog/Category.php';
$defaultWebsite = Mage::app()->getWebsite();
$parentCategory = Mage::getModel('Mage_Catalog_Model_Category')->load($defaultWebsite->getDefaultGroup()->getRootCategoryId());
$categoryFixture->setPath($parentCategory->getPath());
$categoryFixture->setStoreId(0);
$categoryFixture->save();

// create new store fixture
require 'API/fixture/Core/Store/store.php';
/** @var $storeFixture Mage_Core_Model_Store */
$storeFixture = Magento_Test_Webservice::getFixture('store');
$categoryDataOnStore = require 'API/fixture/_data/Catalog/Category/category_store_data.php';
$categoryFixture->setStoreId($storeFixture->getId())->addData($categoryDataOnStore)->save();

Magento_Test_Webservice::setFixture('category', $categoryFixture, Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);
