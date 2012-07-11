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
 * @category    Magento
 * @package     Mage_Category
 * @subpackage  functional_tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Category Permissions tests
 *
 * @package     Mage_Category
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_Category_CategoryPermissions_ConfigLevelTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->logoutCustomer();
        $this->loginAdminUser();
        $this->navigate('system_configuration');
    }

    protected function tearDownAfterTestClass()
    {
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_disable');
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    /**
     * <p>Category Permissions Options block</p>
     * <p>Steps:</p>
     * <p>1. Go to System -> Configuration -> Catalog -> Catalog -> Category Permissions</p>
     * <p>2. Verify that all fields are present</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5078
     */
    public function navigationTest()
    {
        $this->clickControl('tab', 'catalog_catalog', false);
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->assertTrue($this->controlIsPresent('dropdown', 'permission_enable'),
                          'There is no "permission_enable" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'allow_browsing'),
                          'There is no "allow_browsing" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'landing_page'),
                          'There is no "landing_page" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'display_prices'),
                          'There is no "display_prices" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'allow_adding_to_cart'),
                          'There is no "allow_adding_to_cart" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('multiselect', 'disallow_catalog_search'),
                          'There is no "disallow_catalog_search" multiselect on the page');
        $this->assertTrue($this->controlIsPresent('multiselect', 'allow_browsing_customer_groups'),
                          'There is no "allow_browsing_customer_groups" multiselect on the page');
        $this->assertTrue($this->controlIsPresent('multiselect', 'display_prices_customer_groups'),
                          'There is no "display_prices_customer_groups" multiselect on the page');
        $this->assertTrue($this->controlIsPresent('multiselect', 'allow_adding_to_cart_customer_groups'),
                          'There is no "allow_adding_to_cart_customer_groups" multiselect on the page');
    }

    /**
     * Create category, customer and product
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $category = $this->loadDataSet('Category', 'sub_category_required');
        $catPath = $category['parent_category'] . '/' . $category['name'];
        $productCat = array('categories' => $catPath);
        $simple = $this->loadDataSet('Product', 'simple_product_visible', $productCat);
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_categories');
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        return array('user'   => array('email' => $userData['email'], 'password' => $userData['password']),
                     'product'=> array('name' => $simple['general_name'], 'price' =>$simple['prices_price']),
                     'catName'=> $category['name']);
    }

    /**
     * <p>Allow Adding to Cart set up "No"</p>
     * <p>Steps</p>
     * <p>1.Go to System -> Configuration -> Catalog -> Catalog -> Category Permissions</p>
     * <p>2.Select "Yes" in Enable field</p>
     * <p>3.Select "Yes, for Everyone" in Allow Browsing Category field</p>
     * <p>4.Select "Yes, for Everyone" in Display Product Prices</p>
     * <p>5.Select "No" in Allow Adding to Cart field</p>
     * <p>6.Click "Save Config" button</p>
     * <p>7.Clear Magento Cache </p>
     * <p>8.Open Fronend</p>
     * <p>9.Open any category</p>
     * <p>10. Open product page</p>
     * <p>Expected result:</p>
     * <p>1. After 9 Category page is open. Product prices is visible. "Add to cart" button is missing</p>
     * <p>2. After 10 Product page is open. Product prices is visible. "Add to cart" button is missing</p>
     *
     * @param array $testData
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5018
     */
    public function allowAddingToCartIsNo($testData)
    {
        //Data
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_enable',
                                      array ('allow_adding_to_cart' => 'No'));
        $this->addParameter('productName', $testData['product']['name']);
        $this->addParameter('price', '$' . $testData['product']['price']);
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($testData['catName']);
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
        $this->assertTrue($this->controlIsPresent('pageelement', 'price_regular'), 'Product price must be present');
        $this->productHelper()->frontOpenProduct($testData['product']['name']);
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
        $this->assertTrue($this->controlIsPresent('pageelement', 'price_regular'), 'Product price must be present');
    }

    /**
     * <p>Allow Adding to Cart set up "Yes, for Specified Customer Groups"</p>
     * <p>Steps</p>
     * <p>1.Go to System -> Configuration -> Catalog -> Catalog -> Category Permissions</p>
     * <p>2.Select "Yes" in Enable field</p>
     * <p>3.Select "Yes, for Everyone" in Allow Browsing Category field</p>
     * <p>4.Select "Yes, for Everyone" in Display Product Prices</p>
     * <p>5.Select "Yes, for Specified Customer Groups" in Allow Adding to Cart field</p>
     * <p>6.Select "General" in Customer Groups list</p>
     * <p>7.Click "Save Config" button </p>
     * <p>8.Clear Magento Cache </p>
     * <p>9.Open Frontend as a guest</p>
     * <p>10.Open any category</p>
     * <p>11.Open product page</p>
     * <p>12.Login to frontend</p>
     * <p>13.Open any category</p>
     * <p>14.Open product page</p>
     * <p>Expected result:</p>
     * <p>1. After 10 Category page is open. Product prices is visible. "Add to cart" button is missing</p>
     * <p>2. After 11 Product page is open. Product prices is visible. "Add to cart" button is missing</p>
     * <p>3. After 13 Category page is open. Product prices is visible. "Add to cart" button is available</p>
     * <p>4. After 14 Product page is open. Product prices is visible. "Add to cart" button is available</p>
     *
     * @param array $testData
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5016
     */
    public function allowAddingToCartForSpecifiedCustomer($testData)
    {
        //Data
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_enable',
                                     array ('allow_adding_to_cart' => 'Yes, for Specified Customer Groups',
                                            'allow_adding_to_cart_customer_groups' => 'General'));
        $this->addParameter('productName', $testData['product']['name']);
        $this->addParameter('price', '$' . $testData['product']['price']);
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($testData['catName']);
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
        $this->assertTrue($this->controlIsPresent('pageelement', 'price_regular'), 'Product price must be present');
        $this->productHelper()->frontOpenProduct($testData['product']['name']);
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
        $this->assertTrue($this->controlIsPresent('pageelement', 'price_regular'), 'Product price must be present');
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->categoryHelper()->frontOpenCategory($testData['catName']);
        $this->assertTrue($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" must be present');
        $this->assertTrue($this->controlIsPresent('pageelement', 'price_regular'), 'Product price must be present');
        $this->productHelper()->frontOpenProduct($testData['product']['name']);
        $this->assertTrue($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" must be present');
        $this->assertTrue($this->controlIsPresent('pageelement', 'price_regular'), 'Product price must be present');
    }

    /**
     * <p>Allow Adding to Cart set up "Yes, for Specified Customer Groups"</p>
     * <p>Steps</p>
     * <p>1.Go to System -> Configuration -> Catalog -> Catalog -> Category Permissions</p>
     * <p>2.Select "Yes" in Enable field</p>
     * <p>3.Select "Yes, for Everyone" in Allow Browsing Category field</p>
     * <p>4.Select "No" in Display Product Prices</p>
     * <p>5.Click "Save Config" button</p>
     * <p>6.Clear Magento Cache </p>
     * <p>7.Open Frontend</p>
     * <p>8.Open any category</p>
     * <p>9.Open product page</p>
     * <p>Expected result:</p>
     * <p>1. After 8 Category page is open. Product prices is not visible. "Add to cart" button is missing</p>
     * <p>2. After 9 Product page is open. Product prices is not visible. "Add to cart" button is missing</p>
     *
     * @param array $testData
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5170
     */
    public function displayProductPricesIsNo($testData)
    {
        //Data
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_enable',
                                     array ('display_prices' => 'No'));
        $this->addParameter('productName', $testData['product']['name']);
        $this->addParameter('price', '$' . $testData['product']['price']);
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($testData['catName']);
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
        $this->assertFalse($this->controlIsPresent('pageelement', 'price_regular'), 'Product price should be absent');
        $this->productHelper()->frontOpenProduct($testData['product']['name']);
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
        $this->assertFalse($this->controlIsPresent('pageelement', 'price_regular'), 'Product price should be absent');
    }

    /**
     * <p>Allow Adding to Cart set up "Yes, for Specified Customer Groups"</p>
     * <p>Steps</p>
     * <p>1.Go to System -> Configuration -> Catalog -> Catalog -> Category Permissions</p>
     * <p>2.Select "Yes" in Enable field</p>
     * <p>3.Select "Yes, for Everyone" in Allow Browsing Category field</p>
     * <p>4.Select "Yes, for Specified Customer Groups" in Display Product Prices</p>
     * <p>5.Select "General" in Customer Groups list</p>
     * <p>6.Click "Save Config" button</p>
     * <p>7.Clear Magento Cache </p>
     * <p>8.Open Frontend as a guest</p>
     * <p>9.Open any category</p>
     * <p>10.Open product page</p>
     * <p>11.Login to frontend</p>
     * <p>12.Open any category</p>
     * <p>13.Open product page</p>
     * <p>Expected result:</p>
     * <p>1. After 9 Category page is open. Product prices is not visible. "Add to cart" button is missing</p>
     * <p>2. After 10 Product page is open. Product prices is not visible. "Add to cart" button is missing</p>
     * <p>3. After 12 Category page is open. Product prices is visible. "Add to cart" button is available</p>
     * <p>4. After 13 Product page is open. Product prices is visible. "Add to cart" button is available</p>
     *
     * @param array $testData
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5010
     */
    public function displayProductPricesForSpecifiedCustomer($testData)
    {

        //Data
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_enable',
                                     array ('display_prices' => 'Yes, for Specified Customer Groups',
                                            'display_prices_customer_groups' => 'General'));
        $this->addParameter('productName', $testData['product']['name']);
        $this->addParameter('price', '$' . $testData['product']['price']);
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($testData['catName']);
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
        $this->assertFalse($this->controlIsPresent('pageelement', 'price_regular'), 'Product price should be absent');
        $this->productHelper()->frontOpenProduct($testData['product']['name']);
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
        $this->assertFalse($this->controlIsPresent('pageelement', 'price_regular'), 'Product price should be absent');
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->categoryHelper()->frontOpenCategory($testData['catName']);
        $this->assertTrue($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" must be present');
        $this->assertTrue($this->controlIsPresent('pageelement', 'price_regular'), 'Product price must be present');
        $this->productHelper()->frontOpenProduct($testData['product']['name']);
        $this->assertTrue($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" must be present');
        $this->assertTrue($this->controlIsPresent('pageelement', 'price_regular'), 'Product price must be present');
    }

    /**
     * <p>Allow Browsing Category set up "No, Redirect to Landing Page"</p>
     * <p>Steps</p>
     * <p>1.Go to System -> Configuration -> Catalog -> Catalog -> Category Permissions</p>
     * <p>2.Select "Yes" in Enable field</p>
     * <p>3.Select "No, Redirect to Landing Page" in Allow Browsing Category field</p>
     * <p>4.Select "No" in Display Product Prices</p>
     * <p>5.Select "About Us" in Landing Page field</p>
     * <p>6.Clear Magento Cache </p>
     * <p>7.Open Frontend</p>
     * <p>8.Open category page</p>
     * <p>Expected result:</p>
     * <p>1. After 7 Category navigation menu  is missing</p>
     * <p>2. After 8 "About Us" page is open</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5004
     */
    public function browsingCategoryIsNo()
    {
        //Data
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_enable',
                                     array ('allow_browsing' => 'No, Redirect to Landing Page'));
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->frontend();
        $this->assertFalse($this->controlIsPresent('pageelement', 'front_navigation_menu'),
                           'Navigation menu should be absent');
    }

    /**
     * <p>Allow Browsing Category set up "Yes, for Specified Customer Groups"</p>
     * <p>Steps</p>
     * <p>1.Go to System -> Configuration -> Catalog -> Catalog -> Category Permissions</p>
     * <p>2.Select "Yes" in Enable field</p>
     * <p>3.Select "Yes, for Specified Customer Groups" in Allow Browsing Category field</p>
     * <p>4.Select "General" in Customer Groups list</p>
     * <p>5.Select "About Us" in Landing Page field</p>
     * <p>6.Click "Save Config" button</p>
     * <p>7.Clear Magento Cache </p>
     * <p>8.Open Frontend as a guest</p>
     * <p>9.Try Open category</p>
     * <p>10.Login to frontend</p>
     * <p>11.Open category</p>
     * <p>Expected result:</p>
     * <p>1. After 8 Category navigation menu  is missing<</p>
     * <p>2. After 9 "About Us" page is open</p>
     * <p>3. After 10 Category navigation menu  is present</p>
     * <p>4. After 11 Category page is open</p>
     *
     * @param array $testData
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5003
     */
    public function browsingCategoryForSpecifiedCustomer($testData)
    {
        //Data
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_enable',
                                     array ('allow_browsing' => 'Yes, for Specified Customer Groups',
                                            'landing_page' => 'About Us',
                                            'allow_browsing_customer_groups' => 'General'));
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->frontend();
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->assertTrue($this->controlIsPresent('pageelement', 'front_navigation_menu'),
                          'Navigation menu must be present');
        $this->categoryHelper()->frontOpenCategory($testData['catName']);
        $url = $this->getLocation();
        $this->customerHelper()->logoutCustomer();
        $this->open($url);
        $pageTitle = $this->getTitle();
        $this->assertTrue($pageTitle == 'About Us', 'Open wrong page');
    }

    /**
     * <p>Set up Disallow Catalog Search</p>
     * <p>Steps</p>
     * <p>1.Go to System -> Configuration -> Catalog -> Catalog -> Category Permissions</p>
     * <p>2.Select "Yes" in Enable field</p>
     * <p>3.Select "NOT LOGGED IN" in Disallow Catalog Search By list</p>
     * <p>4.Click "Save Config" button</p>
     * <p>5.Clear Magento Cache </p>
     * <p>6.Open Frontend</p>
     * <p>7.Login to Frontend</p>
     * <p>Expected result:</p>
     * <p>1. After 6 ﻿Quick search and Advanced search is not available</p>
     * <p>2. After 7 ﻿Quick search and Advanced search is available</p>
     *
     * @param array $testData
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5019
     */
    public function disallowCatalogSearch($testData)
    {
        //Data
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_enable',
                                     array ('disallow_catalog_search' => 'NOT LOGGED IN'));
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->frontend();
        $this->assertFalse($this->controlIsPresent('button', 'go_search'),
                           'Button "Add go_search cart" should be absent');
        $this->assertFalse($this->controlIsPresent('field', 'search'),
                           'Field "search" should be absent');
        $this->assertFalse($this->controlIsPresent('link', 'advanced_search'),
                           'Link "advanced_search" should be absent');
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->assertTrue($this->controlIsPresent('button', 'go_search'),
                          'Button "Add go_search cart" must be present');
        $this->assertTrue($this->controlIsPresent('field', 'search'),
                          'Field "search" must be present');
        $this->assertTrue($this->controlIsPresent('link', 'advanced_search'),
                          'Link "advanced_search" must be present');
    }

    /**
     * <p>Permissions are apply for products in Wishlist</p>
     * <p>Steps</p>
     * <p>1.Go to System -> Configuration -> Catalog -> Catalog -> Category Permissions</p>
     * <p>2.Select "Yes" in Enable field</p>
     * <p>3.Select "Yes, for Everyone" in Allow Browsing Category field</p>
     * <p>4.Select "No" in Display Product Prices</p>
     * <p>5.Click "Save Config" button</p>
     * <p>6.Clear Magento Cache </p>
     * <p>7.Open Frontend</p>
     * <p>8.Open any category</p>
     * <p>9.Add any product to Wishlist</p>
     * <p>10. Open Wishlist</p>
     * <p>Expected result:</p>
     * <p> Product price is not visible. "Add to cart" button is missing</p>
     *
     * @param array $testData
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5013
     */
    public function permissionsInWishlist($testData)
    {
        //Data
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_enable',
                                      array ('display_prices' => 'No'));
        $this->addParameter('productName', $testData['product']['name']);
        $this->addParameter('price', '$' . $testData['product']['price']);
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->wishlistHelper()
             ->frontAddProductToWishlistFromCatalogPage($testData['product']['name'], $testData['catName']);
        $this->navigate('my_wishlist');
        $this->assertFalse($this->controlIsPresent('pageelement', 'price_regular'), 'Product price should be absent');
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
    }
}

