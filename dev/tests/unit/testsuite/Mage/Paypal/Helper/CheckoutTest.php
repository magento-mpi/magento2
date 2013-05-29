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
 * Test class for Mage_Paypal_Helper_Checkout
 */
class Mage_Paypal_Helper_CheckoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * Get order mock
     *
     * @param bool $hasOrderId
     * @param array $mockMethods
     * @return Mage_Sales_Model_Order|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getOrderMock($hasOrderId, $mockMethods = array())
    {
        $order = $this->getMockBuilder('Mage_Sales_Model_Order')
            ->disableOriginalConstructor()
            ->setMethods(array_merge(array('getId'), $mockMethods))
            ->getMock();
        $order->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($hasOrderId ? 'order id' : null));
        return $order;
    }

    /**
     * Get session mock
     *
     * @param Mage_Sales_Model_Order|PHPUnit_Framework_MockObject_MockObject $order
     * @param array $mockMethods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getSessionMock($order, $mockMethods = array())
    {
        $session = $this->getMockBuilder('Mage_Checkout_Model_Session')
            ->disableOriginalConstructor()
            ->setMethods(array_merge(array('getLastRealOrder'), $mockMethods))
            ->getMock();
        $session->expects($this->any())
            ->method('getLastRealOrder')
            ->will($this->returnValue($order));
        return $session;
    }

    /**
     * Get checkout mock
     *
     * @param Mage_Checkout_Model_Session $session
     * @param array $mockMethods
     * @return PHPUnit_Framework_MockObject_MockObject|Mage_Paypal_Helper_Checkout
     */
    protected function _getCheckoutMock($session, $mockMethods = array())
    {
        $checkout = $this->getMockBuilder('Mage_Paypal_Helper_Checkout')
            ->disableOriginalConstructor()
            ->setMethods(array_merge(array('_getCheckoutSession'), $mockMethods))
            ->getMock();
        $checkout->expects($this->any())
            ->method('_getCheckoutSession')
            ->will($this->returnValue($session));
        return $checkout;
    }

    /**
     * @param bool $hasOrderId
     * @param bool $isOrderCancelled
     * @param bool $expectedResult
     * @dataProvider cancelCurrentOrderDataProvider
     */
    public function testCancelCurrentOrder($hasOrderId, $isOrderCancelled, $expectedResult)
    {
        $comment = 'Some test comment';
        $order = $this->_getOrderMock($hasOrderId, array('registerCancellation', 'save'));
        $order->setData('state', $isOrderCancelled ? Mage_Sales_Model_Order::STATE_CANCELED : 'some another state');
        if ($expectedResult) {
            $order->expects($this->once())
                ->method('registerCancellation')
                ->with($this->equalTo($comment))
                ->will($this->returnSelf());
            $order->expects($this->once())
                ->method('save');
        } else {
            $order->expects($this->never())
                ->method('registerCancellation');
            $order->expects($this->never())
                ->method('save');
        }
        $session = $this->_getSessionMock($order);
        $checkout = $this->_getCheckoutMock($session);
        $this->assertEquals($expectedResult, $checkout->cancelCurrentOrder($comment));
    }

    public function cancelCurrentOrderDataProvider()
    {
        return array(
            array(true, false, true),
            array(true, true, false),
            array(false, true, false),
            array(false, false, false),
        );
    }

    /**
     * @param bool $hasOrderId
     * @param bool $hasQuoteId
     * @param bool $expectedResult
     * @dataProvider restoreQuoteDataProvider
     */
    public function testRestoreQuote($hasOrderId, $hasQuoteId)
    {
        $order = $this->_getOrderMock($hasOrderId);
        $session = $this->_getSessionMock($order, array('replaceQuote', 'unsLastRealOrderId'));
        $checkout = $this->_getCheckoutMock($session, array('_getQuote'));
        if ($hasOrderId) {
            $quoteId = 'quote id';
            $order->setQuoteId($quoteId);
            $quote = $this->getMockBuilder('Mage_Sales_Model_Quote')
                ->disableOriginalConstructor()
                ->setMethods(array('getId', 'save', 'setIsActive', 'setReservedOrderId'))
                ->getMock();
            $quote->expects($this->once())
                ->method('getId')
                ->will($this->returnValue($hasQuoteId ? 'some quote id' : null));
            $checkout->expects($this->once())
                ->method('_getQuote')
                ->with($this->equalTo($quoteId))
                ->will($this->returnValue($quote));
            if ($hasQuoteId) {
                $quote->expects($this->once())
                    ->method('setIsActive')
                    ->with($this->equalTo(1))
                    ->will($this->returnSelf());
                $quote->expects($this->once())
                    ->method('setReservedOrderId')
                    ->with($this->isNull())
                    ->will($this->returnSelf());
                $quote->expects($this->once())
                    ->method('save');
                $session->expects($this->once())
                    ->method('replaceQuote')
                    ->with($quote)
                    ->will($this->returnSelf());
            } else {
                $quote->expects($this->never())
                    ->method('setIsActive');
                $quote->expects($this->never())
                    ->method('setReservedOrderId');
                $quote->expects($this->never())
                    ->method('save');
            }
        }
        if ($hasOrderId && $hasQuoteId) {
            $session->expects($this->once())
                ->method('unsLastRealOrderId');
        } else {
            $session->expects($this->never())
                ->method('replaceQuote');
            $session->expects($this->never())
                ->method('unsLastRealOrderId');
        }
        $this->assertEquals($hasOrderId && $hasQuoteId, $checkout->restoreQuote());
    }

    /**
     * Data Provider
     *
     * @return array
     */
    public function restoreQuoteDataProvider()
    {
        return array(
            array(true, true),
            array(true, false),
            array(false, true),
            array(false, false),
        );
    }
}
