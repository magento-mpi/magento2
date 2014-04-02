<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RequireJs\Model;

class FileManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\RequireJs\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var \Magento\App\FileSystem|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileSystem;

    /**
     * @var \Magento\Filesystem\Directory\WriteInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dir;

    /**
     * @var \Magento\App\State|\PHPUnit_Framework_MockObject_MockObject
     */
    private $appState;

    /**
     * @var \Magento\View\Asset\File|\PHPUnit_Framework_MockObject_MockObject
     */
    private $asset;

    /**
     * @var \Magento\RequireJs\Model\FileManager
     */
    private $object;

    protected function setUp()
    {
        $this->config = $this->getMock('\Magento\RequireJs\Config', array(), array(), '', false);
        $this->fileSystem = $this->getMock('\Magento\App\FileSystem', array(), array(), '', false);
        $this->appState = $this->getMock('\Magento\App\State', array(), array(), '', false);
        $assetRepo = $this->getMock('\Magento\View\Asset\Repository', array(), array(), '', false);
        $this->object = new FileManager($this->config, $this->fileSystem, $this->appState, $assetRepo);
        $this->dir = $this->getMockForAbstractClass('\Magento\Filesystem\Directory\WriteInterface');
        $this->fileSystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(\Magento\App\Filesystem::STATIC_VIEW_DIR)
            ->will($this->returnValue($this->dir))
        ;
        $this->config->expects($this->once())
            ->method('getConfigFileRelativePath')
            ->will($this->returnValue('requirejs/file.js'))
        ;
        $this->asset = $this->getMock('\Magento\View\Asset\File', array(), array(), '', false);
        $assetRepo->expects($this->once())
            ->method('createArbitrary')
            ->with('requirejs/file.js', '')
            ->will($this->returnValue($this->asset));
    }

    /**
     * @param bool $exists
     * @dataProvider createRequireJsAssetDataProvider
     */
    public function testCreateRequireJsAsset($exists)
    {
        $this->appState->expects($this->once())->method('getMode')->will($this->returnValue('anything'));
        $this->dir->expects($this->once())
            ->method('isExist')
            ->with('requirejs/file.js')
            ->will($this->returnValue($exists))
        ;
        if ($exists) {
            $this->config->expects($this->never())->method('getConfig');
            $this->dir->expects($this->never())->method('writeFile');
        } else {
            $data = 'requirejs config data';
            $this->config->expects($this->once())->method('getConfig')->will($this->returnValue($data));
            $this->dir->expects($this->once())->method('writeFile')->with('requirejs/file.js', $data);
        }
        $this->assertSame($this->asset, $this->object->createRequireJsAsset());
    }

    /**
     * @return array
     */
    public function createRequireJsAssetDataProvider()
    {
        return array(array(true), array(false));
    }

    public function testCreateRequireJsAssetDevMode()
    {
        $this->appState->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue(\Magento\App\State::MODE_DEVELOPER))
        ;
        $this->dir->expects($this->never())->method('isExist');
        $data = 'requirejs config data';
        $this->config->expects($this->once())->method('getConfig')->will($this->returnValue($data));
        $this->dir->expects($this->once())->method('writeFile')->with('requirejs/file.js', $data);
        $this->assertSame($this->asset, $this->object->createRequireJsAsset());
    }
}
