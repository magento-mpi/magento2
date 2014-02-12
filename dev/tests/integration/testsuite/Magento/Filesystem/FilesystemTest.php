<?php
/**
 * Test for \Magento\Filesystem
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class FilesystemTest
 * Test for Magento\Filesystem class
 *
 * @package Magento
 */
class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Filesystem
     */
    protected $filesystem;

    protected function setUp()
    {
        $this->filesystem = Bootstrap::getObjectManager()->create('Magento\App\Filesystem');
    }

    /**
     * Test getDirectoryRead method return valid instance
     */
    public function testGetDirectoryReadInstance()
    {
        $dir = $this->filesystem->getDirectoryRead(\Magento\App\Filesystem::VAR_DIR);
        $this->assertInstanceOf('\Magento\Filesystem\Directory\Read', $dir);
    }

    /**
     * Test getDirectoryWrite method return valid instance
     */
    public function testGetDirectoryWriteInstance()
    {
        $dir = $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::VAR_DIR);
        $this->assertInstanceOf('\Magento\Filesystem\Directory\Write', $dir);
    }

    /**
     * Test getDirectoryWrite throws exception on trying to get directory with write access
     *
     * @expectedException \Magento\Filesystem\FilesystemException
     */
    public function testGetDirectoryWriteException()
    {
        $this->filesystem->getDirectoryWrite(\Magento\App\Filesystem::THEMES_DIR);
    }

    /**
     * Test getUri returns right uri
     */
    public function testGetUri()
    {
        $this->assertContains('media', $this->filesystem->getPath(\Magento\App\Filesystem::MEDIA_DIR));
    }
}
