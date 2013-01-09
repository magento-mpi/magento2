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
     * @var Magento_Http_Handler_Composite
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheHandlerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appHandlerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseMock;

    public function setUp()
    {
        $this->_cacheHandlerMock = $this->getMock('Magento_Http_HandlerInterface');
        $this->_appHandlerMock = $this->getMock('Magento_Http_HandlerInterface');
        $this->_requestMock = $this->getMock('Zend_Controller_Request_Http', array(), array(), '', false);
        $this->_responseMock = $this->getMock('Zend_Controller_Response_Http', array(), array(), '', false);
        $this->_model = new Magento_Http_Handler_Composite(array($this->_cacheHandlerMock, $this->_appHandlerMock));
    }

    public function tearDown()
    {
        unset($this->_appHandlerMock);
        unset($this->_cacheHandlerMock);
        unset($this->_requestMock);
        unset($this->_responseMock);
        unset($this->_model);
    }

    public function testHandleBreaksCycleIfRequestIsDispatched()
    {
        $this->_cacheHandlerMock->expects($this->once())->method('handle');
        $this->_appHandlerMock->expects($this->never())->method('handle');
        $this->_requestMock->expects($this->once())->method('isDispatched')->will($this->returnValue(true));
        $this->_model->handle($this->_requestMock, $this->_responseMock);
    }

    public function testHandleCallsAllHandlersUntilRequestIsDispatched()
    {
        $this->_cacheHandlerMock->expects($this->once())->method('handle');
        $this->_appHandlerMock->expects($this->once())->method('handle');
        $this->_requestMock->expects($this->any())->method('isDispatched')->will($this->returnValue(false));
        $this->_model->handle($this->_requestMock, $this->_responseMock);
    }
}
