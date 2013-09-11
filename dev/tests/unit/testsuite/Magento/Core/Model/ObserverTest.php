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
     * @var \Magento\Core\Model\Observer
     */
    protected $_model;

    protected function setUp()
    {
        $this->_cacheFrontendMock = $this->getMockForAbstractClass('Magento\Cache\FrontendInterface');

        $this->_frontendPoolMock = $this->getMock(
            '\Magento\Core\Model\Cache\Frontend\Pool',
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
            '\Magento\Core\Model\Theme\Customization',
            array(),
            array(),
            '',
            false
        );
        $themeMock = $this->getMock('Magento\Core\Model\Theme', array('getCustomization'), array(), '', false);
        $themeMock->expects($this->any())->method('getCustomization')
            ->will($this->returnValue($this->_themeCustomization));

        $designMock = $this->getMock('Magento\Core\Model\View\DesignInterface');
        $designMock
            ->expects($this->any())
            ->method('getDesignTheme')
            ->will($this->returnValue($themeMock))
        ;

        $this->_assetsMock = $this->getMock('Magento\Core\Model\Page\Asset\Collection');
        $this->_configMock = $this->getMock('Magento\Core\Model\ConfigInterface',
            array(), array(), '', false, false);

        $this->_assetFactory = $this->getMock('Magento_Core_Model_Page_Asset_PublicFileFactory',
            array('create'), array(), '', false);

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject(
            '\Magento\Core\Model\Observer',
            array(
                'cacheFrontendPool' => $this->_frontendPoolMock,
                'design'            => $designMock,
                'page'              => new \Magento\Core\Model\Page($this->_assetsMock),
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
        $cronScheduleMock = $this->getMock('Magento\Cron\Model\Schedule', array(), array(), '', false);
        $this->_model->cleanCache($cronScheduleMock);
    }

    public function testApplyThemeCustomization()
    {
        $asset = new \Magento\Core\Model\Page\Asset\Remote('http://127.0.0.1/test.css');
        $file = $this->getMock('Magento\Core\Model\Theme\File', array(), array(), '', false);
        $fileService = $this->getMock('Magento\Core\Model\Theme\Customization\File\Css', array(), array(), '', false);

        $fileService->expects($this->atLeastOnce())->method('getContentType')->will($this->returnValue('css'));

        $file->expects($this->any())->method('getCustomizationService')->will($this->returnValue($fileService));
        $file->expects($this->atLeastOnce())->method('getFullPath')->will($this->returnValue('test.css'));

        $this->_assetFactory->expects($this->any())
            ->method('create')
            ->with(array('file' => 'test.css', 'contentType' => 'css'))
            ->will($this->returnValue($asset));

        $this->_themeCustomization->expects($this->once())->method('getFiles')->will($this->returnValue(array($file)));

        $this->_assetsMock->expects($this->once())->method('add')->with($this->anything(), $asset);

        $observer = new \Magento\Event\Observer;
        $this->_model->applyThemeCustomization($observer);
    }

    public function testProcessReinitConfig()
    {
        $observer = new \Magento\Event\Observer;
        $this->_configMock->expects($this->once())->method('reinit');
        $this->_model->processReinitConfig($observer);
    }
}
