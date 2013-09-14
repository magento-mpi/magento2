<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\FullPageCache\Model\Http\Handler
 */
class Magento_FullPageCache_Model_Http_HandlerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\FullPageCache\Model\Http\Handler
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
    protected $_factoryMock;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Zend_Controller_Request_Http', array(), array(), '', false, false);
        $this->_responseMock = $this->getMock('Zend_Controller_Response_Http', array(), array(), '', false, false);
        $this->_factoryMock = $this->getMock('Magento\FullPageCache\Model\RequestProcessorFactory',
            array(), array(), '', false, false);
    }

    protected function tearDown()
    {
        unset($this->_configMock);
        unset($this->_factoryMock);
        unset($this->_requestMock);
        unset($this->_responseMock);
        unset($this->_model);
    }

    public function testHandleWithoutProcessors()
    {
        $this->_factoryMock->expects($this->never())->method('create');
        $this->_requestMock->expects($this->never())->method('setDispatched');
        $this->_responseMock->expects($this->never())->method('sendResponse');

        $this->_model = new \Magento\FullPageCache\Model\Http\Handler(array(), $this->_factoryMock);
        $this->_model->handle($this->_requestMock, $this->_responseMock);
    }

    public function testHandleWithProcessorsContent()
    {
        $processorMock = $this->getMock(
            'Magento\FullPageCache\Model\Processor', array(), array(), '', false, false
        );
        $this->_factoryMock->expects($this->once())
            ->method('create')->with('processor_class')->will($this->returnValue($processorMock));

        $processorMock->expects($this->once())
            ->method('extractContent')
            ->with($this->_requestMock, $this->_responseMock, false)
            ->will($this->returnValue('cache'));

        $this->_requestMock->expects($this->once())->method('setDispatched')->with(true);

        $this->_responseMock->expects($this->once())->method('appendBody')->with('cache');
        $this->_responseMock->expects($this->once())->method('sendResponse');

        $this->_model = new \Magento\FullPageCache\Model\Http\Handler(
            array(array('sortOrder' => 10, 'class' => 'processor_class')),
            $this->_factoryMock
        );
        $this->_model->handle($this->_requestMock, $this->_responseMock);
    }

    public function testHandleWithoutProcessorsContent()
    {
        $processorMock = $this->getMock(
            'Magento\FullPageCache\Model\Processor', array(), array(), '', false, false
        );
        $this->_factoryMock->expects($this->once())
            ->method('create')->with('processor_class')->will($this->returnValue($processorMock));

        $processorMock->expects($this->once())
            ->method('extractContent')
            ->with($this->_requestMock, $this->_responseMock, false)
            ->will($this->returnValue(false));

        $this->_requestMock->expects($this->never())->method('setDispatched');

        $this->_responseMock->expects($this->never())->method('appendBody');
        $this->_responseMock->expects($this->never())->method('sendResponse');

        $this->_model = new \Magento\FullPageCache\Model\Http\Handler(
            array(array('sortOrder' => 10, 'class' => 'processor_class')),
            $this->_factoryMock
        );
        $this->_model->handle($this->_requestMock, $this->_responseMock);
    }
}
