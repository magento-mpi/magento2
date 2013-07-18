<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_PriceRules
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Applying Shopping Cart Price Rules tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_PriceRules_ShoppingCart_ApplyTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->markTestIncomplete('MAGETWO-11604');
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Tax/default_tax_config');
        $this->systemConfigurationHelper()->configure('ShippingSettings/shipping_settings_default');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $this->systemConfigurationHelper()->configure('Currency/enable_usd');
        $this->systemConfigurationHelper()->configure('PaymentMethod/authorizenet_without_3Dsecure');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
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

    /**
     * Create category,customer and products for test.
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        $taxRule = $this->loadDataSet('Tax', 'new_tax_rule_required',
            array('tax_rate' => 'US-CA-*-Rate 1,US-NY-*-Rate 1'));
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
        $this->navigate('manage_tax_rule');
        $this->taxHelper()->deleteRulesExceptSpecified();
        $this->taxHelper()->createTaxRule($taxRule);
        $this->assertMessagePresent('success', 'success_saved_tax_rule');

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
        return array(array('email' => $user['email'], 'password' => $user['password']), $products, $categoryPath);
    }

    /**
     * <p>Place order with Shopping Cart Price Rule using Authorize.net on frontend</p>
     *
     * @param string $ruleType
     * @param array $testData
     *
     * @test
     * @dataProvider createSCPRDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3563
     */
    public function createSCPR($ruleType, $testData)
    {
        //Data
        list($customer, $products) = $testData;
        $paymentData = $this->loadDataSet('Payment', 'payment_authorizenet');
        $orderData = $this->loadDataSet('OnePageCheckout', 'signedin_flatrate_checkmoney_usa',
            array('payment_data' => $paymentData));
        $cartProductsData = $this->loadDataSet('ShoppingCartPriceRule', 'prices_for_' . $ruleType);
        $checkoutData = $this->loadDataSet('ShoppingCartPriceRule', 'totals_for_' . $ruleType);
        $ruleData = $this->loadDataSet('ShoppingCartPriceRule', 'scpr_' . $ruleType,
            array('conditions' => '%noValue%'));
        //Steps
        $this->navigate('manage_shopping_cart_price_rules');
        $this->priceRulesHelper()->createRule($ruleData);
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->flushCache();
        $this->reindexInvalidedData();
        //navigate to frontend
        $this->customerHelper()->frontLoginCustomer($customer);
        foreach ($products['name'] as $key => $productName) {
            $cartProductsData['product_' . $key]['product_name'] = $productName;
            $this->productHelper()->frontOpenProduct($productName);
            $this->productHelper()->frontAddProductToCart();
        }
        $this->shoppingCartHelper()
            ->frontEstimateShipping('PriceReview/estimate_shipping', 'Shipping/shipping_flatrate');
        $this->addParameter('couponCode', $ruleData['info']['coupon_code']);
        $this->fillFieldset(array('coupon_code' => $ruleData['info']['coupon_code']), 'discount_codes');
        $this->clickButton('apply_coupon');
        //Verifying that coupon is successfully applied
        $this->assertMessagePresent('success', 'success_applied_coupon');
        $this->shoppingCartHelper()->verifyPricesDataOnPage($cartProductsData, $checkoutData);
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($orderData);
        $this->checkoutOnePageHelper()->frontOrderReview($orderData);
        $this->checkoutOnePageHelper()->submitOnePageCheckoutOrder($orderData);
        //Verifying that order is successfully created
        $this->assertMessagePresent('success', 'success_checkout');
    }

    public function createSCPRDataProvider()
    {
        return array(
            array('percent_of_product_price_discount'),
            array('fixed_amount_discount'),
            array('fixed_amount_discount_for_whole_cart')
        );
    }

    /**
     * <p>Place order with Shopping Cart Price Rule using Authorize.net on backend</p>
     *
     * @param string $ruleType
     * @param array $testData
     * @param string $rowSubtotal
     *
     * @test
     * @dataProvider createSCPRonBackendDataProvider
     * @depends preconditionsForTests
     * @skipTearDown
     * @TestlinkId TL-MAGE-6121
     */
    public function createSCPRonBackend($ruleType, $rowSubtotal, $testData)
    {
        //Data
        list(, $products) = $testData;
        $paymentData = $this->loadDataSet('Payment', 'payment_authorizenet');
        $ruleData = $this->loadDataSet('ShoppingCartPriceRule', 'scpr_' . $ruleType,
            array('conditions' => '%noValue%'));
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa', array(
            'coupon_1' => $ruleData['info']['coupon_code'],
            'payment_data' => $paymentData
        ));
        $cartProductsData = $this->loadDataSet('ShoppingCartPriceRule', 'prices_for_' . $ruleType);
        $checkoutData = $this->loadDataSet('ShoppingCartPriceRule', 'totals_for_' . $ruleType);
        //prepare all necessary data to place order
        foreach ($products['sku'] as $key => $productSku) {
            $orderData['products_to_add']['product_' . $key]['filter_sku'] = $productSku;
            $orderData['products_to_add']['product_' . $key]['product_qty'] =
                $cartProductsData['product_' . $key]['qty'];
        }
        $orderData['prod_total_verification']['row_subtotal'] = (string)$rowSubtotal;
        $orderData['prod_total_verification']['product'] = 'Total 3 product(s)';
        $orderData['prod_total_verification']['subtotal'] = $checkoutData['subtotal'];
        $orderData['prod_total_verification']['discount'] =
            $checkoutData['discount_rule_label_shop_cart_for_store_view'];
        //Steps
        $this->navigate('manage_shopping_cart_price_rules');
        $this->priceRulesHelper()->createRule($ruleData);
        //Verifying that rule is successfully created
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->flushCache();
        $this->reindexAllData();
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying that order is successfully placed
        $this->assertMessagePresent('success', 'success_created_order');
    }

    public function createSCPRonBackendDataProvider()
    {
        return array(
            array('percent_of_product_price_discount', '$509.80'),
            array('fixed_amount_discount', '$449.77'),
            array('fixed_amount_discount_for_whole_cart', '$584.77')
        );
    }
}