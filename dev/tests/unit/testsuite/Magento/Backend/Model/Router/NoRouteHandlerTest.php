<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Router;

class NoRouteHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_helperMock;

    /**
     * @var \Magento\Core\Controller\Request\Http
     */
    protected $_requestMock;

    /**
     * @var \Magento\Backend\Model\Router\NoRouteHandler
     */
    protected $_model;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Magento\Core\Controller\Request\Http', array(), array(), '', false);
        $this->_helperMock = $this->getMock('Magento\Backend\Helper\Data', array(), array(), '', false);
        $this->_helperMock->expects($this->any())->method('getAreaFrontName')->will($this->returnValue('backend'));
        $this->_model = new \Magento\Backend\Model\Router\NoRouteHandler($this->_helperMock);
    }

    public function testProcessWithBackendAreaFrontName()
    {
        $this->_requestMock
            ->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('backend/admin/custom'));

        $this->_requestMock
            ->expects($this->once())
            ->method('setModuleName')
            ->with('core')
            ->will($this->returnValue($this->_requestMock));

        $this->_requestMock
            ->expects($this->once())
            ->method('setControllerName')
            ->with('index')
            ->will($this->returnValue($this->_requestMock));

        $this->_requestMock
            ->expects($this->once())
            ->method('setActionName')
            ->with('noRoute')
            ->will($this->returnValue($this->_requestMock));

        $this->assertEquals(true, $this->_model->process($this->_requestMock));
    }

    public function testProcessWithoutAreaFrontName()
    {
        $this->_requestMock
            ->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('module/controller/action'));

        $this->_requestMock
            ->expects($this->never())
            ->method('setModuleName');

        $this->_requestMock
            ->expects($this->never())
            ->method('setControllerName');

        $this->_requestMock
            ->expects($this->never())
            ->method('setActionName');

        $this->assertEquals(false, $this->_model->process($this->_requestMock));
    }
}
