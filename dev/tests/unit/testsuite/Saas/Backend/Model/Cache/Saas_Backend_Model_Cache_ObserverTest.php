<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

class Saas_Backend_Model_Cache_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var Saas_Backend_Model_Cache_Observer
     */
    protected $_observer;

    protected function setUp()
    {
        $this->_requestMock = $this->getMockBuilder('Mage_Core_Controller_Request_Http')
            ->disableOriginalConstructor()
            ->getMock();

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_observer = $objectManagerHelper->getObject('Saas_Backend_Model_Cache_Observer', array(
            'request' => $this->_requestMock,
        ));
    }

    public function testDisabledAdminhtmlCacheController()
    {
        $this->_requestMock->expects($this->once())->method('getControllerName')->will($this->returnValue('cache'));
        $this->_requestMock->expects($this->once())->method('getControllerModule')
            ->will($this->returnValue('Mage_Adminhtml'));
        $this->_requestMock->expects($this->once())->method('setRouteName')->with('noRoute');

        $this->_observer->disableAdminhtmlCacheController();
    }

    public function testNotDisabledAdminhtmlCacheController()
    {
        $this->_requestMock->expects($this->once())->method('getControllerName')->will($this->returnValue('index'));
        $this->_requestMock->expects($this->never())->method('setRouteName');

        $this->_observer->disableAdminhtmlCacheController();
    }

    public function testNotDisabledAdminhtmlCacheControllerWrongControllerModule()
    {
        $this->_requestMock->expects($this->once())->method('getControllerName')->will($this->returnValue('cache'));
        $this->_requestMock->expects($this->once())->method('getControllerModule')
            ->will($this->returnValue('Mage_Checkout'));
        $this->_requestMock->expects($this->never())->method('setRouteName');

        $this->_observer->disableAdminhtmlCacheController();
    }
}
