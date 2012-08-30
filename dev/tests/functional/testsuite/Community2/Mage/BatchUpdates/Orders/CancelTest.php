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
 * Orders updating using batch updates tests for  Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License  (OSL 3.0)
 */
class Community2_Mage_BatchUpdates_Orders_CancelTest extends Mage_Selenium_TestCase
{
    public function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Canceling orders with status "Pending"</p>
     * <p>Steps:</p>
     * <p>1. Create two orders</p>
     * <p>2. Select action "Cancel" for your orders</p>
     * <p>Expected result:</p>
     * <p>Received the message that the orders has been Canceled</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5934
     */
    public function cancelPendingOrders()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        $this->searchAndChoose($searchData['order1']);
        $this->searchAndChoose($searchData['order2']);
        $this->fillDropdown('filter_massaction', 'Cancel');
        $this->clickButton('submit');
        //Verifying
        $this->addParameter('orderQty', '2');
        $this->assertMessagePresent('success', 'success_massaction_canceled_order');
    }

    /**
     * <p>Canceling orders with status "Processing" (only Invoice created)</p>
     * <p>Steps:</p>
     * <p>1. Create two orders</p>
     * <p>2. Create invoice for each order</p>
     * <p>3. Select action "Cancel" for your orders</p>
     * <p>Expected result:</p>
     * <p>Received the message that the orders cannot be Canceled</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5933
     */
    public function cancelProcessingOrdersWithInvoice()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        $this->ordersHelper()->createProcessingOrderWithInvoice($searchData['order1']);
        $this->navigate('manage_sales_orders');
        $this->ordersHelper()->createProcessingOrderWithInvoice($searchData['order2']);
        $this->navigate('manage_sales_orders');
        $this->searchAndChoose($searchData['order1']);
        $this->searchAndChoose($searchData['order2']);
        $this->fillDropdown('filter_massaction', 'Cancel');
        $this->clickButton('submit');
        //Verifying
        $this->addParameter('orderQty', '2');
        $this->assertMessagePresent('success', 'failed_massaction_cancel_order');
    }

    /**
     * <p>Canceling orders with status "Processing" (only Shipment created)</p>
     * <p>Steps:</p>
     * <p>1. Create two orders</p>
     * <p>2. Create shipment for each order</p>
     * <p>3. Select action "Cancel" for your orders</p>
     * <p>Expected result:</p>
     * <p>Received the message that the orders has been Canceled</p>
     *
     *
     * @test
     * @TestlinkId TL-MAGE-5987
     */
    public function cancelProcessingOrdersWithShipment()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        $this->ordersHelper()->createProcessingOrderWithShipment($searchData['order1']);
        $this->navigate('manage_sales_orders');
        $this->ordersHelper()->createProcessingOrderWithShipment($searchData['order2']);
        $this->navigate('manage_sales_orders');
        $this->searchAndChoose($searchData['order1']);
        $this->searchAndChoose($searchData['order2']);
        $this->fillDropdown('filter_massaction', 'Cancel');
        $this->clickButton('submit');
        //Verifying
        $this->addParameter('orderQty', '2');
        $this->assertMessagePresent('success', 'success_massaction_canceled_order');
    }

    /**
     * <p>Canceling orders with status "Complete" (Invoice and Shipment created)</p>
     * <p>Steps:</p>
     * <p>1. Create two orders</p>
     * <p>2. Create shipment for each order</p>
     * <p>2. Create shipment for each order</p>
     * <p>3. Select action "Cancel" for your orders</p>
     * <p>Expected result:</p>
     * <p>Received the message that the orders has been Canceled</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5935
     */
    public function cancelCompleteOrders()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        $this->ordersHelper()->createCompleteOrder($searchData['order1']);
        $this->navigate('manage_sales_orders');
        $this->ordersHelper()->createCompleteOrder($searchData['order2']);
        $this->navigate('manage_sales_orders');
        $this->searchAndChoose($searchData['order1']);
        $this->searchAndChoose($searchData['order2']);
        $this->fillDropdown('filter_massaction', 'Cancel');
        $this->clickButton('submit');
        //Verifying
        $this->addParameter('orderQty', '2');
        $this->assertMessagePresent('success', 'failed_massaction_cancel_order');
    }

    /**
     * <p>Canceling orders with status "Closed" (Invoice, Shipment and Credit Memo created)</p>
     * <p>Steps:</p>
     * <p>1. Create two orders</p>
     * <p>2. Create shipment for each order</p>
     * <p>3. Create shipment for each order</p>
     * <p>4. Create credit memo for each order</p>
     * <p>5. Select action "Cancel" for your orders</p>
     * <p>Expected result:</p>
     * <p>Received the message that the orders cannot be Canceled</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5936
     */
    public function cancelClosedOrders()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        $this->ordersHelper()->createClosedOrder($searchData['order1']);
        $this->navigate('manage_sales_orders');
        $this->ordersHelper()->createClosedOrder($searchData['order2']);
        $this->navigate('manage_sales_orders');
        $this->searchAndChoose($searchData['order1']);
        $this->searchAndChoose($searchData['order2']);
        $this->fillDropdown('filter_massaction', 'Cancel');
        $this->clickButton('submit');
        //Verifying
        $this->addParameter('orderQty', '2');
        $this->assertMessagePresent('success', 'failed_massaction_cancel_order');
    }

    /**
     * <p>Canceling orders with status "Canceled"</p>
     * <p>Steps:</p>
     * <p>1. Create two orders</p>
     * <p>2. Cancel each order</p>
     * <p>3. Select action "Cancel" for your orders</p>
     * <p>Expected result:</p>
     * <p>Received the message that the orders cannot be Canceled</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6000
     */
    public function cancelCanceledOrders()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        $this->ordersHelper()->createCanceledOrder($searchData['order1']);
        $this->navigate('manage_sales_orders');
        $this->ordersHelper()->createCanceledOrder($searchData['order2']);
        $this->navigate('manage_sales_orders');
        $this->searchAndChoose($searchData['order1']);
        $this->searchAndChoose($searchData['order2']);
        $this->fillDropdown('filter_massaction', 'Cancel');
        $this->clickButton('submit');
        //Verifying
        $this->addParameter('orderQty', '2');
        $this->assertMessagePresent('success', 'failed_massaction_cancel_order');
    }

    /**
     * <p>Canceling orders with status "On Hold"</p>
     * <p>Steps:</p>
     * <p>1. Create two orders</p>
     * <p>2. Hold each order</p>
     * <p>3. Select action "Cancel" for your orders</p>
     * <p>Expected result:</p>
     * <p>Received the message that the orders cannot be Canceled</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5937
     */
    public function cancelHoldedOrders()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        $this->ordersHelper()->createHoldedOrder($searchData['order1']);
        $this->navigate('manage_sales_orders');
        $this->ordersHelper()->createHoldedOrder($searchData['order2']);
        $this->navigate('manage_sales_orders');
        $this->searchAndChoose($searchData['order1']);
        $this->searchAndChoose($searchData['order2']);
        $this->fillDropdown('filter_massaction', 'Cancel');
        $this->clickButton('submit');
        //Verifying
        $this->addParameter('orderQty', '2');
        $this->assertMessagePresent('success', 'failed_massaction_cancel_order');
    }

    /**
     * <p>Canceling orders with different status - "Pending" and "On Hold"</p>
     * <p>Steps:</p>
     * <p>1. Create two orders</p>
     * <p>2. Hold one order</p>
     * <p>3. Select action "Cancel" for your orders</p>
     * <p>Expected result:</p>
     * <p>Received two messages that the one order cannot be Canceled and one order has been Canceled</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5938
     */
    public function cancelDifferentOrders()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        $this->ordersHelper()->createProcessingOrderWithShipment($searchData['order1']);
        $this->navigate('manage_sales_orders');
        $this->ordersHelper()->createHoldedOrder($searchData['order2']);
        $this->navigate('manage_sales_orders');
        $this->searchAndChoose($searchData['order1']);
        $this->searchAndChoose($searchData['order2']);
        $this->fillDropdown('filter_massaction', 'Cancel');
        $this->clickButton('submit');
        //Verifying
        $this->addParameter('orderQty', '1');
        $this->assertMessagePresent('success', 'failed_massaction_cancel_different_order');
        $this->assertMessagePresent('success', 'success_massaction_canceled_order');
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
            array('filter_billing_name'=> $orderData2['billing_addr_data']['billing_first_name']));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData1, false);
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData2, false);
        $this->navigate('manage_sales_orders');

        return $searchData;
    }
}