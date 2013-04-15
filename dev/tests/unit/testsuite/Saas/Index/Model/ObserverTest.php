<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Index_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
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
    protected $_modelUrlMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventObserverMock;

    /**
     * @var Saas_Index_Model_Observer
     */
    protected $_modelObserver;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http');
        $this->_responseMock = $this->getMock('Mage_Core_Controller_Response_Http');
        $this->_modelUrlMock = $this->getMock('Mage_Backend_Model_Url', array('getUrl'), array(), '', false);
        $this->_eventObserverMock = $this->getMock('Varien_Event_Observer');

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_modelObserver = $objectManagerHelper->getObject('Saas_Index_Model_Observer', array(
            'request' => $this->_requestMock,
            'response' => $this->_responseMock,
            'modelUrl' => $this->_modelUrlMock,
        ));
    }

    public function testRedefineIndexWithListForward()
    {
        $this->_requestMock->expects($this->once())->method('getControllerModule')
            ->will($this->returnValue('Mage_Index_Adminhtml'));
        $this->_requestMock->expects($this->once())->method('getControllerName')
            ->will($this->returnValue('process'));
        $this->_requestMock->expects($this->once())->method('getActionName')
            ->will($this->returnValue('list'));

        $this->_forward('index');
        $this->_modelObserver->redefineIndex($this->_eventObserverMock);
    }

    public function testRedefineIndexWithNoRouteForward()
    {
        $action = 'unknown';
        $this->_requestMock->expects($this->once())->method('getControllerModule')
            ->will($this->returnValue('Mage_Index_Adminhtml'));
        $this->_requestMock->expects($this->once())->method('getControllerName')
            ->will($this->returnValue('process'));
        $this->_requestMock->expects($this->once())->method('getActionName')
            ->will($this->returnValue($action));

        $this->_forward('noroute');
        $this->_modelObserver->redefineIndex($this->_eventObserverMock);
    }

    protected function _forward($action)
    {
        $this->_requestMock->expects($this->once())->method('initForward')->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setControllerName')->with('saas_index')
            ->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setModuleName')->with('admin')->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setActionName')->with($action)->will($this->returnSelf());
        $this->_requestMock->expects($this->once())->method('setDispatched')->with(false);
    }
}
