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
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_observerMock;

    /**
     * @var Saas_Backend_Model_Cache_Observer
     */
    protected $_modelCacheObserver;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), null, false);
        $this->_observerMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), null, false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_modelCacheObserver = $objectManagerHelper->getObject('Saas_Backend_Model_Cache_Observer', array(
            'request' => $this->_requestMock,
        ));
    }

    public function testDisabledAdminhtmlCacheController()
    {
        $this->_requestMock->expects($this->once())->method('getControllerName')->will($this->returnValue('cache'));
        $this->_requestMock->expects($this->once())->method('getControllerModule')
            ->will($this->returnValue('Mage_Adminhtml'));
        $this->_requestMock->expects($this->once())->method('setRouteName')->with('noRoute');

        $this->_modelCacheObserver->disableAdminhtmlCacheController($this->_observerMock);
    }

    /**
     * @param string $controller
     * @param string $module
     * @dataProvider dataProviderForNotDisabledAdminhtmlCacheControllerWrongModule
     */
    public function testNotDisabledAdminhtmlCacheController($controller, $module)
    {
        $this->_requestMock->expects($this->any())->method('getControllerName')->will($this->returnValue($controller));
        $this->_requestMock->expects($this->any())->method('getControllerModule')->will($this->returnValue($module));
        $this->_requestMock->expects($this->never())->method('setRouteName');

        $this->_modelCacheObserver->disableAdminhtmlCacheController($this->_observerMock);
    }

    /**
     * @return array
     */
    public function dataProviderForNotDisabledAdminhtmlCacheControllerWrongModule()
    {
        return array(
            array('index', 'Mage_Adminhtml'),
            array('cache', 'Mage_Checkout'),
            array('unknown', 'unknown')
        );
    }
}
