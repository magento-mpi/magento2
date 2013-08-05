<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Wishlist
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Wishlist_WishlistTest extends Mage_Selenium_TestCase
{
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
        $attrCode = $attrData['advanced_attribute_properties']['attribute_code'];
        $associatedAttributes = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('Product Details' => $attrData['advanced_attribute_properties']['attribute_code']));
        $productCat = array('general_categories' => $catPath);
        $simple = $this->loadDataSet('Product', 'simple_product_visible', $productCat);
        $simple['general_user_attr']['dropdown'][$attrCode] = $attrData['option_1']['admin_option_name'];
        $virtual = $this->loadDataSet('Product', 'virtual_product_visible', $productCat);
        $virtual['general_user_attr']['dropdown'][$attrCode] = $attrData['option_2']['admin_option_name'];
        $download = $this->loadDataSet('SalesOrder', 'downloadable_product_for_order',
            array('downloadable_links_purchased_separately' => 'No', 'general_categories' => $catPath));
        $download['general_user_attr']['dropdown'][$attrCode] = $attrData['option_3']['admin_option_name'];
        $downloadWithOption = $this->loadDataSet('SalesOrder', 'downloadable_product_for_order', $productCat);
        $bundle = $this->loadDataSet('SalesOrder', 'fixed_bundle_for_order', $productCat,
            array('add_product_1' => $simple['general_sku'], 'add_product_2' => $virtual['general_sku']));
        $configurable = $this->loadDataSet('SalesOrder', 'configurable_product_for_order',
            array('general_categories' => $catPath),
            array(
                'general_attribute_1' => $attrData['attribute_properties']['attribute_label'],
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
        $configurOptName = $attrData['option_1']['store_view_titles']['Default Store View'];
        $customOptions = $this->loadDataSet('Product', 'custom_options_data');
        $simpleWithCO = $this->loadDataSet('Product', 'simple_product_visible',
            array('general_categories' => $catPath, 'custom_options_data' => $customOptions));
        //Steps and Verification
        $this->loginAdminUser();
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent('success', 'success_saved_category');

        $this->navigate('manage_products');
        $this->runMassAction('Delete', 'all');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($virtual, 'virtual');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($download, 'downloadable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($downloadWithOption, 'downloadable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($grouped, 'grouped');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($bundle, 'bundle');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($configurable, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($simpleWithCO);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->reindexInvalidedData();
        $this->flushCache();
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
                'grouped' => $grouped['general_name'],
                'downloadable_opt' => $downloadWithOption['general_name']
            ),
            'configurableOption' => array(
                'title' => $attrData['store_view_titles']['Default Store View'],
                'custom_option_dropdown' => $configurOptName
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
     * <p>Opens My Wishlist using the link in quick access bar</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function openMyWishlistViaQuickAccessLink($testData)
    {
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->clickControl('link', 'my_wishlist');
        //Verify
        $this->assertTrue($this->checkCurrentPage('my_wishlist'), $this->getParsedMessages());
    }

    /**
     * <p>Add products to Wishlist from Product Details page. For all types without additional options.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function addProductsWithoutAdditionalOptionsToWishlistFromProduct($testData)
    {
        //Steps and Verifying
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        foreach ($testData['productNames'] as $productName) {
            $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($productName);
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
     * <p>Add products to Wishlist from Category page. For all types without additional options.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function addProductsWithoutAdditionalOptionsToWishlistFromCatalog($testData)
    {
        //Steps and Verifying
        $this->customerHelper()->frontLoginCustomer($testData['user']);
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
     * <p>Removes all products from My Wishlist. For all product types</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function removeProductsFromWishlist($testData)
    {
        //Steps and Verifying
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        foreach ($testData['productNames'] as $productName) {
            $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($productName);
            $this->assertMessagePresent('success', 'successfully_added_product');
            $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($productName),
                'Product ' . $productName . ' is not added to wishlist.');
        }
        $this->navigate('my_wishlist');
        foreach ($testData['productNames'] as $productName) {
            $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($productName),
                'Product ' . $productName . ' is not in the wishlist.');
        }
        $lastProductName = end($testData['productNames']);
        array_pop($testData['productNames']);
        foreach ($testData['productNames'] as $productName) {
            //Remove all except last
            $this->wishlistHelper()->frontRemoveProductsFromWishlist($productName);
            $this->assertTrue(is_array($this->wishlistHelper()->frontWishlistHasProducts($productName)),
                'Product ' . $productName . ' is in the wishlist, but should be removed.');
        }
        //Remove the last one
        $this->wishlistHelper()->frontRemoveProductsFromWishlist($lastProductName);
        $this->assertTrue($this->controlIsPresent('pageelement', 'no_items'), $this->getParsedMessages());
    }

    /**
     * <p>Adds products to Shopping Cart from Wishlist. For all product types without custom options
     *    (simple, virtual, downloadable)</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function addProductsWithoutOptionsToShoppingCartFromWishlist($testData)
    {
        //Data
        $products = array(
            $testData['productNames']['simple'],
            $testData['productNames']['downloadable'],
            $testData['productNames']['virtual']
        );
        //Steps and Verifying
        $this->customerHelper()->frontLoginCustomer($testData['user']);
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
            $this->assertTrue($this->checkCurrentPage('shopping_cart'), $this->getMessagesOnPage());
            $this->assertTrue($this->shoppingCartHelper()->frontShoppingCartHasProducts($productName),
                'Product ' . $productName . ' is not in the shopping cart.');
        }
    }

    /**
     * <p>Adds products to Shopping Cart from Wishlist. For all product types with additional options
     *    (downloadable, configurable, bundle, grouped)</p>
     *
     * @param array $testData
     * @param string $product
     * @param string $message
     *
     * @test
     * @dataProvider productsWithOptionsNegativeDataProvider
     * @depends preconditionsForTests
     */
    public function addProductsWithOptionsToShoppingCartFromWishlistNegative($product, $message, $testData)
    {
        if ($product == 'downloadable_opt') {
            $this->markTestIncomplete('MAGETWO-11470');
        }
        //Data
        $productName = $testData['productNames'][$product];
        //Steps and Verifying
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($productName);
        $this->assertMessagePresent('success', 'successfully_added_product');
        //Verifying
        $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($productName),
            'Product ' . $productName . ' is not in the wishlist.');
        $this->wishlistHelper()->frontAddToShoppingCartFromWishlist($productName);
        $this->assertMessagePresent('validation', 'specify_product_' . $message);
    }

    public function productsWithOptionsNegativeDataProvider()
    {
        return array(
            array('downloadable_opt', 'link'),
            array('configurable', 'config_option'),
            array('bundle', 'option'),
            array('grouped', 'quantity')
        );
    }

    /**
     * <p>Adds products to Shopping Cart from Wishlist. For all product types with additional options
     *    (downloadable, configurable, bundle, grouped)</p>
     *
     * @param array $testData
     * @param string $product
     * @param string $option
     *
     * @test
     * @dataProvider productsWithOptionsDataProvider
     * @depends preconditionsForTests
     */
    public function addProductsWithOptionsToShoppingCartFromWishlist($product, $option, $testData)
    {
        if ($product == 'bundle' && $this->getBrowser() == 'chrome') {
            $this->markTestIncomplete('MAGETWO-11557');
        }
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
        //Steps and Verifying
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($productName, $options);
        $this->assertMessagePresent('success', 'successfully_added_product');
        //Verifying
        $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($productName),
            'Product ' . $productName . ' is not in the wishlist.');
        $this->wishlistHelper()->frontAddToShoppingCartFromWishlist($productName);
        $this->assertTrue($this->checkCurrentPage('shopping_cart'), $this->getMessagesOnPage());
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
            array('downloadable_opt', 'downloadable_options_to_add_to_shopping_cart'),
            array('configurable', 'configurable_options_to_add_to_shopping_cart'),
            array('bundle', 'bundle_options_to_add_to_shopping_cart'),
            array('grouped', 'grouped_options_to_add_to_shopping_cart')
        );
    }

    /**
     * <p>Add all types of products to Wishlist from Shopping Cart.</p>
     *
     * @param array $testData
     * @param string $product
     * @param string $option
     *
     * @test
     * @dataProvider productsWithOptionsDataProvider
     * @depends preconditionsForTests
     */
    public function addProductWithOptionsToWishlistFromShoppingCart($product, $option, $testData)
    {
        if ($product == 'bundle' && $this->getBrowser() == 'chrome') {
            $this->markTestIncomplete('MAGETWO-11557');
        }
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
        //Steps and Verifying
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->productHelper()->frontOpenProduct($productName);
        $this->productHelper()->frontAddProductToCart($options);
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
            $this->assertTrue($this->checkCurrentPage('shopping_cart'), $this->getMessagesOnPage());
            $this->assertTrue($this->shoppingCartHelper()->frontShoppingCartHasProducts($productName),
                'Product ' . $productName . ' is not in the shopping cart.');
            $this->shoppingCartHelper()->frontMoveToWishlist($productName);
            $this->navigate('my_wishlist');
            $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($productName),
                'Product ' . $productName . ' is not in the wishlist.');
        }
    }

    /**
     * <p>Shares My Wishlist</p>
     *
     * @param array $shareData
     * @param array $testData
     *
     * @test
     * @dataProvider shareWishlistDataProvider
     * @depends preconditionsForTests
     */
    public function shareWishlist($shareData, $testData)
    {
        //Setup
        $shareData = $this->loadDataSet('Wishlist', 'share_data', $shareData);
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($testData['productNames']['simple']);
        $this->assertMessagePresent('success', 'successfully_added_product');
        //Steps
        $this->wishlistHelper()->frontShareWishlist($shareData);
        //Verify
        $this->assertMessagePresent('success', 'successfully_shared_wishlist');
    }

    public function shareWishlistDataProvider()
    {
        return array(
            array(array('emails' => 'autotest@test.com', 'message' => 'autotest message')),
            array(array('message' => ''))
        );
    }

    /**
     * <p>Shares My Wishlist with invalid email(s) provided</p>
     *
     * @param string $emails
     * @param array $testData
     *
     * @test
     * @dataProvider withInvalidEmailDataProvider
     * @depends preconditionsForTests
     */
    public function withInvalidEmail($emails, $testData)
    {
        //Setup
        $shareData = $this->loadDataSet('Wishlist', 'share_data', array('emails' => $emails));
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($testData['productNames']['simple']);
        $this->assertMessagePresent('success', 'successfully_added_product');
        $this->wishlistHelper()->frontShareWishlist($shareData);
        //Verify
        $this->assertMessagePresent('error', 'invalid_emails');
    }

    public function withInvalidEmailDataProvider()
    {
        return array(
            array('email@@unknown-domain.com'),
            array('.email@unknown-domain.com')
        );
    }

    /**
     * <p>Shares My Wishlist with empty email provided</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function shareWishlistWithEmptyEmail($testData)
    {
        //Setup
        $shareData = $this->loadDataSet('Wishlist', 'share_data', array('emails' => ''));
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($testData['productNames']['simple']);
        $this->assertMessagePresent('success', 'successfully_added_product');
        //Steps
        $this->wishlistHelper()->frontShareWishlist($shareData);
        //Verify
        $this->addFieldIdToMessage('field', 'emails');
        $this->assertMessagePresent('validation', 'empty_required_field');
    }

    /**
     * <p>Adds a product with custom options to Wishlist from Product Details page</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function addProductWithCustomOptionsToWishlist($testData)
    {
        $product = $testData['withCustomOption'];
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($product);
        $this->assertMessagePresent('success', 'successfully_added_product');
        $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($product),
            'Product ' . $product . ' is not added to wishlist.');
    }

    /**
     * <p>Adds a product with custom options to Shopping Cart from Wishlist without selected options</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function addProductWithCustomOptionsToShoppingCartFromWishlistNegative($testData)
    {
        $this->markTestIncomplete('MAGETWO-11621');
        $simpleSku = $testData['withCustomOption'];
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($simpleSku);
        $this->assertMessagePresent('success', 'successfully_added_product');
        $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($simpleSku),
            'Product ' . $simpleSku . ' is not added to wishlist.');
        $this->wishlistHelper()->frontAddToShoppingCartFromWishlist($simpleSku);
        $this->assertMessagePresent('validation', 'specify_product_required_option');
    }

    /**
     * <p>Adds a product with custom options to Shopping Cart from Wishlist without selected options</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function addProductWithCustomOptionsToShoppingCartFromWishlist($testData)
    {
        $productName = $testData['withCustomOption'];
        $options = $this->loadDataSet('Product', 'custom_options_to_add_to_shopping_cart');
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($productName, $options);
        $this->assertMessagePresent('success', 'successfully_added_product');
        //Verifying
        $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($productName),
            'Product ' . $productName . ' is not in the wishlist.');
        $this->wishlistHelper()->frontAddToShoppingCartFromWishlist($productName);
        $this->assertTrue($this->checkCurrentPage('shopping_cart'), $this->getMessagesOnPage());
        $this->assertTrue($this->shoppingCartHelper()->frontShoppingCartHasProducts($productName),
            'Product ' . $productName . ' is not in the shopping cart.');
    }

    /**
     * <p>Add simple product with custom options to Wishlist from Shopping Cart.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function addProductWithCustomOptionsToWishlistFromShoppingCart($testData)
    {
        //Data
        $productName = $testData['withCustomOption'];
        $options = $this->loadDataSet('Product', 'custom_options_to_add_to_shopping_cart');
        //Steps and Verifying
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->productHelper()->frontOpenProduct($productName);
        $this->productHelper()->frontAddProductToCart($options);
        $this->assertTrue($this->checkCurrentPage('shopping_cart'), $this->getMessagesOnPage());
        $this->assertTrue($this->shoppingCartHelper()->frontShoppingCartHasProducts($productName),
            'Product ' . $productName . ' is not in the shopping cart.');
        $this->shoppingCartHelper()->frontMoveToWishlist($productName);
        $this->navigate('my_wishlist');
        $this->assertTrue($this->wishlistHelper()->frontWishlistHasProducts($productName),
            'Product ' . $productName . ' is not in the wishlist.');
    }

    /**
     * <p>Verifies that a guest cannot open My Wishlist.</p>
     *
     * @test
     */
    public function guestCannotOpenWishlist()
    {
        //Steps
        $this->logoutCustomer();
        $this->clickControl('link', 'my_wishlist');
        //Verify
        $this->assertTrue($this->checkCurrentPage('customer_login'), $this->getParsedMessages());
    }

    /**
     * <p>Verifies that a guest cannot add a product to a wishlist.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function guestCannotAddProductToWishlist($testData)
    {
        //Steps
        $this->frontend();
        $this->wishlistHelper()->frontAddProductToWishlistFromProductPage($testData['productNames']['simple']);
        //Verify
        $this->assertTrue($this->checkCurrentPage('customer_login'), $this->getParsedMessages());
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->assertMessagePresent('success', 'successfully_added_product');
    }
}