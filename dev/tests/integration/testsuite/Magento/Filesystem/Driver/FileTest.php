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
        $paths = array(
            'foo/bar',
            'foo/bar/baz',
            'foo/bar/baz/file_one.txt',
            'foo/bar/file_two.txt',
            'foo/file_three.txt'
        );
        $expected = array_map(array('self', 'getTestPath'), $paths);
        $actual = $this->driver->readDirectoryRecursively($this->getTestPath('foo'));
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
