<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Filesystem\File;

/**
 * Class WriteFactoryTest
 * @package Magento\Framework\Filesystem\File
 */
class WriteFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Filesystem\DriverFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $driverFactory;

    /**
     * @var WriteFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->driverFactory = $this->getMock('Magento\Framework\Filesystem\DriverFactory', [], [], '', false);
        $this->factory = new WriteFactory($this->driverFactory);
    }

    /**
     * @dataProvider createProvider
     * @param string|null $protocol
     */
    public function testCreate($protocol)
    {
        $path = 'path';
        $directoryDriver = $this->getMockForAbstractClass('Magento\Framework\Filesystem\DriverInterface');
        $mode = 'a+';

        if ($protocol) {
            $this->driverFactory->expects($this->once())
                ->method('get')
                ->with($protocol, get_class($directoryDriver))
                ->will($this->returnValue($directoryDriver));
        } else {
            $this->driverFactory->expects($this->never())
                ->method('get');
        }

        $this->assertInstanceOf(
            'Magento\Framework\Filesystem\File\Write',
            $this->factory->create($path, $protocol, $directoryDriver, $mode)
        );
    }

    public function createProvider()
    {
        return [
            [null],
            ['custom_protocol']
        ];
    }
}
