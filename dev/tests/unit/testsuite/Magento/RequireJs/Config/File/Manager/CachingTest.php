<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RequieJs\Config\File\Manager;

class CachingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\RequireJs\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    /**
     * @var \Magento\App\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filesystem;

    /**
     * @var \Magento\RequireJs\Config\File\Manager\Refresh|\PHPUnit_Framework_MockObject_MockObject
     */
    private $refreshManager;

    /**
     * @var \Magento\RequireJs\Config\File\Manager\Refresh
     */
    private $object;

    protected function setUp()
    {
        $this->config = $this->getMock('\Magento\RequireJs\Config', array(), array(), '', false);
        $this->filesystem = $this->getMock('\Magento\App\Filesystem', array(), array(), '', false);
        $this->refreshManager = $this->getMock(
            '\Magento\RequireJs\Config\File\Manager\Refresh', array(), array(), '', false
        );
        $this->object = new \Magento\RequireJs\Config\File\Manager\Caching(
            $this->config,
            $this->filesystem,
            $this->refreshManager
        );
    }

    /**
     * File doesn't exist and should be created
     */
    public function testGetConfig()
    {
        $expectedPath = 'config.js';
        $expected = 'root/config.js';
        $this->config->expects($this->once())
            ->method('getConfigFileRelativePath')
            ->will($this->returnValue($expectedPath));
        $dir = $this->getMockForAbstractClass('\Magento\Filesystem\Directory\ReadInterface');
        $this->filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->with(\Magento\App\Filesystem::STATIC_VIEW_DIR)
            ->will($this->returnValue($dir));
        $dir->expects($this->once())
            ->method('isExist')
            ->with($expectedPath)
            ->will($this->returnValue(false));
        $this->refreshManager->expects($this->once())
            ->method('getConfigFile')
            ->will($this->returnValue($expected));
        $actual = $this->object->getConfigFile();
        $this->assertSame($expected, $actual);
    }

    /**
     * File exists
     */
    public function testGetConfigFileExists()
    {
        $expectedPath = 'config.js';
        $expected = 'root/config.js';
        $this->config->expects($this->once())
            ->method('getConfigFileRelativePath')
            ->will($this->returnValue($expectedPath));
        $dir = $this->getMockForAbstractClass('\Magento\Filesystem\Directory\ReadInterface');
        $this->filesystem->expects($this->once())
            ->method('getDirectoryRead')
            ->with(\Magento\App\Filesystem::STATIC_VIEW_DIR)
            ->will($this->returnValue($dir));
        $dir->expects($this->once())
            ->method('isExist')
            ->with($expectedPath)
            ->will($this->returnValue(true));
        $dir->expects($this->once())
            ->method('getAbsolutePath')
            ->with($expectedPath)
            ->will($this->returnValue($expected));
        $this->refreshManager->expects($this->never())
            ->method('getConfigFile');
        $actual = $this->object->getConfigFile();
        $this->assertSame($expected, $actual);
    }
}
