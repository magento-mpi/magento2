<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Grid_Reports
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Verify of Reports Customer Customer by Orders Total grid
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Grid_Reports_Customers_CustomerByOrdersTotalTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Get TOP entity, Customer Name and Total Amount, from Grid </p>
     * @param null|string $dateFrom
     * @param null|string $dateTo
     * @return array
     */
    protected function _getTopCustomerNameAndTotalAmount($dateFrom = null, $dateTo = null)
    {
        $this->navigate('report_customer_totals');
        $this->gridHelper()->fillDateFromTo($dateFrom, $dateTo);
        $this->clickButton('refresh');
        //get TOP  "Total Order Amount" from first row in grid
        if (!$this->controlIsVisible('pageelement', 'top_order_amount')) {
            return array('customer_name' => '', 'order_amount' => '', 'number_of_orders' => '');
        }
        $topOrderAmountData = preg_replace('/[^\d.]/', '',
            $this->getControlAttribute('pageelement', 'top_order_amount', 'text'));
        //get TOP "Customer Name" from first row in grid
        $topCustomerNameData = $this->getControlAttribute('pageelement', 'top_customer_name', 'text');
        //get "Number of Orders" from first row in grid
        $topNumberOfOrderData = $this->getControlAttribute('pageelement', 'top_number_of_order', 'text');

        return array(
            'customer_name' => $topCustomerNameData,
            'order_amount' => $topOrderAmountData,
            'number_of_orders' => $topNumberOfOrderData
        );
    }

    /**
     * <p>Preconditions: create customer and order in TOP of the Grid</p>
     *
     * <p>1. Create test customers</p>
     * <p>2. Create product with price </p>
     * <p>2.1 Login to backend</p>
     * <p>2.2 Go to Reports>Customers>Customer by orders total and check Top Order Amout value</p>
     * <p>2.3 Create simple product with price = (Top Order Amout value) * 2</p>
     *
     * @return array
     * @test
     */
    public function createTopEntityInReportGridTest()
    {
        $topReportData = $this->_getTopCustomerNameAndTotalAmount();
        $priceForTestProduct = array();
        if ($topReportData['order_amount'] != '') {
            $priceForTestProduct['general_price'] = $topReportData['order_amount'] * 2;
        }
        $simple = $this->loadDataSet('Product', 'simple_product_visible', $priceForTestProduct);
        $userData = $this->loadDataSet('Customers', 'generic_customer_account',
            array('first_name' => $this->generate('string', 10, ':alnum:')));
        $addressData = $this->loadDataSet('Customers', 'generic_address', array(
            'first_name' => $userData['first_name'],
            'default_billing_address' => 'Yes',
            'default_shipping_address' => 'Yes'
        ));
        $orderCreationData = $this->loadDataSet('SalesOrder', 'order_physical', array(
            'filter_sku' => $simple['general_sku'],
            'email' => $userData['email'],
            'customer_email' => '%noValue%',
            'billing_addr_data' => '%noValue%',
            'shipping_addr_data' => $this->loadDataSet('SalesOrder', 'shipping_address_same_as_blling')
        ));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData, $addressData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderCreationData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        $orderDate = $this->getControlAttribute('pageelement', 'order_date', 'text');
        return array(
            'order_date' => $orderDate,
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'email' => $userData['email'],
            'price' => $simple['general_price'],
            'sku' => $simple['general_sku'],
        );
    }

    /**
     * <p>Verifying that number of Orders and total amount are increased after create new order</p>
     * <p>1. Log in to backend as admin</p>
     * <p>2. Create the first order with test product</p>
     * <p>3. Go to Report>Customers> Orders total</p>
     * <p>4. Create the second order with test product</p>
     * <p>5. Go to Report>Customers> Orders total</p>
     * <p>Expected results:</p>
     * <p>After step 3: TOP line in grid contains First and Last name of test customer, number of order =1.
     *    Total amount is equals of simple test product price</p>
     * <p>After step 5: TOP line in grid contains First and Last name of test customer, number of order =2.
     *    Total amount is equals of simple test product price *2</p>
     *
     * @test
     * @depends createTopEntityInReportGridTest
     * @TestlinkId TL-MAGE-6442
     */
    public function verifyDataInGridTest($orderData)
    {
        $topReportData = $this->_getTopCustomerNameAndTotalAmount($orderData['order_date'], $orderData['order_date']);

        $this->assertEquals($orderData['first_name'] . ' ' . $orderData['last_name'], $topReportData['customer_name'],
            'Customer Name is wrong');
        $this->assertEquals(1, $topReportData['number_of_orders'], 'Number of orders is wrong');
        $this->assertEquals($orderData['price'], $topReportData['order_amount'], 'Order amount is wrong');
        $this->navigate('manage_sales_orders');
        $orderCreationData = $this->loadDataSet('SalesOrder', 'order_physical', array(
            'filter_sku' => $orderData['sku'],
            'email' => $orderData['email'],
            'customer_email' => '%noValue%',
            'billing_addr_data' => '%noValue%',
            'shipping_addr_data' => $this->loadDataSet('SalesOrder', 'shipping_address_same_as_blling')
        ));
        $this->orderHelper()->createOrder($orderCreationData);
        $this->assertMessagePresent('success', 'success_created_order');
        $date = $this->getControlAttribute('pageelement', 'order_date', 'text');

        $topReportDataUpdated = $this->_getTopCustomerNameAndTotalAmount($orderData['order_date'], $date);
        $this->assertEquals($topReportDataUpdated['customer_name'], $orderData['first_name'] . ' '
            . $orderData['last_name'], 'Customer Name is wrong');
        $this->assertEquals(2, $topReportDataUpdated['number_of_orders'], 'Number of orders is wrong');
        $this->assertEquals($orderData['price'] * 2, $topReportDataUpdated['order_amount'],
            'Order amount is wrong');
    }

    /**
     * <p>Verifying that number of elements is increased after create new customer on New Customer Account grid</p>
     * <p>Steps:</p>
     * <p>1. Log in to admin</p>
     * <p>2. Navigate to Reports>Customers>New Account</p>
     * <p>3. Enter current date in to the "From" and "To" field</p>
     * <p>4. Click Refresh button</p>
     * <p>5. See qty in Number of New Accounts column</p>
     * <p>6. Create new customer</p>
     * <p>7. Navigate to Reports>Customers>New Account</p>
     * <p>8. Enter current date in to the "From" and "To" field</p>
     * <p>9. Click Refresh button</p>
     * <p>10. See qty in Sent column</p>
     * <p>Expected result:</p>
     * <p>The qty in Number of New Accounts column is increased on 1 item</p>
     * <p>Expected result:</p>
     * <p>The count of rows is increased on 1 item</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6444
     */
    public function verifyCountOfEntityInNewAccountGrid()
    {
        //Data
        $userData1 = $this->loadDataSet('Customers', 'generic_customer_account');
        $userData2 = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData1);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $firstDate = $this->customerHelper()->getCustomerRegistrationDate(array('email' => $userData1['email']));
        $this->navigate('report_customer_accounts');
        $this->gridHelper()->fillDateFromTo($firstDate, $firstDate);
        $this->clickButton('refresh');
        $gridXpath = $this->_getControlXpath('pageelement', 'report_customer_accounts_table') . '/tfoot/tr/th[2]';
        $beforeCount = trim($this->getElement($gridXpath)->text());

        $this->frontend();
        $this->navigate('customer_login');
        $this->customerHelper()->registerCustomer($userData2);
        $this->assertMessagePresent('success', 'success_registration');
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $secondDate = $this->customerHelper()->getCustomerRegistrationDate(array('email' => $userData1['email']));
        $this->navigate('report_customer_accounts');
        $this->gridHelper()->fillDateFromTo($firstDate, $secondDate);
        $this->clickButton('refresh');
        //Verifying
        $afterCount = trim($this->getElement($gridXpath)->text());
        $this->assertEquals($beforeCount + 1, $afterCount,
            'Wrong records number in grid report_customer_accounts_table. Before was ' . $afterCount
                . ' after - ' . $afterCount);
    }
}