<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_GiftRegistry
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift Registry creation into backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Grid_Reports_GridTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore('StoreView/generic_store_view', 'store_view');
        $this->assertMessagePresent('success', 'success_saved_store_view');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $this->logoutAdminUser();
    }

    /**
     * Need to verify that all elements is presented on invitation report_invitations_customers page
     * @test
     * @dataProvider uiElementsTestDataProvider
     *
     */
    public function uiElementsTest($pageName)
    {
        $this->navigate($pageName);
        $page = $this->loadDataSet('Report', 'grid/' . $pageName);
        foreach ($page as $control => $type) {
            foreach ($type as $typeName => $name) {
                if (!$this->controlIsPresent($control, $typeName)) {
                    $this->addVerificationMessage("The $control $typeName is not present on page $pageName");
                }
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    public function uiElementsTestDataProvider()
    {
        return array(
            array('report_customer_totals'),
            array('report_product_sold'),
            array('report_customer_accounts')

        );
    }

    /**
     * Need to verify count of Grid Rows according to "From:", "To:","Show By:" values
     * @test
     *
     * @dataProvider countGridRowsTestDataProvider
     */
    public function countGridRowsTest($page, $gridTableElement, $dataSet)
    {
        $this->navigate($page);
        $data = $this->loadDataSet('Report', $dataSet);
        $this->fillFieldset($data, $page);
        $this->clickButton('refresh');
        $gridXpath = $this->_getControlXpath('pageelement', $gridTableElement);
        $this->assertCount(3, $this->getElements($gridXpath . '/tbody/tr'),
            "Wrong records number in grid $gridTableElement");
    }

    public function countGridRowsTestDataProvider()
    {
        return array(
            array('report_product_sold', 'product_sold_grid', 'count_rows_by_day'),
            array('report_product_sold', 'product_sold_grid', 'count_rows_by_month'),
            array('report_product_sold', 'product_sold_grid', 'count_rows_by_year'),
            array('report_customer_totals', 'customer_by_orders_total_table', 'count_rows_by_day'),
            array('report_customer_totals', 'customer_by_orders_total_table', 'count_rows_by_month'),
            array('report_customer_totals', 'customer_by_orders_total_table', 'count_rows_by_year'),
            array('report_customer_accounts', 'report_customer_accounts_table', 'count_rows_by_day'),
            array('report_customer_accounts', 'report_customer_accounts_table', 'count_rows_by_month'),
            array('report_customer_accounts', 'report_customer_accounts_table', 'count_rows_by_year')
        );
    }

    /**
     *<p>PreConditions</p>
     *<p>1.Go to Report - Product Ordered page</p>
     *<p>2.Filter data with filled "From", "To" used current Day value</p>
     *<p>2.Get Total product quantity ordered value</p>
     *<p>3.Create new Product</p>
     *<p>4.Create Order with created Product</p>
     *<p>Steps:</p>
     *<p>1.Go to Report - Product Ordered page</p>
     *<p>2.Filter data with filled "From", "To" used current Day value</p>
     *<p>Actual Results:</p>
     *<p>1.Quantity Ordered value = Value from PreConditions +1 </p>
     *
     *
     * @test
     */
    public function checkQuantityOrderedProductSoldGridTest()
    {
        $simple1 = $this->loadDataSet('Product', 'simple_product_visible');
        $simple2 = $this->loadDataSet('Product', 'simple_product_visible');
        $orderData1 = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simple1['general_sku']));
        $orderData2 = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simple2['general_sku']));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple1);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($simple2);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Create first order
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData1);
        $this->assertMessagePresent('success', 'success_created_order');
        $firstDate = $this->getControlAttribute('pageelement', 'order_date', 'text');
        // Check current quantity ordered value
        $this->navigate('report_product_sold');
        $this->gridHelper()->fillDateFromTo($firstDate, $firstDate);
        $this->clickButton('refresh');
        $lineLocator = $this->_getControlXpath('pageelement', 'product_sold_grid_line') . "/*[3]";
        $totalBefore = trim($this->getElement($lineLocator)->text());
        //Create second order
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData2);
        $this->assertMessagePresent('success', 'success_created_order');
        $secondDate = $this->getControlAttribute('pageelement', 'order_date', 'text');
        //Check Quantity Ordered after second order created
        $this->navigate('report_product_sold');
        $this->gridHelper()->fillDateFromTo($firstDate, $secondDate);
        $this->clickButton('refresh');
        $totalAfter = trim($this->getElement($lineLocator)->text());
        $this->assertEquals($totalBefore + 1, $totalAfter,
            'Wrong records number in grid product_sold_grid_line. Before was ' . $totalBefore
                . ' after - ' . $totalAfter);
    }

    /**
     *<p>PreConditions</p>
     *<p>1.Go to Report - Customers - Customers by Number of Orders</p>
     *<p>2.Filter data with filled "From", "To" used current Day value</p>
     *<p>2.Get Total Number of Orders</p>
     *<p>3.Create new Product</p>
     *<p>4.Create Order with created Product</p>
     *<p>Steps:</p>
     *<p>1.Go to Report - Product Ordered page</p>
     *<p>2.Filter data with filled "From", "To" used current Day value</p>
     *<p>Actual Results:</p>
     *<p>1.Total Number of Orders value = Value from PreConditions +1 </p>
     *
     * @test
     */
    public function checkTotalNumberOfOrdersGridTest()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simple['general_sku']));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        //
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $firstDate = $this->getControlAttribute('pageelement', 'order_date', 'text');
        //Get Total Number of Orders
        $this->navigate('report_customer_orders');
        $this->gridHelper()->fillDateFromTo($firstDate, $firstDate);
        $this->clickButton('refresh');
        $lineLocator = $this->_getControlXpath('pageelement', 'customer_orders_grid_line') . "/*[3]";
        $totalBefore = trim($this->getElement($lineLocator)->text());
        //
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $secondDate = $this->getControlAttribute('pageelement', 'order_date', 'text');
        //
        $this->navigate('report_customer_orders');
        $this->gridHelper()->fillDateFromTo($firstDate, $secondDate);
        $this->clickButton('refresh');
        //Check Quantity Ordered after  new order created
        $totalAfter = trim($this->getElement($lineLocator)->text());
        $this->assertEquals($totalBefore + 1, $totalAfter,
            'Wrong records number in report_customer_orders grid. Before was ' . $totalBefore
                . ' after - ' . $totalAfter);
    }
}