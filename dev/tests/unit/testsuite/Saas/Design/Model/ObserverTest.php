<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Design_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_saasHelperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventObserverMock;

    /**
     * @var Saas_Design_Model_Observer
     */
    protected $_modelObserver;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http');
        $this->_saasHelperMock = $this->getMock('Saas_Saas_Helper_Data', array(), array(), '', false);
        $this->_eventObserverMock = $this->getMock('Varien_Event_Observer');

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_modelObserver = $objectManagerHelper->getObject('Saas_Design_Model_Observer', array(
            'request' => $this->_requestMock,
            'saasHelper' => $this->_saasHelperMock,
        ));
    }

    /**
     * @param string $action
     * @dataProvider dataProviderForDisabledThemeActions
     */
    public function testLimitThemesFunctionality($action)
    {
        $this->_requestMock->expects($this->any())->method('getControllerModule')
            ->will($this->returnValue('Mage_Theme_Adminhtml'));
        $this->_requestMock->expects($this->any())->method('getControllerName')
            ->will($this->returnValue('system_design_theme'));
        $this->_requestMock->expects($this->any())->method('getActionName')
            ->will($this->returnValue($action));
        $this->_saasHelperMock->expects($this->once())->method('customizeNoRoutForward');

        $this->_modelObserver->limitThemesFunctionality($this->_eventObserverMock);
    }

    /**
     * @param string $module
     * @param string $controller
     * @param string $action
     * @dataProvider dataProviderForNonLimitThemesFunctionality
     */
    public function testNonLimitThemesFunctionality($module, $controller, $action)
    {
        $this->_requestMock->expects($this->any())->method('getControllerModule')->will($this->returnValue($module));
        $this->_requestMock->expects($this->any())->method('getControllerName')->will($this->returnValue($controller));
        $this->_requestMock->expects($this->any())->method('getActionName')->will($this->returnValue($action));
        $this->_saasHelperMock->expects($this->never())->method('customizeNoRoutForward');

        $this->_modelObserver->limitThemesFunctionality($this->_eventObserverMock);
    }

    public function testDisableScheduleFunctionality()
    {
        $this->_requestMock->expects($this->any())->method('getControllerModule')
            ->will($this->returnValue('Mage_Adminhtml'));
        $this->_requestMock->expects($this->any())->method('getControllerName')
            ->will($this->returnValue('system_design'));
        $this->_saasHelperMock->expects($this->once())->method('customizeNoRoutForward');

        $this->_modelObserver->disableScheduleFunctionality($this->_eventObserverMock);
    }

    /**
     * @param string $module
     * @param string $controller
     * @dataProvider dataProviderForNonDisableScheduleFunctionality
     */
    public function testNonDisableScheduleFunctionality($module, $controller)
    {
        $this->_requestMock->expects($this->any())->method('getControllerModule')->will($this->returnValue($module));
        $this->_requestMock->expects($this->any())->method('getControllerName')->will($this->returnValue($controller));
        $this->_saasHelperMock->expects($this->never())->method('customizeNoRoutForward');

        $this->_modelObserver->disableScheduleFunctionality($this->_eventObserverMock);
    }

    /**
     * @return array
     */
    public function dataProviderForDisabledThemeActions()
    {
        return array(
            array('index'),
            array('new'),
            array('grid'),
            array('edit'),
        );
    }

    /**
     * @return array
     */
    public function dataProviderForNonLimitThemesFunctionality()
    {
        return array(
            array('Mage_Theme_Adminhtml', 'unknown', 'unknown'),
            array('Mage_Theme_Adminhtml', 'system_design_theme', 'unknown'),
            array('unknown', 'system_design_theme', 'index'),
            array('unknown', 'unknown', 'new'),
            array('unknown', 'unknown', 'unknown'),
        );
    }

    /**
     * @return array
     */
    public function dataProviderForNonDisableScheduleFunctionality()
    {
        return array(
            array('Mage_Adminhtml', 'unknown'),
            array('unknown', 'system_design'),
            array('unknown', 'unknown'),
        );
    }
}
