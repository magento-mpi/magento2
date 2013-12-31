<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Authorizenet\Model\Directpost;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Observer
     */
    protected $model;

    /**
     * @var \Magento\Core\Model\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->coreRegistry = $this->getMock('Magento\Core\Model\Registry', []);
        $this->storeManager = $this->getMockForAbstractClass('Magento\Core\Model\StoreManagerInterface');
        $this->model = $helper->getObject('Magento\Authorizenet\Model\Directpost\Observer', [
            'coreRegistry' => $this->coreRegistry,
            'storeManager' => $this->storeManager
        ]);
    }

    public function testAddAdditionalFieldsToResponseFrontend()
    {
        $store = $this->getMock('Magento\Core\Model\Store', [], [], '', false);
        $this->storeManager->expects($this->once())->method('getStore')->will($this->returnValue($store));

        $directpostRequest = $this->getMock('Magento\Authorizenet\Model\Directpost\Request', []);

        $methodInstance = $this->getMock('Magento\Authorizenet\Model\Directpost', [], [], '', false);
        $methodInstance->expects($this->once())
            ->method('generateRequestFromOrder')
            ->will($this->returnValue($directpostRequest));

        $payment = $this->getMock('Magento\Sales\Model\Order\Payment', [], [], '', false);
        $payment->expects($this->once())->method('getMethodInstance')->will($this->returnValue($methodInstance));
        $payment->setMethod('authorizenet_directpost');

        $order = $this->getMock('Magento\Sales\Model\Order', [], [], '', false);
        $order->expects($this->once())->method('getId')->will($this->returnValue(1));
        $order->expects($this->any())->method('getPayment')->will($this->returnValue($payment));

        $this->coreRegistry->expects($this->once())
            ->method('registry')
            ->with('directpost_order')
            ->will($this->returnValue($order));

        $request = new \Magento\Object();
        $response = $this->getMock('Magento\App\Response\Http', [], [], '', false);
        $controller = $this->getMock('Magento\Checkout\Controller\Action', [
            'getRequest',
            'getResponse'
        ], [], '', false);
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $controller->expects($this->once())->method('getResponse')->will($this->returnValue($response));
        $observer = new \Magento\Event\Observer(['event' => new \Magento\Object(['controller_action' => $controller])]);
        $this->model->addAdditionalFieldsToResponseFrontend($observer);
    }
}
