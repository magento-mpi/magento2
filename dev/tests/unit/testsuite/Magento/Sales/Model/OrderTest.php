<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Sales\Model\Order
 */
namespace Magento\Sales\Model;

class OrderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $paymentCollectionFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderItemCollectionFactoryMock;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->paymentCollectionFactoryMock = $this->getMock(
            'Magento\Sales\Model\Resource\Order\Payment\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->orderItemCollectionFactoryMock = $this->getMock(
            'Magento\Sales\Model\Resource\Order\Item\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->order = $helper->getObject(
            'Magento\Sales\Model\Order',
            [
                'paymentCollectionFactory' => $this->paymentCollectionFactoryMock,
                'orderItemCollectionFactory' => $this->orderItemCollectionFactoryMock
            ]
        );
    }

    public function testCanCancelCanUnhold()
    {
        $this->order->setActionFlag(\Magento\Sales\Model\Order::ACTION_FLAG_UNHOLD, true);
        $this->order->setState(\Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW);
        $this->assertFalse($this->order->canCancel());
    }

    public function testCanCancelIsPaymentReview()
    {
        $this->order->setActionFlag(\Magento\Sales\Model\Order::ACTION_FLAG_UNHOLD, false);
        $this->order->setState(\Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW);
        $this->assertFalse($this->order->canCancel());
    }

    public function testCanCancelCanReviewPayment()
    {
        $paymentMock = $this->getMockBuilder('Magento\Sales\Model\Resource\Order\Payment')
            ->disableOriginalConstructor()
            ->setMethods(['isDeleted', 'canReviewPayment', 'canFetchTransactionInfo'])
            ->getMock();
        $paymentMock->expects($this->once())
            ->method('canReviewPayment')
            ->will($this->returnValue(false));
        $paymentMock->expects($this->once())
            ->method('canFetchTransactionInfo')
            ->will($this->returnValue(true));

        $this->preparePaymentMock($paymentMock);

        $this->order->setActionFlag(\Magento\Sales\Model\Order::ACTION_FLAG_UNHOLD, false);
        $this->order->setState(\Magento\Sales\Model\Order::STATE_NEW);
        $this->assertFalse($this->order->canCancel());
    }

    public function testCanCancelAllInvoiced()
    {
        $paymentMock = $this->getMockBuilder('Magento\Sales\Model\Resource\Order\Payment')
            ->disableOriginalConstructor()
            ->setMethods(['isDeleted', 'canReviewPayment', 'canFetchTransactionInfo'])
            ->getMock();
        $paymentMock->expects($this->once())
            ->method('canReviewPayment')
            ->will($this->returnValue(false));
        $paymentMock->expects($this->once())
            ->method('canFetchTransactionInfo')
            ->will($this->returnValue(false));

        $this->preparePaymentMock($paymentMock);

        $this->prepareItemMock(0);

        $this->order->setActionFlag(\Magento\Sales\Model\Order::ACTION_FLAG_UNHOLD, false);
        $this->order->setState(\Magento\Sales\Model\Order::STATE_NEW);
        $this->assertFalse($this->order->canCancel());
    }

    public function testCanCancelState()
    {
        $paymentMock = $this->getMockBuilder('Magento\Sales\Model\Resource\Order\Payment')
            ->disableOriginalConstructor()
            ->setMethods(['isDeleted', 'canReviewPayment', 'canFetchTransactionInfo'])
            ->getMock();
        $paymentMock->expects($this->once())
            ->method('canReviewPayment')
            ->will($this->returnValue(false));
        $paymentMock->expects($this->once())
            ->method('canFetchTransactionInfo')
            ->will($this->returnValue(false));

        $this->preparePaymentMock($paymentMock);

        $this->prepareItemMock(1);
        $this->order->setActionFlag(\Magento\Sales\Model\Order::ACTION_FLAG_UNHOLD, false);
        $this->order->setState(\Magento\Sales\Model\Order::STATE_CANCELED);
        $this->assertFalse($this->order->canCancel());
    }

    /**
     * @param bool $cancelActionFlag
     * @dataProvider dataProviderActionFlag
     */
    public function testCanCancelActionFlag($cancelActionFlag)
    {
        $paymentMock = $this->getMockBuilder('Magento\Sales\Model\Resource\Order\Payment')
            ->disableOriginalConstructor()
            ->setMethods(['isDeleted', 'canReviewPayment', 'canFetchTransactionInfo'])
            ->getMock();
        $paymentMock->expects($this->once())
            ->method('canReviewPayment')
            ->will($this->returnValue(false));
        $paymentMock->expects($this->once())
            ->method('canFetchTransactionInfo')
            ->will($this->returnValue(false));

        $this->preparePaymentMock($paymentMock);

        $this->prepareItemMock(1);

        $actionFlags= [
            \Magento\Sales\Model\Order::ACTION_FLAG_UNHOLD => false,
            \Magento\Sales\Model\Order::ACTION_FLAG_CANCEL => $cancelActionFlag
        ];
        foreach ($actionFlags as $action => $flag) {
            $this->order->setActionFlag($action, $flag);
        }
        $this->order->setState(\Magento\Sales\Model\Order::STATE_NEW);
        $this->assertEquals($cancelActionFlag, $this->order->canCancel());
    }

    /**
     * @param array $actionFlags
     * @param string $orderState
     * @dataProvider canVoidPaymentDataProvider
     */
    public function testCanVoidPayment($actionFlags, $orderState)
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var Order $order */
        $order = $helper->getObject('Magento\Sales\Model\Order');
        foreach ($actionFlags as $action => $flag) {
            $order->setActionFlag($action, $flag);
        }
        $order->setData('state', $orderState);
        $payment = $this->_prepareOrderPayment($order);
        $canVoidOrder = true;
        if ($orderState == \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW) {
            $canVoidOrder = false;
        }
        if ($orderState == \Magento\Sales\Model\Order::STATE_HOLDED && (!isset(
                    $actionFlags[\Magento\Sales\Model\Order::ACTION_FLAG_UNHOLD]
                ) || $actionFlags[\Magento\Sales\Model\Order::ACTION_FLAG_UNHOLD] !== false)
        ) {
            $canVoidOrder = false;
        }

        $expected = false;
        if ($canVoidOrder) {
            $expected = 'some value';
            $payment->expects(
                $this->once()
            )->method(
                'canVoid'
            )->with(
                new \PHPUnit_Framework_Constraint_IsIdentical($payment)
            )->will(
                $this->returnValue($expected)
            );
        } else {
            $payment->expects($this->never())->method('canVoid');
        }
        $this->assertEquals($expected, $order->canVoidPayment());
    }

    /**
     * @param $paymentMock
     */
    protected function preparePaymentMock($paymentMock)
    {
        $iterator = new \ArrayIterator([$paymentMock]);

        $collectionMock = $this->getMockBuilder('Magento\Sales\Model\Resource\Order\Payment\Collection')
            ->disableOriginalConstructor()
            ->setMethods(['setOrderFilter', 'getIterator'])
            ->getMock();
        $collectionMock->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($iterator));
        $collectionMock->expects($this->once())
            ->method('setOrderFilter')
            ->will($this->returnSelf());

        $this->paymentCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($collectionMock));
    }

    /**
     * Prepare payment for the order
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $order
     * @param array $mockedMethods
     * @return \Magento\Sales\Model\Order\Payment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function _prepareOrderPayment($order, $mockedMethods = array())
    {
        $payment = $this->getMockBuilder('Magento\Sales\Model\Order\Payment')->disableOriginalConstructor()->getMock();
        foreach ($mockedMethods as $method => $value) {
            $payment->expects($this->any())->method($method)->will($this->returnValue($value));
        }
        $payment->expects($this->any())->method('isDeleted')->will($this->returnValue(false));

        $itemsProperty = new \ReflectionProperty('Magento\Sales\Model\Order', '_payments');
        $itemsProperty->setAccessible(true);
        $itemsProperty->setValue($order, array($payment));
        return $payment;
    }

    /**
     * Get action flags
     *
     */
    protected function _getActionFlagsValues()
    {
        return array(
            array(),
            array(
                \Magento\Sales\Model\Order::ACTION_FLAG_UNHOLD => false,
                \Magento\Sales\Model\Order::ACTION_FLAG_CANCEL => false
            ),
            array(
                \Magento\Sales\Model\Order::ACTION_FLAG_UNHOLD => false,
                \Magento\Sales\Model\Order::ACTION_FLAG_CANCEL => true
            )
        );
    }

    /**
     * Get order statuses
     *
     * @return array
     */
    protected function _getOrderStatuses()
    {
        return array(
            \Magento\Sales\Model\Order::STATE_HOLDED,
            \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW,
            \Magento\Sales\Model\Order::STATE_CANCELED,
            \Magento\Sales\Model\Order::STATE_COMPLETE,
            \Magento\Sales\Model\Order::STATE_CLOSED,
            \Magento\Sales\Model\Order::STATE_PROCESSING
        );
    }

    /**
     * @param int $qtyInvoiced
     * @return void
     */
    protected function prepareItemMock($qtyInvoiced)
    {
        $itemMock = $this->getMockBuilder('Magento\Sales\Model\Resource\Order\Item')
            ->disableOriginalConstructor()
            ->setMethods(['isDeleted', 'filterByTypes', 'filterByParent', 'getQtyToInvoice'])
            ->getMock();

        $itemMock->expects($this->once())
            ->method('getQtyToInvoice')
            ->will($this->returnValue($qtyInvoiced));

        $iterator = new \ArrayIterator([$itemMock]);

        $itemCollectionMock = $this->getMockBuilder('Magento\Sales\Model\Resource\Order\Item\Collection')
            ->disableOriginalConstructor()
            ->setMethods(['setOrderFilter', 'getIterator'])
            ->getMock();
        $itemCollectionMock->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue($iterator));
        $itemCollectionMock->expects($this->once())
            ->method('setOrderFilter')
            ->will($this->returnSelf());

        $this->orderItemCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($itemCollectionMock));
    }

    public function canVoidPaymentDataProvider()
    {
        $data = array();
        foreach ($this->_getActionFlagsValues() as $actionFlags) {
            foreach ($this->_getOrderStatuses() as $status) {
                $data[] = array($actionFlags, $status);
            }
        }
        return $data;
    }

    public function dataProviderActionFlag()
    {
        return [
            [false],
            [true]
        ];
    }
}
