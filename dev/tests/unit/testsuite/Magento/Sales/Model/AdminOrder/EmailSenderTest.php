<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\AdminOrder;

class EmailSenderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * @var EmailSender
     */
    protected $emailSender;

    protected function setUp()
    {
        $this->messageManagerMock = $this->getMock(
            '\Magento\Framework\Message\Manager',
            [],
            [],
            '',
            false
        );
        $this->loggerMock = $this->getMock(
            '\Magento\Framework\Logger',
            [],
            [],
            '',
            false
        );
        $this->orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            [],
            [],
            '',
            false
        );
        $this->emailSender = new EmailSender($this->messageManagerMock, $this->loggerMock);
    }

    public function testSendSuccess()
    {
        $this->orderMock->expects($this->once())
            ->method('sendNewOrderEmail');
        $this->assertTrue($this->emailSender->send($this->orderMock));
    }

    public function testSendFailure()
    {
        $this->orderMock->expects($this->once())
            ->method('sendNewOrderEmail')
            ->will($this->throwException(new \Magento\Framework\Mail\Exception('test message')));
        $this->messageManagerMock->expects($this->once())
            ->method('addWarning');
        $this->loggerMock->expects($this->once())
            ->method('logException');

        $this->assertFalse($this->emailSender->send($this->orderMock));
    }
}
