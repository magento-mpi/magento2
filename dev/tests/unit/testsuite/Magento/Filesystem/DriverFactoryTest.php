<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class DriverFactoryTest
 * @package Magento\Filesystem
 */
class DriverFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DirectoryList | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $directoryList;

    /**
     * @var DriverFactory
     */
    protected $driverFactory;

    protected function setUp()
    {
        $this->directoryList = $this->getMock('Magento\Framework\App\Filesystem\DirectoryList', [], [], '', false);
        $this->driverFactory = new DriverFactory($this->directoryList);
    }

    public function testGetByProtocolConfig()
    {
        $protocolCode = 'protocol';
        $expectedDriverClass = '\Magento\Filesystem\Driver\Zlib';
        $protocolConfig = ['driver' => $expectedDriverClass];

        $this->directoryList->expects($this->once())
            ->method('getProtocolConfig')
            ->with($protocolCode)
            ->will($this->returnValue($protocolConfig));

        $this->assertInstanceOf($expectedDriverClass, $this->driverFactory->get($protocolCode));
    }

    public function testGetSpecifiedDriver()
    {
        $driverClass = '\Magento\Filesystem\Driver\Http';
        $this->assertInstanceOf($driverClass, $this->driverFactory->get(null, $driverClass));
    }

    public function testGetDefault()
    {
        $this->assertInstanceOf('\Magento\Filesystem\Driver\File', $this->driverFactory->get());
    }
}
