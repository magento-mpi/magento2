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
 * @magentoDataFixture Magento/Sales/Model/Order/Api/_files/order.php
 */
class Magento_Sales_Model_Order_ApiTest extends PHPUnit_Framework_TestCase
{
    const STATUS_PENDING = 'pending';

    protected $_order;

    protected function setUp()
    {
        /** @var \Magento\Sales\Model\Resource\Order\Collection $orderCollection */
        $orderCollection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Resource\Order\Collection');
        $orders = $orderCollection->getItems();
        $this->assertCount(2, $orders);
        $this->_order = array_shift($orders);
    }

    /**
     * Test info method of sales order API.
     * @magentoAppArea frontend
     */
    public function testInfo()
    {
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_order;
        $orderInfo = Magento_TestFramework_Helper_Api::call(
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

        Magento_TestFramework_Helper_Api::checkEntityFields($this, $order->getData(), $orderInfo, $fieldsToCompare);
    }

    /**
     * Test 'addComment' method of sales order API.
     */
    public function testAddComment()
    {
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_order;

        $historySizeBefore = count($order->getAllStatusHistory());
        $newOrderStatus = self::STATUS_PENDING;
        $statusChangeComment = "Order status change comment.";
        $isAdded = Magento_TestFramework_Helper_Api::call(
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

        /** @var \Magento\Sales\Model\Order $orderAfter */
        $orderAfter = Mage::getModel('Magento\Sales\Model\Order')->load($order->getId());
        $historyAfter = $orderAfter->getAllStatusHistory();
        $this->assertCount($historySizeBefore + 1, $historyAfter, "History item was not created.");
        /** @var \Magento\Sales\Model\Order\Status\History $createdHistoryItem */
        $createdHistoryItem = reset($historyAfter);
        $this->assertEquals($statusChangeComment, $createdHistoryItem->getComment(), 'Comment is invalid.');
    }

    /**
     * Test getting sales order list in other methods
     */
    public function testList()
    {
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_order;

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

        $result = Magento_TestFramework_Helper_Api::call($this, 'salesOrderList', $filters);

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
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_order;

        $order->setStatus(self::STATUS_PENDING)
            ->save();

        $soapResult = Magento_TestFramework_Helper_Api::call(
            $this,
            'salesOrderCancel',
            array(
                'orderIncrementId' => $order->getIncrementId()
            )
        );

        $this->assertTrue((bool)$soapResult, 'API call result in not TRUE');

        // reload order to obtain new status
        $order->load($order->getId());

        $this->assertEquals(\Magento\Sales\Model\Order::STATE_CANCELED, $order->getStatus(), 'Status is not CANCELED');
    }

    /**
     * Test for salesOrderHold when order is in 'processing' status
     */
    public function testHoldProcessingOrder()
    {
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_order;

        $order->setState(\Magento\Sales\Model\Order::STATE_NEW, self::STATUS_PENDING)
            ->save();

        $soapResult = Magento_TestFramework_Helper_Api::call(
            $this,
            'salesOrderHold',
            array(
                'orderIncrementId' => $order->getIncrementId()
            )
        );

        $this->assertTrue((bool)$soapResult, 'API call result in not TRUE');

        // reload order to obtain new status
        $order->load($order->getId());

        $this->assertEquals(\Magento\Sales\Model\Order::STATE_HOLDED, $order->getStatus(), 'Status is not HOLDED');
    }

    /**
     * Test for 'unhold' method of sales order API.
     *
     * @depends testHoldProcessingOrder
     */
    public function testUnhold()
    {
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_order;
        $isUnholded = Magento_TestFramework_Helper_Api::call(
            $this,
            'salesOrderUnhold',
            array(
                $order->getIncrementId()
            )
        );
        $this->assertTrue($isUnholded, "The order was not unholded.");
        /** @var \Magento\Sales\Model\Order $updatedOrder */
        $updatedOrder = Mage::getModel('Magento\Sales\Model\Order');
        $updatedOrder->load($order->getId());
        $this->assertEquals(self::STATUS_PENDING, $updatedOrder->getStatus(), 'Order was not unholded.');
    }
}
