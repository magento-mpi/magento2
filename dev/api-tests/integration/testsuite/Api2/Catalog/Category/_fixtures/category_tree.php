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

define('CATEGORIES_IN_TREE', 3);

$fixturesDir = realpath(dirname(__FILE__) . '/../../../../../fixture');

$categoryTree = array();
$parentPath = '1/' . Mage::app()->getDefaultStoreView()->getRootCategoryId();

for ($i = 0; $i < CATEGORIES_IN_TREE; $i++) {
    /* @var $categoryFixture Mage_Catalog_Model_Category */
    $categoryFixture = require $fixturesDir . '/_block/Catalog/Category.php';
    $categoryFixture->setPath($parentPath);
    $categoryFixture->save();

    $parentPath .= '/' . $categoryFixture->getId();
    $categoryTree[] = $categoryFixture;
}

Magento_Test_Webservice::setFixture('category_tree', $categoryTree);
