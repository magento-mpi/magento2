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
     * @var Mage_DesignEditor_Model_State
     */
    protected $_model;

    /**
     * @var Mage_Backend_Model_Session
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

    /**
     * @var Mage_Core_Model_Cache
     */
    protected $_cacheManager;

    /**
     * @var Mage_DesignEditor_Helper_Data
     */
    protected $_dataHelper;

    /**
     * @var Magento_ObjectManager_Zend
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_cacheTypes = array('type1', 'type2');

    public function setUp()
    {
        $this->_backendSession = $this->getMock('Mage_Backend_Model_Session', array('setData'),
            array(), '', false
        );
        $this->_layoutFactory = $this->getMock('Mage_Core_Model_Layout_Factory', array('createLayout'),
            array(), '', false
        );
        $this->_urlModelFactory = $this->getMock('Mage_DesignEditor_Model_Url_Factory', array('replaceClassName'),
            array(), '', false
        );
        $this->_cacheManager = $this->getMock('Mage_Core_Model_Cache', array('canUse', 'banUse'),
            array(), '', false
        );
        $this->_dataHelper = $this->getMock('Mage_DesignEditor_Helper_Data', array('getDisabledCacheTypes'),
            array(), '', false
        );
        $this->_objectManager = $this->getMock('Magento_ObjectManager_Zend', array('addAlias'),
            array(), '', false
        );
        $this->_model = new Mage_DesignEditor_Model_State(
            $this->_backendSession,
            $this->_layoutFactory,
            $this->_urlModelFactory,
            $this->_cacheManager,
            $this->_dataHelper,
            $this->_objectManager
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

        $this->_urlModelFactory->expects($this->once())
            ->method('replaceClassName')
            ->with(self::URL_MODEL_DESIGN_MODE_CLASS_NAME);

        $this->_layoutFactory->expects($this->once())
            ->method('createLayout')
            ->with(array('area' => self::AREA_CODE), self::LAYOUT_DESIGN_CLASS_NAME);

        $this->_objectManager->expects($this->once())
            ->method('addAlias')
            ->with(self::LAYOUT_UPDATE_RESOURCE_MODEL_CORE_CLASS_NAME,
            self::LAYOUT_UPDATE_RESOURCE_MODEL_VDE_CLASS_NAME);

        $this->_model->update(self::AREA_CODE, $request, $controller);
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
            ->method('addAlias')
            ->with(self::LAYOUT_UPDATE_RESOURCE_MODEL_CORE_CLASS_NAME,
            self::LAYOUT_UPDATE_RESOURCE_MODEL_VDE_CLASS_NAME);

        $this->_model->update(self::AREA_CODE, $request, $controller);
    }
}
