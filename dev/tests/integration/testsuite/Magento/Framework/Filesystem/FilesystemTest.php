<?php
/**
 * Test for \Magento\Framework\Filesystem
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filesystem;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class FilesystemTest
 * Test for Magento\Framework\Filesystem class
 *
 */
class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Filesystem
     */
    protected $filesystem;

    protected function setUp()
    {
        $this->filesystem = Bootstrap::getObjectManager()->create('Magento\Framework\App\Filesystem');
    }

    /**
     * Test getDirectoryRead method return valid instance
     */
    public function testGetDirectoryReadInstance()
    {
        $dir = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem::VAR_DIR);
        $this->assertInstanceOf('\Magento\Framework\Filesystem\Directory\Read', $dir);
    }

    /**
     * Test getDirectoryWrite method return valid instance
     */
    public function testGetDirectoryWriteInstance()
    {
        $dir = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::VAR_DIR);
        $this->assertInstanceOf('\Magento\Framework\Filesystem\Directory\Write', $dir);
    }

    /**
     * Test getDirectoryWrite throws exception on trying to get directory with write access
     *
     * @expectedException \Magento\Framework\Filesystem\FilesystemException
     */
    public function testGetDirectoryWriteException()
    {
        $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem::THEMES_DIR);
    }

    /**
     * Test getUri returns right uri
     */
    public function testGetUri()
    {
        $this->assertContains('media', $this->filesystem->getPath(\Magento\Framework\App\Filesystem::MEDIA_DIR));
    }
}
