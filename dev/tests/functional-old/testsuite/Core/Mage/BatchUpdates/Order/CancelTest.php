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
class Core_Mage_BatchUpdates_Order_CancelTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
    }

    public function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Canceling orders with status "Pending"</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5934
     */
    public function cancelPendingOrders()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        foreach ($searchData as $order) {
            $this->searchAndChoose($order, 'sales_order_grid');
        }
        $this->fillDropdown('filter_massaction', 'Cancel');
        $this->clickButton('submit');
        //Verifying
        $this->addParameter('orderQty', '2');
        $this->assertMessagePresent('success', 'success_massaction_canceled_order');
    }

    /**
     * <p>Canceling orders with status "Processing" (only Invoice created)</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5933
     */
    public function cancelProcessingOrdersWithInvoice()
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
        $this->fillDropdown('filter_massaction', 'Cancel');
        $this->clickButton('submit');
        //Verifying
        $this->addParameter('orderQty', '2');
        $this->assertMessagePresent('success', 'failed_massaction_cancel_order');
    }

    /**
     * <p>Canceling orders with status "Processing" (only Shipment created)</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5987
     */
    public function cancelProcessingOrdersWithShipment()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        $this->orderHelper()->createProcessingOrderWithShipment($searchData['order1']);
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createProcessingOrderWithShipment($searchData['order2']);
        $this->navigate('manage_sales_orders');
        foreach ($searchData as $order) {
            $this->searchAndChoose($order, 'sales_order_grid');
        }
        $this->fillDropdown('filter_massaction', 'Cancel');
        $this->clickButton('submit');
        //Verifying
        $this->addParameter('orderQty', '2');
        $this->assertMessagePresent('success', 'success_massaction_canceled_order');
    }

    /**
     * <p>Canceling orders with status "Complete" (Invoice and Shipment created)</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5935
     */
    public function cancelCompleteOrders()
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
        $this->fillDropdown('filter_massaction', 'Cancel');
        $this->clickButton('submit');
        //Verifying
        $this->addParameter('orderQty', '2');
        $this->assertMessagePresent('success', 'failed_massaction_cancel_order');
    }

    /**
     * <p>Canceling orders with status "Closed" (Invoice, Shipment and Credit Memo created)</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5936
     */
    public function cancelClosedOrders()
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
        $this->fillDropdown('filter_massaction', 'Cancel');
        $this->clickButton('submit');
        //Verifying
        $this->addParameter('orderQty', '2');
        $this->assertMessagePresent('success', 'failed_massaction_cancel_order');
    }

    /**
     * <p>Canceling orders with status "Canceled"</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6000
     */
    public function cancelCanceledOrders()
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
        $this->fillDropdown('filter_massaction', 'Cancel');
        $this->clickButton('submit');
        //Verifying
        $this->addParameter('orderQty', '2');
        $this->assertMessagePresent('success', 'failed_massaction_cancel_order');
    }

    /**
     * <p>Canceling orders with status "On Hold"</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5937
     */
    public function cancelHoldenOrders()
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
        $this->fillDropdown('filter_massaction', 'Cancel');
        $this->clickButton('submit');
        //Verifying
        $this->addParameter('orderQty', '2');
        $this->assertMessagePresent('success', 'failed_massaction_cancel_order');
    }

    /**
     * <p>Canceling orders with different status - "Pending" and "On Hold"</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5938
     */
    public function cancelDifferentOrders()
    {
        //Data
        $searchData = $this->createOrders();
        //Steps
        $this->orderHelper()->createProcessingOrderWithShipment($searchData['order1']);
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createHoldenOrder($searchData['order2']);
        $this->navigate('manage_sales_orders');
        foreach ($searchData as $order) {
            $this->searchAndChoose($order, 'sales_order_grid');
        }
        $this->fillDropdown('filter_massaction', 'Cancel');
        $this->clickButton('submit');
        //Verifying
        $this->addParameter('orderQty', '1');
        $this->assertMessagePresent('success', 'failed_massaction_cancel_different_order');
        $this->assertMessagePresent('success', 'success_massaction_canceled_order');
    }

    /**
     * <p>Creating one product and two orders for tests</p>
     *
     * @return array $searchData
     */
    private function createOrders()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        $simpleSku = $productData['general_sku'];
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->navigate('manage_sales_orders');
        $orderId1 = $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->navigate('manage_sales_orders');
        $orderId2 = $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->navigate('manage_sales_orders');
        $searchData['order1'] = $this->loadDataSet('SalesOrder', 'backend_search_order',
            array('filter_order_id' => $orderId1));
        $searchData['order2'] = $this->loadDataSet('SalesOrder', 'backend_search_order',
            array('filter_order_id' => $orderId2));
        return $searchData;
    }
}