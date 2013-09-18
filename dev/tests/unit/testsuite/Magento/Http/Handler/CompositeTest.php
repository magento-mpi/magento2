<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Http_Handler_CompositeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\HTTP\Handler\Composite
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_handlerFactoryMock;

    protected function setUp()
    {
        $handlers = array(
            'app' => array(
                'sortOrder' => 50,
                'class' => 'Class_App_Handler',
            ),
            'fpc' => array(
                'sortOrder' => 20,
                'class' => 'Class_Fpc_Handler',
            ),
        );
        $this->_requestMock = $this->getMock('Zend_Controller_Request_Http', array(), array(), '', false);
        $this->_responseMock = $this->getMock('Zend_Controller_Response_Http', array(), array(), '', false);
        $this->_handlerFactoryMock = $this->getMock('Magento\HTTP\HandlerFactory', array(), array(), '', false, false);
        $this->_handlerMock = $this->getMock('Magento\HTTP\HandlerInterface', array(), array(), '', false, false);
        $this->_model = new \Magento\HTTP\Handler\Composite($this->_handlerFactoryMock, $handlers);
    }

    protected function tearDown()
    {
        unset($this->_requestMock);
        unset($this->_responseMock);
        unset($this->_handlerFactoryMock);
        unset($this->_model);
    }

    public function testHandleBreaksCycleIfRequestIsDispatched()
    {
        $this->_handlerFactoryMock->expects($this->once())
            ->method('create')->with('Class_Fpc_Handler')->will($this->returnValue($this->_handlerMock));
        $this->_handlerMock->expects($this->once())
            ->method('handle')->with($this->_requestMock, $this->_responseMock);
        $this->_requestMock->expects($this->once())->method('isDispatched')->will($this->returnValue(true));

        $this->_model->handle($this->_requestMock, $this->_responseMock);
    }

    public function testSorting()
    {
        $handlers = array(
            'app' => array(
                'sortOrder' => 50,
                'class' => 'Class_App_Handler',
            ),
            'fpc' => array(
                'sortOrder' => 20,
                'class' => 'Class_Fpc_Handler',
            ),
        );

        $model = new \Magento\HTTP\Handler\Composite($this->_handlerFactoryMock, $handlers);

        $this->_handlerMock->expects($this->exactly(2))->method('handle')
            ->with($this->_requestMock, $this->_responseMock);

        $this->_handlerFactoryMock->expects($this->at(0))
            ->method('create')
            ->with('Class_Fpc_Handler')
            ->will($this->returnValue($this->_handlerMock));

        $this->_handlerFactoryMock->expects($this->at(1))
            ->method('create')
            ->with('Class_App_Handler')
            ->will($this->returnValue($this->_handlerMock));

        $model->handle($this->_requestMock, $this->_responseMock);
    }
}
