<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model;

use Magento\Sales\Model\Resource\Order\Status\History\CollectionFactory;
use Magento\Framework\Mail\Exception;

/**
 * Class InvoiceNotifierTest
 */
class InvoiceNotifierTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CollectionFactory |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $historyCollectionFactory;

    /**
     * @var \Magento\Sales\Model\InvoiceNotifier
     */
    protected $notifier;

    /**
     * @var \Magento\Sales\Model\Order\Invoice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoice;

    /**
     * @var \Magento\Framework\ObjectManager |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * @var \Magento\Framework\ObjectManager\ObjectManager |\PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoiceSenderMock;

    public function setUp()
    {
        $this->historyCollectionFactory = $this->getMock(
            'Magento\Sales\Model\Resource\Order\Status\History\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->invoice = $this->getMock(
            'Magento\Sales\Model\Order\Invoice',
            ['__wakeUp', 'getEmailSent'],
            [],
            '',
            false
        );
        $this->invoiceSenderMock = $this->getMock(
            'Magento\Sales\Model\Order\Email\Sender\InvoiceSender',
            ['send'],
            [],
            '',
            false
        );
        $this->loggerMock = $this->getMock(
            'Magento\Framework\Logger',
            ['logException'],
            [],
            '',
            false
        );
        $this->notifier = new InvoiceNotifier(
            $this->historyCollectionFactory,
            $this->loggerMock,
            $this->invoiceSenderMock
        );
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
            ->with($this->invoice, \Magento\Sales\Model\Order\Invoice::HISTORY_ENTITY_NAME)
            ->will($this->returnValue($historyItem));
        $this->invoice->expects($this->once())
            ->method('getEmailSent')
            ->will($this->returnValue(true));
        $this->historyCollectionFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($historyCollection));

        $this->invoiceSenderMock->expects($this->once())
            ->method('send')
            ->with($this->equalTo($this->invoice));

        $this->assertTrue($this->notifier->notify($this->invoice));
    }

    /**
     * Test case when email has not been sent
     */
    public function testNotifyFail()
    {
        $this->invoice->expects($this->once())
            ->method('getEmailSent')
            ->will($this->returnValue(false));
        $this->assertFalse($this->notifier->notify($this->invoice));
    }

    /**
     * Test case when Mail Exception has been thrown
     */
    public function testNotifyException()
    {
        $exception = new Exception('Email has not been sent');
        $this->invoiceSenderMock->expects($this->once())
            ->method('send')
            ->with($this->equalTo($this->invoice))
            ->will($this->throwException($exception));
        $this->loggerMock->expects($this->once())
            ->method('logException')
            ->with($this->equalTo($exception));
        $this->assertFalse($this->notifier->notify($this->invoice));
    }
}
