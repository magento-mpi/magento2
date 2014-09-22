<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

/**
 * Class InvoiceGetTest
 */
class InvoiceGetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\InvoiceGet
     */
    protected $invoiceGet;

    /**
     * @var \Magento\Sales\Model\Order\InvoiceRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoiceRepositoryMock;

    /**
     * @var \Magento\Sales\Service\V1\Data\InvoiceMapper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoiceMapperMock;

    /**
     * @var \Magento\Sales\Model\Order\Invoice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoiceMock;

    /**
     * @var \Magento\Sales\Service\V1\Data\Invoice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataObjectMock;

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
        $this->invoiceMapperMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\InvoiceMapper',
            [],
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
        $this->dataObjectMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\Invoice',
            [],
            [],
            '',
            false
        );
        $this->invoiceGet = new InvoiceGet(
            $this->invoiceRepositoryMock,
            $this->invoiceMapperMock
        );
    }

    /**
     * test invoice get service
     */
    public function testInvoke()
    {
        $this->invoiceRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo(1))
            ->will($this->returnValue($this->invoiceMock));
        $this->invoiceMapperMock->expects($this->once())
            ->method('extractDto')
            ->with($this->equalTo($this->invoiceMock))
            ->will($this->returnValue($this->dataObjectMock));
        $this->assertEquals($this->dataObjectMock, $this->invoiceGet->invoke(1));
    }
}
