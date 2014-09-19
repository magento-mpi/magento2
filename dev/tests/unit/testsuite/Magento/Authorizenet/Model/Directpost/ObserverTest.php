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
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Core\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $coreData;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $store = $this->getMock('Magento\Store\Model\Store', array(), array(), '', false);
        $this->coreRegistry = $this->getMock('Magento\Framework\Registry', array());
        $storeManager = $this->getMockForAbstractClass('Magento\Framework\StoreManagerInterface');
        $storeManager->expects($this->any())->method('getStore')->will($this->returnValue($store));
        $payment = $this->getMock('Magento\Authorizenet\Model\Directpost', null, array(), '', false);
        $this->coreData = $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false);
        $this->model = $helper->getObject(
            'Magento\Authorizenet\Model\Directpost\Observer',
            array(
                'coreRegistry' => $this->coreRegistry,
                'storeManager' => $storeManager,
                'payment' => $payment,
                'coreData' => $this->coreData
            )
        );
    }

    public function testAddAdditionalFieldsToResponseFrontend()
    {
        $directpostRequest = $this->getMock('Magento\Authorizenet\Model\Directpost\Request', array());
        $order = $this->getMock('Magento\Sales\Model\Order', array(), array(), '', false);

        $methodInstance = $this->getMock('Magento\Authorizenet\Model\Directpost', array(), array(), '', false);
        $methodInstance->expects(
            $this->once()
        )->method(
            'generateRequestFromOrder'
        )->with(
            $this->identicalTo($order)
        )->will(
            $this->returnValue($directpostRequest)
        );

        $payment = $this->getMock(
            'Magento\Sales\Model\Order\Payment',
            array('getMethodInstance', '__wakeup'),
            array(),
            '',
            false
        );
        $payment->expects($this->once())->method('getMethodInstance')->will($this->returnValue($methodInstance));
        $payment->setMethod('authorizenet_directpost');

        $order->expects($this->once())->method('getId')->will($this->returnValue(1));
        $order->expects($this->atLeastOnce())->method('getPayment')->will($this->returnValue($payment));


        $this->coreRegistry->expects(
            $this->once()
        )->method(
            'registry'
        )->with(
            'directpost_order'
        )->will(
            $this->returnValue($order)
        );

        $request = new \Magento\Framework\Object();
        $response = $this->getMock('Magento\Framework\App\Response\Http', array(), array(), '', false);
        $controller = $this->getMock(
            'Magento\Checkout\Controller\Action',
            array('getRequest', 'getResponse'),
            array(),
            '',
            false
        );
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));
        $controller->expects($this->once())->method('getResponse')->will($this->returnValue($response));
        $observer = new \Magento\Framework\Event\Observer(
            array('event' => new \Magento\Framework\Object(array('controller_action' => $controller)))
        );

        $this->coreData->expects(
            $this->once()
        )->method(
            'jsonEncode'
        )->with(
            self::logicalNot(self::isEmpty())
        )->will(
            $this->returnValue('encoded response')
        );
        $response->expects($this->once())->method('clearHeader')->with('Location');
        $response->expects($this->once())->method('representJson')->with('encoded response');
        $this->model->addAdditionalFieldsToResponseFrontend($observer);
    }
}
