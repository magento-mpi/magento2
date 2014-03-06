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
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $closureMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestProcessor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    protected function setUp()
    {
        $this->requestProcessor = $this->getMock('Magento\FullPageCache\Model\RequestProcessorInterface');
        $requestMethods =
            array('setDispatched', 'getModuleName', 'setModuleName', 'getActionName', 'setActionName', 'getParam');
        $this->requestMock = $this->getMock('Magento\App\Request\Http', $requestMethods, array(), '', false);
        $requestArray = array(
            'sortOrder' => array('class' => 'name')
        );
        $this->closureMock = function () {
            return 'Expected';
        };
        $this->responseFactoryMock = $this->getMock('\Magento\App\ResponseFactory', array(), array(), '', false);
        $this->requestFactoryMock =
            $this->getMock('Magento\FullPageCache\Model\RequestProcessorFactory', array(), array(), '', false);
        $this->requestFactoryMock
            ->expects($this->once())->method('create')->will($this->returnValue($this->requestProcessor));
        $this->subjectMock = $this->getMock('Magento\App\FrontController', array(), array(), '', false);
        $this->model = new \Magento\FullPageCache\App\FrontController\Plugin(
            $this->responseFactoryMock,
            $this->requestFactoryMock,
            $requestArray
        );
    }

    public function testAroundDispatchIfProcessorsAndContentExist()
    {
        $responseMock = $this->getMock('\Magento\App\ResponseInterface', array('appendBody', 'sendResponse'));
        $this->responseFactoryMock->expects($this->once())->method('create')->will($this->returnValue($responseMock));
        $this->requestProcessor->expects($this->once())
            ->method('extractContent')->with($this->requestMock, $responseMock, false)->will($this->returnValue(true));
        $this->assertEquals($responseMock,
            $this->model->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock)
        );
    }

    public function testAroundDispatchIfProcessorsExistAndContentNotExist()
    {
        $responseMock = $this->getMock('\Magento\App\ResponseInterface', array('appendBody', 'sendResponse'));
        $this->responseFactoryMock->expects($this->once())->method('create')->will($this->returnValue($responseMock));
        $this->requestProcessor->expects($this->once())
            ->method('extractContent')->with($this->requestMock, $responseMock, false)->will($this->returnValue(false));
        $responseMock->expects($this->never())->method('sendResponse');
        $this->model->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock);
    }

    public function testAroundDispatchIfProcessorsNotExist()
    {
        $this->model = new \Magento\FullPageCache\App\FrontController\Plugin(
            $this->responseFactoryMock,
            $this->requestFactoryMock,
            array()
        );
        $this->responseFactoryMock->expects($this->never())->method('create');
        $this->model->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock);
    }
}
