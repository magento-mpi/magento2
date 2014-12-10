<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Pbridge\Model;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Pbridge\Model\Observer */
    protected $observer;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject */
    protected $registryMock;

    /** @var \Magento\Core\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $coreHelperMock;

    /** @var \Magento\Framework\App\ViewInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $viewInterfaceMock;

    /**
     * @var \Magento\Framework\Event\Observer
     */
    protected $_observerEvent;

    /**
     * @var \Magento\Framework\Object
     */
    protected $_event;

    protected function setUp()
    {
        $this->registryMock = $this->getMock('Magento\Framework\Registry');
        $this->coreHelperMock = $this->getMock('Magento\Core\Helper\Data', [], [], '', false);
        $this->viewInterfaceMock = $this->getMock('Magento\Framework\App\ViewInterface');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->observer = $this->objectManagerHelper->getObject(
            'Magento\Pbridge\Model\Observer',
            [
                'registry' => $this->registryMock,
                'coreData' => $this->coreHelperMock,
                'view' => $this->viewInterfaceMock
            ]
        );

        $this->_event = new \Magento\Framework\Object();
        $this->_observerEvent = new \Magento\Framework\Event\Observer();
        $this->_observerEvent->setEvent($this->_event);
    }

    public function testSaveOrderAfterSubmit()
    {
        $orderMock = $this->getMockBuilder('Magento\Sales\Model\Order')->disableOriginalConstructor()->setMethods(
            []
        )->getMock();
        $this->_event->setOrder($orderMock);

        $this->registryMock->expects($this->once())->method('register')->with('pbridge_order', $orderMock, true);
        $this->observer->saveOrderAfterSubmit($this->_observerEvent);
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testSetResponseAfterSaveOrder()
    {
        $orderId = 1;
        $orderMock = $this->getMockBuilder('Magento\Sales\Model\Order')->disableOriginalConstructor()->setMethods(
            []
        )->getMock();
        $this->registryMock->expects($this->once())->method('registry')->with('pbridge_order')->will(
            $this->returnValue($orderMock)
        );
        $orderMock->expects($this->once())->method('getId')->will($this->returnValue($orderId));

        $paymentMock = $this->getMockBuilder(
            'Magento\Sales\Model\Order\Payment'
        )->disableOriginalConstructor()->setMethods(['__wakeup', 'getMethodInstance'])->getMock();
        $orderMock->expects($this->once())->method('getPayment')->will($this->returnValue($paymentMock));

        $paymentMethodMock = $this->getMockBuilder(
            'Magento\Payment\Model\MethodInterface'
        )->disableOriginalConstructor()->setMethods(
            [
                'getCode',
                'getFormBlockType',
                'getTitle',
                'getIsPendingOrderRequired',
                'getRedirectUrlSuccess',
                'getRedirectUrlError',
            ]
        )->getMock();

        $paymentMock->expects($this->any())->method('getMethodInstance')->will($this->returnValue($paymentMethodMock));
        $paymentMethodMock->expects($this->once())->method('getIsPendingOrderRequired')->will($this->returnValue(true));

        $expectedJsonPre = '{"error":""}';
        $expectedJsonArrayPre = ['error' => ''];
        $actionMock = $this->getMockBuilder(
            'Magento\Framework\App\Action\Action'
        )->disableOriginalConstructor()->setMethods([])->getMock();
        $this->_event->setData('controller_action', $actionMock);

        $responseMock = $this->getMockBuilder(
            'Magento\Framework\App\Response\Http'
        )->disableOriginalConstructor()->setMethods([])->getMock();
        $responseMock->expects($this->once())->method('getBody')->with('default')->will(
            $this->returnValue($expectedJsonPre)
        );
        $actionMock->expects($this->exactly(3))->method('getResponse')->will($this->returnValue($responseMock));
        $this->coreHelperMock->expects($this->once())->method('jsonDecode')->with($expectedJsonPre)->will(
            $this->returnValue($expectedJsonArrayPre)
        );

        $this->viewInterfaceMock->expects($this->once())->method('loadLayout')->with('checkout_onepage_review');
        $blockMock = $this->getMockBuilder(
            'Magento\Pbridge\Block\Checkout\Payment\Review\Iframe'
        )->disableOriginalConstructor()->setMethods(
            ['setMethod', 'setRedirectUrlSuccess', 'setRedirectUrlError', 'getIframeBlock']
        )->getMock();

        $layout = $this->getMockBuilder(
            'Magento\Framework\View\LayoutInterface'
        )->disableOriginalConstructor()->setMethods([])->getMock();
        $layout->expects($this->once())->method('createBlock')->with(
            'Magento\Pbridge\Block\Checkout\Payment\Review\Iframe'
        )->will($this->returnValue($blockMock));
        $this->viewInterfaceMock->expects($this->once())->method('getLayout')->will($this->returnValue($layout));

        $redirectUrlSuccess = 'redirectUrlSuccess.com';
        $redirectUrlError = 'redirectUrlError.com';
        $paymentMethodMock->expects($this->once())->method('getRedirectUrlSuccess')->will(
            $this->returnValue($redirectUrlSuccess)
        );
        $paymentMethodMock->expects($this->once())->method('getRedirectUrlError')->will(
            $this->returnValue($redirectUrlError)
        );

        $blockMock->expects($this->any())->method('setMethod')->with($paymentMethodMock)->will(
            $this->returnValue($blockMock)
        );
        $blockMock->expects($this->any())->method('setRedirectUrlSuccess')->with(
            $redirectUrlSuccess
        )->will($this->returnValue($blockMock));
        $blockMock->expects($this->any())->method('setRedirectUrlError')->with(
            $redirectUrlError
        )->will($this->returnValue($blockMock));

        $iframeBlockMock = $this->getMockBuilder(
            'Magento\Framework\View\Element\Template'
        )->disableOriginalConstructor()->setMethods([])->getMock();

        $blockMock->expects($this->once())->method('getIframeBlock')->will($this->returnValue($iframeBlockMock));

        $html = 'HTML';
        $iframeBlockMock->expects($this->once())->method('toHtml')->will($this->returnValue($html));

        $responseMock->expects($this->once())->method('clearHeader')->with('Location');
        $expectedResult = [
            'error' => '',
            'update_section' => [
                'name' => 'pbridgeiframe',
                'html' => $html,
            ],
            'redirect' => false,
            'success' => false,
        ];
        $expectedResultJson = '{}';
        $this->coreHelperMock->expects($this->once())->method('jsonEncode')->with($expectedResult)->will(
            $this->returnValue($expectedResultJson)
        );
        $responseMock->expects($this->once())->method('representJson')->with($expectedResultJson);

        $this->observer->setResponseAfterSaveOrder($this->_observerEvent);
    }
}
