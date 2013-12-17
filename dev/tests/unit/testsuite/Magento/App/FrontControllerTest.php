<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App;

class FrontControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\FrontController
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_routerList;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_router;

    protected function setUp()
    {
        $this->_request = $this->getMock('Magento\App\Request\Http', array(), array(), '', false);
        $this->_router = $this->getMock('Magento\App\Router\AbstractRouter',
            array('match'), array(), '', false);
        $this->_routerList = $this->getMock('Magento\App\RouterList', array(), array(), '', false);
        $this->_routerList->expects($this->any())
            ->method('getIterator')->will($this->returnValue($this->_routerList));
        $this->_model = new \Magento\App\FrontController($this->_routerList);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage  Front controller reached 100 router match iterations
     */
    public function testDispatchThrowException()
    {
        $this->_request->expects($this->any())->method('isDispatched')->will($this->returnValue(false));
        $this->_model->dispatch($this->_request);
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage  Front controller reached 100 router match iterations
     */
    public function testWhenDispatchedActionInterface()
    {
        $this->_request->expects($this->any())->method('isDispatched')->will($this->returnValue(false));
        $this->_routerList->expects($this->atLeastOnce())->method('valid')->will($this->returnValue(true));
        $this->_routerList->expects($this->atLeastOnce())->method('current')->will($this->returnValue($this->_router));
        $controllerInstance = $this->getMock('Magento\App\ActionInterface');
        $this->_router->expects($this->atLeastOnce())->method('match')->will($this->returnValue($controllerInstance));
        $controllerInstance->expects($this->atLeastOnce())->method('dispatch')->with($this->_request);
        $this->_model->dispatch($this->_request);
    }
}
