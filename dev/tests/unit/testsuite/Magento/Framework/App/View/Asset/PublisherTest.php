<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\App\View\Asset;

use Magento\Framework\App\View\Asset\Publisher;

class PublisherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\State|\PHPUnit_Framework_MockObject_MockObject
     */
    private $appState;

    /**
     * @var \Magento\Framework\App\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $rootDirWrite;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $staticDirRead;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $staticDirWrite;

    /**
     * @var \Magento\Framework\App\View\Asset\Publisher
     */
    private $object;

    protected function setUp()
    {
        $this->appState = $this->getMock('Magento\Framework\App\State', array(), array(), '', false);
        $this->filesystem = $this->getMock('Magento\Framework\App\Filesystem', array(), array(), '', false);
        $this->object = new Publisher($this->appState, $this->filesystem);

        $this->rootDirWrite = $this->getMockForAbstractClass('Magento\Framework\Filesystem\Directory\WriteInterface');
        $this->staticDirRead = $this->getMockForAbstractClass('Magento\Framework\Filesystem\Directory\ReadInterface');
        $this->staticDirWrite = $this->getMockForAbstractClass('Magento\Framework\Filesystem\Directory\WriteInterface');
        $this->filesystem->expects($this->any())
            ->method('getDirectoryRead')
            ->with(\Magento\Framework\App\Filesystem::STATIC_VIEW_DIR)
            ->will($this->returnValue($this->staticDirRead));
        $this->filesystem->expects($this->any())
            ->method('getDirectoryWrite')
            ->will($this->returnValueMap([
                [\Magento\Framework\App\Filesystem::ROOT_DIR, $this->rootDirWrite],
                [\Magento\Framework\App\Filesystem::STATIC_VIEW_DIR, $this->staticDirWrite],
            ]));
    }

    public function testPublishNotAllowed()
    {
        $this->appState->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue(\Magento\Framework\App\State::MODE_DEVELOPER));
        $this->assertFalse($this->object->publish($this->getAsset()));
    }

    public function testPublishExistsBefore()
    {
        $this->appState->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue(\Magento\Framework\App\State::MODE_PRODUCTION));
        $this->staticDirRead->expects($this->once())
            ->method('isExist')
            ->with('some/file.ext')
            ->will($this->returnValue(true));
        $this->assertTrue($this->object->publish($this->getAsset()));
    }

    public function testPublish()
    {
        $this->appState->expects($this->once())
            ->method('getMode')
            ->will($this->returnValue(\Magento\Framework\App\State::MODE_PRODUCTION));
        $this->staticDirRead->expects($this->once())
            ->method('isExist')
            ->with('some/file.ext')
            ->will($this->returnValue(false));

        $this->rootDirWrite->expects($this->once())
            ->method('getRelativePath')
            ->with('/root/some/file.ext')
            ->will($this->returnValue('some/file.ext'));
        $this->rootDirWrite->expects($this->once())
            ->method('copyFile')
            ->with('some/file.ext', 'some/file.ext', $this->staticDirWrite)
            ->will($this->returnValue(true));

        $this->assertTrue($this->object->publish($this->getAsset()));
    }

    /**
     * Create an asset mock
     *
     * @return \Magento\Framework\View\Asset\File|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAsset()
    {
        $asset = $this->getMock('Magento\Framework\View\Asset\File', array(), array(), '', false);
        $asset->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue('some/file.ext'));
        $asset->expects($this->any())
            ->method('getSourceFile')
            ->will($this->returnValue('/root/some/file.ext'));
        return $asset;
    }
} 
