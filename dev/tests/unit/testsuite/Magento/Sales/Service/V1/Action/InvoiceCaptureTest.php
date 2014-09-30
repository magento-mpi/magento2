<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

/**
 * Class InvoiceCaptureTest
 */
class InvoiceCaptureTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\InvoiceCapture
     */
    protected $invoiceCapture;

    /**
     * @var \Magento\Sales\Model\Order\InvoiceRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoiceRepositoryMock;

    /**
     * @var \Magento\Sales\Model\Order\Invoice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoiceMock;

    /**
     * SetUp
     */
    protected function setUp()
    {
        $this->invoiceRepositoryMock = $this->getMock(
            'Magento\Sales\Model\Order\InvoiceRepository',
            ['get'],
            [],
            '',
            false
        );
        $this->invoiceMock = $this->getMock(
            'Magento\Sales\Model\Order\Invoice',
            [],
            [],
            '',
            false
        );
        $this->invoiceCapture = new InvoiceCapture(
            $this->invoiceRepositoryMock
        );
    }

    /**
     * test invoice capture service
     */
    public function testInvoke()
    {
        $this->invoiceRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo(1))
            ->will($this->returnValue($this->invoiceMock));
        $this->invoiceMock->expects($this->once())
            ->method('capture')
            ->will($this->returnSelf());
        $this->assertTrue($this->invoiceCapture->invoke(1));
    }
}
