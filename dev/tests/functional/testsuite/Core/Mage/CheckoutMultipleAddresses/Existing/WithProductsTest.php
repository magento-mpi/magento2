<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CheckoutMultipleAddresses
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Checkout Multiple Addresses tests with different product types
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CheckoutMultipleAddresses_Existing_WithProductsTest extends Mage_Selenium_TestCase
{
    private static $_productTypes = array('grouped', 'simple', 'virtual', 'downloadable', 'bundle', 'configurable');

    protected function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->frontend();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
    }

    /**
     * <p>Create all types of products</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $products = array();
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $this->navigate('manage_products');
        $this->runMassAction('Delete', 'all');
        foreach (self::$_productTypes as $type) {
            $method = 'create' . ucfirst($type) . 'Product';
            $products[$type] = $this->productHelper()->$method();
        }
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');

        return array($products, 'email' => $userData['email']);
    }

    /**
     * <p>Checkout with multiple addresses simple and virtual/downloadable products</p>
     *
     * @param string $productType
     * @param array $testData
     *
     * @test
     * @dataProvider virtualProductsDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5234
     */
    public function withVirtualTypeOfProducts($productType, $testData)
    {
        //Data
        list($products) = $testData;
        $simple = $products['simple']['simple']['product_name'];
        $virtual = $products['configurable'][$productType]['product_name'];
        $checkout = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login_virtual',
            array('email' => $testData['email']),
            array('product_1' => $simple, 'product_2' => $virtual));
        //Steps and Verify
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkout);
        $this->assertMessagePresent('success', 'success_checkout');
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    public function virtualProductsDataProvider()
    {
        return array(
            array('downloadable'),
            array('virtual')
        );
    }

    /**
     * <p>Checkout with multiple addresses grouped products</p>
     *
     * @param string $productType
     * @param string $dateSet
     * @param array $testData
     *
     * @test
     * @dataProvider productsDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5236
     */
    public function withGroupedProduct($productType, $dateSet, $testData)
    {
        //Data
        list($products) = $testData;
        $simple = $products['simple']['simple']['product_name'];
        $grouped = $products['grouped']['grouped']['product_name'];
        $optionParams = $products['grouped'][$productType]['product_name'];
        $productOptions = $this->loadDataSet('Product', 'grouped_options_to_add_to_shopping_cart', null,
            array('subProduct_1' => $optionParams));
        $checkout = $this->loadDataSet('MultipleAddressesCheckout', $dateSet, array('email' => $testData['email']),
            array('product_1' => $simple, 'product_2' => $grouped, 'option_product_2' => $productOptions));
        $checkout['shipping_data'] = $this->loadDataSet('MultipleAddressesCheckout', $dateSet . '/shipping_data', null,
            array('product_1' => $simple, 'product_2' => $optionParams));
        //Steps and Verify
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkout);
        $this->assertMessagePresent('success', 'success_checkout');
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    /**
     * <p>Checkout with multiple addresses bundle products</p>
     *
     * @param string $productType
     * @param string $dateSet
     * @param array $testData
     *
     * @test
     * @dataProvider withBundleProductDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5237
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function withBundleProduct($productType, $dateSet, $testData)
    {
        //Data
        list($products) = $testData;
        $simple = $products['simple']['simple']['product_name'];
        $bundle = $products['bundle']['bundle']['product_name'];
        $optionParams = $products['bundle']['bundleOption'];
        foreach ($optionParams as $key => $value) {
            $optionParams[$key] = $products['bundle'][$productType]['product_name'];
        }
        $productOptions = $this->loadDataSet('Product', 'bundle_options_to_add_to_shopping_cart', null, $optionParams);
        $checkout = $this->loadDataSet('MultipleAddressesCheckout', $dateSet, array('email' => $testData['email']),
            array('product_1' => $simple, 'product_2' => $bundle, 'option_product_2' => $productOptions));
        //Steps and Verify
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkout);
        $this->assertMessagePresent('success', 'success_checkout');
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    /**
     * <p>Checkout with multiple addresses configurable product with associated products</p>
     *
     * @param string $productType
     * @param string $dateSet
     * @param array $testData
     *
     * @test
     * @dataProvider productsDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5238
     */
    public function withConfigurable($productType, $dateSet, $testData)
    {
        //Data
        list($products) = $testData;
        $simple = $products['simple']['simple']['product_name'];
        $configurable = $products['configurable']['configurable']['product_name'];
        $optionParams = $products['configurable']['configurableOption'];
        $optionParams['custom_option_dropdown'] = $products['configurable'][$productType . 'Option']['option_front'];
        $productOptions = $this->loadDataSet('Product', 'configurable_options_to_add_to_shopping_cart', $optionParams);
        $checkout = $this->loadDataSet('MultipleAddressesCheckout', $dateSet, array('email' => $testData['email']),
            array('product_1' => $simple, 'product_2' => $configurable, 'option_product_2' => $productOptions));
        //Steps and Verify
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkout);
        $this->assertMessagePresent('success', 'success_checkout');
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    /**
     * <p>Checkout with multiple addresses Downloadable product with associated links</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function withDownloadable($testData)
    {
        //Data
        list($products) = $testData;
        $simple = $products['simple']['simple']['product_name'];
        $downloadable = $products['downloadable']['downloadable']['product_name'];
        $optionParams = $products['downloadable']['downloadableOption'];
        $productOptions = $this->loadDataSet('Product', 'downloadable_options_to_add_to_shopping_cart', $optionParams);
        $checkout = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login_virtual',
            array('email' => $testData['email']),
            array('product_1' => $simple, 'product_2' => $downloadable, 'option_product_2' => $productOptions));
        //Steps and Verify
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkout);
        $this->assertMessagePresent('success', 'success_checkout');
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    public function productsDataProvider()
    {
        return array(
            array('simple', 'multiple_with_login'),
            array('virtual', 'multiple_with_login_virtual'),
            array('downloadable', 'multiple_with_login_virtual')
        );
    }

    public function withBundleProductDataProvider()
    {
        return array(
            array('simple', 'multiple_with_login'),
            array('virtual', 'multiple_with_login_virtual')
        );
    }

    /**
     * <p>Checkout with multiple addresses products with custom options</p>
     *
     * @param string $productType
     * @param string $dateSet
     * @param array $testData
     *
     * @test
     * @dataProvider withCustomOptionsDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5235
     */
    public function withCustomOptions($productType, $dateSet, $testData)
    {
        //Data
        list($products) = $testData;
        $simple = $products['simple']['simple']['product_name'];
        $productData = $products[$productType];
        if ($productType == 'simple') {
            $productData = $products['bundle'];
        }
        $secondProduct = $productData[$productType]['product_name'];
        $optionParams = (isset($productData[$productType . 'Option']))
            ? $productData[$productType . 'Option']
            : array();
        $productOptions = array();
        if (!empty($optionParams)) {
            $name = '_options_to_add_to_shopping_cart';
            if ($productType == 'configurable' || $productType == 'downloadable') {
                $productOptions = $this->loadDataSet('Product', $productType . $name, $optionParams);
            } else {
                $productOptions = $this->loadDataSet('Product', $productType . $name, null, $optionParams);
            }
        }
        $customOptions = $this->loadDataSet('Product', 'custom_options_to_add_to_shopping_cart');
        $productOptions = array_merge($productOptions, $customOptions);
        $checkout = $this->loadDataSet('MultipleAddressesCheckout', $dateSet, array('email' => $testData['email']),
            array('product_1' => $simple, 'product_2' => $secondProduct, 'option_product_2' => $productOptions));
        $search = $this->loadDataSet('Product', 'product_search', array('product_name' => $secondProduct));
        $customOptionsData['custom_options_data'] = $this->loadDataSet('Product', 'custom_options_data');
        //Steps and Verify
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->productHelper()->fillProductTab($customOptionsData, 'custom_options');
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkout);
        $this->assertMessagePresent('success', 'success_checkout');
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    public function withCustomOptionsDataProvider()
    {
        return array(
            array('virtual', 'multiple_with_login_virtual'),
            array('downloadable', 'multiple_with_login_virtual'),
            array('bundle', 'multiple_with_login'),
            array('configurable', 'multiple_with_login'),
            array('simple', 'multiple_with_login')
        );
    }
}