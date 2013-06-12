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

require 'Mage/DesignEditor/controllers/Adminhtml/System/Design/EditorController.php';
/**
 * Test backend controller for the design editor
 */
class Mage_DesignEditor_Controller_Adminhtml_System_Design_EditorControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_DesignEditor_Adminhtml_System_Design_EditorController
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');

        $request = $this->getMock('Mage_Core_Controller_Request_Http');
        $request->expects($this->any())->method('setActionName')->will($this->returnSelf());

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

        /** @var $layoutMock Mage_Core_Model_Layout|PHPUnit_Framework_MockObject_MockObject */
        $layoutMock = $this->getMock('Mage_Core_Model_Layout',
            array('getBlock', 'getUpdate', 'addHandle', 'load', 'generateXml', 'getNode',
                  'generateElements', 'getMessagesBlock'),
            array(), '', false);
        /** @var $layoutMock Mage_Core_Model_Layout */
        $layoutMock->expects($this->any())->method('generateXml')->will($this->returnSelf());
        $layoutMock->expects($this->any())->method('getNode')
            ->will($this->returnValue(new Varien_Simplexml_Element('<root />')));
        $blockMessage = $this->getMock('Mage_Core_Block_Messages',
            array('addMessages', 'setEscapeMessageFlag', 'addStorageType'), array(), '', false);
        $layoutMock->expects($this->any())->method('getMessagesBlock')->will($this->returnValue($blockMessage));

        $blockMock = $this->getMock('Mage_Core_Block_Template', array('setActive', 'getMenuModel', 'getParentItems'),
            array(), '', false);
        $blockMock->expects($this->any())->method('getMenuModel')->will($this->returnSelf());
        $blockMock->expects($this->any())->method('getParentItems')->will($this->returnValue(array()));

        $layoutMock->expects($this->any())->method('getBlock')->will($this->returnValue($blockMock));
        $layoutMock->expects($this->any())->method('getUpdate')->will($this->returnSelf());

        $layoutFactory = $this->getMock('Mage_Core_Model_Layout_Factory', array('createLayout'), array(), '', false);
        $layoutFactory->expects($this->any())->method('createLayout')->will($this->returnValue($layoutMock));


        $constructArguments = $objectManagerHelper->getConstructArguments(
            'Mage_DesignEditor_Adminhtml_System_Design_EditorController',
            array(
                'request' => $request,
                'objectManager' => $this->_objectManagerMock,
                'layoutFactory' => $layoutFactory,
                'invokeArgs' => array(
                    'helper' => $this->getMock('Mage_Backend_Helper_Data', array(), array(), '', false),
                    'session'=> $this->getMock('Mage_Backend_Model_Session', array(), array(), '', false),
            ))
        );

        $this->_model = $objectManagerHelper
            ->getObject('Mage_DesignEditor_Adminhtml_System_Design_EditorController', $constructArguments);
    }

    /**
     * Return mocked theme service model
     *
     * @param  bool $hasCustomizedThemes
     * @param string $action
     * @return Mage_Core_Model_Theme_Service
     */
    protected function _getThemeService($hasCustomizedThemes, $action = '')
    {
        $themeService = $this->getMock('Mage_Core_Model_Theme_Service',
            array('isCustomizationsExist'), array(), '', false);


        $themeService->expects($this->at(0))
            ->method('isCustomizationsExist')
            ->will($this->returnValue($hasCustomizedThemes));

        if ($hasCustomizedThemes xor $action !== 'index') {
            $themeService->expects($this->at(1))
                ->method('isCustomizationsExist')
                ->will($this->returnValue(false));
        }
        return $themeService;
    }

    /**
     * @covers Mage_DesignEditor_Adminhtml_System_Design_EditorController::indexAction
     * @dataProvider indexActionDataProvider
     */
    public function testIndexAction($hasCustomizedThemes)
    {
        $this->_objectManagerMock->expects($this->any())->method('get')
            ->will($this->returnValueMap($this->_getObjectManagerMap($hasCustomizedThemes, 'index')));
        $this->assertNull($this->_model->indexAction());
    }

    /**
     * @return array
     */
    public function indexActionDataProvider()
    {
        return array(
            array(true),
            array(false)
        );
    }

    /**
     * @covers Mage_DesignEditor_Adminhtml_System_Design_EditorController::firstEntranceAction
     * @dataProvider firstEntranceActionDataProvider
     */
    public function testFirstEntranceAction($hasCustomizedThemes)
    {
        $this->_objectManagerMock->expects($this->any())->method('get')
            ->will($this->returnValueMap($this->_getObjectManagerMap($hasCustomizedThemes)));
        $this->assertNull($this->_model->firstEntranceAction());
    }

    /**
     * @return array
     */
    public function firstEntranceActionDataProvider()
    {
        return array(
            array(true),
            array(false)
        );
    }

    /**
     * @param bool $hasCustomizedThemes
     * @param string $action
     * @return array
     */
    protected function _getObjectManagerMap($hasCustomizedThemes, $action = '')
    {
        $translate = $this->getMock('Mage_Core_Model_Translate', array(), array(), '', false);
        $translate->expects($this->any())->method('translate')
            ->will($this->returnSelf());

        $storeManager = $this->getMock('Mage_Core_Model_StoreManager',
            array('getStore', 'getBaseUrl'), array(), '', false);
        $storeManager->expects($this->any())->method('getStore')
            ->will($this->returnSelf());

        $eventManager = $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false);
        $configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $authMock = $this->getMock('Mage_Core_Model_Authorization', array('filterAclNodes'), array(), '', false);
        $authMock->expects($this->any())->method('filterAclNodes')->will($this->returnSelf());
        $backendSession = $this->getMock('Mage_Backend_Model_Session', array('getMessages', 'getEscapeMessages'),
            array(), '', false);
        $backendSession->expects($this->any())->method('getMessages')->will(
            $this->returnValue($this->getMock('Mage_Core_Model_Message_Collection', array(), array(), '', false))
        );

        $inlineMock = $this->getMock('Mage_Core_Model_Translate_Inline', array(), array(), '', false);

        return array(
            array('Mage_Core_Model_Theme_Service', $this->_getThemeService($hasCustomizedThemes, $action)),
            array('Mage_Core_Model_Translate', $translate),
            array('Mage_Core_Model_Config', $configMock),
            array('Mage_Core_Model_Event_Manager', $eventManager),
            array('Mage_Core_Model_StoreManager', $storeManager),
            array('Mage_Core_Model_Authorization', $authMock),
            array('Mage_Backend_Model_Session', $backendSession),
            array('Mage_Core_Model_Translate_Inline', $inlineMock),
        );
    }
}
