<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller;

/**
 * Class GuestTest
 * @package Magento\Rma\Controller
 */
class GuestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Guest
     */
    protected $controller;

    /**
     * @var \Magento\Framework\Registry | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\App\Request\Http | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\Response\Http | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $response;

    /**
     * @var \Magento\Framework\ObjectManager | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Url | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $url;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface  | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $redirect;

    /**
     * @var \Magento\Framework\Message\Manager | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManager;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->request = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $this->response = $this->getMock('Magento\Framework\App\Response\Http', [], [], '', false);
        $this->objectManager = $this->getMock('Magento\Framework\ObjectManager', [], [], '', false);
        $this->messageManager = $this->getMock('Magento\Framework\Message\Manager', [], [], '', false);
        $this->redirect = $this->getMock('Magento\Store\App\Response\Redirect', [], [], '', false);
        $this->url = $this->getMock('Magento\Framework\Url', [], [], '', false);

        $context = $this->getMock('Magento\Framework\App\Action\Context', [], [], '', false);
        $context->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->request));
        $context->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($this->response));
        $context->expects($this->once())
            ->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));
        $context->expects($this->once())
            ->method('getMessageManager')
            ->will($this->returnValue($this->messageManager));
        $context->expects($this->once())
            ->method('getRedirect')
            ->will($this->returnValue($this->redirect));
        $context->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue($this->url));

        $this->coreRegistry = $this->getMock('Magento\Framework\Registry', ['registry'], [], '', false);

        $this->controller = $objectManagerHelper->getObject(
            'Magento\Rma\Controller\Guest',
            [
                'coreRegistry' => $this->coreRegistry,
                'context' => $context
            ]
        );
    }

    public function testCreateAction()
    {
        $orderId = 2;
        $post = ['customer_custom_email' => true, 'items' => ['1', '2'], 'rma_comment' => 'comment'];

        $order = $this->getMock(
            'Magento\Sales\Model\Order',
            ['__wakeup', 'getCustomerId', 'load', 'getId'],
            [],
            '',
            false
        );
        $order->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($orderId));

        $dateTime = $this->getMock('Magento\Framework\Stdlib\DateTime\DateTime', [], [], '', false);
        $rma = $this->getMock('Magento\Rma\Model\Rma', [], [], '', false);
        $rma->expects($this->once())
            ->method('setData')
            ->will($this->returnSelf());
        $rma->expects($this->once())
            ->method('saveRma')
            ->will($this->returnSelf());
        $history1 = $this->getMock('Magento\Rma\Model\Rma\Status\History', [], [], '', false);
        $history2 = $this->getMock('Magento\Rma\Model\Rma\Status\History', [], [], '', false);
        $rmaHelper = $this->getMock('Magento\Rma\Helper\Data', [], [], '', false);
        $rmaHelper->expects($this->once())
            ->method('canCreateRma')
            ->with($orderId)
            ->will($this->returnValue(true));
        $guestHelper = $this->getMock('Magento\Sales\Helper\Guest', [], [], '', false);
        $guestHelper->expects($this->once())
            ->method('loadValidOrder')
            ->with($this->request, $this->response)
            ->will($this->returnValue(true));

        $this->objectManager->expects($this->at(0))
            ->method('get')
            ->with('Magento\Sales\Helper\Guest')
            ->will($this->returnValue($guestHelper));
        $this->objectManager->expects($this->at(1))
            ->method('get')
            ->with('Magento\Rma\Helper\Data')
            ->will($this->returnValue($rmaHelper));
        $this->objectManager->expects($this->at(2))
            ->method('get')
            ->with('Magento\Framework\Stdlib\DateTime\DateTime')
            ->will($this->returnValue($dateTime));
        $this->objectManager->expects($this->at(3))
            ->method('create')
            ->with('Magento\Rma\Model\Rma')
            ->will($this->returnValue($rma));
        $this->objectManager->expects($this->at(4))
            ->method('create')
            ->with('Magento\Rma\Model\Rma\Status\History')
            ->will($this->returnValue($history1));
        $this->objectManager->expects($this->at(5))
            ->method('create')
            ->with('Magento\Rma\Model\Rma\Status\History')
            ->will($this->returnValue($history2));

        $this->request->expects($this->once())
            ->method('getPost')
            ->will($this->returnValue($post));
        $this->coreRegistry->expects($this->once())
            ->method('registry')
            ->with('current_order')
            ->will($this->returnValue($order));

        $this->controller->createAction();
    }

    public function testAddCommentAction()
    {
        $entityId = 7;
        $orderId = 5;
        $comment = 'comment';

        $this->request->expects($this->any())
            ->method('getParam')
            ->with('entity_id')
            ->will($this->returnValue($entityId));
        $this->request->expects($this->any())
            ->method('getPost')
            ->with('comment')
            ->will($this->returnValue($comment));

        $rmaHelper = $this->getMock('Magento\Rma\Helper\Data', [], [], '', false);
        $rmaHelper->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));

        $guestHelper = $this->getMock('Magento\Sales\Helper\Guest', [], [], '', false);
        $guestHelper->expects($this->once())
            ->method('loadValidOrder')
            ->with($this->request, $this->response)
            ->will($this->returnValue(true));

        $rma = $this->getMock(
            'Magento\Rma\Model\Rma',
            ['__wakeup', 'load', 'getCustomerId', 'getId', 'getOrderId'],
            [],
            '',
            false
        );
        $rma->expects($this->once())
            ->method('load')
            ->with($entityId)
            ->will($this->returnSelf());
        $rma->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($entityId));
        $rma->expects($this->any())
            ->method('getOrderId')
            ->will($this->returnValue($orderId));

        $order = $this->getMock(
            'Magento\Sales\Model\Order',
            ['__wakeup', 'getCustomerId', 'load', 'getId'],
            [],
            '',
            false
        );
        $order->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($orderId));

        $history = $this->getMock('Magento\Rma\Model\Rma\Status\History', [], [], '', false);
        $history->expects($this->once())
            ->method('sendCustomerCommentEmail');
        $history->expects($this->once())
            ->method('saveComment')
            ->with($comment, true, false);

        $this->objectManager->expects($this->at(0))
            ->method('get')
            ->with('Magento\Rma\Helper\Data')
            ->will($this->returnValue($rmaHelper));
        $this->objectManager->expects($this->at(1))
            ->method('get')
            ->with('Magento\Sales\Helper\Guest')
            ->will($this->returnValue($guestHelper));
        $this->objectManager->expects($this->at(2))
            ->method('create')
            ->with('Magento\Rma\Model\Rma')
            ->will($this->returnValue($rma));
        $this->objectManager->expects($this->at(3))
            ->method('create')
            ->with('Magento\Rma\Model\Rma\Status\History')
            ->will($this->returnValue($history));

        $this->coreRegistry->expects($this->at(0))
            ->method('registry')
            ->with('current_order')
            ->will($this->returnValue($order));
        $this->coreRegistry->expects($this->at(1))
            ->method('registry')
            ->with('current_rma')
            ->will($this->returnValue($rma));

        $this->controller->addCommentAction();
    }
}
