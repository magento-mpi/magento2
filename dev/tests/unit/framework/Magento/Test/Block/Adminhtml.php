<?php
/**
 * Framework for testing Block_Adminhtml code
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * Number of fields is necessary because of the number of fields used by multiple layers
 * of parent classes.
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Magento_Test_Block_Adminhtml extends PHPUnit_Framework_TestCase
{
    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected $_designMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected $_sessionMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected  $_translatorMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected $_layoutMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected $_requestMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected $_messagesMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected $_urlMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected $_eventManagerMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected $_controllerMock;

    /** @var  Magento_Backend_Block_Template_Context */
    protected  $_context;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected $_dirMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected $_loggerMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected $_filesystemMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected $_cacheMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected $_storeConfigMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    protected $_helperFactoryMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject|Magento_Core_Model_StoreManager */
    protected $_storeManagerMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject|Magento_Core_Model_LocaleInterface */
    protected $_localeMock;

    protected function setUp()
    {
        // These mocks are accessed via context
        $this->_designMock         = $this->_makeMock('Magento_Core_Model_View_DesignInterface');
        $this->_sessionMock         = $this->_makeMock('Magento_Core_Model_Session');
        $this->_translatorMock      = $this->_makeMock('Magento_Core_Model_Translate');
        $this->_layoutMock          = $this->_makeMock('Magento_Core_Model_Layout');
        $this->_requestMock         = $this->_makeMock('Magento_Core_Controller_Request_Http');
        $this->_messagesMock        = $this->_makeMock('Magento_Core_Block_Messages');
        $this->_urlMock             = $this->_makeMock('Magento_Core_Model_UrlInterface');
        $this->_eventManagerMock    = $this->_makeMock('Magento_Core_Model_Event_Manager');
        $this->_controllerMock      = $this->_makeMock('Magento_Core_Controller_Varien_Front');
        $this->_dirMock             = $this->_makeMock('Magento_Core_Model_Dir');
        $this->_loggerMock          = $this->_makeMock('Magento_Core_Model_Logger');
        $this->_filesystemMock      = $this->_makeMock('Magento_Filesystem');
        $this->_cacheMock           = $this->_makeMock('Magento_Core_Model_CacheInterface');
        $this->_storeConfigMock     = $this->_makeMock('Magento_Core_Model_Store_Config');
        $this->_storeManagerMock    = $this->_makeMock('Magento_Core_Model_StoreManager');
        $this->_helperFactoryMock   = $this->_makeMock('Magento_Core_Model_Factory_Helper');
        $viewUrlMock                = $this->_makeMock('Magento_Core_Model_View_Url');
        $viewConfigMock             = $this->_makeMock('Magento_Core_Model_View_Config');
        $viewFileSystemMock         = $this->_makeMock('Magento_Core_Model_View_FileSystem');
        $templateFactoryMock        = $this->_makeMock('Magento_Core_Model_TemplateEngine_Factory');
        $authorizationMock          = $this->_makeMock('Magento_AuthorizationInterface');
        $cacheStateMock             = $this->_makeMock('Magento_Core_Model_Cache_StateInterface');
        $appMock                    = $this->_makeMock('Magento_Core_Model_App');
        $backendSessionMock         = $this->_makeMock('Magento_Backend_Model_Session');
        $this->_localeMock          = $this->_makeMock('Magento_Core_Model_LocaleInterface');

        $this->_translatorMock
            ->expects($this->any())
            ->method('translate')
            ->will($this->returnCallback(array($this, 'translateCallback')));

        $this->_context = new Magento_Backend_Block_Template_Context(
            $this->_storeManagerMock,
            $this->_requestMock,
            $this->_layoutMock,
            $this->_eventManagerMock,
            $this->_urlMock,
            $this->_translatorMock,
            $this->_cacheMock,
            $this->_designMock,
            $this->_sessionMock,
            $this->_storeConfigMock,
            $this->_controllerMock,
            $this->_helperFactoryMock,
            $viewUrlMock,
            $viewConfigMock,
            $cacheStateMock,
            $this->_dirMock,
            $this->_loggerMock,
            $this->_filesystemMock,
            $viewFileSystemMock,
            $templateFactoryMock,
            $authorizationMock,
            $appMock
            $backendSessionMock,
            $this->_localeMock
        );
    }

    /**
     * Generates a mocked object
     *
     * @param string $className
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _makeMock($className)
    {
        return $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Sets up a stubbed method with specified behavior and expectations
     *
     * @param PHPUnit_Framework_MockObject_MockObject                       $object
     * @param string                                                        $stubName
     * @param mixed                                                         $return
     * @param PHPUnit_Framework_MockObject_Matcher_InvokedCount|null        $expects
     *
     * @return PHPUnit_Framework_MockObject_Builder_InvocationMocker
     */
    protected function _setStub(
        PHPUnit_Framework_MockObject_MockObject $object,
        $stubName,
        $return = null,
        $expects = null
    ) {
        $expects = isset($expects) ? $expects : $this->any();
        $return = isset($return) ? $this->returnValue($return) : $this->returnSelf();

        return $object->expects($expects)
            ->method($stubName)
            ->will($return);
    }

    /**
     * Return the English text passed into the __() translate method
     *
     * @param $args
     * @return mixed
     */
    public function translateCallback($args)
    {
        return $args[0]->getText();
    }
}
