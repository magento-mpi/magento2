<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Sales_Model_Order
 */
class Magento_Sales_Model_OrderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Prepare items for the order
     *
     * @param PHPUnit_Framework_MockObject_MockObject $order
     * @param bool $allInvoiced
     */
    protected function _prepareOrderItems($order, $allInvoiced)
    {
        $items = array();
        if (!$allInvoiced) {
            $item = $this->getMockBuilder('Magento_Sales_Model_Order_Item')
                ->setMethods(array('getQtyToInvoice', 'isDeleted'))
                ->disableOriginalConstructor()
                ->getMock();
            $item->expects($this->any())
                ->method('getQtyToInvoice')
                ->will($this->returnValue(1));
            $item->expects($this->any())
                ->method('isDeleted')
                ->will($this->returnValue(false));
            $items[] = $item;
        }

        $itemsProperty = new ReflectionProperty('Magento_Sales_Model_Order', '_items');
        $itemsProperty->setAccessible(true);
        $itemsProperty->setValue($order, $items);
    }

    /**
     * Prepare payment for the order
     *
     * @param PHPUnit_Framework_MockObject_MockObject $order
     * @param bool $canReviewPayment
     * @param bool $canUpdatePayment
     */
    protected function _prepareOrderPayment($order, $canReviewPayment, $canUpdatePayment)
    {
        $payment = $this->getMockBuilder('Magento_Sales_Model_Order_Payment')
            ->disableOriginalConstructor()
            ->getMock();
        $payment->expects($this->any())
            ->method('canReviewPayment')
            ->will($this->returnValue($canReviewPayment));
        $payment->expects($this->any())
            ->method('canFetchTransactionInfo')
            ->will($this->returnValue($canUpdatePayment));
        $payment->expects($this->any())
            ->method('isDeleted')
            ->will($this->returnValue(false));

        $itemsProperty = new ReflectionProperty('Magento_Sales_Model_Order', '_payments');
        $itemsProperty->setAccessible(true);
        $itemsProperty->setValue($order, array($payment));
    }

    /**
     * @SuppressWarnings("complexity")
     *
     * @param array $actionFlags
     * @param string $orderState
     * @param bool $canReviewPayment
     * @param bool $canUpdatePayment
     * @param bool $allInvoiced
     * @dataProvider canCancelDataProvider
     */
    public function testCanCancel($actionFlags, $orderState, $canReviewPayment, $canUpdatePayment, $allInvoiced)
    {
        /** @var $order Magento_Sales_Model_Order */
        $order = $this->getMockBuilder('Magento_Sales_Model_Order')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
        foreach ($actionFlags as $action => $flag) {
            $order->setActionFlag($action, $flag);
        }
        $order->setData('state', $orderState);
        $this->_prepareOrderPayment($order, $canReviewPayment, $canUpdatePayment);
        $this->_prepareOrderItems($order, $allInvoiced);

        // Calculate result
        $expectedResult = true;
        if ((!isset($actionFlags[Magento_Sales_Model_Order::ACTION_FLAG_UNHOLD])
            || $actionFlags[Magento_Sales_Model_Order::ACTION_FLAG_UNHOLD] !== false)
            && $orderState == Magento_Sales_Model_Order::STATE_HOLDED
        ) {
            $expectedResult = false;
        }
        if ($orderState == Magento_Sales_Model_Order::STATE_PAYMENT_REVIEW && !$canReviewPayment && $canUpdatePayment) {
            $expectedResult = false;
        }
        if ($allInvoiced || in_array($orderState, array(
            Magento_Sales_Model_Order::STATE_CANCELED,
            Magento_Sales_Model_Order::STATE_COMPLETE,
            Magento_Sales_Model_Order::STATE_CLOSED
        ))) {
            $expectedResult = false;
        }
        if (isset($actionFlags[Magento_Sales_Model_Order::ACTION_FLAG_CANCEL])
            && $actionFlags[Magento_Sales_Model_Order::ACTION_FLAG_CANCEL] === false
        ) {
            $expectedResult = false;
        }

        $this->assertEquals($expectedResult, $order->canCancel());
    }

    public function canCancelDataProvider()
    {
        $actionFlagsValues = array(
            array(),
            array(
                Magento_Sales_Model_Order::ACTION_FLAG_UNHOLD => false,
                Magento_Sales_Model_Order::ACTION_FLAG_CANCEL => false,
            ),
            array(
                Magento_Sales_Model_Order::ACTION_FLAG_UNHOLD => false,
                Magento_Sales_Model_Order::ACTION_FLAG_CANCEL => true,
            ),
        );
        $boolValues = array(true, false);
        $orderStatuses = array(
            Magento_Sales_Model_Order::STATE_HOLDED,
            Magento_Sales_Model_Order::STATE_PAYMENT_REVIEW,
            Magento_Sales_Model_Order::STATE_CANCELED,
            Magento_Sales_Model_Order::STATE_COMPLETE,
            Magento_Sales_Model_Order::STATE_CLOSED,
            Magento_Sales_Model_Order::STATE_PROCESSING,
        );

        $data = array();
        foreach ($actionFlagsValues as $actionFlags) {
            foreach ($orderStatuses as $status) {
                foreach ($boolValues as $canReviewPayment) {
                    foreach ($boolValues as $canUpdatePayment) {
                        foreach ($boolValues as $allInvoiced) {
                            $data[] = array($actionFlags, $status, $canReviewPayment, $canUpdatePayment, $allInvoiced);
                        }
                    }
                }
            }
        }

        return $data;
    }
}
