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

    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p>Preconditions: create customer and order</p>
     * @test
     */
    public function createTestEntityInReportGrid()
    {
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account', array('first_name'=> $this->generate('string', 10, ':alnum:') ));
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
       $orderData = array('sku'   => $simple['general_name'], 'email' => $userData['email']);
        $orderCreationData = $this->loadDataSet('SalesOrderActions', 'order_data',
            array('filter_sku' => $orderData['sku'], 'email'      => $orderData['email']));
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderCreationData);
        $this->assertMessagePresent('success', 'success_created_order');
    }

    /**
     * <p>Verifying that number of elements is increased after create new order</p>
     * <p>Preconditions:</p>
     * <p>1. At least one simple product is created</p>
     * <p>2. Two customers for test order are created</p>
     * <p>Steps:</p>
     * <p>1. Log in to admin</p>
     * <p>2. Create new order for first customer<p>
     * <p>2. Navigate to Reports>Customers>Customer by orders total</p>
     * <p>3. Enter current date in to the "From" and "To" field</p>
     * <p>4. Click Refresh button</p>
     * <p>5. Count qty of rows</p>
     * <p>6. Create new order for second customer</p>
     * <p>8. Navigate to Customers>Customer by orders total</p>
     * <p>9. Enter current date in to the "From" and "To" field</p>
     * <p>10. Click Refresh button</p>
     * <p>11. Count qty of rows</p>
     * <p>Expected result:</p>
     * <p>The count of rows is increased on 1 item</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6442
     */
    public function verifyCountOfEntityInGrid()
    {
        $this->navigate('report_customer_totals');
        $this->gridHelper()->fillDateFromTo();
        $this->clickButton('refresh');
        $this->pleaseWait();
        $gridXpath = $this->_getControlXpath('pageelement', 'customer_by_orders_total_table');
        $count = $this->getElementsByXpath($gridXpath . '/tbody/tr');
        $newCount = count($count) + 1;
        $this->createTestEntityInReportGrid();
        $this->navigate('report_customer_totals');
        $this->gridHelper()->fillDateFromTo();
        $this->assertCount($newCount, $this->getElementsByXpath($gridXpath . '/tbody/tr'),
            'Wrong records number in grid customer_by_orders_total_table:');
    }
}