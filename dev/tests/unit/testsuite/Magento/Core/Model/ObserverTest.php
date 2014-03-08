<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheFrontendMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_frontendPoolMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_themeCustomization;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_assetService;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_assetsMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
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
            'Magento\App\Cache\Frontend\Pool',
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
            'Magento\View\Design\Theme\Customization',
            array(),
            array(),
            '',
            false
        );
        $themeMock = $this->getMock(
            'Magento\Core\Model\Theme',
            array('__wakeup', 'getCustomization'),
            array(),
            '',
            false
        );
        $themeMock->expects($this->any())->method('getCustomization')
            ->will($this->returnValue($this->_themeCustomization));

        $designMock = $this->getMock('Magento\View\DesignInterface');
        $designMock
            ->expects($this->any())
            ->method('getDesignTheme')
            ->will($this->returnValue($themeMock))
        ;

        $this->_assetsMock = $this->getMock('Magento\View\Asset\GroupedCollection',
            array(), array(), '', false, false);
        $this->_configMock = $this->getMock('\Magento\App\ReinitableConfigInterface',
            array(), array(), '', false, false);

        $this->_assetService = $this->getMock('Magento\View\Asset\Service',
            array('createFileAsset'), array(), '', false);

        $this->_model = new Observer(
            $this->_frontendPoolMock,
            $designMock,
            $this->_assetsMock,
            $this->_configMock,
            $this->_assetService,
            $this->getMock('\Magento\Core\Model\Theme\Registration', array(), array(), '', false),
            $this->getMock('\Magento\Logger', array(), array(), '', false)
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
            ->with(\Zend_Cache::CLEANING_MODE_OLD, array())
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
        $asset = $this->getMockForAbstractClass('\Magento\View\Asset\LocalInterface');
        $file = $this->getMock('Magento\Core\Model\Theme\File', array(), array(), '', false);
        $fileService = $this->getMock('Magento\View\Design\Theme\Customization\File\Css', array(), array(), '', false);
        $file->expects($this->any())->method('getCustomizationService')->will($this->returnValue($fileService));
        $file->expects($this->atLeastOnce())->method('getFullPath')->will($this->returnValue('test.css'));

        $this->_assetService->expects($this->once())
            ->method('createFileAsset')
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
