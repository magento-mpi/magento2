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
    protected $_assetRepo;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_assetsMock;

    /**
     * @var \Magento\Core\Model\Observer
     */
    protected $_model;

    protected function setUp()
    {
        $this->_cacheFrontendMock = $this->getMockForAbstractClass('Magento\Cache\FrontendInterface');

        $this->_frontendPoolMock = $this->getMock(
            'Magento\Framework\App\Cache\Frontend\Pool',
            array(),
            array(),
            '',
            false
        );
        $this->_frontendPoolMock->expects($this->any())->method('valid')->will($this->onConsecutiveCalls(true, false));
        $this->_frontendPoolMock->expects(
            $this->any()
        )->method(
            'current'
        )->will(
            $this->returnValue($this->_cacheFrontendMock)
        );

        $this->_themeCustomization = $this->getMock(
            'Magento\Framework\View\Design\Theme\Customization',
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
        $themeMock->expects(
            $this->any()
        )->method(
            'getCustomization'
        )->will(
            $this->returnValue($this->_themeCustomization)
        );

        $designMock = $this->getMock('Magento\Framework\View\DesignInterface');
        $designMock->expects($this->any())->method('getDesignTheme')->will($this->returnValue($themeMock));

        $this->_assetsMock = $this->getMock(
            'Magento\Framework\View\Asset\GroupedCollection',
            array(),
            array(),
            '',
            false,
            false
        );
        $this->_configMock = $this->getMock(
            '\Magento\Framework\App\Config\ReinitableConfigInterface',
            array(),
            array(),
            '',
            false,
            false
        );

        $this->_assetRepo = $this->getMock('Magento\View\Asset\Repository', array(), array(), '', false);

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject(
            'Magento\Core\Model\Observer',
            array(
                'cacheFrontendPool' => $this->_frontendPoolMock,
                'design' => $designMock,
                'assets' => $this->_assetsMock,
                'assetRepo' => $this->_assetRepo,
            )
        );
    }

    protected function tearDown()
    {
        $this->_cacheFrontendMock = null;
        $this->_frontendPoolMock = null;
        $this->_themeCustomization = null;
        $this->_assetsMock = null;
        $this->_model = null;
    }

    public function testCleanCache()
    {
        $cacheBackendMock = $this->getMockForAbstractClass('Zend_Cache_Backend_Interface');
        $cacheBackendMock->expects($this->once())->method('clean')->with(\Zend_Cache::CLEANING_MODE_OLD, array());
        $this->_cacheFrontendMock->expects(
            $this->once()
        )->method(
            'getBackend'
        )->will(
            $this->returnValue($cacheBackendMock)
        );
        $cronScheduleMock = $this->getMock('Magento\Cron\Model\Schedule', array(), array(), '', false);
        $this->_model->cleanCache($cronScheduleMock);
    }

    public function testApplyThemeCustomization()
    {
        $asset = $this->getMock('\Magento\View\Asset\File', array(), array(), '', false);
        $file = $this->getMock('Magento\Core\Model\Theme\File', array(), array(), '', false);
        $fileService = $this->getMockForAbstractClass('\Magento\Framework\View\Design\Theme\Customization\FileAssetInterface');
        $file->expects($this->any())->method('getCustomizationService')->will($this->returnValue($fileService));

        $this->_assetRepo->expects($this->once())
            ->method('createArbitrary')
            ->will($this->returnValue($asset));

        $this->_themeCustomization->expects($this->once())->method('getFiles')->will($this->returnValue(array($file)));
        $this->_assetsMock->expects($this->once())->method('add')->with($this->anything(), $asset);

        $observer = new \Magento\Event\Observer();
        $this->_model->applyThemeCustomization($observer);
    }
}
