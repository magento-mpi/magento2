<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order\Invoice;

use Magento\Backend\App\Action;

class UpdateQtyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoiceLoaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $titleMock;

    /**
     * @var \Magento\Sales\Controller\Adminhtml\Order\Invoice\UpdateQty
     */
    protected $controller;

    public function setUp()
    {
        $this->titleMock = $this->getMockBuilder('Magento\Framework\App\Action\Title')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->requestMock = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->responseMock = $this->getMockBuilder('Magento\Framework\App\Response\Http')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->viewMock = $this->getMockBuilder('Magento\Backend\Model\View')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->objectManagerMock = $this->getMockBuilder('Magento\Framework\ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $contextMock = $this->getMockBuilder('Magento\Backend\App\Action\Context')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $contextMock->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->requestMock));
        $contextMock->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($this->responseMock));
        $contextMock->expects($this->any())
            ->method('getTitle')
            ->will($this->returnValue($this->titleMock));
        $contextMock->expects($this->any())
            ->method('getView')
            ->will($this->returnValue($this->viewMock));
        $contextMock->expects($this->any())
            ->method('getObjectManager')
            ->will($this->returnValue($this->objectManagerMock));

        $this->invoiceLoaderMock = $this->getMockBuilder('Magento\Sales\Controller\Adminhtml\Order\InvoiceLoader')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->controller = new \Magento\Sales\Controller\Adminhtml\Order\Invoice\UpdateQty(
            $contextMock,
            $this->invoiceLoaderMock
        );
    }

    public function testExecute()
    {
        $orderId = 1;
        $invoiceId = 2;
        $invoiceData = ['comment_text' => 'test'];
        $response = 'test data';

        $this->requestMock->expects($this->at(0))
            ->method('getParam')
            ->with('order_id')
            ->will($this->returnValue($orderId));
        $this->requestMock->expects($this->at(1))
            ->method('getParam')
            ->with('invoice_id')
            ->will($this->returnValue($invoiceId));
        $this->requestMock->expects($this->at(2))
            ->method('getParam')
            ->with('invoice', [])
            ->will($this->returnValue($invoiceData));

        $this->responseMock->expects($this->once())
            ->method('setBody')
            ->with($response);

        $invoiceMock = $this->getMockBuilder('Magento\Sales\Model\Order\Invoice')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $blockItemMock = $this->getMockBuilder('Magento\Sales\Block\Order\Items')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $blockItemMock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($response));

        $layoutMock = $this->getMockBuilder('Magento\Framework\View\Layout')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('order_items')
            ->will($this->returnValue($blockItemMock));

        $this->viewMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));

        $this->invoiceLoaderMock->expects($this->once())
            ->method('load')
            ->with($orderId, $invoiceId, [])
            ->will($this->returnValue($invoiceMock));

        $this->assertNull($this->controller->execute());
    }

    public function testExecuteModelException()
    {
        $message = 'test message';
        $e = new \Magento\Framework\Model\Exception($message);
        $response = ['error' => true, 'message' => $message];

        $this->titleMock->expects($this->once())
            ->method('add')
            ->with('Invoices')
            ->will($this->throwException($e));

        $this->responseMock->expects($this->once())
            ->method('representJson')
            ->with(json_encode($response));

        $helperMock = $this->getMockBuilder('Magento\Core\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $helperMock->expects($this->once())
            ->method('jsonEncode')
            ->with($response)
            ->will($this->returnValue(json_encode($response)));

        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\Core\Helper\Data')
            ->will($this->returnValue($helperMock));

        $this->assertNull($this->controller->execute());
    }

    public function testExecuteException()
    {
        $message = 'Cannot update item quantity.';
        $e = new \Exception($message);
        $response = ['error' => true, 'message' => $message];

        $this->titleMock->expects($this->once())
            ->method('add')
            ->with('Invoices')
            ->will($this->throwException($e));

        $this->responseMock->expects($this->once())
            ->method('representJson')
            ->with(json_encode($response));

        $helperMock = $this->getMockBuilder('Magento\Core\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $helperMock->expects($this->once())
            ->method('jsonEncode')
            ->with($response)
            ->will($this->returnValue(json_encode($response)));

        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\Core\Helper\Data')
            ->will($this->returnValue($helperMock));

        $this->assertNull($this->controller->execute());
    }
}
