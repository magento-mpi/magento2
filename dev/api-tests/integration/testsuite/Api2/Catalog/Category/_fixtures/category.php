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

// Re-init Mage config to have store data in cache
Mage::getConfig()->reinit();
/* @var $categoryFixture Mage_Catalog_Model_Category */
$categoryFixture = require TESTS_FIXTURES_DIRECTORY . '/_block/Catalog/Category.php';
$defaultWebsite = Mage::app()->getWebsite();
$parentCategory = Mage::getModel('Mage_Catalog_Model_Category')->load($defaultWebsite->getDefaultGroup()->getRootCategoryId());
$categoryFixture->setPath($parentCategory->getPath());
$categoryFixture->setStoreId(0);
$categoryFixture->save();
Magento_Test_Webservice::setFixture('category', $categoryFixture, Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);
