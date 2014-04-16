<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Filesystem\File;

/**
 * Class ReadFactoryTest
 * @package Magento\Filesystem\File
 */
class ReadFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Filesystem\DriverFactory | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $driverFactory;

    /**
     * @var WriteFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->driverFactory = $this->getMock('Magento\Filesystem\DriverFactory', [], [], '', false);
        $this->factory = new ReadFactory($this->driverFactory);
    }

    /**
     * @dataProvider createProvider
     * @param string|null $protocol
     */
    public function testCreate($protocol)
    {
        $path = 'path';
        $directoryDriver = $this->getMockForAbstractClass('Magento\Filesystem\DriverInterface');
        $directoryDriver->expects($this->once())
            ->method('isExists')
            ->will($this->returnValue(true));

        if ($protocol) {
            $this->driverFactory->expects($this->once())
                ->method('get')
                ->with($protocol, $directoryDriver)
                ->will($this->returnValue($directoryDriver));
        } else {
            $this->driverFactory->expects($this->never())
                ->method('get');
        }

        $this->assertInstanceOf(
            'Magento\Filesystem\File\Read',
            $this->factory->create($path, $protocol, $directoryDriver)
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
