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
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wishlist tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Wishlist_Wishlist extends Mage_Selenium_TestCase
{

    /**
     * <p>Login as a registered user</p>
     */
    public function setUpBeforeTests()
    {
        $this->logoutCustomer();
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>@TODO</p>
     */
    protected function assertPreConditions()
    {
        $this->addParameter('categoryUrl', '0');
    }

    /**
     * <p>Preconditions</p>
     * <p>Create a new customer for tests</p>
     *
     * @test
     */
    public function createCustomer()
    {
        $this->loginAdminUser();
        $userData = $this->loadData('customer_account_for_prices_validation', NULL, 'email');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);

        return array('email' => $userData['email'], 'password' => $userData['password']);
    }

    /**
     * <p>Preconditions</p>
     * <p>Creates Category to use during tests</p>
     *
     * @test
     */
    public function createCategory()
    {
        $this->loginAdminUser();
        $this->navigate('manage_categories');
        $this->categoryHelper()->checkCategoriesPage();
        $rootCat = 'Default Category';
        $categoryData = $this->loadData('sub_category_required', null, 'name');
        $this->categoryHelper()->createSubCategory($rootCat, $categoryData);
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        $this->categoryHelper()->checkCategoriesPage();

        return $rootCat . '/' . $categoryData['name'];
    }

    /**
     * <p>Preconditions</p>
     * <p>Creating configurable product</p>
     *
     * @test
     */
    public function createConfigurableAttribute()
    {
        //Data
        $attrData = $this->loadData('product_attribute_dropdown_with_options', null,
                array('admin_title', 'attribute_code'));
        $associatedAttributes = $this->loadData('associated_attributes',
                array('General' => $attrData['attribute_code']));
        //Steps
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_attribute'), $this->messages);
        //Steps
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        //Verifying
        $this->assertTrue($this->successMessage('success_attribute_set_saved'), $this->messages);

        return $attrData;
    }

    /**
     * <p>Create a new product of the specified type</p>
     */
    protected function createProduct(array $productData, $productType)
    {
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, $productType);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);

        return $productData['general_name'];
    }

    /**
     * <p>Preconditions</p>
     * <p>Create products of all types for the tests</p>
     *
     * @test
     */
    public function createProductSimple()
    {
        // TODO Create products of all types
        $productData = $this->loadData('simple_product_required', null, array('general_name', 'general_sku'));
        $productSimple = $this->createProduct($productData, 'simple');
        return $productSimple;
    }

    /**
     * <p>Preconditions</p>
     * <p>Create products of all types for the tests</p>
     *
     * @test
     */
    public function createAllProducts()
    {
        // TODO Create products of all types
        $productData = $this->loadData('simple_product_required', null, array('general_name', 'general_sku'));
        $productSimple = $this->createProduct($productData, 'simple');
        $productData = $this->loadData('virtual_product_required', null, array('general_name', 'general_sku'));
        $productVirtual = $this->createProduct($productData, 'virtual');
        $productData = $this->loadData('downloadable_product_required', null, array('general_name', 'general_sku'));
        $productDownloadable = $this->createProduct($productData, 'downloadable');
        $productData = $this->loadData('grouped_product_required', null, array('general_name', 'general_sku'));
        $productGrouped = $this->createProduct($productData, 'grouped');
        $attrData = $this->createConfigurableAttribute();
        $productData = $this->loadData('configurable_product_required',
                array('configurable_attribute_title' => $attrData['admin_title']),
                array('general_sku', 'general_name'));
        $productConfigurable = $this->productHelper()->createProduct($productData, 'configurable');
        $productData = $this->loadData('dynamic_bundle_required', null, array('general_name', 'general_sku'));
        $productBundle = $this->productHelper()->createProduct($productData, 'bundle');
        $allProducts = array($productSimple, $productVirtual, $productDownloadable,
            $productGrouped, $productConfigurable, $productBundle);
        return $allProducts;
    }

    /**
     * <p>Adds a product to Wishlist from Product Details page. For all product types</p>
     * <p>Steps:</p>
     * <p>1. Open product</p>
     * <p>2. Add product to wishlist</p>
     * <p>Expected result:</p>
     * <p>Success message is displayed</p>
     *
     * @depends createCustomer
     * @depends createAllProducts
     * @depends createCategory
     * @test
     */
    public function addProductToWishlistFromProductPage($customer, $productNameSet, $categoryPath)
    {
        //Setup
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->wishlistHelper()->frontClearWishlist();
        //Steps
        foreach ($productNameSet as $productName) {
            $this->wishlistHelper()->addProductToWishlistFromProductPage($productName, $categoryPath);
        }
        //Verify
        $this->navigate('my_wishlist');
        foreach ($productNameSet as $productName) {
            $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($productName), $this->messages);
        }
        //Cleanup
    }

    /**
     * <p>Adds a simple product to Wishlist from Catalog page.</p>
     * <p>Steps:</p>
     * <p>1. Open category</p>
     * <p>2. Find product</p>
     * <p>3. Add product to wishlist</p>
     * <p>Expected result:</p>
     * <p>Success message is displayed</p>
     *
     * @depends createCustomer
     * @depends createCategory
     * @depends createProductSimple
     * @test
     */
    public function addProductToWishlistFromCatalog($customer, $categoryPath, $simpleProductName)
    {
        //Setup
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->wishlistHelper()->frontClearWishlist();
        //Steps
        $this->wishlistHelper()->addProductToWishlistFromCatalogPage($simpleProductName, $categoryPath);
        //Verify
        $this->navigate('my_wishlist');
        $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($simpleProductName), $this->messages);
        //Cleanup
    }

    /**
     * <p>Adds a simple product to Wishlist from Shopping Cart.</p>
     * <p>Steps:</p>
     * <p>1. Add the product to the shopping cart</p>
     * <p>2. Move the product to wishlist</p>
     * <p>3. Open the wishlist</p>
     * <p>Expected result:</p>
     * <p>The product is in the wishlist</p>
     *
     * @depends createCustomer
     * @test
     */
    public function addProductToWishlistFromShoppingCart($customer, $productData, $categoryPath)
    {
        //Setup
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->wishlistHelper()->frontClearWishlist();
        //Steps
        //Verify
        //Cleanup
    }

    /**
     * <p>Adds a product to Shopping Cart from Wishlist. For all product types</p>
     * <p>Steps:</p>
     * <p>1. Empty the shopping cart</p>
     * <p>2. Add a product to the wishlist</p>
     * <p>3. Open the wishlist</p>
     * <p>4. Add the product to the shopping cart</p>
     * <p>Expected result:</p>
     * <p>The product is in the shopping cart</p>
     *
     * @depends createCustomer
     * @test
     */
    public function addProductToShoppingCartFromWishlist($customer, $productData)
    {
        //Setup
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->wishlistHelper()->frontClearWishlist();
        //Steps
        //Verify
        //Cleanup
    }

    /**
     * <p>Adds all products to Shopping Cart from Wishlist. For all product types</p>
     * <p>Steps:</p>
     * <p>1. Empty the shopping cart</p>
     * <p>2. Add products to the wishlist</p>
     * <p>3. Open the wishlist</p>
     * <p>4. Add all products to the shopping cart</p>
     * <p>Expected result:</p>
     * <p>The products are in the shopping cart</p>
     *
     * @depends createCustomer
     * @test
     */
    public function addAllProductsToShoppingCartFromWishlist($customer, array $productDataSet)
    {
        //Setup
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->wishlistHelper()->frontClearWishlist();
        //Steps
        //Verify
        //Cleanup
    }

    /**
     * <p>Verifies that a guest cannot open My Wishlist.</p>
     * <p>Steps:</p>
     * <p>1. Logout customer</p>
     * <p>2. Navigate to My Wishlist</p>
     * <p>Expected result:</p>
     * <p>Guest is redirected to login/register page.</p>
     *
     * @test
     */
    public function guestCannotOpenWishlist()
    {
        //Setup
        $this->logoutCustomer();
        //Steps
        $this->navigate('my_wishlist');
        //Verify
        $this->assertTrue($this->checkCurrentPage('customer_login'), $this->messages);
        //Cleanup
    }

    /**
     * <p>Verifies that a guest cannot add a product to a wishlist.</p>
     * <p>Steps:</p>
     * <p>1. Logout customer</p>
     * <p>2. Open a product</p>
     * <p>3. Add products to the wishlist</p>
     * <p>Expected result:</p>
     * <p>Guest is redirected to login/register page.</p>
     *
     * @depends createProductSimple
     * @test
     */
    public function guestCannotAddProductToWishlist($simpleProductName)
    {
        //Setup
        $this->logoutCustomer();
        //Steps
        $this->wishlistHelper()->addProductToWishlistFromProductPage($simpleProductName);
        //Verify
        $this->assertTrue($this->checkCurrentPage('customer_login'), $this->messages);
        //Cleanup
    }

    /**
     * <p>Opens My Wishlist using the link in quick access bar</p>
     * <p>Steps:</p>
     * <p>1. Open home page</p>
     * <p>2. Click "My Wishlist" link</p>
     * <p>Expected result:</p>
     * <p>The wishlist is opened.</p>
     *
     * @depends createCustomer
     * @test
     */
    public function openMyWishlistViaQuickAccessLink($customer)
    {
        //Setup
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->navigate('home');
        //Steps
        $this->clickControl('link', 'my_wishlist');
        //Verify
        $this->assertTrue($this->checkCurrentPage('my_wishlist'), $this->messages);
        //Cleanup
    }

    /**
     * <p>Removes all products from My Wishlist. For all product types</p>
     * <p>Steps:</p>
     * <p>1. Add products to the wishlist</p>
     * <p>2. Remove one product from the wishlist</p>
     * <p>Expected result:</p>
     * <p>The product is no longer in wishlist</p>
     * <p>3. Repeat for all products until the last one</p>
     * <p>4. Remove the last product from the wishlist</p>
     * <p>Expected result:</p>
     * <p>Message 'You have no items in your wishlist.' is displayed</p>
     *
     * @depends createCustomer
     * @depends createAllProducts
     * @test
     */
    public function removeProductsFromWishlist($customer, $productNameSet)
    {
        //Setup
        $this->addProductToWishlistFromProductPage($productNameSet);
        //Steps
        for ($i = 0; $i < count($productNameSet) - 1; $i++) {
            $this->wishlistHelper()->frontRemoveProductFromWishlist($productNameSet[$i]);
            //Verify
            $this->assertFalse($this->wishlistHelper()->frontWishlistHasProducts($productNameSet[$i]));
        }
        $lastProductName = $productNameSet[count($productNameSet) - 1];
        $this->wishlistHelper()->frontRemoveProductFromWishlist($lastProductName);
        //Verify
        $this->assertTrue($this->controlIsPresent('pageelement', 'no_items'), $this->messages);
        //Cleanup
    }

    /**
     * <p>Shares My Wishlist</p>
     * <p>Steps:</p>
     * <p>1. Add a product to the wishlist</p>
     * <p>2. Open My Wishlist</p>
     * <p>3. Click "Share Wishlist" button</p>
     * <p>4. Enter a valid email and a message</p>
     * <p>5. Click "Share Wishlist" button
     * <p>Expected result:</p>
     * <p>The success message is displayed</p>
     *
     * @depends createCustomer
     * @depends createProductSimple
     * @dataProvider dataToShare
     * @test
     */
    public function shareWishlist($customer, $simpleProductName, $shareData)
    {
        //Setup
        $shareData = $this->loadData('share_data', null, $shareData);
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->wishlistHelper()->addProductToWishlistFromProductPage($this->productSimple);
        //Steps
        $this->navigate('my_wishlist');
        $this->wishlistHelper()->frontShareWishlist($shareData);
        //Verify
        $this->assertTrue($this->successMessage('successfully_shared_wishlist'), $this->messages);
        //Cleanup
    }

    public function dataToShare()
    {
        return array(
            array('emails' => 'test@test.com', 'message' => 'test message'),
            array('message' => ''),
        );
    }

    /**
     * <p>Shares My Wishlist with invalid email(s) provided</p>
     * <p>Steps:</p>
     * <p>1. Add a product to the wishlist</p>
     * <p>2. Open My Wishlist</p>
     * <p>3. Click "Share Wishlist" button</p>
     * <p>4. Enter an invalid email and a message</p>
     * <p>5. Click "Share Wishlist" button
     * <p>Expected result:</p>
     * <p>An error message is displayed</p>
     *
     * @depends createCustomer
     * @depends createProductSimple
     * @dataProvider dataInvalidEmail
     * @test
     */
    public function shareWishlistWithInvalidEmail($customer, $simpleProductName, $emails)
    {
        //Setup
        $shareData = $this->loadData('share_data', null, array('emails' => $emails));
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->wishlistHelper()->addProductToWishlistFromProductPage($this->productSimple);
        //Steps
        $this->navigate('my_wishlist');
        $this->wishlistHelper()->frontShareWishlist($shareData);
        //Verify
        $this->assertTrue($this->successMessage('invalid_emails'), $this->messages);
        //Cleanup
    }

    public function dataInvalidEmail()
    {
        return array(
            array('emails' => '@test.com'),
            array('emails' => 'test@'),
            array('emails' => 'test@test.com, test2@'),
        );
    }

    /**
     * <p>Shares My Wishlist with empty email provided</p>
     * <p>Steps:</p>
     * <p>1. Add a product to the wishlist</p>
     * <p>2. Open My Wishlist</p>
     * <p>3. Click "Share Wishlist" button</p>
     * <p>4. Enter an invalid email and a message</p>
     * <p>5. Click "Share Wishlist" button
     * <p>Expected result:</p>
     * <p>An error message is displayed</p>
     *
     * @depends createCustomer
     * @depends createProductSimple
     * @test
     */
    public function shareWishlistWithEmptyEmail($customer, $simpleProductName)
    {
        //Setup
        $shareData = $this->loadData('share_data', 'emails');
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->wishlistHelper()->addProductToWishlistFromProductPage($this->productSimple);
        //Steps
        $this->navigate('my_wishlist');
        $this->wishlistHelper()->frontShareWishlist($shareData);
        //Verify
        $this->assertTrue($this->successMessage('required_emails'), $this->messages);
        //Cleanup
    }

}
