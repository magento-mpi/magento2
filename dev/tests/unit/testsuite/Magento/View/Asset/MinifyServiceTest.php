<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Asset;

class MinifyServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Asset\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_config;

    /**
     * @var \Magento\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var \Magento\View\Asset\MinifyService
     */
    protected $_model;

    /**
     * @var \Magento\Framework\App\State|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appState;

    protected function setUp()
    {
        $this->_config = $this->getMock('Magento\View\Asset\ConfigInterface', array(), array(), '', false);
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
        $this->_appState = $this->getMock('Magento\Framework\App\State', array(), array(), '', false);
        $filesystem = $this->getMock('Magento\Framework\App\Filesystem', array(), array(), '', false);
        $directory = $this->getMock('Magento\Filesystem\Directory\Read', array(), array(), '', false);
        $filesystem->expects($this->any())->method('getDirectoryRead')->will($this->returnValue($directory));
        $directory->expects($this->any())->method('getAbsolutePath')->will($this->returnArgument(0));
        $this->_model = new \Magento\View\Asset\MinifyService(
            $this->_config,
            $this->_objectManager,
            $this->_appState,
            $filesystem
        );
    }

    public function testGetAssets()
    {
        $assetOne = $this->getMockForAbstractClass('Magento\View\Asset\LocalInterface');
        $assetOne->expects($this->once())->method('getContentType')->will($this->returnValue('js'));
        $assetTwo = $this->getMockForAbstractClass('Magento\View\Asset\LocalInterface');
        $assetTwo->expects($this->once())->method('getContentType')->will($this->returnValue('js'));

        $this->_config->expects(
            $this->once()
        )->method(
            'isAssetMinification'
        )->with(
            'js'
        )->will(
            $this->returnValue(true)
        );
        $this->_config->expects(
            $this->once()
        )->method(
            'getAssetMinificationAdapter'
        )->with(
            'js'
        )->will(
            $this->returnValue('Magento\Code\Minifier\AdapterInterface')
        );

        $self = $this;
        $this->_objectManager->expects($this->any())->method('create')->will(
            $this->returnCallback(
                function ($className) use ($self) {
                    return $self->getMock($className, array(), array(), '', false);
                }
            )
        );

        $minifiedAssets = $this->_model->getAssets(array($assetOne, $assetTwo));
        $this->assertCount(2, $minifiedAssets);
        $this->assertNotSame($minifiedAssets[0], $minifiedAssets[1]);
        $this->assertInstanceOf('Magento\View\Asset\Minified', $minifiedAssets[0]);
        $this->assertInstanceOf('Magento\View\Asset\Minified', $minifiedAssets[1]);
    }

    public function testGetAssetsDisabled()
    {
        $asset = $this->getMockForAbstractClass('Magento\View\Asset\LocalInterface');
        $asset->expects($this->once())->method('getContentType')->will($this->returnValue('js'));

        $this->_config->expects(
            $this->once()
        )->method(
            'isAssetMinification'
        )->with(
            'js'
        )->will(
            $this->returnValue(false)
        );
        $this->_config->expects($this->never())->method('getAssetMinificationAdapter');

        $minifiedAssets = $this->_model->getAssets(array($asset));
        $this->assertCount(1, $minifiedAssets);
        $this->assertSame($asset, $minifiedAssets[0]);
    }

    /**
     * @expectedException \Magento\Exception
     * @expectedExceptionMessage Minification adapter is not specified for 'js' content type
     */
    public function testGetAssetsNoAdapterDefined()
    {
        $asset = $this->getMockForAbstractClass('Magento\View\Asset\LocalInterface');
        $asset->expects($this->once())->method('getContentType')->will($this->returnValue('js'));

        $this->_config->expects(
            $this->once()
        )->method(
            'isAssetMinification'
        )->with(
            'js'
        )->will(
            $this->returnValue(true)
        );
        $this->_config->expects($this->once())->method('getAssetMinificationAdapter')->with('js');

        $this->_model->getAssets(array($asset));
    }

    /**
     * @param string $mode
     * @param string $expectedStrategy
     * @dataProvider getAssetsAppModesDataProvider
     */
    public function testGetAssetsAppModes($mode, $expectedStrategy)
    {
        $this->_appState->expects($this->once())->method('getMode')->will($this->returnValue($mode));

        $asset = $this->getMockForAbstractClass('Magento\View\Asset\LocalInterface');
        $asset->expects($this->once())->method('getContentType')->will($this->returnValue('js'));

        $this->_config->expects(
            $this->once()
        )->method(
            'isAssetMinification'
        )->with(
            'js'
        )->will(
            $this->returnValue(true)
        );
        $this->_config->expects(
            $this->once()
        )->method(
            'getAssetMinificationAdapter'
        )->with(
            'js'
        )->will(
            $this->returnValue('Magento\Code\Minifier\AdapterInterface')
        );

        $this->_objectManager->expects($this->at(1))->method('create')->with($expectedStrategy);

        $this->_model->getAssets(array($asset));
    }

    /**
     * @return array
     */
    public function getAssetsAppModesDataProvider()
    {
        return array(
            'production' => array(\Magento\Framework\App\State::MODE_PRODUCTION, 'Magento\Code\Minifier\Strategy\Lite'),
            'default' => array(\Magento\Framework\App\State::MODE_DEFAULT, 'Magento\Code\Minifier\Strategy\Generate'),
            'developer' => array(\Magento\Framework\App\State::MODE_DEVELOPER, 'Magento\Code\Minifier\Strategy\Generate')
        );
    }
}
