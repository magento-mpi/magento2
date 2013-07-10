<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tax
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Prices Validation on the frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Tax_TaxAndPricesValidationFrontendTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->markTestIncomplete('MAGE-1987');
        $taxRule = $this->loadDataSet('Tax', 'new_tax_rule_required',
            array('tax_rate' => 'US-CA-*-Rate 1,US-NY-*-Rate 1'));
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Tax/default_tax_config');
        $this->systemConfigurationHelper()->configure('ShippingSettings/shipping_settings_default');
        $this->systemConfigurationHelper()->configure('Currency/enable_usd');
        $this->systemConfigurationHelper()->configure('Tax/flat_rate_for_price_verification');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
        $this->navigate('manage_tax_rule');
        $this->taxHelper()->deleteRulesExceptSpecified();
        $this->taxHelper()->createTaxRule($taxRule);
        $this->assertMessagePresent('success', 'success_saved_tax_rule');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->frontend();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $this->systemConfigurationHelper()->configure('Tax/default_tax_config');
    }

    /**
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        $user = $this->loadDataSet('Customers', 'customer_account_register');
        $searchData = $this->loadDataSet('Customers', 'search_customer', array('email' => $user['email']));
        $address = $this->loadDataSet('PriceReview', 'customer_account_address_for_prices_validation');
        $category = $this->loadDataSet('Category', 'sub_category_required');
        $categoryPath = $category['parent_category'] . '/' . $category['name'];
        $products = array();
        //Steps
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($user);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();

        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer($searchData);
        $this->customerHelper()->addAddress($address);
        $this->saveForm('save_customer');
        $this->assertMessagePresent('success', 'success_saved_customer');

        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($category);
        $this->assertMessagePresent('success', 'success_saved_category');
        $this->categoryHelper()->checkCategoriesPage();

        $this->navigate('manage_products');
        for ($i = 1; $i <= 3; $i++) {
            $simple = $this->loadDataSet('PriceReview', 'simple_product_for_prices_validation_front_' . $i,
                array('general_categories' => $categoryPath));
            $this->productHelper()->createProduct($simple);
            $this->assertMessagePresent('success', 'success_saved_product');
            $products['sku'][$i] = $simple['general_sku'];
            $products['name'][$i] = $simple['general_name'];
        }
        return array(array('email' => $user['email'], 'password' => $user['password']), $products, $category['name']);
    }

    /**
     * Create Order on the backend and validate prices with taxes
     *
     * @param string $configName
     * @param array $testData
     *
     * @test
     * @dataProvider validateTaxFrontendDataProvider
     * @depends preconditionsForTests
     */
    public function validateTaxFrontend($configName, $testData)
    {
        //Data
        list($customer, $products, $category) = $testData;
        $cartProductsData = $this->loadDataSet('PriceReview', $configName . '_front_prices_in_cart_simple');
        $checkoutData = $this->loadDataSet('PriceReview', $configName . '_front_prices_checkout_data');
        $orderDetailsData = $this->loadDataSet('PriceReview', $configName . '_front_prices_on_order_details');
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Tax/' . $configName);
        $this->customerHelper()->frontLoginCustomer($customer);
        //Verify and add products to shopping cart
        foreach ($products['name'] as $key => $productName) {
            //Data
            $priceInCategory = $this->loadDataSet(
                'PriceReview',
                $configName . '_front_prices_in_category_simple_' . $key,
                array('product_name' => $productName, 'category' => $category)
            );
            $priceInProdDetails =
                $this->loadDataSet('PriceReview', $configName . '_front_prices_in_product_simple_' . $key);
            $cartProductsData['product_' . $key]['product_name'] = $productName;
            $checkoutData['validate_prod_data']['product_' . $key]['product_name'] = $productName;
            $orderDetailsData['validate_prod_data']['product_' . $key]['product_name'] = $productName;
            $orderDetailsData['validate_prod_data']['product_' . $key]['sku'] = $products['sku'][$key];
            //Steps
            $this->categoryHelper()->frontOpenCategoryAndValidateProduct($priceInCategory);
            $this->productHelper()->frontOpenProduct($productName);
            $this->categoryHelper()->frontVerifyProductPrices($priceInProdDetails);
            $this->productHelper()->frontAddProductToCart();
        }
        $this->shoppingCartHelper()
            ->frontEstimateShipping('PriceReview/estimate_shipping', 'Shipping/shipping_flatrate');
        $this->shoppingCartHelper()->verifyPricesDataOnPage($cartProductsData, $checkoutData['validate_total_data']);
        $orderId = '# ' . $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $this->addParameter('orderId', $orderId);
        $this->clickControl('link', 'order_number');
        $this->shoppingCartHelper()
            ->verifyPricesDataOnPage($orderDetailsData['validate_prod_data'], $orderDetailsData['validate_total_data']);
    }

    public function validateTaxFrontendDataProvider()
    {
        return array(
            array('unit_cat_ex_ship_in'),
            array('unit_cat_ex_ship_ex'),
            array('row_cat_ex_ship_ex'),
            array('total_cat_ex_ship_ex'),
            array('row_cat_ex_ship_in'),
            array('total_cat_ex_ship_in'),
            array('unit_cat_in_ship_ex'),
            array('row_cat_in_ship_ex'),
            array('total_cat_in_ship_ex'),
            array('unit_cat_in_ship_in'),
            array('row_cat_in_ship_in'),
            array('total_cat_in_ship_in')
        );
    }
}