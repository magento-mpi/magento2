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
class Enterprise_Mage_Category_CategoryPermissions_ConfigLevelTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
    }

    protected function tearDownAfterTest()
    {
        $this->logoutCustomer();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('CategoryPermissions/category_permissions_disable');
    }

    /**
     * <p>Category Permissions Options block</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5078
     */
    public function navigationTest()
    {
        $this->navigate('system_configuration');
        $this->clickControl('tab', 'catalog_catalog', false);
        $this->waitForPageToLoad();
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
        $productCat = array('general_categories' => $catPath);
        $simple = $this->loadDataSet('Product', 'simple_product_visible', $productCat);
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();

        return array(
            'user' => array('email' => $userData['email'], 'password' => $userData['password']),
            'product' => array('name' => $simple['general_name'], 'price' => $simple['general_price']),
            'catName' => $category['name']
        );
    }

    /**
     * <p>Allow Adding to Cart set up "No"</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5018
     */
    public function allowAddingToCartIsNo($testData)
    {
        //Data
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_enable',
            array('allow_adding_to_cart' => 'No'));
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
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5016
     */
    public function allowAddingToCartForSpecifiedCustomer($testData)
    {
        //Data
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_enable', array(
            'allow_adding_to_cart' => 'Yes, for Specified Customer Groups',
            'allow_adding_to_cart_customer_groups' => 'General'
        ));
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
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5170
     */
    public function displayProductPricesIsNo($testData)
    {
        //Data
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_enable',
            array('display_prices' => 'No'));
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
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5010
     */
    public function displayProductPricesForSpecifiedCustomer($testData)
    {
        //Data
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_enable', array(
            'display_prices' => 'Yes, for Specified Customer Groups',
            'display_prices_customer_groups' => 'General'
        ));
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
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5004
     */
    public function browsingCategoryIsNo()
    {
        //Data
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_enable',
            array('allow_browsing' => 'No, Redirect to Landing Page'));
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->frontend();
        $this->assertFalse($this->controlIsVisible('pageelement', 'categories_menu'),
            'Navigation menu should be absent');
    }

    /**
     * <p>Allow Browsing Category set up "Yes, for Specified Customer Groups"</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5003
     */
    public function browsingCategoryForSpecifiedCustomer($testData)
    {
        //Data
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_enable', array(
            'allow_browsing' => 'Yes, for Specified Customer Groups',
            'landing_page' => 'About Us', 'allow_browsing_customer_groups' => 'General'
        ));
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->frontend();
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->assertTrue($this->controlIsVisible('pageelement', 'categories_menu'),
            'Navigation menu must be present');
        $this->categoryHelper()->frontOpenCategory($testData['catName']);
        $url = $this->url();
        $this->customerHelper()->logoutCustomer();
        $this->url($url);
        $this->assertSame('about_us', $this->_findCurrentPageFromUrl(), 'Wrong page was opened');
    }

    /**
     * <p>Set up Disallow Catalog Search</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5019
     */
    public function disallowCatalogSearch($testData)
    {
        //Data
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_enable',
            array('disallow_catalog_search' => 'NOT LOGGED IN'));
        //Steps
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->frontend();
        $this->assertFalse($this->controlIsPresent('button', 'go_search'),
            'Button "go_search" should be absent');
        $this->assertFalse($this->controlIsPresent('field', 'search'), 'Field "search" should be absent');
        $this->assertFalse($this->controlIsPresent('link', 'advanced_search'),
            'Link "advanced_search" should be absent');
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->assertTrue($this->controlIsPresent('button', 'go_search'),
            'Button "go_search" must be present');
        $this->assertTrue($this->controlIsPresent('field', 'search'), 'Field "search" must be present');
        $this->assertTrue($this->controlIsPresent('link', 'advanced_search'), 'Link "advanced_search" must be present');
    }

    /**
     * <p>Permissions are apply for products in Wishlist</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5013
     */
    public function permissionsInWishlist($testData)
    {
        //Data
        $config = $this->loadDataSet('CategoryPermissions', 'category_permissions_enable',
            array('display_prices' => 'No'));
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

