<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define('WEBSITES_COUNT_TEST_WEBSITES', 2);
define('WEBSITES_COUNT_TEST_STORES', 2);

$fixturesDir = realpath(dirname(__FILE__) . '/../../../../../../fixtures');

// Product (MUST be created before created not assigned Websites)
/* @var $productFixture Mage_Catalog_Model_Product */
$product = require $fixturesDir . '/Catalog/Product.php';
$product->save(); // the save method MUST be called till setWebsiteIds

// Assigned Websites
$websitesAssignedToProduct = array();
$categories = array();
$storeGroups = array();
$stores = array();
for ($i = 0; $i < WEBSITES_COUNT_TEST_WEBSITES; $i++) {
    /* @var $websiteAssignedToProduct Mage_Core_Model_Website */
    $websiteAssignedToProduct = require $fixturesDir . '/Core/Website.php';
    $websiteAssignedToProduct->save();
    $websitesAssignedToProduct[] = $websiteAssignedToProduct;
    $websiteAssignedToProductIds[] = $websiteAssignedToProduct->getId();

    // Category
    /* @var $category Mage_Catalog_Model_Category */
    $category = require $fixturesDir . '/Catalog/Category.php';
    $category->save();
    $categories[] = $category;

    // Store Group
    /* @var $storeGroup Mage_Core_Model_Store_Group */
    $storeGroup = require $fixturesDir . '/Core/Store/Group.php';
    $storeGroup->addData(array(
        'website_id' => $websiteAssignedToProduct->getId(),
        'root_category_id' => $category->getId()
    ));
    $storeGroup->save();
    $storeGroups[] = $storeGroup;

    // Stores
    for ($j = 0; $j < WEBSITES_COUNT_TEST_STORES; $j++) {
        /* @var $store Mage_Core_Model_Store */
        $store = require $fixturesDir . '/Core/Store.php';
        $store->addData(array(
            'group_id' => $storeGroup->getId(),
            'website_id' => $websiteAssignedToProduct->getId()
        ));
        $store->save();
        $stores[] = $store;
    }
}
$product->setWebsiteIds($websiteAssignedToProductIds);
$product->save();

// it is needed for testing detalization of "Store Data Copying"
Mage::getModel('catalog/product')
    ->load($product->getId())
    ->setStoreId($stores[0])
    ->addData(array(
        'name' => 'Product Store Title' . uniqid()
    ))
    ->save();

// Not assigned Websites
$websitesNotAssignedToProduct = array();
for ($i = 0; $i < WEBSITES_COUNT_TEST_WEBSITES; $i++) {
    /* @var $websiteNotAssignedToProduct Mage_Core_Model_Website */
    $websiteNotAssignedToProduct = require $fixturesDir . '/Core/Website.php';
    $websiteNotAssignedToProduct->save();
    $websitesNotAssignedToProduct[] = $websiteNotAssignedToProduct;

    // Category
    /* @var $category Mage_Catalog_Model_Category */
    $category = require $fixturesDir . '/Catalog/Category.php';
    $category->save();
    $categories[] = $category;

    // Store Group
    /* @var $storeGroup Mage_Core_Model_Store_Group */
    $storeGroup = require $fixturesDir . '/Core/Store/Group.php';
    $storeGroup->addData(array(
        'website_id' => $websiteNotAssignedToProduct->getId(),
        'root_category_id' => $category->getId()
    ));
    $storeGroup->save();
    $storeGroups[] = $storeGroup;

    // Stores
    for ($j = 0; $j < WEBSITES_COUNT_TEST_STORES; $j++) {
        /* @var $store Mage_Core_Model_Store */
        $store = require $fixturesDir . '/Core/Store.php';
        $store->addData(array(
            'group_id' => $storeGroup->getId(),
            'website_id' => $websiteNotAssignedToProduct->getId()
        ));
        $store->save();
        $stores[] = $store;
    }
}

Magento_Test_Webservice::setFixture('product', $product);
Magento_Test_Webservice::setFixture('websitesAssignedToProduct', $websitesAssignedToProduct);
Magento_Test_Webservice::setFixture('websitesNotAssignedToProduct', $websitesNotAssignedToProduct);
Magento_Test_Webservice::setFixture('categories', $categories);
Magento_Test_Webservice::setFixture('storeGroups', $storeGroups);
Magento_Test_Webservice::setFixture('stores', $stores);
