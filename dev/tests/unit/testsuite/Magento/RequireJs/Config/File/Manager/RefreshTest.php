<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\RequieJs\Config\File\Manager;

class RefreshTest extends \PHPUnit_Framework_TestCase
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
     * @var \Magento\RequireJs\Config\File\Manager\Refresh
     */
    private $object;

    protected function setUp()
    {
        $this->config = $this->getMock('\Magento\RequireJs\Config', array(), array(), '', false);
        $this->filesystem = $this->getMock('\Magento\App\Filesystem', array(), array(), '', false);
        $this->object = new \Magento\RequireJs\Config\File\Manager\Refresh($this->config, $this->filesystem);
    }

    public function testGetConfigFile()
    {
        $expectedContent = 'content';
        $expectedPath = 'config.js';
        $expected = 'root/config.js';
        $this->config->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue($expectedContent));
        $this->config->expects($this->once())
            ->method('getConfigFileRelativePath')
            ->will($this->returnValue($expectedPath));
        $dir = $this->getMockForAbstractClass('\Magento\Filesystem\Directory\WriteInterface');
        $this->filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(\Magento\App\Filesystem::STATIC_VIEW_DIR)
            ->will($this->returnValue($dir));
        $dir->expects($this->once())
            ->method('writeFile')
            ->with($expectedPath, $expectedContent);
        $dir->expects($this->once())
            ->method('getAbsolutePath')
            ->with($expectedPath)
            ->will($this->returnValue($expected));
        $actual = $this->object->getConfigFile();
        $this->assertSame($expected, $actual);
    }
}
