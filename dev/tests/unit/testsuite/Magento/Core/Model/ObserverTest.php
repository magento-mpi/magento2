<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheFrontendMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_frontendPoolMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_themeCustomization;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_assetFactory;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_assetsMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var Magento_Core_Model_Observer
     */
    protected $_model;

    protected function setUp()
    {
        $this->_cacheFrontendMock = $this->getMockForAbstractClass('Magento_Cache_FrontendInterface');

        $this->_frontendPoolMock = $this->getMock(
            'Magento_Core_Model_Cache_Frontend_Pool',
            array(),
            array(),
            '',
            false
        );
        $this->_frontendPoolMock
            ->expects($this->any())
            ->method('valid')
            ->will($this->onConsecutiveCalls(true, false))
        ;
        $this->_frontendPoolMock
            ->expects($this->any())
            ->method('current')
            ->will($this->returnValue($this->_cacheFrontendMock))
        ;

        $this->_themeCustomization = $this->getMock(
            'Magento_Core_Model_Theme_Customization',
            array(),
            array(),
            '',
            false
        );
        $themeMock = $this->getMock('Magento_Core_Model_Theme', array('getCustomization'), array(), '', false);
        $themeMock->expects($this->any())->method('getCustomization')
            ->will($this->returnValue($this->_themeCustomization));

        $designMock = $this->getMock('Magento_Core_Model_View_DesignInterface');
        $designMock
            ->expects($this->any())
            ->method('getDesignTheme')
            ->will($this->returnValue($themeMock))
        ;

        $this->_assetsMock = $this->getMock('Magento_Core_Model_Page_Asset_Collection');
        $this->_configMock = $this->getMock('Magento_Core_Model_ConfigInterface',
            array(), array(), '', false, false);

        $this->_assetFactory = $this->getMock('Magento_Core_Model_Page_Asset_PublicFileFactory',
            array('create'), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject(
            'Magento_Core_Model_Observer',
            array(
                'cacheFrontendPool' => $this->_frontendPoolMock,
                'design'            => $designMock,
                'page'              => new Magento_Core_Model_Page($this->_assetsMock),
                'config'            => $this->_configMock,
                'assetFileFactory'  => $this->_assetFactory
            )
        );
    }

    protected function tearDown()
    {
        $this->_cacheFrontendMock = null;
        $this->_frontendPoolMock = null;
        $this->_themeCustomization = null;
        $this->_assetsMock = null;
        $this->_configMock = null;
        $this->_model = null;
    }
    
    public function testCleanCache()
    {
        $cacheBackendMock = $this->getMockForAbstractClass('Zend_Cache_Backend_Interface');
        $cacheBackendMock
            ->expects($this->once())
            ->method('clean')
            ->with(Zend_Cache::CLEANING_MODE_OLD, array())
        ;
        $this->_cacheFrontendMock
            ->expects($this->once())
            ->method('getBackend')
            ->will($this->returnValue($cacheBackendMock))
        ;
        $cronScheduleMock = $this->getMock('Magento_Cron_Model_Schedule', array(), array(), '', false);
        $this->_model->cleanCache($cronScheduleMock);
    }

    public function testApplyThemeCustomization()
    {
        $asset = new Magento_Core_Model_Page_Asset_Remote('http://127.0.0.1/test.css');
        $file = $this->getMock('Magento_Core_Model_Theme_File', array(), array(), '', false);
        $fileService = $this->getMock('Magento_Core_Model_Theme_Customization_File_Css', array(), array(), '', false);

        $fileService->expects($this->atLeastOnce())->method('getContentType')->will($this->returnValue('css'));

        $file->expects($this->any())->method('getCustomizationService')->will($this->returnValue($fileService));
        $file->expects($this->atLeastOnce())->method('getFullPath')->will($this->returnValue('test.css'));

        $this->_assetFactory->expects($this->any())
            ->method('create')
            ->with(array('file' => 'test.css', 'contentType' => 'css'))
            ->will($this->returnValue($asset));

        $this->_themeCustomization->expects($this->once())->method('getFiles')->will($this->returnValue(array($file)));

        $this->_assetsMock->expects($this->once())->method('add')->with($this->anything(), $asset);

        $observer = new Magento_Event_Observer;
        $this->_model->applyThemeCustomization($observer);
    }

    public function testProcessReinitConfig()
    {
        $observer = new Magento_Event_Observer;
        $this->_configMock->expects($this->once())->method('reinit');
        $this->_model->processReinitConfig($observer);
    }
}
