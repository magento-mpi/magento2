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

$fixturesDir = realpath(dirname(__FILE__) . '/../../../../../fixture');

/* @var $categoryFixture Mage_Catalog_Model_Category */
$categoryFixture = require $fixturesDir . '/Catalog/Category.php';
$defaultWebsite = Mage::app()->getWebsite();
$parentCategory = Mage::getModel('Mage_Catalog_Model_Category')->load($defaultWebsite->getDefaultGroup()->getRootCategoryId());
$categoryFixture->setPath($parentCategory->getPath());
$categoryFixture->setIsActive(false);
$categoryFixture->save();
Magento_Test_Webservice::setFixture('category_disabled', $categoryFixture);
