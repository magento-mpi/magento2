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
     * Set up
     */
    protected function setUp()
    {
        $this->driver = $this->getMock('Magento\Framework\Filesystem\Driver\File', array(), array(), '', false);
        $this->fileFactory = $this->getMock('Magento\Framework\Filesystem\File\ReadFactory', array(), array(), '', false);
        $this->read = new \Magento\Framework\Filesystem\Directory\Read(array(), $this->fileFactory, $this->driver);
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
}
