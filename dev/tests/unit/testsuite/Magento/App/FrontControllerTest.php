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
    protected $_eventManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_response;

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
        $this->_eventManager = $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false);
        $this->_response = $this->getMock('Magento\App\ResponseInterface', array(), array(), '', false);
        $this->_routerList = $this->getMock('Magento\App\RouterList', array('getRouters'), array(), '', false);
        $this->_request = $this->getMock('Magento\App\RequestInterface', array('isDispatched', 'getModuleName',
            'setModuleName', 'getActionName', 'setActionName', 'getParam'));
        $this->_router = $this->getMock('Magento\App\Router\AbstractRouter',
            array('setFront', 'match'), array(), '', false);
        $this->_model = new \Magento\App\FrontController($this->_eventManager, $this->_response, $this->_routerList);
    }

    /**
     * @expectedException \LogicException
     */
    public function testDispatchThrowException()
    {
        $this->_routerList->expects($this->any())->method('getRouters')->will($this->returnValue($this->_router));
        $this->_model->dispatch($this->_request);
        $this->_eventManager->expects($this->never())->method('dispatch');
    }

    public function testWhenRequestDispatched()
    {
        $this->_request->expects($this->once())->method('isDispatched')->will($this->returnValue(true));
        $this->_routerList->expects($this->never())->method('getRouters');
        $this->_eventManager->expects($this->atLeastOnce())->method('dispatch');
        $this->_model->dispatch($this->_request);
    }

    /**
     * @expectedException \LogicException
     */
    public function testWhenDispatchedActionInterface()
    {
        $this->_router = $this->getMock('Magento\App\Router\AbstractRouter',
            array('setFront', 'match'), array(), '', false);
        $this->_routerList->expects($this->atLeastOnce())->method('getRouters')
            ->will($this->returnValue(array($this->_router)));
        $controllerInstance = $this->getMock('\Magento\App\ActionInterface');
        $this->_router->expects($this->atLeastOnce())->method('match')->will($this->returnValue($controllerInstance));
        $this->_request->expects($this->atLeastOnce())->method('getActionName')->will($this->returnValue('testString'));
        $controllerInstance->expects($this->atLeastOnce())->method('dispatch')->with('testString');

        $this->_model->dispatch($this->_request);
    }
}
