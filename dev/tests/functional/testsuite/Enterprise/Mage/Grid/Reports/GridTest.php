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
class Enterprise_Mage_Grid_Reports_GridTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Post conditions:</p>
     * <p>Log out from Backend.</p>
     */
    protected function tearDownAfterTestClass()
    {
        $this->logoutAdminUser();
    }

    /**
     * Need to verify that all elements is presented on invitation reports_invitations_customers page
     * @test
     * @dataProvider uiElementsTestDataProvider
     *
     */
    public function uiElementsTest($pageName)
    {
        $this->navigate($pageName);
        $page = $this->loadDataSet('Report', 'grid');
        foreach ($page[$pageName] as $control => $type) {
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
            array('reports_invitations_customers'),
            array('report_product_sold'),
            array('report_customer_totals'),
            array('reports_invitations_general')
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
        $gridElement = $this->getControlElement('pageelement', $gridTableElement);
        $this->assertEquals(3, count($this->getChildElements($gridElement, 'tbody/tr', false)),
            "Wrong records number in grid $gridTableElement");
    }

    public function countGridRowsTestDataProvider()
    {
        return array(
            array('report_product_sold', 'product_sold_grid', 'count_rows_by_day'),
            array('report_product_sold', 'product_sold_grid', 'count_rows_by_month'),
            array('report_product_sold', 'product_sold_grid', 'count_rows_by_year'),
            array('reports_invitations_customers', 'report_invitations_customers_grid', 'count_rows_by_day'),
            array('reports_invitations_customers', 'report_invitations_customers_grid', 'count_rows_by_month'),
            array('reports_invitations_customers', 'report_invitations_customers_grid', 'count_rows_by_year'),
            array('report_customer_totals', 'customer_by_orders_total_table', 'count_rows_by_day'),
            array('report_customer_totals', 'customer_by_orders_total_table', 'count_rows_by_month'),
            array('report_customer_totals', 'customer_by_orders_total_table', 'count_rows_by_year'),
            array('invitations_order_conversion_rate', 'invitations_order_conversion_rate_grid', 'count_rows_by_day'),
            array('invitations_order_conversion_rate', 'invitations_order_conversion_rate_grid', 'count_rows_by_month'),
            array('invitations_order_conversion_rate', 'invitations_order_conversion_rate_grid', 'count_rows_by_year'),
            array('reports_invitations_general', 'report_invitations_general_grid', 'count_rows_by_day'),
            array('reports_invitations_general', 'report_invitations_general_grid', 'count_rows_by_month'),
            array('reports_invitations_general', 'report_invitations_general_grid', 'count_rows_by_year')
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
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $firstDate = $this->customerHelper()->getCustomerRegistrationDate(array('email' => $userData['email']));
        // Check current quantity ordered value
        $this->navigate('report_product_sold');
        $this->gridHelper()->fillDateFromTo($firstDate, $firstDate);
        $this->clickButton('refresh');
        $lineLocator = $this->_getControlXpath('pageelement', 'product_sold_grid_line');
        $count = $this->getControlCount('pageelement', 'product_sold_grid_line');
        $totalBefore = $this->getElement($lineLocator . "[$count]/*[3]")->text();
        // Create Product
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Create Order
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simple['general_sku']));
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $secondDate = $this->getControlAttribute('pageelement', 'order_date', 'text');
        // Steps
        $this->navigate('report_product_sold');
        $this->gridHelper()->fillDateFromTo($firstDate, $secondDate);
        $this->clickButton('refresh');
        //Check Quantity Ordered after  new order created
        $count = $this->getControlCount('pageelement', 'product_sold_grid_line');
        $totalAfter = $this->getElement($lineLocator . "[$count]/*[3]")->text();
        $this->assertEquals($totalBefore + 1, $totalAfter);
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
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $firstDate = $this->customerHelper()->getCustomerRegistrationDate(array('email' => $userData['email']));
        // Get Total Number of Orders
        $this->navigate('report_customer_orders');
        $this->gridHelper()->fillDateFromTo($firstDate, $firstDate);
        $this->clickButton('refresh');
        $lineLocator = $this->_getControlXpath('pageelement', 'customer_orders_grid_line');
        $count = $this->getControlCount('pageelement', 'customer_orders_grid_line');
        $totalBefore = $this->getElement($lineLocator . "[$count]/*[3]")->text();
        // Create Product
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Create Order
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simple['general_sku']));
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $secondDate = $this->getControlAttribute('pageelement', 'order_date', 'text');
        // Steps
        $this->navigate('report_customer_orders');
        $this->gridHelper()->fillDateFromTo($firstDate, $secondDate);
        $this->clickButton('refresh');
        //Check Quantity Ordered after  new order created
        $count = $this->getControlCount('pageelement', 'customer_orders_grid_line');
        $totalAfter = $this->getElement($lineLocator . "[$count]/*[3]")->text();
        $this->assertEquals($totalBefore + 1, $totalAfter);
    }

    /**
     *<p>PreConditions</p>
     *<p>1.Go to Report - Invitations - Order Conversion rate</p>
     *<p>2.Filter data with filled "From", "To" used current Day value</p>
     *<p>2.Get Invitation Sent Number </p>
     *<p>3.Send Invitation from customer account on frontend with newly created customer on backend</p>
     *<p>Steps:</p>
     *<p>1.Go to Report - Product Ordered page</p>
     *<p>2.Filter data with filled "From", "To" used current Day value</p>
     *<p>Actual Results:</p>
     *<p>1.Invitation Sent value = Value from PreConditions +1 </p>
     *
     * @test
     */
    public function checkInvitationSentCustomerOrderGridTestTest()
    {
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $loginData = array('email' => $userData['email'], 'password' => $userData['password']);
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $firstDate = $this->customerHelper()->getCustomerRegistrationDate(array('email' => $userData['email']));
        //Go to Report - Invitations - Order Conversion rate
        $this->navigate('invitations_order_conversion_rate');
        //Get Invitation Sent Number
        $this->gridHelper()->fillDateFromTo($firstDate, $firstDate);
        $this->clickButton('refresh');
        $lineXpath = $this->_getControlXpath('pageelement', 'invitations_order_conversion_rate_line');
        $count = $this->getControlCount('pageelement', 'invitations_order_conversion_rate_line');
        $totalBefore = $this->getElement($lineXpath . "[$count]/*[2]")->text();
        //Send Invitation from customer account on frontend with newly created customer on backend
        $this->customerHelper()->frontLoginCustomer($loginData);
        $this->validatePage('customer_account');
        $this->invitationHelper()->sendInvitationFrontend(1, $messageType = 'success', 'success_send');
        // Steps
        $this->loginAdminUser();
        $this->navigate('invitations_order_conversion_rate');
        $this->gridHelper()->fillDateFromTo($firstDate, $firstDate);;
        $this->clickButton('refresh');
        //Check Invitation sent value
        $count = $this->getControlCount('pageelement', 'invitations_order_conversion_rate_line');
        $totalAfter = $this->getElement($lineXpath . "[$count]/*[2]")->text();
        $this->assertEquals($totalBefore + 1, $totalAfter,
            'Wrong records number in invitations_order_conversion_rate grid');
    }
}