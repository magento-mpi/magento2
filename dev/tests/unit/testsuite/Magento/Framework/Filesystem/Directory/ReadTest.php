<?php
/**
 * Unit Test for \Magento\Framework\Filesystem\Directory\Read
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filesystem\Directory;

class ReadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * \Magento\Framework\Filesystem\Driver
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $driver;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Read
     */
    protected $read;

    /**
     * \Magento\Framework\Filesystem\File\ReadFactory
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileFactory;

    /**
     * Directory path
     *
     * @var string
     */
    protected $path;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->driver = $this->getMock('Magento\Filesystem\Driver\File', array(), array(), '', false);
        $this->fileFactory = $this->getMock('Magento\Filesystem\File\ReadFactory', array(), array(), '', false);
        $this->read = new \Magento\Framework\Filesystem\Directory\Read(
            array('path' => $this->path),
            $this->fileFactory,
            $this->driver
        );
    }

    /**
     * Tear down
     */
    protected function tearDown()
    {
        $this->driver = null;
        $this->fileFactory = null;
        $this->read = null;
    }

    public function testIsExist()
    {
        $this->driver->expects($this->once())->method('isExists')->will($this->returnValue(true));
        $this->assertTrue($this->read->isExist('correct-path'));
    }

    public function testStat()
    {
        $this->driver->expects($this->once())->method('stat')->will($this->returnValue(array('some-stat-data')));
        $this->assertEquals(array('some-stat-data'), $this->read->stat('correct-path'));
    }

    public function testReadFileNoProtocol()
    {
        $path = 'filepath';
        $flag = 'flag';
        $context = 'context';
        $contents = 'contents';

        $this->driver->expects($this->once())
            ->method('getAbsolutePath')
            ->with($this->path, $path)
            ->will($this->returnValue($path));
        $this->driver->expects($this->once())
            ->method('fileGetContents')
            ->with($path, $flag, $context)
            ->will($this->returnValue($contents));

        $this->assertEquals($contents, $this->read->readFile($path, $flag, $context));
    }

    public function testReadFileCustomProtocol()
    {
        $path = 'filepath';
        $flag = 'flag';
        $context = 'context';
        $protocol = 'ftp';
        $contents = 'contents';

        $fileMock = $this->getMock('Magento\Filesystem\File\Read', [], [], '', false);
        $fileMock->expects($this->once())
            ->method('readAll')
            ->with($flag, $context)
            ->will($this->returnValue($contents));

        $this->driver->expects($this->once())
            ->method('getAbsolutePath')
            ->with($this->path, $path, $protocol)
            ->will($this->returnValue($path));
        $this->driver->expects($this->never())
            ->method('fileGetContents');
        $this->fileFactory->expects($this->once())
            ->method('create')
            ->with($path, $protocol, $this->driver)
            ->will($this->returnValue($fileMock));

        $this->assertEquals($contents, $this->read->readFile($path, $flag, $context, $protocol));
    }
}
