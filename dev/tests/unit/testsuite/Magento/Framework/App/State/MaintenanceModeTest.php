<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\State;

class MaintenanceModeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\State\MaintenanceMode
     */
    protected $model;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $directoryWrite;

    /**
     * @var \Magento\Framework\App\Filesystem | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystem;

    protected function setUp()
    {
        $this->directoryWrite = $this->getMock('Magento\Framework\Filesystem\Directory\Write', [], [], '', false);
        $this->filesystem = $this->getMock('Magento\Framework\App\Filesystem', [], [], '', false);

        $this->model = (new \Magento\TestFramework\Helper\ObjectManager($this))->getObject(
            'Magento\Framework\App\State\MaintenanceMode',
            ['filesystem' => $this->filesystem]
        );
    }

    protected function getDirectory()
    {
        $this->filesystem->expects($this->once())->method('getDirectoryWrite')->with(MaintenanceMode::FLAG_DIR)
            ->will($this->returnValue($this->directoryWrite));
    }

    public function testTurnOnMaintenanceMode()
    {
        $this->getDirectory();
        $this->directoryWrite->expects($this->once())->method('writeFile')
            ->with(MaintenanceMode::FLAG_FILENAME, 'data')
            ->will($this->returnValue(123));

        $this->assertTrue($this->model->turnOn('data'));
    }

    public function testTurnOnMaintenanceModeFailed()
    {
        $this->getDirectory();
        $this->directoryWrite->expects($this->once())->method('writeFile')
            ->with(MaintenanceMode::FLAG_FILENAME, 'data')
            ->will($this->throwException(new \Magento\Framework\Filesystem\FilesystemException('failed')));

        $this->assertFalse($this->model->turnOn('data'));
    }

    public function testTurnOffMaintenanceMode()
    {
        $this->getDirectory();
        $this->directoryWrite->expects($this->once())->method('delete')->with(MaintenanceMode::FLAG_FILENAME);

        $this->assertTrue($this->model->turnOff());
    }

    public function testTurnOffMaintenanceModeFailed()
    {
        $this->getDirectory();
        $this->directoryWrite->expects($this->once())->method('delete')->with(MaintenanceMode::FLAG_FILENAME)
            ->will($this->throwException(new \Magento\Framework\Filesystem\FilesystemException('failed')));

        $this->assertFalse($this->model->turnOff());
    }
}
