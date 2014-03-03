<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\View\Deployment\Version\Storage;

class FileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var File
     */
    private $object;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $directory;

    protected function setUp()
    {
        $this->directory = $this->getMock('Magento\Filesystem\Directory\WriteInterface');
        $filesystem = $this->getMock('Magento\App\Filesystem', array(), array(), '', false);
        $filesystem
            ->expects($this->once())
            ->method('getDirectoryWrite')
            ->with('fixture_dir')
            ->will($this->returnValue($this->directory))
        ;
        $this->object = new File($filesystem, 'fixture_dir', 'fixture_file.txt');
    }

    public function testLoad()
    {
        $this->directory
            ->expects($this->once())
            ->method('readFile')
            ->with('fixture_file.txt')
            ->will($this->returnValue('fixture/dir/fixture_file.txt'))
        ;
        $this->assertEquals('fixture/dir/fixture_file.txt', $this->object->load());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Exception to be propagated
     */
    public function testLoadExceptionPropagation()
    {
        $this->directory
            ->expects($this->once())
            ->method('readFile')
            ->with('fixture_file.txt')
            ->will($this->throwException(new \Exception('Exception to be propagated')))
        ;
        $this->object->load();
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Unable to retrieve deployment version of static files from the file system
     */
    public function testLoadExceptionWrapping()
    {
        $filesystemException = new \Magento\Filesystem\FilesystemException('File does not exist');
        $this->directory
            ->expects($this->once())
            ->method('readFile')
            ->with('fixture_file.txt')
            ->will($this->throwException($filesystemException))
        ;
        try {
            $this->object->load();
        } catch (\Exception $e) {
            $this->assertSame($filesystemException, $e->getPrevious(), 'Wrapping of original exception is expected');
            throw $e;
        }
    }
}
