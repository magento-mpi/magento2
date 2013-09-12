<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test backend controller for the design editor
 */
class Magento_DesignEditor_Controller_Adminhtml_System_Design_EditorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_DesignEditor_Controller_Adminhtml_System_Design_Editor
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');

        $request = $this->getMock('Magento_Core_Controller_Request_Http');
        $request->expects($this->any())->method('setActionName')->will($this->returnSelf());

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);

        /** @var $layoutMock Magento_Core_Model_Layout|PHPUnit_Framework_MockObject_MockObject */
        $layoutMock = $this->getMock('Magento_Core_Model_Layout',
            array(
                'getBlock',
                'getUpdate',
                'addHandle',
                'load',
                'generateXml',
                'getNode',
                'generateElements',
                'getMessagesBlock'
            ),
            array(), '', false);
        /** @var $layoutMock Magento_Core_Model_Layout */
        $layoutMock->expects($this->any())->method('generateXml')->will($this->returnSelf());
        $layoutMock->expects($this->any())->method('getNode')
            ->will($this->returnValue(new Magento_Simplexml_Element('<root />')));
        $blockMessage = $this->getMock('Magento_Core_Block_Messages',
            array('addMessages', 'setEscapeMessageFlag', 'addStorageType'), array(), '', false);
        $layoutMock->expects($this->any())->method('getMessagesBlock')->will($this->returnValue($blockMessage));

        $blockMock = $this->getMock('Magento_Core_Block_Template', array('setActive', 'getMenuModel', 'getParentItems'),
            array(), '', false);
        $blockMock->expects($this->any())->method('getMenuModel')->will($this->returnSelf());
        $blockMock->expects($this->any())->method('getParentItems')->will($this->returnValue(array()));

        $layoutMock->expects($this->any())->method('getBlock')->will($this->returnValue($blockMock));
        $layoutMock->expects($this->any())->method('getUpdate')->will($this->returnSelf());

        $constructArguments = $objectManagerHelper->getConstructArguments(
            'Magento_DesignEditor_Controller_Adminhtml_System_Design_Editor',
            array(
                'request' => $request,
                'objectManager' => $this->_objectManagerMock,
                'layout' => $layoutMock,
                'invokeArgs' => array(
                    'helper' => $this->getMock('Magento_Backend_Helper_Data', array(), array(), '', false),
                    'session'=> $this->getMock('Magento_Backend_Model_Session', array(), array(), '', false),
            ))
        );

        $this->_model = $objectManagerHelper
            ->getObject('Magento_DesignEditor_Controller_Adminhtml_System_Design_Editor', $constructArguments);
    }

    /**
     * Return mocked theme collection factory model
     *
     * @param int $countCustomization
     * @return Magento_Core_Model_Resource_Theme_CollectionFactory
     */
    protected function _getThemeCollectionFactory($countCustomization)
    {
        $themeCollectionMock = $this->getMockBuilder('Magento_Core_Model_Resource_Theme_Collection')
            ->disableOriginalConstructor()
            ->setMethods(array('addTypeFilter', 'getSize'))
            ->getMock();

        $themeCollectionMock->expects($this->once())
            ->method('addTypeFilter')
            ->with(Magento_Core_Model_Theme::TYPE_VIRTUAL)
            ->will($this->returnValue($themeCollectionMock));

        $themeCollectionMock->expects($this->once())
            ->method('getSize')
            ->will($this->returnValue($countCustomization));

        /** @var Magento_Core_Model_Resource_Theme_CollectionFactory $collectionFactory */
        $collectionFactory = $this->getMock(
            'Magento_Core_Model_Resource_Theme_CollectionFactory', array('create'), array(), '', false
        );
        $collectionFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($themeCollectionMock));

        return $collectionFactory;
    }

    /**
     * @covers Magento_DesignEditor_Controller_Adminhtml_System_Design_Editor::indexAction
     * @dataProvider indexActionDataProvider
     */
    public function testIndexAction($countCustomization)
    {
        $this->_objectManagerMock->expects($this->any())->method('get')
            ->will($this->returnValueMap($this->_getObjectManagerMap($countCustomization, 'index')));
        $this->assertNull($this->_model->indexAction());
    }

    /**
     * @return array
     */
    public function indexActionDataProvider()
    {
        return array(
            array(4),
            array(0)
        );
    }

    /**
     * @covers Magento_DesignEditor_Controller_Adminhtml_System_Design_Editor::firstEntranceAction
     * @dataProvider firstEntranceActionDataProvider
     */
    public function testFirstEntranceAction($countCustomization)
    {
        $this->_objectManagerMock->expects($this->any())->method('get')
            ->will($this->returnValueMap($this->_getObjectManagerMap($countCustomization)));
        $this->assertNull($this->_model->firstEntranceAction());
    }

    /**
     * @return array
     */
    public function firstEntranceActionDataProvider()
    {
        return array(
            array(3),
            array(0)
        );
    }

    /**
     * @param int $countCustomization
     * @return array
     */
    protected function _getObjectManagerMap($countCustomization)
    {
        $translate = $this->getMock('Magento_Core_Model_Translate', array(), array(), '', false);
        $translate->expects($this->any())->method('translate')
            ->will($this->returnSelf());

        $storeManager = $this->getMock('Magento_Core_Model_StoreManager',
            array('getStore', 'getBaseUrl'), array(), '', false);
        $storeManager->expects($this->any())->method('getStore')
            ->will($this->returnSelf());

        $eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $configMock = $this->getMock('Magento_Core_Model_Config', array(), array(), '', false);
        $authMock = $this->getMock('Magento_AuthorizationInterface');
        $authMock->expects($this->any())->method('filterAclNodes')->will($this->returnSelf());
        $backendSession = $this->getMock('Magento_Backend_Model_Session', array('getMessages', 'getEscapeMessages'),
            array(), '', false);
        $backendSession->expects($this->any())->method('getMessages')->will(
            $this->returnValue($this->getMock('Magento_Core_Model_Message_Collection', array(), array(), '', false))
        );

        $inlineMock = $this->getMock('Magento_Core_Model_Translate_Inline', array(), array(), '', false);
        $aclFilterMock = $this->getMock('Magento_Core_Model_Layout_Filter_Acl', array(), array(), '', false);

        return array(
            array(
                'Magento_Core_Model_Resource_Theme_CollectionFactory',
                $this->_getThemeCollectionFactory($countCustomization)
            ),
            array('Magento_Core_Model_Translate', $translate),
            array('Magento_Core_Model_Config', $configMock),
            array('Magento_Core_Model_Event_Manager', $eventManager),
            array('Magento_Core_Model_StoreManager', $storeManager),
            array('Magento_AuthorizationInterface', $authMock),
            array('Magento_Backend_Model_Session', $backendSession),
            array('Magento_Core_Model_Translate_Inline', $inlineMock),
            array('Magento_Core_Model_Layout_Filter_Acl', $aclFilterMock),
        );
    }
}
