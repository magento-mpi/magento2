<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Controller;

class NorouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Controller\Noroute
     */
    protected $_controller;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_viewMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_statusMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_requestMock = $this->getMock('Magento\Framework\App\Request\Http', array(), array(), '', false);
        $this->_viewMock = $this->getMock('\Magento\Framework\App\ViewInterface');
        $this->_statusMock = $this->getMock('Magento\Object', array('getLoaded'), array(), '', false);
        $this->_controller = $helper->getObject(
            'Magento\Core\Controller\Noroute',
            array('request' => $this->_requestMock, 'view' => $this->_viewMock)
        );
    }

    public function testIndexActionWhenStatusNotLoaded()
    {
        $this->_requestMock->expects(
            $this->once()
        )->method(
            'getParam'
        )->with(
            '__status__'
        )->will(
            $this->returnValue($this->_statusMock)
        );
        $this->_statusMock->expects($this->any())->method('getLoaded')->will($this->returnValue(false));
        $this->_viewMock->expects($this->once())->method('loadLayout')->with(array('default', 'noroute'));
        $this->_viewMock->expects($this->once())->method('renderLayout');
        $this->_controller->indexAction();
    }

    public function testIndexActionWhenStatusLoaded()
    {
        $this->_requestMock->expects(
            $this->once()
        )->method(
            'getParam'
        )->with(
            '__status__'
        )->will(
            $this->returnValue($this->_statusMock)
        );
        $this->_statusMock->expects($this->any())->method('getLoaded')->will($this->returnValue(true));
        $this->_statusMock->expects($this->any())->method('getForwarded')->will($this->returnValue(false));
        $this->_viewMock->expects($this->never())->method('loadLayout');
        $this->_requestMock->expects(
            $this->once()
        )->method(
            'setActionName'
        )->will(
            $this->returnValue($this->_requestMock)
        );
        $this->_controller->indexAction();
    }

    public function testIndexActionWhenStatusNotInstanceofMagentoObject()
    {
        $this->_requestMock->expects(
            $this->once()
        )->method(
            'getParam'
        )->with(
            '__status__'
        )->will(
            $this->returnValue('string')
        );
        $this->_controller->indexAction();
    }
}
