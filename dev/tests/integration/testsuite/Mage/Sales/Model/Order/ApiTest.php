<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
/**
 * Test API getting orders list method
 *
 * @magentoDataFixture Mage/Sales/Model/Order/Api/_files/order.php
 */
class Mage_Sales_Model_Order_ApiTest extends PHPUnit_Framework_TestCase
{
    const STATUS_PENDING = 'pending';

    /**
     * Test info method of sales order API.
     */
    public function testInfo()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::registry('order');
        $orderInfo = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderInfo',
            array(
                $order->getIncrementId()
            )
        );
        /** Check availability of some important fields in response */
        $expectedArrayFields = array('shipping_address', 'billing_address', 'items', 'payment', 'status_history');
        $missingFields = array_diff($expectedArrayFields, array_keys($orderInfo));
        $this->assertEmpty(
            $missingFields,
            sprintf("The following fields must be present in response: %s.", implode(', ', $missingFields))
        );

        /** Check values of some fields received from order info */
        $fieldsToCompare = array(
            'entity_id' => 'order_id',
            'state',
            'status',
            'customer_id',
            'store_id',
            'base_grand_total',
            'increment_id',
            'customer_email',
            'order_currency_code'
        );

        Magento_Test_Helper_Api::checkEntityFields($this, $order->getData(), $orderInfo, $fieldsToCompare);
    }

    /**
     * Test 'addComment' method of sales order API.
     */
    public function testAddComment()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::registry('order');

        $historySizeBefore = count($order->getAllStatusHistory());
        $newOrderStatus = self::STATUS_PENDING;
        $statusChangeComment = "Order status change comment.";
        $isAdded = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderAddComment',
            array(
                $order->getIncrementId(),
                $newOrderStatus,
                $statusChangeComment,
                true
            )
        );
        $this->assertTrue($isAdded, "Comment was not added");

        /** @var Mage_Sales_Model_Order $orderAfter */
        $orderAfter = Mage::getModel('Mage_Sales_Model_Order')->load($order->getId());
        $historyAfter = $orderAfter->getAllStatusHistory();
        $this->assertCount($historySizeBefore + 1, $historyAfter, "History item was not created.");
        /** @var Mage_Sales_Model_Order_Status_History $createdHistoryItem */
        $createdHistoryItem = reset($historyAfter);
        $this->assertEquals($statusChangeComment, $createdHistoryItem->getComment(), 'Comment is invalid.');
    }

    /**
     * Test getting sales order list in other methods
     */
    public function testList()
    {
        if (Magento_Test_Helper_Bootstrap::getInstance()->getDbVendorName() != 'mysql') {
            $this->markTestSkipped('Legacy API is expected to support MySQL only.');
        }
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::registry('order');

        $filters = array(
            'filters' => (object)array(
                'filter' => array(
                    (object)array('key' => 'status', 'value' => $order->getData('status')),
                    (object)array('key' => 'created_at', 'value' => $order->getData('created_at'))
                ),
                'complex_filter' => array(
                    (object)array(
                        'key' => 'order_id',
                        'value' => (object)array('key' => 'in', 'value' => "{$order->getId()},0")
                    ),
                    array(
                        'key' => 'protect_code',
                        'value' => (object)array('key' => 'in', 'value' => $order->getData('protect_code'))
                    )
                )
            )
        );

        $result = Magento_Test_Helper_Api::call($this, 'salesOrderList', $filters);

        if (!isset($result[0])) { // workaround for WS-I
            $result = array($result);
        }
        //should be got array with one order item
        $this->assertInternalType('array', $result);
        $this->assertEquals(1, count($result));
        $this->assertEquals($order->getId(), $result[0]['order_id']);
    }

    /**
     * Test for salesOrderCancel when order is in 'pending' status
     */
    public function testCancelPendingOrder()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::registry('order');

        $order->setStatus(self::STATUS_PENDING)
            ->save();

        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderCancel',
            array(
                'orderIncrementId' => $order->getIncrementId()
            )
        );

        $this->assertTrue((bool)$soapResult, 'API call result in not TRUE');

        // reload order to obtain new status
        $order->load($order->getId());

        $this->assertEquals(Mage_Sales_Model_Order::STATE_CANCELED, $order->getStatus(), 'Status is not CANCELED');
    }

    /**
     * Test for salesOrderHold when order is in 'processing' status
     */
    public function testHoldProcessingOrder()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::registry('order');

        $order->setState(Mage_Sales_Model_Order::STATE_NEW, self::STATUS_PENDING)
            ->save();

        $soapResult = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderHold',
            array(
                'orderIncrementId' => $order->getIncrementId()
            )
        );

        $this->assertTrue((bool)$soapResult, 'API call result in not TRUE');

        // reload order to obtain new status
        $order->load($order->getId());

        $this->assertEquals(Mage_Sales_Model_Order::STATE_HOLDED, $order->getStatus(), 'Status is not HOLDED');
    }

    /**
     * Test for 'unhold' method of sales order API.
     *
     * @depends testHoldProcessingOrder
     */
    public function testUnhold()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::registry('order');
        $isUnholded = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderUnhold',
            array(
                $order->getIncrementId()
            )
        );
        $this->assertTrue($isUnholded, "The order was not unholded.");
        /** @var Mage_Sales_Model_Order $updatedOrder */
        $updatedOrder = Mage::getModel('Mage_Sales_Model_Order');
        $updatedOrder->load($order->getId());
        $this->assertEquals(self::STATUS_PENDING, $updatedOrder->getStatus(), 'Order was not unholded.');
    }
}
