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
 * Prices Validation on the Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Tax_TaxAndPricesValidationBackendTest extends Mage_Selenium_TestCase
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
            $simple = $this->loadDataSet('PriceReview', 'simple_product_for_prices_validation_' . $i,
                array('general_categories' => $categoryPath));
            $this->productHelper()->createProduct($simple);
            $this->assertMessagePresent('success', 'success_saved_product');
            $products['sku'][$i] = $simple['general_sku'];
            $products['name'][$i] = $simple['general_name'];
        }
        return array($user['email'], $products, $category['name']);
    }

    /**
     * Create Order on the backend and validate prices with taxes
     *
     * @param string $sysConfigData
     * @param array $testData
     *
     * @test
     * @dataProvider createOrderBackendDataProvider
     * @depends preconditionsForTests
     */
    public function createOrderBackend($sysConfigData, $testData)
    {
        list($customer, $products) = $testData;
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Tax/' . $sysConfigData);
        //Data for order creation
        $order =
            $this->loadDataSet('PriceReview', $sysConfigData . '_backend_create_order', array('email' => $customer));
        //Data for prices and total verification after order creation
        $priceAftOrd = $this->loadDataSet('PriceReview', $sysConfigData . '_backend_product_prices_after_order');
        $totAftOrd = $this->loadDataSet('PriceReview', $sysConfigData . '_backend_total_after_order');
        //Data for prices and total verification before invoice creation
        $priceBefInv = $this->loadDataSet('PriceReview', $sysConfigData . '_backend_product_prices_before_invoice');
        $totBefInv = $this->loadDataSet('PriceReview', $sysConfigData . '_backend_total_before_invoice');
        //Data for prices and total verification after invoice creation on order page
        $priceAftInv = $this->loadDataSet('PriceReview', $sysConfigData . '_backend_product_prices_after_invoice');
        $totAftInv = $this->loadDataSet('PriceReview', $sysConfigData . '_backend_total_after_invoice');
        //Data for prices and total verification after invoice creation on invoice page
        $priceAftInvOnInv =
            $this->loadDataSet('PriceReview', $sysConfigData . '_backend_product_prices_after_invoice_on_invoice');
        $totAftInvOnInv = $this->loadDataSet('PriceReview', $sysConfigData . '_backend_total_after_invoice_on_invoice');
        //Data for prices and total verification after invoice creation on invoice page
        $priceAftShip = $this->loadDataSet('PriceReview', $sysConfigData . '_backend_product_prices_after_shipment');
        $totAftShip = $this->loadDataSet('PriceReview', $sysConfigData . '_backend_total_after_shipment');
        //Data for prices and total verification before refund creation on refund page
        $priceBefRef = $this->loadDataSet('PriceReview', $sysConfigData . '_backend_product_prices_before_refund');
        $totBefRef = $this->loadDataSet('PriceReview', $sysConfigData . '_backend_total_before_refund');
        //Data for prices and total verification after refund creation on order page
        $priceAftRef = $this->loadDataSet('PriceReview', $sysConfigData . '_backend_product_prices_after_refund');
        $totAftRef = $this->loadDataSet('PriceReview', $sysConfigData . '_backend_total_after_refund');
        //Data for prices and total verification after refund creation on refund page
        $priceAftRefOnRef =
            $this->loadDataSet('PriceReview', $sysConfigData . '_backend_product_prices_after_refund_on_refund');
        $totAftRefOnRef = $this->loadDataSet('PriceReview', $sysConfigData . '_backend_total_after_refund_on_refund');
        for ($i = 1; $i <= 3; $i++) {
            $order['products_to_add']['product_' . $i]['filter_sku'] = $products['sku'][$i];
            $order['prod_verification']['product_' . $i]['product'] = $products['name'][$i];
            $priceAftOrd['product_' . $i]['product'] = $products['name'][$i];
            $priceBefInv['product_' . $i]['product'] = $products['name'][$i];
            $priceAftInv['product_' . $i]['product'] = $products['name'][$i];
            $priceAftInvOnInv['product_' . $i]['product'] = $products['name'][$i];
            $priceAftShip['product_' . $i]['product'] = $products['name'][$i];
            $priceBefRef['product_' . $i]['product'] = $products['name'][$i];
            $priceAftRef['product_' . $i]['product'] = $products['name'][$i];
            $priceAftRefOnRef['product_' . $i]['product'] = $products['name'][$i];
        }
        //Create Order and validate prices during order creation
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($order);
        //Define Order Id to work with
        $orderId = $this->orderHelper()->defineOrderId();
        //Verify prices on order review page after order creation
        $this->shoppingCartHelper()->verifyPricesDataOnPage($priceAftOrd, $totAftOrd);
        //Verify prices before creating Invoice
        $this->clickButton('invoice');
        $this->shoppingCartHelper()->verifyPricesDataOnPage($priceBefInv, $totBefInv);
        //Verify prices after creating invoice on order page
        $this->clickButton('submit_invoice');
        $this->shoppingCartHelper()->verifyPricesDataOnPage($priceAftInv, $totAftInv);
        //Verify prices after creating shipment on order page
        $this->clickButton('ship');
        $this->clickButton('submit_shipment');
        $this->shoppingCartHelper()->verifyPricesDataOnPage($priceAftShip, $totAftShip);
        //Verify prices before creating refund on refund page
        $this->clickButton('credit_memo');
        $this->shoppingCartHelper()->verifyPricesDataOnPage($priceBefRef, $totBefRef);
        //Verify prices after creating refund on order page
        $this->clickButton('refund_offline');
        $this->shoppingCartHelper()->verifyPricesDataOnPage($priceAftRef, $totAftRef);
        //Verify prices after creating invoice on invoice page
        $this->navigate('manage_sales_invoices');
        $this->orderInvoiceHelper()->openInvoice(array('filter_order_id' => $orderId));
        $this->shoppingCartHelper()->verifyPricesDataOnPage($priceAftInvOnInv, $totAftInvOnInv);
        //Verify prices after creating Refund on Refund page
        $this->navigate('manage_sales_credit_memos');
        $this->searchAndOpen(array('filter_order_id' => $orderId), 'sales_creditmemo_grid');
        $this->shoppingCartHelper()->verifyPricesDataOnPage($priceAftRefOnRef, $totAftRefOnRef);
    }

    public function createOrderBackendDataProvider()
    {
        return array(
            array('unit_cat_ex_ship_ex'),
            array('row_cat_ex_ship_ex'),
            array('total_cat_ex_ship_ex'),
            array('unit_cat_ex_ship_in'),
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
