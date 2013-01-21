<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Enterprise_PageCache_Model_Processor
 */
class Enterprise_PageCache_Model_ProcessorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_PageCache_Model_Processor
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_scopeCodeMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseMock;

    protected function setUp()
    {
        $this->markTestIncomplete('MAGETWO-6406');
        $this->_requestMock = $this->getMock('Zend_Controller_Request_Http', array(), array(), '', false, false);
        $this->_responseMock = $this->getMock('Zend_Controller_Response_Http', array(), array(), '', false, false);
        $this->_model = $this->getMock('Enterprise_PageCache_Model_Processor',
            array('extractContent'), array(), '', false, false);
    }

    protected function tearDown()
    {
        unset($this->_scopeCodeMock);
        unset($this->_requestMock);
        unset($this->_responseMock);
        unset($this->_model);
    }

    public function testHandleWhenContentIsEmpty()
    {
        $this->_model->expects($this->once())
            ->method('extractContent')
            ->will($this->returnValue(false));
        $this->_requestMock->expects($this->never())->method('setDispatched');
        $this->_responseMock->expects($this->never())->method('appendBody');
        $this->_responseMock->expects($this->never())->method('sendResponse');
        $this->_model->handle($this->_requestMock, $this->_responseMock);
    }

    public function testHandleWhenContentNotEmpty()
    {
        $this->_model->expects($this->once())
            ->method('extractContent')
            ->will($this->returnValue('some_content'));
        $this->_requestMock->expects($this->once())->method('setDispatched')->with(true);
        $this->_responseMock->expects($this->once())->method('appendBody')->with('some_content');
        $this->_responseMock->expects($this->once())->method('sendResponse');
        $this->_model->handle($this->_requestMock, $this->_responseMock);
    }
}
