<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filesystem;

use Magento\Framework\App\Filesystem\DirectoryList as AppDirList;

/**
 * Class WrapperFactoryTest
 */
class WrapperFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AppDirList | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $directoryList;

    /**
     * @var WrapperFactory
     */
    protected $wrapperFactory;

    protected function setUp()
    {
        $this->directoryList = $this->getMock('Magento\Framework\App\Filesystem\DirectoryList', [], [], '', false);
        $this->wrapperFactory = new WrapperFactory($this->directoryList);
    }

    public function testGetByProtocolConfig()
    {
        $protocolCode = 'protocol';
        $expectedWrapperClass = '\Magento\Framework\Filesystem\Stub\Wrapper';
        $protocolConfig = ['driver' => $expectedWrapperClass];
        $driver = $this->getMockForAbstractClass('Magento\Framework\Filesystem\DriverInterface');

        $this->directoryList->expects($this->once())
            ->method('getProtocolConfig')
            ->with($protocolCode)
            ->will($this->returnValue($protocolConfig));

        $this->assertInstanceOf($expectedWrapperClass, $this->wrapperFactory->get($protocolCode, $driver));
    }
}
