<?php
/**
 * Test for \Magento\Filesystem\Stream\Local
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
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    protected function setUp()
    {
        $this->filesystem = Bootstrap::getObjectManager()->create('Magento\Filesystem');
    }

    /**
     * Test getDirectoryRead method return valid instance
     */
    public function testGetDirectoryReadInstance()
    {
        $dir = $this->filesystem->getDirectoryRead(\Magento\Filesystem\DirectoryList::VAR_DIR);
        $this->assertInstanceOf('\Magento\Filesystem\Directory\Read', $dir);
    }

    /**
     * Test getDirectoryWrite method return valid instance
     */
    public function testGetDirectoryWriteInstance()
    {
        $dir = $this->filesystem->getDirectoryWrite(\Magento\Filesystem\DirectoryList::VAR_DIR);
        $this->assertInstanceOf('\Magento\Filesystem\Directory\Write', $dir);
    }

    /**
     * Test getDirectoryWrite throws exception on trying to get directory with read access
     *
     * @expectedException \Magento\Filesystem\FilesystemException
     */
    public function testGetDirectoryWriteException()
    {
        $this->filesystem->getDirectoryWrite(\Magento\Filesystem\DirectoryList::ROOT);
    }

    /**
     * Test getPath returns right path
     */
    public function testGetPath()
    {
        $this->assertContains('var', $this->filesystem->getPath(\Magento\Filesystem\DirectoryList::VAR_DIR));
    }

    /**
     * Test getUri returns right uri
     */
    public function testGetUri()
    {
        $this->assertContains('media', $this->filesystem->getPath(\Magento\Filesystem\DirectoryList::MEDIA));
    }
}
