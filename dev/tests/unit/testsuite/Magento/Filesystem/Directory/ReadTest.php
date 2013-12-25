<?php
/**
 * Unit Test for \Magento\Filesystem\Directory\Read
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\Directory;

class ReadTest extends \PHPUnit_Framework_TestCase
{

    /**
     * \Magento\Filesystem\Driver
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $driver;

    /**
     * @var \Magento\Filesystem\Directory\Read
     */
    protected $read;

    /**
     * \Magento\Filesystem\File\ReadFactory
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fileFactory;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->driver = $this->getMock('Magento\Filesystem\Driver\File', array(), array(), '', false);
        $this->fileFactory = $this->getMock('Magento\Filesystem\File\ReadFactory', array(), array(), '', false);
        $this->read = new \Magento\Filesystem\Directory\Read(array(), $this->fileFactory, $this->driver);
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
        $this->driver->expects($this->once())
            ->method('isExists')
            ->will($this->returnValue(true));
        $this->assertTrue($this->read->isExist('correct-path'));
    }

    public function testStat()
    {
        $this->driver->expects($this->once())
            ->method('stat')
            ->will($this->returnValue(array('some-stat-data')));
        $this->assertEquals(array('some-stat-data'), $this->read->stat('correct-path'));
    }
}
