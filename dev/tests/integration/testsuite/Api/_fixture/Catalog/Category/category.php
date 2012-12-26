<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $categoryFixture Mage_Catalog_Model_Category */
$categoryFixture = require 'API/_fixture/_block/Catalog/Category.php';
$defaultWebsite = Mage::app()->getWebsite();
$parentCategory = Mage::getModel('Mage_Catalog_Model_Category')->load(
    $defaultWebsite->getDefaultGroup()->getRootCategoryId()
);
$categoryFixture->setPath($parentCategory->getPath());
$categoryFixture->setStoreId(0);
$categoryFixture->save();
Magento_Test_TestCase_ApiAbstract::setFixture(
    'category',
    $categoryFixture,
    Magento_Test_TestCase_ApiAbstract::AUTO_TEAR_DOWN_DISABLED
);
