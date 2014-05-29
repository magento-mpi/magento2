<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller;

class ReturnsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Returns
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
     * @var \Magento\Framework\App\View | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $view;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->request = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $this->objectManager = $this->getMock('Magento\Framework\ObjectManager', [], [], '', false);
        $this->view = $this->getMock('Magento\Framework\App\View', [], [], '', false);
        $this->response = $this->getMock('Magento\Framework\App\Response\Http', [], [], '', false);

        $context = $this->getMock('Magento\Framework\App\Action\Context', [], [], '', false);
        $context->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($this->request));
        $context->expects($this->once())
            ->method('getObjectManager')
            ->will($this->returnValue($this->objectManager));
        $context->expects($this->once())
            ->method('getView')
            ->will($this->returnValue($this->view));
        $context->expects($this->once())
            ->method('getResponse')
            ->will($this->returnValue($this->response));

        $this->coreRegistry = $this->getMock('Magento\Framework\Registry');

        $this->controller = $objectManager->getObject(
            'Magento\Rma\Controller\Returns',
            [
                'coreRegistry' => $this->coreRegistry,
                'context' => $context
            ]
        );
    }

    public function testReturnsAction()
    {
        $orderId = 5;
        $customerId = 7;
        $visibleStatuses = ['status1', 'status2'];

        $this->request->expects($this->once())
            ->method('getParam')
            ->with('order_id')
            ->will($this->returnValue($orderId));

        $order = $this->getMock(
            'Magento\Sales\Model\Order',
            ['getId', 'getCustomerId', 'getStatus', '__wakeup', 'load'],
            [],
            '',
            false
        );
        $order->expects($this->once())
            ->method('load')
            ->with($orderId)
            ->will($this->returnValue($order));
        $order->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($orderId));
        $order->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue($customerId));
        $order->expects($this->any())
            ->method('getStatus')
            ->will($this->returnValue($visibleStatuses[0]));
        $rmaHelper = $this->getMock('Magento\Rma\Helper\Data', [], [], '', false);
        $rmaHelper->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $session = $this->getMock('Magento\Customer\Model\Session', [], [], '', false);
        $session->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue($customerId));
        $orderConfig = $this->getMock('Magento\Sales\Model\Order\Config', [], [], '', false);
        $orderConfig->expects($this->once())
            ->method('getVisibleOnFrontStatuses')
            ->will($this->returnValue($visibleStatuses));

        $layout = $this->getMock('Magento\Framework\View\Layout', [], [], '', false);
        $this->view->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layout));

        $this->objectManager->expects($this->at(0))
            ->method('get')
            ->with('Magento\Customer\Model\Session')
            ->will($this->returnValue($session));
        $this->objectManager->expects($this->at(1))
            ->method('get')
            ->with('Magento\Rma\Helper\Data')
            ->will($this->returnValue($rmaHelper));
        $this->objectManager->expects($this->at(2))
            ->method('create')
            ->with('Magento\Sales\Model\Order')
            ->will($this->returnValue($order));
        $this->objectManager->expects($this->at(3))
            ->method('get')
            ->with('Magento\Sales\Model\Order\Config')
            ->will($this->returnValue($orderConfig));

        $this->coreRegistry->expects($this->once())
            ->method('register')
            ->with('current_order', $order);
        $this->view->expects($this->once())
            ->method('renderLayout');

        $this->controller->returnsAction();
    }
}
