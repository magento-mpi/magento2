<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Logging\App\Action\Plugin;

class LogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $processorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $requestMock;

    /**
     * @var Log
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /**
     * @var \Closure
     */
    protected $closureMock;

    protected function setUp()
    {
        $this->processorMock = $this->getMock('\Magento\Logging\Model\Processor', array(), array(), '', false);
        $this->requestMock = $this->getMock('\Magento\App\Request\Http', array(), array(), '', false);
        $this->requestMock->expects($this->once())->method('getRequestedActionName')->will($this->returnValue(
            'taction'
        ));
        $this->subjectMock = $this->getMock('Magento\App\ActionInterface');
        $this->closureMock = function () {
            return 'Expected';
        };
        $this->model = new Log($this->processorMock);
    }

    public function testAroundDispatchWithoutForward()
    {
        $this->requestMock->expects($this->once())->method('getFullActionName')->will($this->returnValue(
            'tmodule_tcontroller_taction'
        ));
        $this->processorMock->expects($this->once())->method('initAction')->with(
            'tmodule_tcontroller_taction', 'taction'
        );
        $this->assertEquals('Expected',
            $this->model->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock)
        );
    }

    public function testAroundDispatchWithForward()
    {
        $this->requestMock->expects($this->once())->method('getRequestedRouteName')->will($this->returnValue(
            'origRoute'
        ));

        $this->requestMock->expects($this->once())->method('getBeforeForwardInfo')->will($this->returnValue(
            array('controller_name' => 'origcontroller', 'action_name' => 'origaction')
        ));
        $this->processorMock->expects($this->once())->method('initAction')->with(
            'origRoute_origcontroller_origaction', 'taction'
        );
        $this->assertEquals('Expected',
            $this->model->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock)
        );
    }


    public function testAroundDispatchWithForwardAndWithoutOriginalInfo()
    {
        $this->requestMock->expects($this->once())->method('getRequestedRouteName')->will($this->returnValue(
            'origRoute'
        ));
        $this->requestMock->expects($this->once())->method('getRequestedControllerName')->will($this->returnValue(
            'requestedController'
        ));
        $this->requestMock->expects($this->once())->method('getBeforeForwardInfo')->will($this->returnValue(
            array('forward')
        ));
        $this->processorMock->expects($this->once())->method('initAction')->with(
            'origRoute_requestedController_taction', 'taction'
        );
        $this->assertEquals('Expected',
            $this->model->aroundDispatch($this->subjectMock, $this->closureMock, $this->requestMock)
        );
    }
}
