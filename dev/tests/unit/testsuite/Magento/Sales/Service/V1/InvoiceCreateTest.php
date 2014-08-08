<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

class InvoiceCreateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\InvoiceCreate
     */
    protected $invoiceCreate;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoiceConverterMock;

    public function setUp()
    {
        $this->invoiceConverterMock = $this->getMockBuilder('Magento\Sales\Service\V1\Data\InvoiceConverter')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->invoiceCreate = new \Magento\Sales\Service\V1\InvoiceCreate($this->invoiceConverterMock);
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
}

