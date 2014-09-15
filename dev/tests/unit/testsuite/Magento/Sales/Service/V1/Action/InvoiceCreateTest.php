<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

/**
 * Class InvoiceCreateTest
 */
class InvoiceCreateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\InvoiceCreate
     */
    protected $invoiceCreate;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoiceConverterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    public function setUp()
    {
        $this->invoiceConverterMock = $this->getMockBuilder('Magento\Sales\Service\V1\Data\InvoiceConverter')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->loggerMock = $this->getMockBuilder('Magento\Framework\Logger')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->invoiceCreate = new InvoiceCreate(
            $this->invoiceConverterMock,
            $this->loggerMock
        );
    }

    public function testInvoke()
    {
        $invoiceMock = $this->getMockBuilder('Magento\Sales\Model\Order\Invoice')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $invoiceMock->expects($this->once())
            ->method('register');
        $invoiceMock->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));
        $invoiceDataObjectMock = $this->getMockBuilder('Magento\Sales\Service\V1\Data\Invoice')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->invoiceConverterMock->expects($this->once())
            ->method('getModel')
            ->with($invoiceDataObjectMock)
            ->will($this->returnValue($invoiceMock));
        $this->assertTrue($this->invoiceCreate->invoke($invoiceDataObjectMock));
    }

    public function testInvokeNoInvoice()
    {
        $invoiceDataObjectMock = $this->getMockBuilder('Magento\Sales\Service\V1\Data\Invoice')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->invoiceConverterMock->expects($this->once())
            ->method('getModel')
            ->with($invoiceDataObjectMock)
            ->will($this->returnValue(false));
        $this->assertFalse($this->invoiceCreate->invoke($invoiceDataObjectMock));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage An error has occurred during creating Invoice
     */
    public function testInvokeException()
    {
        $message = 'Can not save Invoice';
        $e = new \Exception($message);

        $invoiceDataObjectMock = $this->getMockBuilder('Magento\Sales\Service\V1\Data\Invoice')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->loggerMock->expects($this->once())
            ->method('logException')
            ->with($e);
        $this->invoiceConverterMock->expects($this->once())
            ->method('getModel')
            ->with($invoiceDataObjectMock)
            ->will($this->throwException($e));
        $this->invoiceCreate->invoke($invoiceDataObjectMock);
    }
}
