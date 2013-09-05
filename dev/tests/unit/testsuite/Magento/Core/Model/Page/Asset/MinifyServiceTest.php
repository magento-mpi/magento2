<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Page_Asset_MinifyServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Store_Config|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeConfig;

    /**
     * @var \Magento\ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var Magento_Core_Model_Page_Asset_MinifyService
     */
    protected $_model;

    /**
     * @var Magento_Core_Model_App_State|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appState;

    protected function setUp()
    {
        $this->_storeConfig = $this->getMock('Magento_Core_Model_Store_Config');
        $dirs = $this->getMock('Magento_Core_Model_Dir', array(), array(), '', false);
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
        $this->_appState = $this->getMock('Magento_Core_Model_App_State');

        $this->_model = new Magento_Core_Model_Page_Asset_MinifyService($this->_storeConfig, $this->_objectManager,
            $dirs, $this->_appState);
    }

    public function testGetAssets()
    {
        $assetOne = $this->getMockForAbstractClass('Magento_Core_Model_Page_Asset_LocalInterface');
        $assetOne->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue('js'));
        $assetTwo = $this->getMockForAbstractClass('Magento_Core_Model_Page_Asset_LocalInterface');
        $assetTwo->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue('js'));

        $this->_storeConfig->expects($this->once())
            ->method('getConfigFlag')
            ->with('dev/js/minify_files')
            ->will($this->returnValue(true));
        $this->_storeConfig->expects($this->once())
            ->method('getConfig')
            ->with('dev/js/minify_adapter')
            ->will($this->returnValue('\Magento\Code\Minifier\AdapterInterface'));

        $self = $this;
        $this->_objectManager->expects($this->any())
            ->method('create')
            ->will($this->returnCallback(
                function ($className) use ($self) {
                    return $self->getMock($className, array(), array(), '', false);
                }
            ));

        $minifiedAssets = $this->_model->getAssets(array($assetOne, $assetTwo));
        $this->assertCount(2, $minifiedAssets);
        $this->assertNotSame($minifiedAssets[0], $minifiedAssets[1]);
        $this->assertInstanceOf('Magento_Core_Model_Page_Asset_Minified', $minifiedAssets[0]);
        $this->assertInstanceOf('Magento_Core_Model_Page_Asset_Minified', $minifiedAssets[1]);
    }

    public function testGetAssetsDisabled()
    {
        $asset = $this->getMockForAbstractClass('Magento_Core_Model_Page_Asset_LocalInterface');
        $asset->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue('js'));

        $this->_storeConfig->expects($this->once())
            ->method('getConfigFlag')
            ->with('dev/js/minify_files')
            ->will($this->returnValue(false));
        $this->_storeConfig->expects($this->never())
            ->method('getConfig');

        $minifiedAssets = $this->_model->getAssets(array($asset));
        $this->assertCount(1, $minifiedAssets);
        $this->assertSame($asset, $minifiedAssets[0]);
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage Minification adapter is not specified for 'js' content type
     */
    public function testGetAssetsNoAdapterDefined()
    {
        $this->_storeConfig->expects($this->once())
            ->method('getConfigFlag')
            ->with('dev/js/minify_files')
            ->will($this->returnValue(true));
        $asset = $this->getMockForAbstractClass('Magento_Core_Model_Page_Asset_LocalInterface');
        $asset->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue('js'));
        $this->_model->getAssets(array($asset));
    }

    /**
     * @param string $mode
     * @param string $expectedStrategy
     * @dataProvider getAssetsAppModesDataProvider
     */
    public function testGetAssetsAppModes($mode, $expectedStrategy)
    {
        $this->_appState->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue($mode));

        $asset = $this->getMockForAbstractClass('Magento_Core_Model_Page_Asset_LocalInterface');
        $asset->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue('js'));

        $this->_storeConfig->expects($this->once())
            ->method('getConfigFlag')
            ->with('dev/js/minify_files')
            ->will($this->returnValue(true));
        $this->_storeConfig->expects($this->once())
            ->method('getConfig')
            ->with('dev/js/minify_adapter')
            ->will($this->returnValue('\Magento\Code\Minifier\AdapterInterface'));

        $this->_objectManager->expects($this->at(1))
            ->method('create')
            ->with($expectedStrategy);

        $this->_model->getAssets(array($asset));
    }

    /**
     * @return array
     */
    public function getAssetsAppModesDataProvider()
    {
        return array(
            'production' => array(
                Magento_Core_Model_App_State::MODE_PRODUCTION,
                'Magento\Code\Minifier\Strategy\Lite'
            ),
            'default'    => array(
                Magento_Core_Model_App_State::MODE_DEFAULT,
                'Magento\Code\Minifier\Strategy\Generate'
            ),
            'developer'  => array(
                Magento_Core_Model_App_State::MODE_DEVELOPER,
                'Magento\Code\Minifier\Strategy\Generate'
            ),
        );
    }
}
