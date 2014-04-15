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

    protected function setUp()
    {
        $this->_config = $this->getMock('Magento\View\Asset\ConfigInterface', array(), array(), '', false);
        $this->_objectManager = $this->getMockForAbstractClass('Magento\ObjectManager');
        $this->_model = new MinifyService($this->_config, $this->_objectManager);
    }

    /**
     * @param $appMode
     * @param $expectedStrategy
     * @dataProvider getAssetsDataProvider
     */
    public function testGetAssets($appMode, $expectedStrategy)
    {
        $assetOne = $this->getMockForAbstractClass('Magento\View\Asset\LocalInterface');
        $assetOne->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue('js'));
        $resultOne = $this->getMock('Magento\View\Asset\Minified', array(), array(), '', false);
        $assetTwo = $this->getMockForAbstractClass('Magento\View\Asset\LocalInterface');
        $assetTwo->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue('js'));
        $resultTwo = $this->getMock('Magento\View\Asset\Minified', array(), array(), '', false);
        $this->_config->expects($this->once())
            ->method('isAssetMinification')
            ->with('js')
            ->will($this->returnValue(true));
        $minifier = $this->getMockForAbstractClass('Magento\Code\Minifier\AdapterInterface');
        $this->_config->expects($this->once())
            ->method('getAssetMinificationAdapter')
            ->with('js')
            ->will($this->returnValue('Magento\Code\Minifier\AdapterInterface'));
        $this->_objectManager->expects($this->once())
            ->method('get')
            ->with('Magento\Code\Minifier\AdapterInterface')
            ->will($this->returnValue($minifier));
        $this->_objectManager->expects($this->exactly(2))
            ->method('create')
            ->will($this->returnValueMap(
                array(
                    array(
                        'Magento\View\Asset\Minified',
                        array('asset' => $assetOne, 'strategy' => $expectedStrategy, 'adapter' => $minifier),
                        $resultOne
                    ),
                    array(
                        'Magento\View\Asset\Minified',
                        array('asset' => $assetTwo, 'strategy' => $expectedStrategy, 'adapter' => $minifier),
                        $resultTwo
                    ),
                )
            ));
        $model = new MinifyService($this->_config, $this->_objectManager, $appMode);
        $result = $model->getAssets(array($assetOne, $assetTwo));
        $this->assertArrayHasKey(0, $result);
        $this->assertSame($resultOne, $result[0]);
        $this->assertArrayHasKey(1, $result);
        $this->assertSame($resultTwo, $result[1]);
    }

    /**
     * @return array
     */
    public function getAssetsDataProvider()
    {
        return array(
            'production' => array(
                \Magento\App\State::MODE_PRODUCTION,
                Minified::FILE_EXISTS
            ),
            'default'    => array(
                \Magento\App\State::MODE_DEFAULT,
                Minified::MTIME
            ),
            'developer'  => array(
                \Magento\App\State::MODE_DEVELOPER,
                Minified::MTIME
            ),
        );
    }

    public function testGetAssetsDisabled()
    {
        $asset = $this->getMockForAbstractClass('Magento\View\Asset\LocalInterface');
        $asset->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue('js'));

        $this->_config->expects($this->once())
            ->method('isAssetMinification')
            ->with('js')
            ->will($this->returnValue(false));
        $this->_config->expects($this->never())
            ->method('getAssetMinificationAdapter');

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
        $asset->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue('js'));

        $this->_config->expects($this->once())
            ->method('isAssetMinification')
            ->with('js')
            ->will($this->returnValue(true));
        $this->_config->expects($this->once())
            ->method('getAssetMinificationAdapter')
            ->with('js');

        $this->_model->getAssets(array($asset));
    }

    /**
     * @expectedException \Magento\Exception
     * @expectedExceptionMessage Invalid adapter: 'stdClass'. Expected: \Magento\Code\Minifier\AdapterInterface
     */
    public function testGetAssetsInvalidAdapter()
    {
        $asset = $this->getMockForAbstractClass('Magento\View\Asset\LocalInterface');
        $asset->expects($this->once())
            ->method('getContentType')
            ->will($this->returnValue('js'));
        $this->_config->expects($this->once())
            ->method('isAssetMinification')
            ->with('js')
            ->will($this->returnValue(true));
        $this->_config->expects($this->once())
            ->method('getAssetMinificationAdapter')
            ->with('js')
            ->will($this->returnValue('StdClass'));
        $obj = new \StdClass;
        $this->_objectManager->expects($this->once())->method('get')->with('StdClass')->will($this->returnValue($obj));

        $this->_model->getAssets(array($asset));
    }
}
