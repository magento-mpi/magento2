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
     * <p>Holding orders with status "Pending"</p>
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