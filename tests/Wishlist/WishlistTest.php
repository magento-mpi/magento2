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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
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
    }

    /**
     * <p>Preconditions:</p>
     */
    protected function assertPreConditions()
    {
        $this->addParameter('id', '0');
    }

    /**
     * <p>Preconditions</p>
     * <p>Create a new customer for tests</p>
     * @return array Customer 'email' and 'password'
     * @test
     */
    public function createCustomer()
    {
        $this->loginAdminUser();
        $userData = $this->loadData('customer_account_for_prices_validation', null, 'email');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        return array('email'    => $userData['email'],
                     'password' => $userData['password']);
    }

    /**
     * <p>Preconditions</p>
     * <p>Creates Category to use during tests</p>
     * @return array Category 'name' and 'path'
     * @test
     */
    public function createCategory()
    {
        //Data
        $rootCategoryData = $this->loadData('root_category_required');
        $subCategoryData = $this->loadData('sub_category_required',
                                           array('parent_category' => $rootCategoryData['name']));
        //Steps and Verification
        $this->loginAdminUser();
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($rootCategoryData);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->createCategory($subCategoryData);
        $this->assertMessagePresent('success', 'success_saved_category');

        return array('name' => $subCategoryData['name'],
                     'path' => $rootCategoryData['name'] . '/' . $subCategoryData['name']);
    }

    /**
     * <p>Creating configurable product</p>
     * @return array
     * @test
     */
    public function createConfigurableAttribute()
    {
        //Data
        $attrData = $this->loadData('product_attribute_dropdown_with_options',
                                    null, array('admin_title', 'attribute_code'));
        $associatedAttributes = $this->loadData('associated_attributes',
                                                array('General' => $attrData['attribute_code']));
        //Steps
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        //Verifying
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        return $attrData;
    }

    /**
     * <p>Create a new product of the specified type</p>
     *
     * @param array $productData Product data to fill in backend
     * @param null|string $productType E.g. 'simple'|'configurable' etc.
     *
     * @return array $productData

     */
    protected function createProduct(array $productData, $productType)
    {
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, $productType);
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * <p>Preconditions</p>
     * <p>Create a simple product within a category</p>
     * @depends createCategory
     *
     * @param array $categoryData
     *
     * @test
     */
    public function createProductSimple($categoryData)
    {
        $productData = $this->loadData('simple_product_visible',
                                       array('categories' => $categoryData['path']),
                                       array('general_name', 'general_sku'));
        $productSimple = $this->createProduct($productData, 'simple');
        return $productSimple['general_name'];
    }

    /**
     * <p>Preconditions</p>
     * <p>Create products of all types for the tests without custom options</p>
     * @depends createConfigurableAttribute
     *
     * @param array $attrData
     *
     * @return array Array of product names
     * @test
     */
    public function createAllProductsWithoutCustomOptions($attrData)
    {
        // Create simple product, so that it can be used in Configurable product.
        $simpleData = $this->loadData('simple_product_visible', null, array('general_name', 'general_sku'));
        $productSimple = $this->createProduct($simpleData, 'simple');
        // Create a configurable product
        $productData = $this->loadData('configurable_product_visible',
                                       array('associated_configurable_data' => '%noValue%',
                                            'configurable_attribute_title'  => $attrData['admin_title']),
                                       array('general_sku', 'general_name'));
        $productConfigurable = $this->createProduct($productData, 'configurable');
        //Create a virtual product
        $productData = $this->loadData('virtual_product_visible', null, array('general_name', 'general_sku'));
        $productVirtual = $this->createProduct($productData, 'virtual');
        //Create a downloadable product
        $productData = $this->loadData('downloadable_product_visible',
                                       array('downloadable_information_data' => '%noValue%'),
                                       array('general_name', 'general_sku'));
        $productDownloadable = $this->createProduct($productData, 'downloadable');
        //Create a grouped product
        $productData = $this->loadData('grouped_product_visible',
                                       array('associated_grouped_data' => '%noValue%'),
                                       array('general_name', 'general_sku'));
        $productGrouped = $this->createProduct($productData, 'grouped');
        //Create a bundle product
        $productData = $this->loadData('fixed_bundle_visible',
                                       array('bundle_items_data' => '%noValue%'), array('general_name', 'general_sku'));
        $productBundle = $this->createProduct($productData, 'bundle');

        $allProducts = array('simple'       => $productSimple,
                             'virtual'      => $productVirtual,
                             'downloadable' => $productDownloadable,
                             'grouped'      => $productGrouped,
                             'configurable' => $productConfigurable,
                             'bundle'       => $productBundle);
        return $allProducts;
    }

    /**
     * <p>Preconditions</p>
     * <p>Create products of all types for the tests with custom options</p>
     * @depends createConfigurableAttribute
     *
     * @param array $attrData
     *
     * @return array Array of product names
     * @test
     */
    public function createAllProductsWithCustomOptions($attrData)
    {
        // Create simple product, so that it can be used in Configurable product.
        $simpleData = $this->loadData('simple_product_visible', null, array('general_name', 'general_sku'));
        $simpleData['general_user_attr']['dropdown'][$attrData['attribute_code']] =
            $attrData['option_1']['admin_option_name'];
        $productSimple = $this->createProduct($simpleData, 'simple');
        // Create a configurable product
        $productData = $this->loadData('configurable_product_visible',
                                       array('configurable_attribute_title' => $attrData['admin_title']),
                                       array('general_sku', 'general_name'));
        $productData['associated_configurable_data'] = $this->loadData('associated_configurable_data',
                                                                       array('associated_search_sku' => $simpleData['general_sku']));
        $productConfigurable = $this->createProduct($productData, 'configurable');
        //Create a virtual product
        $productData = $this->loadData('virtual_product_visible', null, array('general_name', 'general_sku'));
        $productVirtual = $this->createProduct($productData, 'virtual');
        //Create a downloadable product
        $productData = $this->loadData('downloadable_product_visible', null, array('general_name', 'general_sku'));
        $productDownloadable = $this->createProduct($productData, 'downloadable');
        //Create a grouped product
        $productData = $this->loadData('grouped_product_visible',
                                       array('associated_search_name'        => $simpleData['general_name'],
                                            'associated_product_default_qty' => '3'),
                                       array('general_name', 'general_sku'));
        $productGrouped = $this->createProduct($productData, 'grouped');
        //Create a bundle product
        $productData = $this->loadData('fixed_bundle_visible', null, array('general_name', 'general_sku'));
        $productData['bundle_items_data']['item_1'] = $this->loadData('bundle_item_1',
                                                                      array('bundle_items_search_sku' => $simpleData['general_sku']));
        $productBundle = $this->createProduct($productData, 'bundle');

        $allProducts = array('simple'       => $productSimple,
                             'virtual'      => $productVirtual,
                             'downloadable' => $productDownloadable,
                             'grouped'      => $productGrouped,
                             'configurable' => $productConfigurable,
                             'bundle'       => $productBundle);
        return $allProducts;
    }

    /**
     * @param array $productDataSet Array of product data
     *
     * @return array Array of product names
     */
    private function _getProductNames($productDataSet)
    {
        $productNamesSet = array();
        foreach ($productDataSet as $productData) {
            $productNamesSet[] = $productData['general_name'];
        }
        return $productNamesSet;
    }

    /**
     * <p>Adds a product to Wishlist from Product Details page. For all products with custom options</p>
     * <p>Steps:</p>
     * <p>1. Open product</p>
     * <p>2. Add product to wishlist</p>
     * <p>Expected result:</p>
     * <p>Success message is displayed</p>
     * @depends createCustomer
     * @depends createAllProductsWithCustomOptions
     *
     * @param array $customer
     * @param array $productDataSet
     *
     * @test
     */
    public function addProductsWithCustomOptionsToWishlistFromProductPage($customer, $productDataSet)
    {
        //Setup
        $productNameSet = $this->_getProductNames($productDataSet);
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->navigate('my_wishlist');
        $this->wishlistHelper()->frontClearWishlist();
        //Steps
        foreach ($productNameSet as $productName) {
            $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($productName);
            $this->assertMessagePresent('success', 'successfully_added_product');
        }
        //Verify
        $this->navigate('my_wishlist');
        foreach ($productNameSet as $productName) {
            $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($productName),
                              'Product ' . $productName . ' is not in the wishlist.');
        }
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
     * @depends createCustomer
     * @depends createAllProductsWithCustomOptions
     *
     * @param array $customer
     * @param $productDataSet
     *
     * @internal param string $simpleProductName
     * @test
     */
    public function removeProductsFromWishlist($customer, $productDataSet)
    {
        //Setup
        $productNameSet = $this->_getProductNames($productDataSet);
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->navigate('my_wishlist');
        $this->wishlistHelper()->frontClearWishlist();
        foreach ($productNameSet as $productName) {
            $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($productName);
            $this->assertMessagePresent('success', 'successfully_added_product');
        }
        //Steps
        $lastProductName = end($productNameSet);
        array_pop($productNameSet);
        foreach ($productNameSet as $productName) {
            $this->wishlistHelper()->frontRemoveProductsFromWishlist($productName); // Remove all but last
            //Verify
            $this->assertTrue(is_array($this->wishlistHelper()->frontWishlistHasProducts($productName)),
                              'Product ' . $productName . ' is in the wishlist, but should be removed.');
        }
        //Steps
        $this->wishlistHelper()->frontRemoveProductsFromWishlist($lastProductName); //Remove the last one
        //Verify
        $this->assertTrue($this->controlIsPresent('pageelement', 'no_items'), $this->getParsedMessages());
        //Cleanup
    }

    /**
     * <p>Adds a product to Wishlist from Product Details page. For all types without custom options.</p>
     * <p>Steps:</p>
     * <p>1. Open product</p>
     * <p>2. Add product to wishlist</p>
     * <p>Expected result:</p>
     * <p>Success message is displayed</p>
     *      *
     * @depends createCustomer
     * @depends createAllProductsWithoutCustomOptions
     *
     * @param array $customer
     * @param array $productDataSet
     *
     * @test
     *
     * @group skip_due_to_bug
     */
    public function addProductsWithoutCustomOptionsToWishlistFromProductPage($customer, $productDataSet)
    {
        //Setup
        $productNameSet = $this->_getProductNames($productDataSet);
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->navigate('my_wishlist');
        $this->wishlistHelper()->frontClearWishlist();
        //Steps
        foreach ($productNameSet as $productName) {
            $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($productName);
            $this->assertMessagePresent('success', 'successfully_added_product');
        }
        //Verify
        $this->navigate('my_wishlist');
        foreach ($productNameSet as $productName) {
            $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($productName),
                              'Product ' . $productName . ' is not in the wishlist.');
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
     * @depends createCustomer
     * @depends createCategory
     * @depends createProductSimple
     *
     * @param array $customer
     * @param array $categoryData
     * @param string $simpleProductName
     *
     * @test
     */
    public function addProductToWishlistFromCatalog($customer, $categoryData, $simpleProductName)
    {
        //Setup
        $categoryName = $categoryData['name'];
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->navigate('my_wishlist');
        $this->wishlistHelper()->frontClearWishlist();
        //Steps
        $this->wishlistHelper()->frontAddProductToWishlistFromCatalogPage($simpleProductName, $categoryName);
        //Verify
        $this->navigate('my_wishlist');
        $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($simpleProductName),
                          'Product ' . $simpleProductName . ' is not in the wishlist.');
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
     * @depends createCustomer
     * @depends createProductSimple
     *
     * @param array $customer
     * @param string $simpleProductName
     *
     * @test
     */
    public function addProductToWishlistFromShoppingCart($customer, $simpleProductName)
    {
        //Setup
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->navigate('my_wishlist');
        $this->wishlistHelper()->frontClearWishlist();
        //Steps
        $this->productHelper()->frontOpenProduct($simpleProductName);
        $this->productHelper()->frontAddProductToCart();
        $this->shoppingCartHelper()->frontMoveToWishlist($simpleProductName);
        //Verify
        $this->navigate('my_wishlist');
        $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($simpleProductName),
                          'Product ' . $simpleProductName . ' is not in the wishlist.');
        //Cleanup
    }

    /**
     * <p>Adds products to Shopping Cart from Wishlist. For all product types without custom options</p>
     * <p>Steps:</p>
     * <p>1. Empty the shopping cart</p>
     * <p>2. Add a product to the wishlist</p>
     * <p>3. Open the wishlist</p>
     * <p>4. Click 'Add to Cart' button for each product</p>
     * <p>Expected result:</p>
     * <p>The products are in the shopping cart</p>
     * @depends createCustomer
     * @depends createAllProductsWithoutCustomOptions
     *
     * @param array $customer
     * @param array $productDataSet
     *
     * @depends addProductsWithoutCustomOptionsToWishlistFromProductPage
     * @test
     */
    public function addProductsWithoutCustomOptionsToShoppingCartFromWishlist($customer, $productDataSet)
    {
        //Setup
        $productNameSet = $this->_getProductNames($productDataSet);
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->navigate('shopping_cart');
        $this->shoppingCartHelper()->frontClearShoppingCart();
        foreach ($productNameSet as $productName) {
            $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($productName);
            $this->assertMessagePresent('success', 'successfully_added_product');
        }
        //Steps
        $this->navigate('my_wishlist');
        $this->wishlistHelper()->frontAddToShoppingCart($productNameSet);
        //Verify
        $this->assertTrue($this->checkCurrentPage('shopping_cart'), $this->getParsedMessages());
        foreach ($productNameSet as $productName) {
            $this->assertTrue($this->shoppingCartHelper()->frontShoppingCartHasProducts($productName),
                              'Product ' . $productName . ' is not in the shopping cart.');
        }
        //Cleanup
    }

    /**
     * <p>Adds products to Shopping Cart from Wishlist. For all product types with custom options</p>
     * <p>Steps:</p>
     * <p>1. Empty the shopping cart</p>
     * <p>2. Add a product to the wishlist, fill its custom options</p>
     * <p>3. Open the wishlist</p>
     * <p>4. Click 'Add to Cart' button for each product</p>
     * <p>Expected result:</p>
     * <p>The products are in the shopping cart</p>
     * @depends createCustomer
     * @depends createAllProductsWithCustomOptions
     *
     * @param array $customer
     * @param array $productDataSet
     *
     * @depends addProductsWithCustomOptionsToWishlistFromProductPage
     * @test
     */
    public function addProductsWithCustomOptionsToShoppingCartFromWishlist($customer, $productDataSet)
    {
        $this->markTestIncomplete('@TODO: Need to implement Helper for filling product custom options.');
    }

    /**
     * <p>Adds products to Shopping Cart from Wishlist. For all product types with custom options</p>
     * <p>Steps:</p>
     * <p>1. Empty the shopping cart</p>
     * <p>2. Add products to the wishlist</p>
     * <p>3. Open the wishlist</p>
     * <p>4. Click 'Add All to Cart' button</p>
     * <p>Expected result:</p>
     * <p>Error messages for configurable and downloadable products are displayed.</p>
     * <p>Success message for other products is displayed.</p>
     * <p>All products except grouped, configurable and downloadable are in the shopping cart</p>
     * @depends createCustomer
     * @depends createAllProductsWithCustomOptions
     *
     * @param array $customer
     * @param array $productDataSet
     *
     * @depends addProductsWithCustomOptionsToWishlistFromProductPage
     * @test
     */
    public function addAllProductsToShoppingCartFromWishlist($customer, $productDataSet)
    {
        //Setup
        $productNameSet = $this->_getProductNames($productDataSet);
        $downloadableProductName = $productDataSet['downloadable']['general_name'];
        $configurableProductName = $productDataSet['configurable']['general_name'];
        $groupedProductName = $productDataSet['grouped']['general_name'];
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->navigate('my_wishlist');
        $this->wishlistHelper()->frontClearWishlist();
        $this->navigate('shopping_cart');
        $this->shoppingCartHelper()->frontClearShoppingCart();
        foreach ($productNameSet as $productName) {
            $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($productName);
            $this->assertMessagePresent('success', 'successfully_added_product');
        }
        //Steps
        $this->navigate('my_wishlist');
        $this->clickButton('add_all_to_cart');
        //Verify
        //Check error message for downloadable product
        $this->addParameter('productName', $downloadableProductName);
        $this->assertMessagePresent('error', 'specify_product_links');
        //Check error message for configurable product
        $this->addParameter('productName', $configurableProductName);
        $this->assertMessagePresent('error', 'specify_product_options');
        //Check success message for other products
        $this->addParameter('productQty', '4');
        $this->assertMessagePresent('success', 'successfully_added_products');
        //Check if the products are in the shopping cart
        $this->navigate('shopping_cart');
        foreach ($productNameSet as $productName) {
            if ($productName == $downloadableProductName || $productName == $configurableProductName
                || $productName == $groupedProductName
            ) {
                $this->assertTrue(is_array($this->shoppingCartHelper()->frontShoppingCartHasProducts($productName)),
                                  'Product ' . $productName . ' is in the shopping cart, but should not be.');
            } else {
                $this->assertTrue($this->shoppingCartHelper()->frontShoppingCartHasProducts($productName),
                                  'Product ' . $productName . ' is not in the shopping cart.');
            }
        }
        //Cleanup
    }

    /**
     * Grouped product is added as several simple products to the shopping cart
     * @test
     */
    public function addGroupedProductToShoppingCartFromWishlist()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Opens My Wishlist using the link in quick access bar</p>
     * <p>Steps:</p>
     * <p>1. Open home page</p>
     * <p>2. Click "My Wishlist" link</p>
     * <p>Expected result:</p>
     * <p>The wishlist is opened.</p>
     * @depends createCustomer
     *
     * @param array $customer
     *
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
        $this->assertTrue($this->checkCurrentPage('my_wishlist'), $this->getParsedMessages());
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
     * @dataProvider shareWishlistDataProvider
     * @depends createCustomer
     * @depends createProductSimple
     *
     * @param array $shareData
     * @param array $customer
     * @param string $simpleProductName
     *
     * @test
     */
    public function shareWishlist($shareData, $customer, $simpleProductName)
    {
        //Setup
        $shareData = $this->loadData('share_data', $shareData);
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($simpleProductName);
        $this->assertMessagePresent('success', 'successfully_added_product');
        //Steps
        $this->navigate('my_wishlist');
        $this->wishlistHelper()->frontShareWishlist($shareData);
        //Verify
        $this->assertMessagePresent('success', 'successfully_shared_wishlist');
        //Cleanup
    }

    public function shareWishlistDataProvider()
    {
        return array(
            array(array('emails'  => 'autotest@test.com',
                        'message' => 'autotest message')),
            array(array('message' => '')),
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
     * @dataProvider withInvalidEmailDataProvider
     * @depends createCustomer
     * @depends createProductSimple
     *
     * @param string $emails
     * @param string $errorMessage
     * @param array $customer
     * @param string $simpleProductName
     *
     * @test
     */
    public function withInvalidEmail($emails, $errorMessage, $customer, $simpleProductName)
    {
        //Setup
        $shareData = $this->loadData('share_data', array('emails' => $emails));
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($simpleProductName);
        $this->assertMessagePresent('success', 'successfully_added_product');
        $this->navigate('my_wishlist');
        $this->wishlistHelper()->frontShareWishlist($shareData);
        //Verify
        if ($errorMessage == 'invalid_emails') {
            $this->assertMessagePresent('validation', $errorMessage);
        } else {
            $this->assertMessagePresent('error', $errorMessage);
        }
        //Cleanup
    }

    public function withInvalidEmailDataProvider()
    {
        return array(
            array('email@@domain.com', 'invalid_emails_js'),
            array('.email@domain.com', 'invalid_emails'),
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
     * @depends createCustomer
     * @depends createProductSimple
     *
     * @param array $customer
     * @param string $simpleProductName
     *
     * @test
     */
    public function shareWishlistWithEmptyEmail($customer, $simpleProductName)
    {
        //Setup
        $shareData = $this->loadData('share_data', array('emails' => ''));
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($simpleProductName);
        $this->assertMessagePresent('success', 'successfully_added_product');
        //Steps
        $this->navigate('my_wishlist');
        $this->wishlistHelper()->frontShareWishlist($shareData);
        //Verify
        $this->assertMessagePresent('validation', 'required_emails');
        //Cleanup
    }

    /**
     * <p>Verifies that a guest cannot open My Wishlist.</p>
     * <p>Steps:</p>
     * <p>1. Logout customer</p>
     * <p>2. Navigate to My Wishlist</p>
     * <p>Expected result:</p>
     * <p>Guest is redirected to login/register page.</p>
     * @test
     */
    public function guestCannotOpenWishlist()
    {
        //Setup
        $this->logoutCustomer();
        //Steps
        $this->clickControl('link', 'my_wishlist');
        //Verify
        $this->assertTrue($this->checkCurrentPage('customer_login'), $this->getParsedMessages());
        //Cleanup
        $this->navigate('home'); // So that user is not redirected in further tests.
    }

    /**
     * <p>Verifies that a guest cannot add a product to a wishlist.</p>
     * <p>Steps:</p>
     * <p>1. Logout customer</p>
     * <p>2. Open a product</p>
     * <p>3. Add products to the wishlist</p>
     * <p>Expected result:</p>
     * <p>Guest is redirected to login/register page.</p>
     * @depends createProductSimple
     *
     * @param string $simpleProductName
     *
     * @test
     */
    public function guestCannotAddProductToWishlist($simpleProductName)
    {
        //Setup
        $this->logoutCustomer();
        //Steps
        $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($simpleProductName);
        //Verify
        $this->assertTrue($this->checkCurrentPage('customer_login'), $this->getParsedMessages());
        //Cleanup
        $this->navigate('home'); // So that user is not redirected in further tests.
    }
}