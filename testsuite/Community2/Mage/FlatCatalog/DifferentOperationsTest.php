<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_FlatCatalog
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configure Flat Catalog in System Configuration tests
 * @package     Mage_FlatCatalog
 * @subpackage  functional_tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_FlatCatalog_DifferentOperationsTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $flatCatalogData =
            $this->loadDataSet('FlatCatalog', 'flat_catalog_frontend', array('use_flat_catalog_product' => 'Yes'));
        $this->systemConfigurationHelper()->configure($flatCatalogData);
        $this->reindexInvalidedData();
    }

    protected function tearDownAfterTest()
    {
        $this->frontend();
        if ($this->controlIsPresent('link', 'log_out')) {
            $this->navigate('my_wishlist');
            $this->wishlistHelper()->frontClearWishlist();
            $this->shoppingCartHelper()->frontClearShoppingCart();
            $this->logoutCustomer();
        }
    }

    public function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $flatCatalogData =
            $this->loadDataSet('FlatCatalog', 'flat_catalog_frontend', array('use_flat_catalog_product' => 'No'));
        $this->systemConfigurationHelper()->configure($flatCatalogData);
        $this->reindexInvalidedData();
    }

    /**
     * Create all types of products
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        //Data
        $category = $this->loadDataSet('Category', 'sub_category_required');
        $catPath = $category['parent_category'] . '/' . $category['name'];
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $attrCode = $attrData['attribute_code'];
        $associatedAttributes = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('General' => $attrData['attribute_code']));
        $productCat = array('categories' => $catPath);
        $simple = $this->loadDataSet('Product', 'simple_product_visible', $productCat);
        $simple['general_user_attr']['dropdown'][$attrCode] = $attrData['option_1']['admin_option_name'];
        $virtual = $this->loadDataSet('Product', 'virtual_product_visible', $productCat);
        $virtual['general_user_attr']['dropdown'][$attrCode] = $attrData['option_2']['admin_option_name'];
        $download = $this->loadDataSet('SalesOrder', 'downloadable_product_for_order',
            array('downloadable_links_purchased_separately' => 'No',
                'categories' => $catPath));
        $download['general_user_attr']['dropdown'][$attrCode] = $attrData['option_3']['admin_option_name'];
        $bundle = $this->loadDataSet('SalesOrder', 'fixed_bundle_for_order', $productCat,
            array('add_product_1' => $simple['general_sku'],
                'add_product_2' => $virtual['general_sku']));
        $configurable = $this->loadDataSet('SalesOrder', 'configurable_product_for_order',
            array('configurable_attribute_title' => $attrData['admin_title'],
                'categories'   => $catPath), array('associated_1' => $simple['general_sku'],
                'associated_2' => $virtual['general_sku'],
                'associated_3' => $download['general_sku']));
        $grouped = $this->loadDataSet('SalesOrder', 'grouped_product_for_order', $productCat,
            array('associated_1' => $simple['general_sku'],
                'associated_2' => $virtual['general_sku'],
                'associated_3' => $download['general_sku']));
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $configurableOptionName = $attrData['option_1']['store_view_titles']['Default Store View'];
        $customOptions = $this->loadDataSet('Product', 'custom_options_data');
        $simpleWithCustomOptions =
            $this->loadDataSet('Product', 'simple_product_visible', array('categories' => $catPath,
                'custom_options_data' => $customOptions));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        //Verifying
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_category');
        //Steps
        $this->navigate('manage_products');
        $allProducts = array('simple'           => $simple,
                             'virtual'          => $virtual,
                             'downloadable'     => $download,
                             'bundle'           => $bundle,
                             'configurable'     => $configurable,
                             'grouped'          => $grouped);
        //Steps
        foreach ($allProducts as $key => $value) {
            //Verifying
            $this->productHelper()->createProduct($value, $key);
        }
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->createProduct($simpleWithCustomOptions);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->flushCache();
        $this->reindexInvalidedData();

        return array('productNames' => array('simple' => $simple['general_name'],
                                             'virtual'          => $virtual['general_name'],
                                             'bundle'           => $bundle['general_name'],
                                             'downloadable'     => $download['general_name'],
                                             'configurable'     => $configurable['general_name'],
                                             'grouped'          => $grouped['general_name']),
                                             'configurableOption' => array('title' => $attrData['admin_title'],
                                                 'custom_option_dropdown'=> $configurableOptionName),
                                             'groupedOption'      => array('subProduct_1' => $simple['general_name'],
                                                 'subProduct_2' => $virtual['general_name'],
                                                 'subProduct_3' => $download['general_name']),
                                             'bundleOption'       => array('subProduct_1' => $simple['general_name'],
                                                 'subProduct_2' => $virtual['general_name'],
                                                 'subProduct_3' => $simple['general_name'],
                                                 'subProduct_4' => $virtual['general_name']),
                                             'user'               => array('email'    => $userData['email'],
                                                 'password' => $userData['password']),
                                             'withCustomOption'   => $simpleWithCustomOptions['general_name'],
                                             'catName'            => $category['name'],
                                             'catPath'            => $catPath);
    }

    /**
     * <p>Add products to Wishlist from Product Details page. For all types without additional options.</p>
     * <p>Steps:</p>
     * <p>1. Open product</p>
     * <p>2. Add product to wishlist</p>
     * <p>Expected result:</p>
     * <p>Success message is displayed</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2020, TL-MAGE-2021, TL-MAGE-1998, TL-MAGE-2000, TL-MAGE-2002
     * @author artem.anokhin
     * @group skip_due_to_bug
     * MAGETWO-2829
     */
    public function addProductsWithoutOptionsToWishlistFromProductPage($testData)
    {
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        //Verifying
        foreach ($testData['productNames'] as $productName) {
            $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($productName);
            $this->assertMessagePresent('success', 'successfully_added_product');
            $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($productName),
                'Product ' . $productName . ' is not added to wishlist.');
        }
        //Verifying
        $this->navigate('my_wishlist');
        foreach ($testData['productNames'] as $productName) {
            $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($productName),
                'Product ' . $productName . ' is not in the wishlist.');
        }
    }

    /**
     * <p>Add products to Wishlist from Category page. For all types without additional options.</p>
     * <p>Steps:</p>
     * <p>1. Open category</p>
     * <p>2. Find product</p>
     * <p>3. Add product to wishlist</p>
     * <p>Expected result:</p>
     * <p>Success message is displayed</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2020, TL-MAGE-2021, TL-MAGE-1998, TL-MAGE-2000, TL-MAGE-2002
     * @author artem.anokhin
     * @group skip_due_to_bug
     * MAGETWO-2829
     */
    public function addProductsWithoutOptionsToWishlistFromCatalog($testData)
    {
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        //Verifying
        foreach ($testData['productNames'] as $productName) {
            $this->wishlistHelper()->frontAddProductToWishlistFromCatalogPage($productName, $testData['catName']);
            $this->assertMessagePresent('success', 'successfully_added_product');
            $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($productName),
                'Product ' . $productName . ' is not added to wishlist.');
        }
        $this->navigate('my_wishlist');
        foreach ($testData['productNames'] as $productName) {
            $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($productName),
                'Product ' . $productName . ' is not in the wishlist.');
        }
    }

    /**
     * <p>Adds products to Shopping Cart from Wishlist. For all product types without custom options
     *    (simple, virtual, downloadable)</p>
     * <p>Steps:</p>
     * <p>1. Empty the shopping cart</p>
     * <p>2. Add a product to the wishlist</p>
     * <p>3. Open the wishlist</p>
     * <p>4. Click 'Add to Cart' button for each product</p>
     * <p>Expected result:</p>
     * <p>The products are in the shopping cart</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2020, TL-MAGE-2021, TL-MAGE-1998, TL-MAGE-2000, TL-MAGE-2002
     * @author artem.anokhin
     * @group skip_due_to_bug
     * MAGETWO-2829
     */
    public function addProductsWithoutOptionsToShoppingCartFromWishlist($testData)
    {
        //Data
        $products = array($testData['productNames']['simple'], $testData['productNames']['downloadable'],
            $testData['productNames']['virtual']);
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        //Verifying
        foreach ($products as $productName) {
            $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($productName);
            $this->assertMessagePresent('success', 'successfully_added_product');
            $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($productName),
                'Product ' . $productName . ' is not added to wishlist.');
        }
        foreach ($products as $productName) {
            $this->navigate('my_wishlist');
            $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($productName),
                'Product ' . $productName . ' is not in the wishlist.');
            $this->wishlistHelper()->frontAddToShoppingCartFromWishlist($productName);
            $this->assertTrue($this->shoppingCartHelper()->frontShoppingCartHasProducts($productName),
                'Product ' . $productName . ' is not in the shopping cart.');
        }
    }

    /**
     * <p>Adds products to Shopping Cart from Wishlist. For all product types with additional options
     *    (downloadable, configurable, bundle, grouped)</p>
     * <p>Steps:</p>
     * <p>1. Empty the shopping cart</p>
     * <p>2. Add a product to the wishlist</p>
     * <p>3. Open the wishlist</p>
     * <p>4. Click 'Add to Cart' button for each product</p>
     * <p>Expected result:</p>
     * <p>The products are not in the shopping cart.
     *    Message 'Please specify the product's option(s) is displayed'</p>
     *
     * @param array $testData
     * @param string $product
     * @param string $option
     *
     * @test
     * @dataProvider productsWithOptionsDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2000, TL-MAGE-2002, TL-MAGE-1998, TL-MAGE-2001
     * @author artem.anokhin
     * @group skip_due_to_bug
     * MAGETWO-2829
     */
    public function addProductsWithOptionsToShoppingCartFromWishlist($product, $option, $testData)
    {
        //Data
        $productName = $testData['productNames'][$product];
        if (isset($testData[$product . 'Option'])) {
            if ($product == 'configurable') {
                $options = $this->loadDataSet('Product', $option, $testData[$product . 'Option']);
            } else {
                $options = $this->loadDataSet('Product', $option, null, $testData[$product . 'Option']);
            }
        } else {
            $options = $this->loadDataSet('Product', $option);
        }
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($productName, $options);
        //Verifying
        $this->assertMessagePresent('success', 'successfully_added_product');
        //Steps
        $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($productName),
            'Product ' . $productName . ' is not in the wishlist.');
        $this->wishlistHelper()->frontAddToShoppingCartFromWishlist($productName);
        if ($product == 'grouped') {
            foreach ($testData[$product . 'Option'] as $name) {
                $this->assertTrue($this->shoppingCartHelper()->frontShoppingCartHasProducts($name),
                    'Product ' . $name . ' is not in the shopping cart.');
            }
        } else {
            $this->assertTrue($this->shoppingCartHelper()->frontShoppingCartHasProducts($productName),
                'Product ' . $productName . ' is not in the shopping cart.');
        }
    }

    public function productsWithOptionsDataProvider()
    {
        return array(
            array('configurable', 'configurable_options_to_add_to_shopping_cart'),
            array('bundle', 'bundle_options_to_add_to_shopping_cart'),
            array('grouped', 'grouped_options_to_add_to_shopping_cart')
        );
    }

    /**
     * <p>Add all types of products to Wishlist from Shopping Cart.</p>
     * <p>Steps:</p>
     * <p>1. Add products to the shopping cart</p>
     * <p>2. Move the products to wishlist</p>
     * <p>3. Open the wishlist</p>
     * <p>Expected result:</p>
     * <p>Products are in the wishlist</p>
     *
     * @param array $testData
     * @param string $product
     * @param string $option
     *
     * @test
     * @dataProvider productsWithOptionsDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2000, TL-MAGE-2002, TL-MAGE-1998, TL-MAGE-2001
     * @author artem.anokhin
     * @group skip_due_to_bug
     * MAGETWO-2829
     */
    public function addProductWithOptionsToWishlistFromShoppingCart($product, $option, $testData)
    {
        //Data
        $productName = $testData['productNames'][$product];
        if (isset($testData[$product . 'Option'])) {
            if ($product == 'configurable') {
                $options = $this->loadDataSet('Product', $option, $testData[$product . 'Option']);
            } else {
                $options = $this->loadDataSet('Product', $option, null, $testData[$product . 'Option']);
            }
        } else {
            $options = $this->loadDataSet('Product', $option);
        }
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->productHelper()->frontOpenProduct($productName);
        $this->productHelper()->frontAddProductToCart($options);
        //Verifying
        if ($product == 'grouped') {
            foreach ($testData[$product . 'Option'] as $name) {
                $this->navigate('shopping_cart');
                $this->assertTrue($this->shoppingCartHelper()->frontShoppingCartHasProducts($name),
                    'Product ' . $name . ' is not in the shopping cart.');
                $this->shoppingCartHelper()->frontMoveToWishlist($name);
                $this->navigate('my_wishlist');
                $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($name),
                    'Product ' . $name . ' is not in the wishlist.');
            }
        } else {
            $this->assertTrue($this->checkCurrentPage('shopping_cart'));
            $this->assertTrue($this->shoppingCartHelper()->frontShoppingCartHasProducts($productName),
                'Product ' . $productName . ' is not in the shopping cart.');
            $this->shoppingCartHelper()->frontMoveToWishlist($productName);
            $this->navigate('my_wishlist');
            $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($productName),
                'Product ' . $productName . ' is not in the wishlist.');
        }
    }

    /**
     * <p>Adds a product with custom options to Wishlist from Product Details page</p>
     * <p>Steps:</p>
     * <p>1. Open product</p>
     * <p>2. Add product to wishlist</p>
     * <p>Expected result:</p>
     * <p>Success message is displayed</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2004
     * @author artem.anokhin
     * @group skip_due_to_bug
     * MAGETWO-2829
     */
    public function addProductWithCustomOptionsToWishlist($testData)
    {
        //Data
        $product = $testData['withCustomOption'];
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($product);
        //Verifying
        $this->assertMessagePresent('success', 'successfully_added_product');
        $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($product),
            'Product ' . $product . ' is not added to wishlist.');
    }

    /**
     * <p>Adds a product with custom options to Shopping Cart from Wishlist without selected options</p>
     * <p>Steps:</p>
     * <p>1. Open product</p>
     * <p>2. Add product to wishlist</p>
     * <p>3. Open wishlist</p>
     * <p>4. Add product to Shopping Cart</p>
     * <p>Expected result:</p>
     * <p>Success message is displayed. Product is added to Shopping Cart</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2004
     * @author artem.anokhin
     * @group skip_due_to_bug
     * MAGETWO-2829
     */
    public function addProductWithCustomOptionsToShoppingCartFromWishlist($testData)
    {
        //Data
        $productName = $testData['withCustomOption'];
        $options = $this->loadDataSet('Product', 'custom_options_to_add_to_shopping_cart');
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($productName, $options);
        $this->assertMessagePresent('success', 'successfully_added_product');
        //Verifying
        $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($productName),
            'Product ' . $productName . ' is not in the wishlist.');
        $this->wishlistHelper()->frontAddToShoppingCartFromWishlist($productName);
        $this->assertTrue($this->shoppingCartHelper()->frontShoppingCartHasProducts($productName),
            'Product ' . $productName . ' is not in the shopping cart.');
    }

    /**
     * <p>Add simple product with custom options to Wishlist from Shopping Cart.</p>
     * <p>Steps:</p>
     * <p>1. Add product to the shopping cart</p>
     * <p>2. Move the product to wishlist</p>
     * <p>3. Open the wishlist</p>
     * <p>Expected result:</p>
     * <p>Product is in the wishlist</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2004
     * @author artem.anokhin
     * @group skip_due_to_bug
     * MAGETWO-2829
     */
    public function addProductWithCustomOptionsToWishlistFromShoppingCart($testData)
    {
        //Data
        $productName = $testData['withCustomOption'];
        $options = $this->loadDataSet('Product', 'custom_options_to_add_to_shopping_cart');
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->productHelper()->frontOpenProduct($productName);
        $this->productHelper()->frontAddProductToCart($options);
        //Verifying
        $this->assertTrue($this->shoppingCartHelper()->frontShoppingCartHasProducts($productName),
            'Product ' . $productName . ' is not in the shopping cart.');
        $this->shoppingCartHelper()->frontMoveToWishlist($productName);
        $this->navigate('my_wishlist');
        //Verifying
        $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($productName),
            'Product ' . $productName . ' is not in the wishlist.');
    }
}