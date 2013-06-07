<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Checkout_Model_Session
 */
class Mage_Checkout_Model_SessionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $loadOrderIds
     * @dataProvider getLastRealOrderDataProvider
     */
    public function testGetLastRealOrder($loadOrderIds)
    {
        /** @var $order PHPUnit_Framework_MockObject_MockObject|Mage_Sales_Model_Order */
        $order = $this->getMockBuilder('Mage_Sales_Model_Order')
            ->disableOriginalConstructor()
            ->setMethods(array('loadByIncrementId'))
            ->getMock();
        /** @var $session PHPUnit_Framework_MockObject_MockObject|Mage_Checkout_Model_Session */
        $session = $this->getMockBuilder('Mage_Checkout_Model_Session')
            ->disableOriginalConstructor()
            ->setMethods(array('_getOrderModel'))
            ->getMock();
        // calculate expectations
        $lastOrderId = null;
        $getOrderModelInvokes = 0;
        $loadOrderInvokes = 0;
        foreach ($loadOrderIds as $orderId) {
            if (!isset($lastOrderId) || $lastOrderId != $orderId) {
                ++$getOrderModelInvokes;
                if ($orderId) {
                    $order->expects($this->at($loadOrderInvokes))
                        ->method('loadByIncrementId')
                        ->with($orderId);
                    ++$loadOrderInvokes;
                }
            }
            $lastOrderId = $orderId;
        }
        $session->expects($this->exactly($getOrderModelInvokes))
            ->method('_getOrderModel')
            ->will($this->returnValue($order));
        // test getting order
        foreach ($loadOrderIds as $orderId) {
            if (isset($orderId)) {
                $session->setLastRealOrderId($orderId);
            } else {
                $session->unsLastRealOrderId();
            }
            $this->assertEquals($order, $session->getLastRealOrder());
            if (isset($orderId)) {
                $order->setIncrementId($orderId);
            } else {
                $order->unsIncrementId();
            }
        }
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function getLastRealOrderDataProvider()
    {
        return array(
            array(array(null)),
            array(array(null, 1)),
            array(array(1, 2)),
            array(array(1, 2, 3)),
            array(array(2, 2, 2)),
            array(array(2, 1, 2, 2)),
        );
    }
}
