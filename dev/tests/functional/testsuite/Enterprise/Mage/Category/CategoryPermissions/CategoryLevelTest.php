<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Category
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Category Permissions tests
 *
 * @package     Mage_Category
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_Category_CategoryPermissions_CategoryLevelTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('CategoryPermissions/category_permissions_enable');
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore('StoreView/generic_store_view', 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->logoutCustomer();
    }

    /**
     * Create website, category, customer and product
     * return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $category = $this->loadDataSet('Category', 'sub_category_required');
        $catPath = $category['parent_category'] . '/' . $category['name'];
        $simple = $this->loadDataSet('Product', 'simple_product_visible', array('general_categories' => $catPath));
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps
        $this->navigate('manage_categories');
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        return array(
            'user' => array('email' => $userData['email'], 'password' => $userData['password']),
            'product' => array('name' => $simple['general_name'], 'price' => $simple['general_price']),
            'catName' => $category['name'],
            'catPath' => $catPath
        );
    }

    /**
     * <p>Set up several permissions for category</p>
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
        $this->categoryHelper()->addNewCategoryPermissions($permission);
        $this->saveForm('save_category', false);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->flushCache();
        $this->frontend();
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
     * <p>Enable Category Permissions</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5091, TL-MAGE-5081
     */
    public function enablePermission($testData)
    {
        //Data
        $this->addParameter('row', '1');
        //Steps
        $this->navigate('manage_categories');
        $this->categoryHelper()->selectCategory($testData['catPath']);
        $this->assertTrue($this->controlIsPresent('tab', 'category_permissions_tab'),
            'Category permissions must be present');
        $this->openTab('category_permissions_tab');
        $this->clickButton('new_permission', false);
        $this->waitForElement($this->_getControlXpath('button', 'delete_permissions'));
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
        $this->categoryHelper()->addNewCategoryPermissions($permission);
        $this->saveForm('save_category', false);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->flushCache();
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($testData['catName']);
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
        $this->assertTrue($this->controlIsPresent('pageelement', 'price_regular'), 'Product price must be present');
        $this->productHelper()->frontOpenProduct($testData['product']['name']);
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
        $this->assertTrue($this->controlIsPresent('pageelement', 'price_regular'), 'Product price must be present');
    }

    /**
     * <p>Deny display price</p>
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
        $this->categoryHelper()->addNewCategoryPermissions($permission);
        $this->saveForm('save_category', false);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->flushCache();
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($testData['catName']);
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
        $this->assertFalse($this->controlIsPresent('pageelement', 'price_regular'), 'Product price must be present');
        $this->productHelper()->frontOpenProduct($testData['product']['name']);
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
        $this->assertFalse($this->controlIsPresent('pageelement', 'price_regular'), 'Product price must be present');
    }

    /**
     * <p>Deny Browsing Category</p>
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
        $this->categoryHelper()->addNewCategoryPermissions($permission);
        $this->saveForm('save_category', false);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->flushCache();
        $this->frontend();
        $this->assertFalse($this->controlIsPresent('button', 'category_button'));
    }

    /**
     * <p>Set up several permissions for category</p>
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
        $this->categoryHelper()->addNewCategoryPermissions($permission);
        $this->saveForm('save_category', false);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->flushCache();
        $this->frontend();
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
     *
     * @param array $testData
     *
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
        $this->categoryHelper()->addNewCategoryPermissions($permission);
        $this->saveForm('save_category', false);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->flushCache();
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->wishlistHelper()
            ->frontAddProductToWishlistFromCatalogPage($testData['product']['name'], $testData['catName']);
        $this->navigate('my_wishlist');
        $this->assertFalse($this->controlIsPresent('pageelement', 'price_regular'), 'Product price should be absent');
        $this->assertFalse($this->controlIsPresent('button', 'add_to_cart'), 'Button "Add to cart" should be absent');
    }

    /**
     * <p>Disable Category Permissions</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5798
     */
    public function disablePermission()
    {
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('CategoryPermissions/category_permissions_disable');
        $this->navigate('manage_categories');
        $this->assertFalse($this->controlIsPresent('tab', 'category_permissions_tab'),
            'Category permissions tab must be absent');
    }
}
