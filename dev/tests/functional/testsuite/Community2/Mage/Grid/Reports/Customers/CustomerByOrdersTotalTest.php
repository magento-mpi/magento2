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
class Community2_Mage_Grid_Report_Customers_CustomerByOrdersTotalTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Get TOP entity, Customer Name and Total Amount, from Grid </p>
     * @return array
     */
    protected  function _getTopCustomerNameAndTotalAmount()
    {
        $this->navigate('report_customer_totals');
        $this->gridHelper()->fillDateFromTo();
        $this->clickButton('refresh');
        //get TOP  "Total Order Amount" from first row in grid
        $topOrderAmountXpath = $this->_getControlXpath('pageelement', 'top_order_amount');
        if($this->isElementPresent($topOrderAmountXpath))
        {
            $topOrderAmountData = preg_replace("/[^\d.]/", "", $this->getElementByXpath($topOrderAmountXpath));
            //get TOP "Customer Name" from first row in grid
            $topCustomerNameXpath = $this->_getControlXpath('pageelement', 'top_customer_name');
            $topCustomerNameData = $this->getElementByXpath($topCustomerNameXpath);
            //get "Number of Orders" from first row in grid
            $topNumberOfOrderXpath = $this->_getControlXpath('pageelement', 'top_number_of_order');
            $topNumberOfOrderData = $this->getElementByXpath($topNumberOfOrderXpath);

            return array('customer_name'    => $topCustomerNameData,
                         'order_amount'     => $topOrderAmountData,
                         'number_of_orders' => $topNumberOfOrderData
            );
        }

        return null;
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
        if(isset($topReportData))
        {
            $priceForTestProduct['prices_price'] = $topReportData['order_amount'] * 2;
        }
        $simple = $this->loadDataSet('Product', 'simple_product_visible', $priceForTestProduct);
        $userData = $this->loadDataSet('Customers', 'generic_customer_account',
            array('first_name' => $this->generate('string', 10, ':alnum:')));
        $addressData = $this->loadDataSet('SalesOrderActions', 'customer_addresses');
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
       $orderData = array(
           'sku'   => $simple['general_name'],
           'email' => $userData['email'],
       );
        $orderCreationData = $this->loadDataSet('SalesOrderActions', 'order_data',
            array('filter_sku' => $orderData['sku'], 'email' => $orderData['email']));
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderCreationData);
        $this->assertMessagePresent('success', 'success_created_order');

        return array(
            'first_name' => $userData['first_name'],
            'last_name'  => $userData['last_name'],
            'email'      => $userData['email'],
            'price'      => $simple['prices_price'],
            'sku'        => $simple['general_name'],
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
     * <p>After step 3: TOP line in grid conatains First and Last name of test customer, number of order =1. Total amount is equals of simple test product price</p>
     * <p>After step 5: TOP line in grid conatains First and Last name of test customer, number of order =2. Total amount is equals of simple test product price *2</p>
     *
     * @depends createTopEntityInReportGridTest
     *
     * @test
     * @TestlinkId TL-MAGE-6442
     */
    public function verifyDataInGridTest($testOrderData)
    {
        $topReportGridData = $this->_getTopCustomerNameAndTotalAmount();

        $this->assertEquals($topReportGridData['customer_name'], $testOrderData['first_name'] . ' '
                             . $testOrderData['last_name'], 'Customer Name is wrong' );
        $this->assertEquals(1 , $topReportGridData['number_of_orders'], 'Number of orders is wrong');
        $this->assertEquals($testOrderData['price'] , $topReportGridData['order_amount'], 'Order amount is wrong');
        $this->navigate('manage_sales_orders');
        $orderCreationData = $this->loadDataSet('SalesOrderActions', 'order_data', array(
            'filter_sku' => $testOrderData['sku'],
            'email' => $testOrderData['email'])
        );
        $this->orderHelper()->createOrder($orderCreationData);
        $this->assertMessagePresent('success', 'success_created_order');
        $topReportGridDataUpdated = $this->_getTopCustomerNameAndTotalAmount();
        $this->assertEquals($topReportGridDataUpdated['customer_name'], $testOrderData['first_name'] . ' '
                             . $testOrderData['last_name'], 'Customer Name is wrong' );
        $this->assertEquals(2 , $topReportGridDataUpdated['number_of_orders'], 'Number of orders is wrong');
        $this->assertEquals($testOrderData['price'] * 2 , $topReportGridDataUpdated['order_amount'],
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
        $this->navigate('report_customer_accounts');
        $this->gridHelper()->fillDateFromTo();
        $this->clickButton('refresh');
        $this->pleaseWait();
        $gridXpath = $this->_getControlXpath('pageelement', 'report_customer_accounts_table').'/tfoot/tr/th[2]';
        $count = $this->getElementByXpath($gridXpath);
        //Steps
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $this->frontend();
        $this->navigate('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_registration');
        $this->loginAdminUser();
        $this->navigate('report_customer_accounts');
        $this->gridHelper()->fillDateFromTo();
        $this->clickButton('refresh');
        $this->pleaseWait();
        //Verifying
        $this->assertEquals($count+1, $this->getElementByXpath($gridXpath),
            'Wrong records number in grid report_customer_accounts_table');
    }
}