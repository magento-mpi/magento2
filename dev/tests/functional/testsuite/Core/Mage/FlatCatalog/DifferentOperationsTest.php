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
class Core_Mage_FlatCatalog_DifferentOperationsTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->reindexInvalidedData();
        $this->navigate('system_configuration');
        $flatCatalogData = $this->loadDataSet('FlatCatalog', 'flat_catalog_frontend',
            array('use_flat_catalog_product' => 'Yes'));
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
        $flatCatalogData = $this->loadDataSet('FlatCatalog', 'flat_catalog_frontend',
            array('use_flat_catalog_product' => 'No'));
        $this->systemConfigurationHelper()->configure($flatCatalogData);
        $this->reindexInvalidedData();
    }

    /**
     * <p>Create all types of products</p>
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
            array('Product Details' => $attrData['attribute_code']));
        $productCat = array('general_categories' => $catPath);
        $simple = $this->loadDataSet('Product', 'simple_product_visible', $productCat);
        $simple['general_user_attr']['dropdown'][$attrCode] = $attrData['option_1']['admin_option_name'];
        $virtual = $this->loadDataSet('Product', 'virtual_product_visible', $productCat);
        $virtual['general_user_attr']['dropdown'][$attrCode] = $attrData['option_2']['admin_option_name'];
        $download = $this->loadDataSet('SalesOrder', 'downloadable_product_for_order',
            array('downloadable_links_purchased_separately' => 'No', 'general_categories' => $catPath));
        $download['general_user_attr']['dropdown'][$attrCode] = $attrData['option_3']['admin_option_name'];
        $bundle = $this->loadDataSet('SalesOrder', 'fixed_bundle_for_order', $productCat,
            array('add_product_1' => $simple['general_sku'], 'add_product_2' => $virtual['general_sku']));
        $configurable = $this->loadDataSet('SalesOrder', 'configurable_product_for_order',
            array('general_categories' => $catPath),
            array(
                'general_attribute_1' => $attrData['attribute_label'],
                'associated_3' => $download['general_sku'],
                'var1_attr_value1' => $attrData['option_1']['admin_option_name'],
                'var1_attr_value2' => $attrData['option_2']['admin_option_name'],
                'var1_attr_value3' => $attrData['option_3']['admin_option_name']
            )
        );
        $grouped = $this->loadDataSet('SalesOrder', 'grouped_product_for_order', $productCat, array(
            'associated_1' => $simple['general_sku'],
            'associated_2' => $virtual['general_sku'],
            'associated_3' => $download['general_sku']
        ));
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $configurableOptionName = $attrData['option_1']['store_view_titles']['Default Store View'];
        $customOptions = $this->loadDataSet('Product', 'custom_options_data');
        $simpleWithCO = $this->loadDataSet('Product', 'simple_product_visible', array(
            'general_categories' => $catPath,
            'custom_options_data' => $customOptions
        ));
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
        $this->runMassAction('Delete', 'all');
        $allProducts = array(
            'simple' => $simple,
            'virtual' => $virtual,
            'downloadable' => $download,
            'grouped' => $grouped,
            'bundle' => $bundle,
            'configurable' => $configurable,
        );
        //Steps
        foreach ($allProducts as $key => $value) {
            $this->productHelper()->createProduct($value, $key);
            $this->assertMessagePresent('success', 'success_saved_product');
        }
        //Steps
        $this->productHelper()->createProduct($simpleWithCO);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->flushCache();
        $this->reindexInvalidedData();
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();

        return array(
            'productNames' => array(
                'simple' => $simple['general_name'],
                'virtual' => $virtual['general_name'],
                'bundle' => $bundle['general_name'],
                'downloadable' => $download['general_name'],
                'configurable' => $configurable['general_name'],
                'grouped' => $grouped['general_name']
            ),
            'configurableOption' => array(
                'title'                  => $attrData['attribute_label'],
                'custom_option_dropdown' => $configurableOptionName
            ),
            'groupedOption' => array(
                'subProduct_1' => $simple['general_name'],
                'subProduct_2' => $virtual['general_name'],
                'subProduct_3' => $download['general_name']
            ),
            'bundleOption' => array(
                'subProduct_1' => $simple['general_name'],
                'subProduct_2' => $virtual['general_name'],
                'subProduct_3' => $simple['general_name'],
                'subProduct_4' => $virtual['general_name']
            ),
            'user' => array(
                'email' => $userData['email'],
                'password' => $userData['password']
            ),
            'withCustomOption' => $simpleWithCO['general_name'],
            'catName' => $category['name'],
            'catPath' => $catPath
        );
    }

    /**
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2020, TL-MAGE-2021, TL-MAGE-1998, TL-MAGE-2000, TL-MAGE-2002
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
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2020, TL-MAGE-2021, TL-MAGE-1998, TL-MAGE-2000, TL-MAGE-2002
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
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2020, TL-MAGE-2021, TL-MAGE-1998, TL-MAGE-2000, TL-MAGE-2002
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
     * @param array $testData
     * @param string $product
     * @param string $option
     *
     * @test
     * @dataProvider productsWithOptionsDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2000, TL-MAGE-2002, TL-MAGE-1998, TL-MAGE-2001
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
     * @param array $testData
     * @param string $product
     * @param string $option
     *
     * @test
     * @dataProvider productsWithOptionsDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2000, TL-MAGE-2002, TL-MAGE-1998, TL-MAGE-2001
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
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2004
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
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2004
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
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-2004
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