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
namespace Magento\FullPageCache\Model\Http;

class HandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\FullPageCache\Model\Http\Handler
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseFactoryMock;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('\Magento\App\Request\Http', array(), array(), '', false, false);
        $this->_responseMock = $this->getMock('Magento\App\Response\Http', array(), array(), '', false, false);

        $this->_requestFactoryMock = $this->getMock(
            'Magento\FullPageCache\Model\RequestProcessorFactory', array(), array(), '', false, false
        );
        $this->_responseFactoryMock = $this->getMock(
            '\Magento\App\ResponseFactory', array(), array(), '', false, false
        );
    }

    protected function tearDown()
    {
        unset($this->_configMock);
        unset($this->_requestFactoryMock);
        unset($this->_responseFactoryMock);
        unset($this->_requestMock);
        unset($this->_responseMock);
        unset($this->_model);
    }

    public function testHandleWithoutProcessors()
    {
        $this->_requestFactoryMock->expects($this->never())->method('create');
        $this->_requestMock->expects($this->never())->method('setDispatched');
        $this->_responseMock->expects($this->never())->method('sendResponse');

        $this->_model = new \Magento\FullPageCache\Model\Http\Handler(
            $this->_responseFactoryMock,
            $this->_requestFactoryMock,
            array()
        );
        $this->_model->handle($this->_requestMock, $this->_responseMock);
    }

    public function testHandleWithProcessorsContent()
    {
        $processorMock = $this->getMock(
            'Magento\FullPageCache\Model\Processor', array(), array(), '', false, false
        );
        $this->_requestFactoryMock->expects($this->once())
            ->method('create')->with('processor_class')->will($this->returnValue($processorMock));

        $this->_responseFactoryMock->expects($this->once())
            ->method('create')->will($this->returnValue($this->_responseMock));

        $processorMock->expects($this->once())
            ->method('extractContent')
            ->with($this->_requestMock, $this->_responseMock, false)
            ->will($this->returnValue('cache'));

        $this->_requestMock->expects($this->once())->method('setDispatched')->with(true);

        $this->_responseMock->expects($this->once())->method('appendBody')->with('cache');
        $this->_responseMock->expects($this->once())->method('sendResponse');

        $this->_model = new \Magento\FullPageCache\Model\Http\Handler(
            $this->_responseFactoryMock,
            $this->_requestFactoryMock,
            array(array('sortOrder' => 10, 'class' => 'processor_class'))
        );
        $this->_model->handle($this->_requestMock, $this->_responseMock);
    }

    public function testHandleWithoutProcessorsContent()
    {
        $processorMock = $this->getMock(
            'Magento\FullPageCache\Model\Processor', array(), array(), '', false, false
        );
        $this->_requestFactoryMock->expects($this->once())
            ->method('create')->with('processor_class')->will($this->returnValue($processorMock));

        $this->_responseFactoryMock->expects($this->once())
            ->method('create')->will($this->returnValue($this->_responseMock));

        $processorMock->expects($this->once())
            ->method('extractContent')
            ->with($this->_requestMock, $this->_responseMock, false)
            ->will($this->returnValue(false));

        $this->_requestMock->expects($this->never())->method('setDispatched');

        $this->_responseMock->expects($this->never())->method('appendBody');
        $this->_responseMock->expects($this->never())->method('sendResponse');

        $this->_model = new \Magento\FullPageCache\Model\Http\Handler(
            $this->_responseFactoryMock,
            $this->_requestFactoryMock,
            array(array('sortOrder' => 10, 'class' => 'processor_class'))
        );
        $this->_model->handle($this->_requestMock, $this->_responseMock);
    }
}
