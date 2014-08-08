<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Data;

/**
 * Class InvoiceConverterTest
 * @package Magento\Sales\Service\V1\Data
 */
class InvoiceConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoiceLoaderMock;

    /**
     * @var \Magento\Sales\Service\V1\Data\InvoiceConverter
     */
    protected $converter;

    public function setUp()
    {
        $this->invoiceLoaderMock = $this->getMockBuilder('Magento\Sales\Controller\Adminhtml\Order\InvoiceLoader')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->converter = new \Magento\Sales\Service\V1\Data\InvoiceConverter($this->invoiceLoaderMock);
    }

    public function testGetModel()
    {
        $orderId = 1;
        $invoiceId = 2;
        $items = [];

        $invoiceDataObjectMock = $this->getMockBuilder('Magento\Sales\Service\V1\Data\Invoice')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $invoiceDataObjectMock->expects($this->any())
            ->method('getOrderId')
            ->will($this->returnValue($orderId));
        $invoiceDataObjectMock->expects($this->any())
            ->method('getEntityId')
            ->will($this->returnValue($invoiceId));
        $invoiceDataObjectMock->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue($items));

        $invoiceMock = $this->getMockBuilder('Magento\Sales\Model\Order\Invoice')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->invoiceLoaderMock->expects($this->once())
            ->method('load')
            ->with($orderId, $invoiceId, $items)
            ->will($this->returnValue($invoiceMock));

        $this->assertInstanceOf(
            'Magento\Sales\Model\Order\Invoice',
            $this->converter->getModel($invoiceDataObjectMock)
        );
    }
}
