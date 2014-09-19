<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

class OrderLoadAfterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Model\Observer\OrderLoadAfter
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->subject = $objectManager->getObject('\Magento\Reward\Model\Observer\OrderLoadAfter');
    }

    public function testSetForcedCreditmemoFlagIfOrderCanUnhold()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);

        $orderMock = $this->getMock('\Magento\Sales\Model\Order', ['canUnhold', '__wakeup'], [], '', false);
        $orderMock->expects($this->once())->method('canUnhold')->will($this->returnValue(true));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getOrder'], [], '', false);
        $eventMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));
        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testSetForcedCreditmemoFlagIfOrderIsCanceled()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);

        $orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['canUnhold', '__wakeup', 'isCanceled'],
            [],
            '',
            false
        );
        $orderMock->expects($this->once())->method('canUnhold')->will($this->returnValue(false));
        $orderMock->expects($this->once())->method('isCanceled')->will($this->returnValue(true));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getOrder'], [], '', false);
        $eventMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));
        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testSetForcedCreditmemoFlagIfOrderStateIsClosed()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);

        $orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['canUnhold', '__wakeup', 'isCanceled', 'getState'],
            [],
            '',
            false
        );
        $orderMock->expects($this->once())->method('canUnhold')->will($this->returnValue(false));
        $orderMock->expects($this->once())->method('isCanceled')->will($this->returnValue(false));
        $orderMock->expects($this->once())
            ->method('getState')
            ->will($this->returnValue(\Magento\Sales\Model\Order::STATE_CLOSED));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getOrder'], [], '', false);
        $eventMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));
        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testSetForcedCreditmemoFlagIfRewardAmountIsZero()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);

        $orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            [
                'canUnhold',
                '__wakeup',
                'isCanceled',
                'getState',
                'getBaseRwrdCrrncyAmntRefnded',
                'getBaseRwrdCrrncyAmtInvoiced'
            ],
            [],
            '',
            false
        );
        $orderMock->expects($this->once())->method('canUnhold')->will($this->returnValue(false));
        $orderMock->expects($this->once())->method('isCanceled')->will($this->returnValue(false));
        $orderMock->expects($this->once())
            ->method('getState')
            ->will($this->returnValue(\Magento\Sales\Model\Order::STATE_PROCESSING));

        $orderMock->expects($this->once())->method('getBaseRwrdCrrncyAmtInvoiced')->will($this->returnValue(100));
        $orderMock->expects($this->once())->method('getBaseRwrdCrrncyAmntRefnded')->will($this->returnValue(100));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getOrder'], [], '', false);
        $eventMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));
        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }

    public function testSetForcedCreditmemoFlagSuccess()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);

        $orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            [
                'canUnhold',
                '__wakeup',
                'isCanceled',
                'getState',
                'getBaseRwrdCrrncyAmntRefnded',
                'getBaseRwrdCrrncyAmtInvoiced',
                'setForcedCanCreditmemo'
            ],
            [],
            '',
            false
        );
        $orderMock->expects($this->once())->method('canUnhold')->will($this->returnValue(false));
        $orderMock->expects($this->once())->method('isCanceled')->will($this->returnValue(false));
        $orderMock->expects($this->once())
            ->method('getState')
            ->will($this->returnValue(\Magento\Sales\Model\Order::STATE_PROCESSING));

        $orderMock->expects($this->once())->method('getBaseRwrdCrrncyAmtInvoiced')->will($this->returnValue(150));
        $orderMock->expects($this->once())->method('getBaseRwrdCrrncyAmntRefnded')->will($this->returnValue(100));
        $orderMock->expects($this->once())->method('setForcedCanCreditmemo')->with(true)->will($this->returnSelf());

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getOrder'], [], '', false);
        $eventMock->expects($this->once())->method('getOrder')->will($this->returnValue($orderMock));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));
        $this->assertEquals($this->subject, $this->subject->execute($observerMock));
    }
}
 