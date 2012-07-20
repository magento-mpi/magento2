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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
/**
 * Category Permissions tests
 *
 * @package     Mage_Category
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise2_Mage_Category_CategoryPermissions_CategoryLevelTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * Create website, category, customer and product
     * return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view');
        $category = $this->loadDataSet('Category', 'sub_category_required');
        $catPath = $category['parent_category'] . '/' . $category['name'];
        $productCat = array('categories' => $catPath);
        $simple = $this->loadDataSet('Product', 'simple_product_visible', $productCat);
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_enable');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($storeViewData, 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
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
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);

        return array('user'   => array('email' => $userData['email'], 'password' => $userData['password']),
                     'product'=> array('name' => $simple['general_name'], 'price' => $simple['prices_price']),
                     'catName'=> $category['name'], 'catPath'=> $catPath);
    }

    /**
     * <p>Enable Category Permissions</p>
     * <p>Steps</p>
     * <p>1.Go to System -> Configuration -> Catalog -> Catalog -> Category Permissions</p>
     * <p>2.Select "Yes" in Enable field</p>
     * <p>3.Click "Save Config" button</p>
     * <p>4.Clear Magento Cache </p>
     * <p>5.Go to Catalog -> Categories -> Manage Categories</p>
     * <p>6.Open "Category Permissions" tab</p>
     * <p>7.Click "New permission"</p>
     * <p>8.Verify that all elements are present</p>
     * <p>Expected result:</p>
     * <p>1. After 5 Category Permissions tab is present</p>
     * <p>2. After 6 "New Permission" button is present</p>
     * <p>3. After 8 All element are present</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5091, TL-MAGE-5081
     */
    public function enablePermission()
    {
        //Data
        $this->addParameter('row', '1');
        //Steps
        $this->navigate('manage_categories');
        $this->assertTrue($this->controlIsPresent('tab', 'category_permissions_tab'),
            'Category permissions must be present');
        $this->openTab('category_permissions_tab');
        $this->assertTrue($this->controlIsPresent('button', 'new_permission'), 'Button "New permission" is absent');
        $this->clickControl('button', 'new_permission', false);
        $this->waitForAjax();
        $this->assertTrue($this->controlIsPresent('dropdown', 'website'), 'Dropdown "website" is absent');
        $this->assertTrue($this->controlIsPresent('dropdown', 'customer_group'), 'Dropdown "customer_group" is absent');
        $this->assertTrue($this->controlIsPresent('radiobutton', 'browsing_category_allow'),
            'Radiobutton "browsing_category_allow" is absent');
        $this->assertTrue($this->controlIsPresent('radiobutton', 'browsing_category_deny'),
            'Radiobutton "browsing_category_deny" is absent');
        $this->assertTrue($this->controlIsPresent('radiobutton', 'browsing_category_use_parent'),
            'Radiobutton "browsing_category_use_parent" is absent');
        $this->assertTrue($this->controlIsPresent('radiobutton', 'displaying_price_allow'),
            'Radiobutton "displaying_price_allow" is absent');
        $this->assertTrue($this->controlIsPresent('radiobutton', 'displaying_price_deny'),
            'Radiobutton "displaying_price_deny" is absent');
        $this->assertTrue($this->controlIsPresent('radiobutton', 'displaying_price_use_parent'),
            'Radiobutton "displaying_price_use_parent" is absent');
        $this->assertTrue($this->controlIsPresent('radiobutton', 'add_to_cart_allow'),
            'Radiobutton "add_to_cart_allow" is absent');
        $this->assertTrue($this->controlIsPresent('radiobutton', 'add_to_cart_deny'),
            'Radiobutton "add_to_cart_deny" is absent');
        $this->assertTrue($this->controlIsPresent('radiobutton', 'add_to_cart_use_parent'),
            'Radiobutton "add_to_cart_use_parent" is absent');
        $this->assertTrue($this->controlIsPresent('button', 'delete_permissions'),
            'Button "delete_permissions" is absent');
    }

    /**
     * <p>Deny Add to cart</p>
     * <p>Steps</p>
     * <p>1.Go to Catalog -> Categories -> Manage Categories</p>
     * <p>2.Select "Category" in category tree</p>
     * <p>3.Open Category Permissions tab</p>
     * <p>4.Click "New Permission" button</p>
     * <p>5.Select "All Website"</p>
     * <p>6.Select "All Customer Groups" in Customer Group</p>
     * <p>7.Select "Allow" in Browsing Category column</p>
     * <p>8.Select "Allow" in Display Product Prices column</p>
     * <p>9.Select "Deny" in Add to Cart column</p>
     * <p>10.Click "Save Category" button</p>
     * <p>11.Clear Magento Cache </p>
     * <p>12.Open category page at frontend</p>
     * <p>13.Open Product page</p>
     * <p>Expected result:</p>
     * <p>1. After 12 Product price is visible, "Add to cart" button is missing</p>
     * <p>2. After 13 Product price is visible, "Add to cart" button is missing</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5029
     */
    public function denyAddToCart($testData)
    {
        //Data
        $permission = $this->loadDataSet('Category', 'permissions_deny_add_to_cart');
        $this->addParameter('productName', $testData['product']['name']);
        $this->addParameter('price', '$' . $testData['product']['price']);
        //Steps
        $this->navigate('manage_categories');
        $this->categoryHelper()->deleteAllPermissions($testData['catPath']);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->fillCategoryInfo($permission);
        $this->clickButton('save_category');
        $this->pleaseWait();
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->flushCache();
        $this->logoutCustomer();
        $this->categoryHelper()->frontOpenCategory($testData['catName']);
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
        $this->assertTrue($this->controlIsPresent('pageelement', 'price_regular'), 'Product price must be present');
        $this->productHelper()->frontOpenProduct($testData['product']['name']);
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
        $this->assertTrue($this->controlIsPresent('pageelement', 'price_regular'), 'Product price must be present');
    }

    /**
     * <p>Deny display price</p>
     * <p>Steps</p>
     * <p>1.Go to Catalog -> Categories -> Manage Categories</p>
     * <p>2.Select "Category" in category tree</p>
     * <p>3.Open Category Permissions tab</p>
     * <p>4.Click "New Permission" button</p>
     * <p>5.Select "All Website"</p>
     * <p>6.Select "All Customer Groups" in Customer Group</p>
     * <p>7.Select "Allow" in Browsing Category column</p>
     * <p>8.Select "Deny" in Display Product Prices column</p>
     * <p>9.Click "Save Category" button</p>
     * <p>10.Clear Magento Cache </p>
     * <p>11.Open category page at frontend</p>
     * <p>12.Open Product page</p>
     * <p>Expected result:</p>
     * <p>1. After 11 Product price and "Add to cart" button are missing</p>
     * <p>2. After 12 Product price and "Add to cart" button are missing</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5028
     */
    public function denyDisplayPrices($testData)
    {
        //Data
        $permission = $this->loadDataSet('Category', 'permissions_deny_display_prices');
        $this->addParameter('productName', $testData['product']['name']);
        $this->addParameter('price', '$' . $testData['product']['price']);
        //Steps
        $this->navigate('manage_categories');
        $this->categoryHelper()->deleteAllPermissions($testData['catPath']);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->fillCategoryInfo($permission);
        $this->clickButton('save_category');
        $this->pleaseWait();
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->flushCache();
        $this->logoutCustomer();
        $this->categoryHelper()->frontOpenCategory($testData['catName']);
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
        $this->assertFalse($this->controlIsPresent('pageelement', 'price_regular'), 'Product price must be present');
        $this->productHelper()->frontOpenProduct($testData['product']['name']);
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
        $this->assertFalse($this->controlIsPresent('pageelement', 'price_regular'), 'Product price must be present');
    }

    /**
     * <p>Deny Browsing Category</p>
     * <p>Steps</p>
     * <p>1.Go to Catalog -> Categories -> Manage Categories</p>
     * <p>2.Select "Category" in category tree</p>
     * <p>3.Open Category Permissions tab</p>
     * <p>4.Click "New Permission" button</p>
     * <p>5.Select "All Website"</p>
     * <p>6.Select "All Customer Groups" in Customer Group</p>
     * <p>7.Select "Allow" in Browsing Category column</p>
     * <p>8.Select "Deny" in Display Product Prices column</p>
     * <p>9.Click "Save Category" button</p>
     * <p>10.Clear Magento Cache </p>
     * <p>11.Open category page at frontend</p>
     * <p>12.Open Product page</p>
     * <p>Expected result:</p>
     * <p>1. After 11 Product price and "Add to cart" button are missing</p>
     * <p>2. After 12 Product price and "Add to cart" button are missing</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5020
     */
    public function denyBrowsingCategory($testData)
    {
        //Data
        $permission = $this->loadDataSet('Category', 'permissions_deny_browsing_category');
        $this->addParameter('catName', $testData['catName']);
        //Steps
        $this->navigate('manage_categories');
        $this->categoryHelper()->deleteAllPermissions($testData['catPath']);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->fillCategoryInfo($permission);
        $this->clickButton('save_category');
        $this->pleaseWait();
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->flushCache();
        $this->logoutCustomer();
        $this->assertFalse($this->controlIsPresent('button', 'category_button'));
    }

    /**
     * <p>Set up several permissions for category</p>
     * <p>Steps</p>
     * <p>1.Go to Catalog -> Categories -> Manage Categories</p>
     * <p>2.Select "Category" in category tree</p>
     * <p>3.Open Category Permissions tab</p>
     * <p>4.Click "New Permission" button</p>
     * <p>5.Select "All Website"</p>
     * <p>6.Select "All Customer Groups" in Customer Group</p>
     * <p>7.Select "Deny" in Browsing Category column</p>
     * <p>8.Click "New Permission" button</p>
     * <p>9.Select "All Website</p>
     * <p>10.Select "General" in Customer Group</p>
     * <p>11.Select "Allow" in Browsing Category column</p>
     * <p>12.Select "Allow" in Display Product Prices column</p>
     * <p>13.Select "Deny" in Add to Cart column</p>
     * <p>14.Click "Save Category" button</p>
     * <p>15.Clear Magento Cache </p>
     * <p>16.Open frontend</p>
     * <p>17.Login to frontend</p>
     * <p>18.Open Category</p>
     * <p>19.Open Product page</p>
     * <p>Expected result:</p>
     * <p>1. After 16 Category button is absent</p>
     * <p>2. After 17 Category button is present </p>
     * <p>3. After 18 Product price is visible, "Add to cart" button is missing</p>
     * <p>4. After 19 Product price is visible, "Add to cart" button is missing</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5035, TL-MAGE-5042
     */
    public function severalPermissions($testData)
    {
        //Data
        $permission = $this->loadDataSet('Category', 'several_permissions');
        $this->addParameter('productName', $testData['product']['name']);
        $this->addParameter('price', '$' . $testData['product']['price']);
        $this->addParameter('catName', $testData['catName']);
        //Steps
        $this->navigate('manage_categories');
        $this->categoryHelper()->deleteAllPermissions($testData['catPath']);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->fillCategoryInfo($permission);
        $this->clickButton('save_category');
        $this->pleaseWait();
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->flushCache();
        $this->logoutCustomer();
        $this->assertFalse($this->controlIsPresent('button', 'category_button'));
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->assertTrue($this->controlIsPresent('button', 'category_button'));
        $this->categoryHelper()->frontOpenCategory($testData['catName']);
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
        $this->assertTrue($this->controlIsPresent('pageelement', 'price_regular'), 'Product price must be present');
        $this->productHelper()->frontOpenProduct($testData['product']['name']);
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
        $this->assertTrue($this->controlIsPresent('pageelement', 'price_regular'), 'Product price must be present');
    }

    /**
     * <p>Permissions are apply for products in Wishlist</p>
     * <p>Steps</p>
     * <p>1.Go to Catalog -> Categories -> Manage Categories</p>
     * <p>2.Select "Category" in category tree</p>
     * <p>3.Open Category Permissions tab</p>
     * <p>4.Click "New Permission" button</p>
     * <p>5.Select "All Website"</p>
     * <p>6.Select "All Customer Groups" in Customer Group</p>
     * <p>7.Select "Allow" in Browsing Category column</p>
     * <p>8.Select "Deny" in Display Product Prices column</p>
     * <p>9.Click "Save Category" button</p>
     * <p>10.Clear Magento Cache </p>
     * <p>11.Open Frontend</p>
     * <p>12.Open any category</p>
     * <p>13.Add any product to Wishlist</p>
     * <p>14. Open Wishlist</p>
     * <p>Expected result:</p>
     * <p> Product price is not visible. "Add to cart" button is missing</p>
     *
     * @param array $testData
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5166
     */
    public function permissionsInWishlist($testData)
    {
        //Data
        $permission = $this->loadDataSet('Category', 'permissions_deny_display_prices');
        $this->addParameter('productName', $testData['product']['name']);
        $this->addParameter('price', '$' . $testData['product']['price']);
        //Steps
        $this->navigate('manage_categories');
        $this->categoryHelper()->deleteAllPermissions($testData['catPath']);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->fillCategoryInfo($permission);
        $this->clickButton('save_category');
        $this->pleaseWait();
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->flushCache();
        $this->logoutCustomer();
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->wishlistHelper()
            ->frontAddProductToWishlistFromCatalogPage($testData['product']['name'], $testData['catName']);
        $this->navigate('my_wishlist');
        $this->assertFalse($this->controlIsPresent('pageelement', 'price_regular'), 'Product price should be absent');
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
    }

    /**
     * <p>Set up several permissions for category</p>
     * <p>Steps</p>
     * <p>1.Go to System -> Configuration -> Catalog -> Catalog -> Category Permissions</p>
     * <p>2.Select "Yes" in Enable field</p>
     * <p>3.Select "No" in Allow Browsing Category</p>
     * <p>4.Save config</p>
     * <p>5.Go to Catalog -> Categories -> Manage Categories</p>
     * <p>6.Select "Category" in category tree</p>
     * <p>7.Open Category Permissions tab</p>
     * <p>8.Click "New Permission" button</p>
     * <p>9.Select "All Website"</p>
     * <p>10.Select "All Customer Groups" in Customer Group</p>
     * <p>11.Select "Allow" in Browsing Category column</p>
     * <p>12.Select "Allow" in Display Product Prices column</p>
     * <p>13.Select "Allow" in Add to Cart column</p>
     * <p>14.Click "Save Category" button</p>
     * <p>15.Clear Magento Cache </p>
     * <p>16.Open frontend</p>
     * <p>17.Open Category</p>
     * <p>18.Open Product page</p>
     * <p>Expected result:</p>
     * <p>1. After 17 Category panel is present </p>
     * <p>2. After 18 Product price and "Add to cart" button are present</p>
     * <p>3. After 19 Product price and "Add to cart" button are present</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5030, TL-MAGE-5040
     */
    public function allowAll($testData)
    {
        //Data
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_enable',
            array('allow_browsing' => 'No, Redirect to Landing Page'));
        $permission = $this->loadDataSet('Category', 'permissions_allow_all');
        $this->addParameter('productName', $testData['product']['name']);
        $this->addParameter('price', '$' . $testData['product']['price']);
        $this->addParameter('catName', $testData['catName']);
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->navigate('manage_categories');
        $this->categoryHelper()->deleteAllPermissions($testData['catPath']);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->fillCategoryInfo($permission);
        $this->clickButton('save_category');
        $this->pleaseWait();
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->flushCache();
        $this->logoutCustomer();
        $this->assertTrue($this->controlIsPresent('button', 'category_button'));
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->categoryHelper()->frontOpenCategory($testData['catName']);
        $this->assertTrue($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" must be present');
        $this->assertTrue($this->controlIsPresent('pageelement', 'price_regular'), 'Product price must be present');
        $this->productHelper()->frontOpenProduct($testData['product']['name']);
        $this->assertTrue($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" must be present');
        $this->assertTrue($this->controlIsPresent('pageelement', 'price_regular'), 'Product price must be present');
    }

    /**
     * <p>Disable Category Permissions</p>
     * <p>Steps</p>
     * <p>1.Go to System -> Configuration -> Catalog -> Catalog -> Category Permissions</p>
     * <p>2.Select "No" in Enable field</p>
     * <p>3.Click "Save Config" button</p>
     * <p>4.Clear Magento Cache </p>
     * <p>5.Go to Catalog -> Categories -> Manage Categories</p>
     * <p>Expected result:</p>
     * <p>1. Category Permissions tab is absent</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5798
     */
    public function disablePermission()
    {
        //Data
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_disable');
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->navigate('manage_categories');
        $this->assertFalse($this->controlIsPresent('tab', 'category_permissions_tab'),
            'Category permissions tab must be absent');
    }
}
