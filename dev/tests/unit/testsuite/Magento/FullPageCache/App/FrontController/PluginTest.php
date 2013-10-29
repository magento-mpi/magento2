<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\App\FrontController;
class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\FullPageCache\App\FrontController\Plugin
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_invocationChainMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestProcessor;

    protected function setUp()
    {
        $this->_requestProcessor = $this->getMock('\Magento\FullPageCache\Model\RequestProcessorInterface');
        $requestArray = array(
            'sortOrder' => array('class' => 'name')
        );

        $this->_invocationChainMock =
            $this->getMock('\Magento\Code\Plugin\InvocationChain', array(), array(), '', false);
        $this->_responseFactoryMock = $this->getMock('\Magento\App\ResponseFactory', array(), array(), '', false);
        $this->_requestFactoryMock =
            $this->getMock('\Magento\FullPageCache\Model\RequestProcessorFactory', array(), array(), '', false);
        $this->_requestFactoryMock
            ->expects($this->once())->method('create')->will($this->returnValue($this->_requestProcessor));
        $this->_model = new \Magento\FullPageCache\App\FrontController\Plugin(
            $this->_responseFactoryMock,
            $this->_requestFactoryMock,
            $requestArray
        );
    }

    public function testAroundDispatchIfProcessorsAndContentExist()
    {
        $requestMock = $this->getMock('\Magento\App\RequestInterface',
                array('setDispatched', 'getModuleName', 'setModuleName', 'getActionName', 'setActionName', 'getParam'));
        $responseMock = $this->getMock('\Magento\App\ResponseInterface', array('appendBody', 'sendResponse'));
        $this->_responseFactoryMock->expects($this->once())->method('create')->will($this->returnValue($responseMock));
        $arguments = array($requestMock);
        $this->_requestProcessor->expects($this->once())
            ->method('extractContent')->with($requestMock, $responseMock, false)->will($this->returnValue(true));
        $responseMock->expects($this->once())->method('sendResponse');
        $this->assertEquals(null, $this->_model->aroundDispatch($arguments, $this->_invocationChainMock));
    }

    public function testAroundDispatchIfProcessorsExistAndContentNotExist()
    {
        $requestMock =
            $this->getMock('\Magento\App\RequestInterface',
                array('setDispatched', 'getModuleName', 'setModuleName', 'getActionName', 'setActionName', 'getParam'));
        $responseMock = $this->getMock('\Magento\App\ResponseInterface', array('appendBody', 'sendResponse'));
        $this->_responseFactoryMock->expects($this->once())->method('create')->will($this->returnValue($responseMock));
        $arguments = array($requestMock);
        $this->_requestProcessor->expects($this->once())
            ->method('extractContent')->with($requestMock, $responseMock, false)->will($this->returnValue(false));
        $responseMock->expects($this->never())->method('sendResponse');
        $this->_invocationChainMock->expects($this->once())->method('proceed')->with($arguments);
        $this->_model->aroundDispatch($arguments, $this->_invocationChainMock);
    }

    public function testAroundDispatchIfProcessorsNotExist()
    {
        $this->_model = new \Magento\FullPageCache\App\FrontController\Plugin(
            $this->_responseFactoryMock,
            $this->_requestFactoryMock,
            array()
        );
        $this->_responseFactoryMock->expects($this->never())->method('create');
        $this->_invocationChainMock->expects($this->once())->method('proceed')->with(array());
        $this->_model->aroundDispatch(array(), $this->_invocationChainMock);
    }
}
