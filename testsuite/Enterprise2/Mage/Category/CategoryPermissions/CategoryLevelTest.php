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

class Enterprise2_Mage_Category_CategoryPermissions_CategoryLevelTest extends Mage_Selenium_TestCase
{
    /**
     * Create website, category, customer and product
     * return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $websiteData = $this->loadDataSet('Website', 'generic_website');
        $storeData = $this->loadDataSet('Store', 'generic_store', array ('website' => $websiteData['website_name']));
        $storeViewData = $this->loadDataSet('StoreView', 'generic_store_view', array ('store_name' => $storeData['store_name']));
        $category = $this->loadDataSet('Category', 'sub_category_required');
        $catPath = $category['parent_category'] . '/' . $category['name'];
        $productCat = array('categories' => $catPath);
        $simple = $this->loadDataSet('Product', 'simple_product_visible', $productCat);
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($websiteData, 'website');
        $this->assertMessagePresent('success', 'success_saved_website');
        $this->storeHelper()->createStore($storeData, 'store');
        $this->assertMessagePresent('success', 'success_saved_store');
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

        return array('user'   => array('email' => $userData['email'], 'password' => $userData['password']),
                     'product'=> array('name' => $simple['general_name'], 'price' =>$simple['prices_price']),
                     'catName'=> $category['name'],
                     'catPath'=> $catPath);
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
     * test
     * @TestlinkId TL-MAGE-5798
     */
    public function disablePermission()
    {
        //Data
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_disable');
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->navigate('manage_categories');
        $this->assertFalse($this->controlIsPresent('tab', 'category_permissions_tab'),
                                                   'Category permissions must be absent');
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
     * test
     * @TestlinkId TL-MAGE-5091, TL-MAGE-5081
     */
    public function enablePermission()
    {
        //Data
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_enable');
        $this->addParameter('row', '1');
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->navigate('manage_categories');
        $this->assertTrue($this->controlIsPresent('tab', 'category_permissions_tab'), 'Category permissions must be present');
        $this->openTab('category_permissions_tab');
        $this->assertTrue($this->controlIsPresent('button', 'new_permission'), 'Button "New permission" is absent');
        $this->clickControl('button', 'new_permission', false);
        $this->waitForAjax();
        $this->assertTrue($this->controlIsPresent('dropdown', 'website'), 'Dropdown "website" is absent');
        $this->assertTrue($this->controlIsPresent('dropdown', 'customer_group'), 'Dropdown "customer_group" is absent');
        $this->assertTrue($this->controlIsPresent('radiobutton', 'browsing_category_allow'), 'Radiobutton "browsing_category_allow" is absent');
        $this->assertTrue($this->controlIsPresent('radiobutton', 'browsing_category_deny'), 'Radiobutton "browsing_category_deny" is absent');
        $this->assertTrue($this->controlIsPresent('radiobutton', 'browsing_category_use_parent'), 'Radiobutton "browsing_category_use_parent" is absent');
        $this->assertTrue($this->controlIsPresent('radiobutton', 'displaying_price_allow'), 'Radiobutton "displaying_price_allow" is absent');
        $this->assertTrue($this->controlIsPresent('radiobutton', 'displaying_price_deny'), 'Radiobutton "displaying_price_deny" is absent');
        $this->assertTrue($this->controlIsPresent('radiobutton', 'displaying_price_use_parent'), 'Radiobutton "displaying_price_use_parent" is absent');
        $this->assertTrue($this->controlIsPresent('radiobutton', 'add_to_cart_allow'), 'Radiobutton "add_to_cart_allow" is absent');
        $this->assertTrue($this->controlIsPresent('radiobutton', 'add_to_cart_deny'), 'Radiobutton "add_to_cart_deny" is absent');
        $this->assertTrue($this->controlIsPresent('radiobutton', 'add_to_cart_use_parent'), 'Radiobutton "add_to_cart_use_parent" is absent');
        $this->assertTrue($this->controlIsPresent('button', 'delete_permissions'), 'Button "delete_permissions" is absent');
    }

    /**
     * test
     */
    public function samePermissions()
    {

    }

    /**
     * @param array $testData
     * @test
     * @depends preconditionsForTests
     */
    public function denyAddToCart($testData)
    {
        //Data
        $permission = $this->loadDataSet('Category', 'permissions_deny_add_to_cart_for_guest');
        $this->addParameter('productName', $testData['product']['name']);
        $this->addParameter('price', '$' . $testData['product']['price']);
        //Steps
        $this->loginAdminUser();
        $this->categoryHelper()->setPermissions($permission, $testData['catPath']);
        $this->pleaseWait();
        $this->assertMessagePresent('success', 'success_saved_category');
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
     * test
     */
    public function denyDisplayPrices()
    {

    }

    /**
     * test
     */
    public function denyBrowsingCategory()
    {

    }

    /**
     * test
     */
    public function severalPermissions()
    {

    }


}