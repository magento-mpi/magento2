<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model;

use Magento\Sales\Model\Resource\Order\Status\History\CollectionFactory;

/**
 * Class NotifierTest
 */
class NotifierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CollectionFactory |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $historyCollectionFactory;

    /**
     * @var \Magento\Sales\Model\Notifier
     */
    protected $notifier;

    /**
     * @var \Magento\Sales\Model\Order|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $order;

    /**
     * @var \Magento\Framework\ObjectManager |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\ObjectManager\ObjectManager |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderSenderMock;

    public function setUp()
    {
        $this->historyCollectionFactory = $this->getMock(
            'Magento\Sales\Model\Resource\Order\Status\History\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->order = $this->getMock(
            'Magento\Sales\Model\Order',
            ['__wakeUp', 'getEmailSent'],
            [],
            '',
            false
        );
        $this->orderSenderMock = $this->getMock(
            'Magento\Sales\Model\Order\Email\Sender\OrderSender',
            ['send'],
            [],
            '',
            false
        );

        $this->objectManager = $this->getMock('Magento\Framework\ObjectManager\ObjectManager', ['get'], [], '', false);
        $this->notifier = new Notifier($this->historyCollectionFactory, $this->objectManager, $this->orderSenderMock);
    }

    /**
     * Test case for successful email sending
     */
    public function testNotifySuccess()
    {
        $historyCollection = $this->getMock(
            'Magento\Sales\Model\Resource\Order\Status\History\Collection',
            ['getUnnotifiedForInstance', 'save', 'setIsCustomerNotified'],
            [],
            '',
            false
        );
        $historyItem = $this->getMock(
            'Magento\Sales\Model\Order\Status\History',
            ['setIsCustomerNotified', 'save', '__wakeUp'],
            [],
            '',
            false
        );
        $historyItem->expects($this->at(0))
            ->method('setIsCustomerNotified')
            ->with(1);
        $historyItem->expects($this->at(1))
            ->method('save');
        $historyCollection->expects($this->once())
            ->method('getUnnotifiedForInstance')
            ->with($this->order, \Magento\Sales\Model\Order::HISTORY_ENTITY_NAME)
            ->will($this->returnValue($historyItem));
        $this->order->expects($this->once())
            ->method('getEmailSent')
            ->will($this->returnValue(true));
        $this->historyCollectionFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($historyCollection));

        $this->orderSenderMock->expects($this->once())
            ->method('send')
            ->with($this->equalTo($this->order));

        $this->assertTrue($this->notifier->notify($this->order));
    }

    /**
     * Test case when email has not been sent
     */
    public function testNotifyFail()
    {
        $this->order->expects($this->once())
            ->method('getEmailSent')
            ->will($this->returnValue(false));
        $this->assertFalse($this->notifier->notify($this->order));
    }

    /**
     * Test case when Mail Exception has been thrown
     */
    public function testNotifyException()
    {
        $e = 'Email has not been sent';
        $exceptionMock = new \Magento\Framework\Mail\Exception($e);
        $logger = $this->getMock('Magento\Framework\Logger', ['logException'], [], '', false);
        $this->objectManager->expects($this->once())
            ->method('get')
            ->with('Magento\Framework\Logger')
            ->will($this->returnValue($logger));
        $this->orderSenderMock->expects($this->once())
            ->method('send')
            ->with($this->equalTo($this->order))
            ->will($this->throwException($exceptionMock));
        $this->assertFalse($this->notifier->notify($this->order));
    }
}
