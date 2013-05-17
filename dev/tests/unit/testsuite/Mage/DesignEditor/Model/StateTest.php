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
    /**#@+
     * Name of layout classes that will be used as main layout
     */
    const LAYOUT_DESIGN_CLASS_NAME     = 'Mage_DesignEditor_Model_Layout';
    const LAYOUT_NAVIGATION_CLASS_NAME = 'Mage_Core_Model_Layout';
    /**#@-*/

    /**#@+
     * Url model classes that will be used instead of Mage_Core_Model_Url in different vde modes
     */
    const URL_MODEL_NAVIGATION_MODE_CLASS_NAME = 'Mage_DesignEditor_Model_Url_NavigationMode';
    const URL_MODEL_DESIGN_MODE_CLASS_NAME     = 'Mage_DesignEditor_Model_Url_DesignMode';
    /**#@-*/

    /**#@+
     * Layout update resource models
     */
    const LAYOUT_UPDATE_RESOURCE_MODEL_CORE_CLASS_NAME = 'Mage_Core_Model_Resource_Layout_Update';
    const LAYOUT_UPDATE_RESOURCE_MODEL_VDE_CLASS_NAME  = 'Mage_DesignEditor_Model_Resource_Layout_Update';
    /**#@-*/

    /**#@+
     * Import behaviors
     */
    const MODE_DESIGN     = 'design';
    const MODE_NAVIGATION = 'navigation';
    /**#@-*/

    /*
     * Test area code
     */
    const AREA_CODE = 'front';

    /**
     * Test theme id
     */
    const THEME_ID = 1;

    /**
     * @var Mage_DesignEditor_Model_State
     */
    protected $_model;

    /**
     * @var Mage_Backend_Model_Session|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_backendSession;

    /**
     * @var Mage_Core_Model_Layout_Factory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layoutFactory;

    /**
     * @var Mage_DesignEditor_Model_Url_Factory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_urlModelFactory;

    /**
     * @var Mage_Core_Model_Cache|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheManager;

    /**
     * @var Mage_DesignEditor_Helper_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dataHelper;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Model_App|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_application;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_themeContext;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_theme;

    /**
     * @var array
     */
    protected $_cacheTypes = array('type1', 'type2');

    public function setUp()
    {
        $this->_backendSession = $this->getMock('Mage_Backend_Model_Session', array('setData', 'getData', 'unsetData'),
            array(), '', false
        );
        $this->_layoutFactory = $this->getMock('Mage_Core_Model_Layout_Factory', array('createLayout'),
            array(), '', false
        );
        $this->_urlModelFactory = $this->getMock('Mage_DesignEditor_Model_Url_Factory', array('replaceClassName'),
            array(), '', false
        );
        $this->_cacheManager = $this->getMockBuilder('Mage_Core_Model_Cache')->disableOriginalConstructor()->getMock();
        $this->_dataHelper = $this->getMock('Mage_DesignEditor_Helper_Data', array('getDisabledCacheTypes'),
            array(), '', false
        );
        $this->_objectManager = $this->getMock('Magento_ObjectManager');
        $this->_application = $this->getMock('Mage_Core_Model_App', array('getStore', 'getConfig'),
            array(), '', false
        );
        $this->_theme = $this->getMock('Mage_Core_Model_Theme', array('getId'), array(), '', false);
        $this->_themeContext = $this->getMock('Mage_DesignEditor_Model_Theme_Context',
            array('getEditableThemeId', 'getVisibleTheme', 'reset'), array(), '', false);
        $this->_themeContext->expects($this->any())
            ->method('getVisibleTheme')
            ->will($this->returnValue($this->_theme));

        $this->_model = new Mage_DesignEditor_Model_State(
            $this->_backendSession,
            $this->_layoutFactory,
            $this->_urlModelFactory,
            $this->_cacheManager,
            $this->_dataHelper,
            $this->_objectManager,
            $this->_application,
            $this->_themeContext
        );
    }

    public function testConstruct()
    {
        $this->assertAttributeEquals($this->_backendSession, '_backendSession', $this->_model);
        $this->assertAttributeEquals($this->_layoutFactory, '_layoutFactory', $this->_model);
        $this->assertAttributeEquals($this->_urlModelFactory, '_urlModelFactory', $this->_model);
        $this->assertAttributeEquals($this->_cacheManager, '_cacheManager', $this->_model);
        $this->assertAttributeEquals($this->_dataHelper, '_dataHelper', $this->_model);
        $this->assertAttributeEquals($this->_objectManager, '_objectManager', $this->_model);
    }

    protected function _setAdditionalExpectations()
    {
        $this->_dataHelper->expects($this->once())
            ->method('getDisabledCacheTypes')
            ->will($this->returnValue($this->_cacheTypes));

        $this->_cacheManager->expects($this->at(0))
            ->method('canUse')
            ->with('type1')
            ->will($this->returnValue(true));
        $this->_cacheManager->expects($this->at(1))
            ->method('banUse')
            ->with('type1')
            ->will($this->returnSelf());

        $this->_cacheManager->expects($this->at(2))
            ->method('canUse')
            ->with('type2')
            ->will($this->returnValue(true));
        $this->_cacheManager->expects($this->at(3))
            ->method('banUse')
            ->with('type2')
            ->will($this->returnSelf());
    }

    public function testUpdateDesignMode()
    {
        $this->_setAdditionalExpectations();
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
            ->with('vde_current_mode', Mage_DesignEditor_Model_State::MODE_DESIGN);
        $this->_themeContext->expects($this->once())->method('getVisibleTheme');
        $this->_themeContext->expects($this->any())
            ->method('getEditableThemeId')
            ->will($this->returnValue(self::THEME_ID));
        $this->_theme->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(self::THEME_ID));

        $this->_urlModelFactory->expects($this->once())
            ->method('replaceClassName')
            ->with(self::URL_MODEL_DESIGN_MODE_CLASS_NAME);

        $this->_layoutFactory->expects($this->once())
            ->method('createLayout')
            ->with(array('area' => self::AREA_CODE), self::LAYOUT_DESIGN_CLASS_NAME);

        $this->_objectManager->expects($this->once())
            ->method('configure')
            ->with(array('preferences' => array(
                self::LAYOUT_UPDATE_RESOURCE_MODEL_CORE_CLASS_NAME => self::LAYOUT_UPDATE_RESOURCE_MODEL_VDE_CLASS_NAME
            )));
        
        $store = $this->getMock('Mage_Core_Model_Store', array('setConfig'), array(), '', false);
        $store->expects($this->once())
            ->method('setConfig')
            ->with(Mage_Core_Model_Design_PackageInterface::XML_PATH_THEME_ID, self::THEME_ID);

        $this->_application->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($store));

        $config = $this->getMock('Mage_Core_Model_Config', array('setNode'), array(), '', false);
        $config->expects($this->once())
            ->method('setNode')
            ->with('default/' . Mage_Core_Model_Design_PackageInterface::XML_PATH_THEME_ID, self::THEME_ID);

        $this->_application->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue($config));

        $this->_model->update(self::AREA_CODE, $request, $controller);
    }

    public function testReset()
    {
        $this->_backendSession->expects($this->any())
            ->method('unsetData')
            ->with($this->logicalOr(
                Mage_DesignEditor_Model_State::CURRENT_HANDLE_SESSION_KEY,
                Mage_DesignEditor_Model_State::CURRENT_MODE_SESSION_KEY,
                Mage_DesignEditor_Model_State::CURRENT_URL_SESSION_KEY
            ))
            ->will($this->returnValue($this->_backendSession));

        $this->_model->reset();
    }

    public function testUpdateNavigationMode()
    {
        $this->_setAdditionalExpectations();
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
            ->with('vde_current_mode', Mage_DesignEditor_Model_State::MODE_NAVIGATION);

        $this->_urlModelFactory->expects($this->once())
            ->method('replaceClassName')
            ->with(self::URL_MODEL_NAVIGATION_MODE_CLASS_NAME);

        $this->_layoutFactory->expects($this->once())
            ->method('createLayout')
            ->with(array('area' => self::AREA_CODE), self::LAYOUT_NAVIGATION_CLASS_NAME);

        $this->_objectManager->expects($this->once())
            ->method('configure')
            ->with(array('preferences' => array(
                self::LAYOUT_UPDATE_RESOURCE_MODEL_CORE_CLASS_NAME => self::LAYOUT_UPDATE_RESOURCE_MODEL_VDE_CLASS_NAME
            )));

        $this->_model->update(self::AREA_CODE, $request, $controller);
    }
}
