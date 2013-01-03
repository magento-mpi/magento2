<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Test API getting orders list method
 *
 * @magentoDataFixture Mage/Sales/Model/Order/Api/_files/order.php
 */
class Mage_Sales_Model_Order_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test getting sales order list in other methods
     */
    public function testList()
    {
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

        $order->setStatus('pending')
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

        $order->setState(Mage_Sales_Model_Order::STATE_NEW, 'pending')
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
}
