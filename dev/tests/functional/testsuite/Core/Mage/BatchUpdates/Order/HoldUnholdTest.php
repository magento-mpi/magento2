<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_BatchUpdates
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Orders updating using batch updates tests for Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License  (OSL 3.0)
 */
class Core_Mage_BatchUpdates_Order_HoldUnholdTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Sales -> Orders</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Holding orders with status "Pending"</p>
     * <p>Steps:</p>
     * <p>1. Create two orders</p>
     * <p>2. Select action "Hold" for your orders</p>
     * <p>Expected result:</p>
     * <p>Received the message that the orders has been hold</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5939
     */
    public function holdPendingOrders()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        foreach ($searchData as $order) {
            $this->searchAndChoose($order, 'sales_order_grid');
        }
        $this->fillDropdown('filter_massaction', 'Hold');
        $this->clickButton('submit');
        //Verifying
        $this->addParameter('orderQty', '2');
        $this->assertMessagePresent('success', 'success_massaction_hold_order');
    }

    /**
     * <p>Holding orders with status "Processing"</p>
     * <p>Steps:</p>
     * <p>1. Create two orders</p>
     * <p>2. Create invoice for your orders</p>
     * <p>3. Select action "Hold" for your orders</p>
     * <p>Expected result:</p>
     * <p>Received the message that the orders has been hold</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5940
     */
    public function holdProcessingOrders()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        $this->orderHelper()->createProcessingOrderWithInvoice($searchData['order1']);
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createProcessingOrderWithInvoice($searchData['order2']);
        $this->navigate('manage_sales_orders');
        foreach ($searchData as $order) {
            $this->searchAndChoose($order, 'sales_order_grid');
        }
        $this->addParameter('orderQty', '2');
        $this->fillDropdown('filter_massaction', 'Hold');
        $this->clickButton('submit');
        //Verifying
        $this->assertMessagePresent('success', 'success_massaction_hold_order');
    }

    /**
     * <p>Holding orders with status "Complete"</p>
     * <p>Steps:</p>
     * <p>1. Create two orders</p>
     * <p>2. Create invoice and shipment for your orders</p>
     * <p>3. Select action "Hold" for your orders</p>
     * <p>Expected result:</p>
     * <p>Received the message that the orders has not been hold</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5941
     */
    public function holdCompleteOrders()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        $this->orderHelper()->createCompleteOrder($searchData['order1']);
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createCompleteOrder($searchData['order2']);
        $this->navigate('manage_sales_orders');
        foreach ($searchData as $order) {
            $this->searchAndChoose($order, 'sales_order_grid');
        }
        $this->addParameter('orderQty', '2');
        $this->fillDropdown('filter_massaction', 'Hold');
        $this->clickButton('submit');
        //Verifying
        $this->assertMessagePresent('error', 'failed_hold_order');
    }

    /**
     * <p>Holding orders with status "Closed"</p>
     * <p>Steps:</p>
     * <p>1. Create two orders</p>
     * <p>2. Create invoice, shipment and credit memo for your orders</p>
     * <p>3. Select action "Hold" for your orders</p>
     * <p>Expected result:</p>
     * <p>Received the message that the orders has not been hold</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5942
     */
    public function holdClosedOrder()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        $this->orderHelper()->createClosedOrder($searchData['order1']);
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createClosedOrder($searchData['order2']);
        $this->navigate('manage_sales_orders');
        foreach ($searchData as $order) {
            $this->searchAndChoose($order, 'sales_order_grid');
        }
        $this->addParameter('orderQty', '2');
        $this->fillDropdown('filter_massaction', 'Hold');
        $this->clickButton('submit');
        //Verifying
        $this->assertMessagePresent('error', 'failed_hold_order');
    }

    /**
     * <p>Holding orders with status "Cancel"</p>
     * <p>Steps:</p>
     * <p>1. Create two orders</p>
     * <p>2. Create invoice and cancel your orders</p>
     * <p>3. Select action "Hold" for your orders</p>
     * <p>Expected result:</p>
     * <p>Received the message that the orders has not been hold</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5943
     */
    public function holdCancelOrder()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        $this->orderHelper()->createCanceledOrder($searchData['order1']);
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createCanceledOrder($searchData['order2']);
        $this->navigate('manage_sales_orders');
        foreach ($searchData as $order) {
            $this->searchAndChoose($order, 'sales_order_grid');
        }
        $this->addParameter('orderQty', '2');
        $this->fillDropdown('filter_massaction', 'Hold');
        $this->clickButton('submit');
        //Verifying
        $this->assertMessagePresent('error', 'failed_hold_order');
    }

    /**
     * <p>Holding orders with status "On Hold"</p>
     * <p>Steps:</p>
     * <p>1. Create two orders</p>
     * <p>2. Create invoice and hold your orders</p>
     * <p>3. Select action "Hold" for your orders</p>
     * <p>Expected result:</p>
     * <p>Received the message that the orders has not been hold</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6001
     */
    public function holdAlreadyHoldOrders()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        $this->orderHelper()->createHoldenOrder($searchData['order1']);
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createHoldenOrder($searchData['order2']);
        $this->navigate('manage_sales_orders');
        foreach ($searchData as $order) {
            $this->searchAndChoose($order, 'sales_order_grid');
        }
        $this->fillDropdown('filter_massaction', 'Hold');
        $this->clickButton('submit');
        //Verifying
        $this->addParameter('orderQty', '2');
        $this->assertMessagePresent('error', 'failed_hold_order');
    }

    /**
     * <p>Holding orders with different status</p>
     * <p>Steps:</p>
     * <p>1. Create two orders with status "Pending" and "Hold"</p>
     * <p>3. Select action "Hold" for your orders</p>
     * <p>Expected result:</p>
     * <p>Received tow messages that one order has been hold and one not</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5944
     */
    public function holdOrdersWithDifferentStatus()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        $this->orderHelper()->createProcessingOrderWithInvoice($searchData['order1']);
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createHoldenOrder($searchData['order2']);
        $this->navigate('manage_sales_orders');
        foreach ($searchData as $order) {
            $this->searchAndChoose($order, 'sales_order_grid');
        }
        $this->fillDropdown('filter_massaction', 'Hold');
        $this->clickButton('submit');
        //Verifying
        $this->addParameter('orderQty', '1');
        $this->assertMessagePresent('success', 'failed_massaction_hold_order');
        $this->assertMessagePresent('success', 'success_massaction_hold_order');
    }

    /**
     * <p>Unhold orders with status "Pending"</p>
     * <p>Steps:</p>
     * <p>1. Create two orders</p>
     * <p>2. Select action "Unhold" for your orders</p>
     * <p>Expected result:</p>
     * <p>Received the message that the orders has been not unhold</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5946
     */
    public function unholdOrdersNegative()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        foreach ($searchData as $order) {
            $this->searchAndChoose($order, 'sales_order_grid');
        }
        $this->addParameter('orderQty', '2');
        $this->fillDropdown('filter_massaction', 'Unhold');
        $this->clickButton('submit');
        //Verifying
        $this->assertMessagePresent('error', 'failed_unhold_order');
    }

    /**
     * <p>Unhold orders with status "Hold"</p>
     * <p>Steps:</p>
     * <p>1. Create two orders and hold them</p>
     * <p>2. Select action "Unhold" for your orders</p>
     * <p>Expected result:</p>
     * <p>Received the message that the orders has been unholden</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5947
     */
    public function unholdUnholdenOrders()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        $this->orderHelper()->createHoldenOrder($searchData['order1']);
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createHoldenOrder($searchData['order2']);
        $this->navigate('manage_sales_orders');
        foreach ($searchData as $order) {
            $this->searchAndChoose($order, 'sales_order_grid');
        }
        $this->addParameter('orderQty', '2');
        $this->fillDropdown('filter_massaction', 'Unhold');
        $this->clickButton('submit');
        //Verifying
        $this->assertMessagePresent('success', 'success_massaction_unhold_order');
    }

    /**
     * <p>Unhold orders with different status</p>
     * <p>Steps:</p>
     * <p>1. Create two orders with status "Pending" and "Hold"</p>
     * <p>3. Select action "Unhold" for your orders</p>
     * <p>Expected result:</p>
     * <p>Received tow messages that one order has been unhold and one not</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5948
     */
    public function unholdOrdersWithDifferentStatus()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        $this->orderHelper()->createProcessingOrderWithInvoice($searchData['order1']);
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createHoldenOrder($searchData['order2']);
        $this->navigate('manage_sales_orders');
        foreach ($searchData as $order) {
            $this->searchAndChoose($order, 'sales_order_grid');
        }
        $this->fillDropdown('filter_massaction', 'Unhold');
        $this->clickButton('submit');
        //Verifying
        $this->addParameter('orderQty', '1');
        $this->assertMessagePresent('error', 'failed_massaction_unhold_order');
        $this->assertMessagePresent('success', 'success_massaction_unhold_order');
    }

    /**
     * <p>Update Attributes fot orders using Batch Updates Negative test</p>
     * <p>Steps:</p>
     * <p>1. Select any value in "Action" dropdown</p>
     * <p>2. Click button "Submit"</p>
     * <p>Expected result:</p>
     * <p>Received the popup message "Please select items.".</p>
     *
     * @test
     * @param string $actionValue
     * @dataProvider updateAttributesByBatchUpdatesNegativeDataProvider
     */
    public function updateAttributesByBatchUpdatesNegative($actionValue)
    {
        //Steps
        $this->navigate('manage_sales_orders');
        $this->fillDropdown('filter_massaction', $actionValue);
        $this->clickButton('submit', false);
        //Verifying
        $this->assertSame('Please select items.', $this->alertText(),
            'actual and expected confirmation message does not match');
        $this->acceptAlert();
    }

    public function updateAttributesByBatchUpdatesNegativeDataProvider()
    {
        return array(
            array('Cancel'),
            array('Hold'),
            array('Unhold'),
            array('Print Invoices'),
            array('Print Packing Slips'),
            array('Print Credit Memos'),
            array('Print All'),
            array('Print Shipping Labels')
        );
    }

    /**
     * <p>Creating one product and two orders for tests</p>
     * @return array $searchData
     */
    public function createOrders()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        $simpleSku = $productData['general_name'];
        //Data
        $orderData1 = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku));
        $searchData['order1'] = $this->loadDataSet('SalesOrder', 'backend_search_order',
            array('filter_billing_name' => $orderData1['billing_addr_data']['billing_first_name']));
        $orderData2 = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku));
        $searchData['order2'] = $this->loadDataSet('SalesOrder', 'backend_search_order',
            array('filter_billing_name' => $orderData2['billing_addr_data']['billing_first_name']));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData1, false);
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData2, false);
        $this->navigate('manage_sales_orders');

        return $searchData;
    }
}