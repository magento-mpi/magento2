<?php
/**
 * Test for \Magento\Filesystem\Driver\File
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\Driver;

class FileTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Filesystem\Driver\File
     */
    protected $driver;

    /**
     * @var string
     */
    protected $absolutePath;

    /**
     * get relative path for test
     *
     * @param $relativePath
     * @return string
     */
    protected function getTestPath($relativePath)
    {
        return $this->absolutePath . $relativePath;
    }

    /**
     * Set up
     */
    public function setUp()
    {
        $this->driver = new \Magento\Filesystem\Driver\File();
        $this->absolutePath = dirname(__DIR__) . '/_files/';
    }

    /**
     * test read recursively read
     */
    public function testReadDirectoryRecursively()
    {
        $expected = array(
            $this->getTestPath('recursively/directory'),
            $this->getTestPath('recursively/directory.txt'),
            $this->getTestPath('recursively/directory/read.txt')
        );
        $actual = $this->driver->readDirectoryRecursively($this->getTestPath('recursively'));
        sort($actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * test exception
     *
     * @expectedException \Magento\Filesystem\FilesystemException
     */
    public function testReadDirectoryRecursivelyFailure()
    {
        $this->driver->readDirectoryRecursively($this->getTestPath('not-existing-directory'));
    }
}
