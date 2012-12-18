<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_DesignEditor_Model_StateTest extends PHPUnit_Framework_TestCase
{
    /*
     * Test area code
     */
    const AREA_CODE = 'front';

    /**
     * @var Mage_DesignEditor_Model_State
     */
    protected $_model;

    /**
     * @var Mage_Backend_Model_Auth_Session
     */
    protected $_backendSession;

    /**
     * @var Mage_Core_Model_Layout_Factory
     */
    protected $_layoutFactory;

    /**
     * @var Mage_DesignEditor_Model_Url_Factory
     */
    protected $_urlModelFactory;

    public function setUp()
    {
        $this->_backendSession = $this->getMock('Mage_Backend_Model_Auth_Session', array('setData'),
            array(), '', false);
        $this->_layoutFactory = $this->getMock('Mage_Core_Model_Layout_Factory', array('createLayout'),
            array(), '', false);
        $this->_urlModelFactory = $this->getMock('Mage_DesignEditor_Model_Url_Factory', array('replaceClassName'),
            array(), '', false);
        $this->_model = new Mage_DesignEditor_Model_State(
            $this->_backendSession,
            $this->_layoutFactory,
            $this->_urlModelFactory
        );
    }

    public function testConstruct()
    {
        $this->assertAttributeEquals($this->_backendSession, '_backendSession', $this->_model);
        $this->assertAttributeEquals($this->_layoutFactory, '_layoutFactory', $this->_model);
        $this->assertAttributeEquals($this->_urlModelFactory, '_urlModelFactory', $this->_model);
    }

    public function testUpdateDesignMode()
    {
        $request = $this->getMock('Mage_Core_Controller_Request_Http', array('getParam'),
            array(), '', false);

        $controller = $this->getMock('Mage_Adminhtml_Controller_Action', array('getFullActionName'), array(),
            '', false);

        $request->expects($this->once())
            ->method('getParam')
            ->with('handle', '')
            ->will($this->returnValue('default'));

        $this->_backendSession->expects($this->once())
            ->method('setData')
            ->with('vde_current_mode', 0);

        $this->_urlModelFactory->expects($this->once())
            ->method('replaceClassName')
            ->with('Mage_DesignEditor_Model_Url_DesignMode');

        $this->_layoutFactory->expects($this->once())
            ->method('createLayout')
            ->with(array('area' => self::AREA_CODE), 'Mage_DesignEditor_Model_Layout');

        $this->_model->update(self::AREA_CODE, $request, $controller);
    }

    public function testUpdateNavigationMode()
    {
        $request = $this->getMock('Mage_Core_Controller_Request_Http', array('getParam', 'isAjax', 'getPathInfo'),
            array(), '', false);

        $controller = $this->getMock('Mage_Adminhtml_Controller_Action', array('getFullActionName'), array(),
            '', false);

        $request->expects($this->once())
            ->method('getParam')
            ->with('handle', '')
            ->will($this->returnValue(''));

        $request->expects($this->once())
            ->method('isAjax')
            ->will($this->returnValue(false));

        $controller->expects($this->once())
            ->method('getFullActionName')
            ->will($this->returnValue('index'));

        $this->_backendSession->expects($this->at(0))
            ->method('setData')
            ->with('vde_current_handle', 'index');

        $request->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('/'));

        $this->_backendSession->expects($this->at(1))
            ->method('setData')
            ->with('vde_current_url', '/');

        $this->_backendSession->expects($this->at(2))
            ->method('setData')
            ->with('vde_current_mode', '1');

        $this->_urlModelFactory->expects($this->once())
            ->method('replaceClassName')
            ->with('Mage_DesignEditor_Model_Url_NavigationMode');

        $this->_layoutFactory->expects($this->once())
            ->method('createLayout')
            ->with(array('area' => self::AREA_CODE), 'Mage_DesignEditor_Model_Layout');

        $this->_model->update(self::AREA_CODE, $request, $controller);
    }
}
