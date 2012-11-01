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
class Enterprise2_Mage_Grid_Report_GridTest extends Mage_Selenium_TestCase
{
    /**
     *
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Post conditions:</p>
     * <p>Log out from Backend.</p>
     */
    protected function tearDownAfterTest()
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
        $this->loginAdminUser();
        $this->navigate($pageName);
        $page = $this->loadDataSet('Report', 'grid');
        foreach ($page[$pageName] as $control=> $type) {
            foreach ($type as $typeName=> $name) {
                if (!$this->controlIsPresent($control, $typeName)) {
                    $this->addVerificationMessage("The $control $typeName is not present on page $pageName");
                }
            }

        }
        $this->assertEmptyVerificationErrors();
    }

    public function uiElementsTestDataProvider()
    {
        return array(array('report_invitations_customers'),
                     array('report_product_sold'),
                     array('report_customer_totals'),
                     array('report_invitations_general'));
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
        $this->assertCount(3, $this->getElementsByXpath($gridXpath . '/tbody/tr'),
            "Wrong records number in grid $gridTableElement");
    }

    public function countGridRowsTestDataProvider()
    {
        return array(array('report_product_sold', 'product_sold_grid', 'count_rows_by_day'),
                     array('report_product_sold', 'product_sold_grid', 'count_rows_by_month'),
                     array('report_product_sold', 'product_sold_grid', 'count_rows_by_year'),
                     array('report_invitations_customers', 'report_invitations_customers_grid', 'count_rows_by_day'),
                     array('report_invitations_customers', 'report_invitations_customers_grid', 'count_rows_by_month'),
                     array('report_invitations_customers', 'report_invitations_customers_grid', 'count_rows_by_year'),
                     array('report_customer_totals', 'customer_by_orders_total_table', 'count_rows_by_year'),
                     array('invitations_order_conversion_rate', 'invitations_order_conversion_rate', 'count_rows_by_day'),
                     array('invitations_order_conversion_rate', 'invitations_order_conversion_rate', 'count_rows_by_month'),
                     array('invitations_order_conversion_rate', 'invitations_order_conversion_rate', 'count_rows_by_year'),
                     array('report_customer_totals', 'customer_by_orders_total_table', 'count_rows_by_year'),
                     array('report_invitations_general', 'report_invitations_general_grid', 'count_rows_by_day'),
                     array('report_invitations_general', 'report_invitations_general_grid', 'count_rows_by_month'),
                     array('report_invitations_general', 'report_invitations_general_grid', 'count_rows_by_year'));
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
        // Check current quantity ordered value
        $this->navigate('report_product_sold');
        $currentDate = date('m/d/y');
        $this->fillField('filter_from', $currentDate);
        $this->fillField('filter_to', $currentDate);
        $this->clickButton('refresh');
        $setXpath = $this->_getControlXpath('pageelement', 'product_sold_grid') . '/tfoot' . '/tr';
        $count = $this->getXpathCount($setXpath);
        $totalBefore = $this->getText($setXpath . "[$count]/*[3]");
        // Create Product
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Create Order
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simple['general_name']));
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        // Steps
        $this->navigate('report_product_sold');
        $currentDate = date('m/d/y');
        $this->fillField('filter_from', $currentDate);
        $this->fillField('filter_to', $currentDate);
        $this->clickButton('refresh');
        //Check Quantity Ordered after  new order created
        $setXpath = $this->_getControlXpath('pageelement', 'product_sold_grid') . '/tfoot' . '/tr';
        $count = $this->getXpathCount($setXpath);
        $totalAfter = $this->getText($setXpath . "[$count]/*[3]");
        $this->assertEquals($totalBefore + 1, $totalAfter);
    }
}