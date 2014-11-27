<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Controller\Adminhtml\Order\Invoice;

use Magento\Backend\App\Action;

/**
 * Class UpdateQtyTest
 * @package Magento\Sales\Controller\Adminhtml\Order\Invoice
 */
class UpdateQtyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var int
     */
    protected $orderId = 1;

    /**
     * @var int
     */
    protected $invoiceId = 2;

    /**
     * @var []
     */
    protected $invoiceData = ['comment_text' => 'test'];

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoiceLoaderMock;

    /**
     * @var \Magento\Sales\Model\Order\Invoice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoiceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $viewInterfaceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultPageMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $pageConfigMock;

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
        $this->titleMock = $this->getMockBuilder('Magento\Framework\View\Page\Title')
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = $this->getMockBuilder('Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()
            ->setMethods(['getParam'])
            ->getMock();
        $this->responseMock = $this->getMockBuilder('Magento\Framework\App\Response\Http')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultPageMock = $this->getMockBuilder('Magento\Framework\View\Result\Page')
            ->disableOriginalConstructor()
            ->getMock();
        $this->pageConfigMock = $this->getMockBuilder('Magento\Framework\View\Page\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->viewInterfaceMock = $this->getMockBuilder('Magento\Framework\App\ViewInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['order_id', null, $this->orderId],
                    ['invoice_id', null, $this->invoiceId],
                    ['invoice', [], $this->invoiceData]
                ]
            );
        $this->viewInterfaceMock->expects($this->any())->method('getPage')->will(
            $this->returnValue($this->resultPageMock)
        );
        $this->resultPageMock->expects($this->any())->method('getConfig')->will(
            $this->returnValue($this->pageConfigMock)
        );

        $this->pageConfigMock->expects($this->any())->method('getTitle')->will($this->returnValue($this->titleMock));

        $this->objectManagerMock = $this->getMockBuilder('Magento\Framework\ObjectManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock = $this->getMockBuilder('Magento\Backend\App\Action\Context')
            ->disableOriginalConstructor()
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
            ->will($this->returnValue($this->viewInterfaceMock));
        $contextMock->expects($this->any())
            ->method('getObjectManager')
            ->will($this->returnValue($this->objectManagerMock));

        $this->invoiceLoaderMock = $this->getMockBuilder('Magento\Sales\Controller\Adminhtml\Order\InvoiceLoader')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->invoiceMock = $this->getMockBuilder('Magento\Sales\Model\Order\Invoice')
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller = new \Magento\Sales\Controller\Adminhtml\Order\Invoice\UpdateQty(
            $contextMock,
            $this->invoiceLoaderMock
        );

        $this->invoiceLoaderMock->expects($this->any())
            ->method('load')
            ->with($this->orderId, $this->invoiceId, [])
            ->will($this->returnValue($this->invoiceMock));
    }

    public function testExecute()
    {
        $response = 'test data';

        $this->responseMock->expects($this->once())
            ->method('setBody')
            ->with($response);

        $blockItemMock = $this->getMockBuilder('Magento\Sales\Block\Order\Items')
            ->disableOriginalConstructor()
            ->getMock();
        $blockItemMock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($response));

        $layoutMock = $this->getMockBuilder('Magento\Framework\View\Layout')
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('order_items')
            ->will($this->returnValue($blockItemMock));

        $this->viewInterfaceMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));

        $this->assertNull($this->controller->execute());
    }

    public function testExecuteModelException()
    {
        $message = 'test message';
        $e = new \Magento\Framework\Model\Exception($message);
        $response = ['error' => true, 'message' => $message];

        $this->titleMock->expects($this->once())
            ->method('prepend')
            ->with('Invoices')
            ->willThrowException($e);

        $this->responseMock->expects($this->once())
            ->method('representJson')
            ->with(json_encode($response));

        $helperMock = $this->getMockBuilder('Magento\Core\Helper\Data')
            ->disableOriginalConstructor()
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
            ->method('prepend')
            ->with('Invoices')
            ->will($this->throwException($e));

        $this->responseMock->expects($this->once())
            ->method('representJson')
            ->with(json_encode($response));

        $helperMock = $this->getMockBuilder('Magento\Core\Helper\Data')
            ->disableOriginalConstructor()
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
