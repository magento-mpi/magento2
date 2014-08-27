<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\App;

class MaintenanceModeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MaintenanceMode
     */
    protected $model;

    /**
     * @var \Magento\Framework\App\Filesystem | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\App\Filesystem\Write | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $write;

    /**
     * @var \Magento\Framework\Filesystem\DriverInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $driveInterface;

    // public static function setupBeforeClass()
    protected function setup()
    {
        $this->write = $this->getMock('Magento\Framework\Filesystem\Directory\Write', [], [], '', false);
        $this->filesystem = $this->getMock('Magento\Framework\App\Filesystem', [], [], '', false);
        $this->filesystem->expects($this->any())
            ->method('getDirectoryWrite')
            ->will($this->returnValue($this->write));

        $this->write->expects($this->any())->method('getAbsolutePath')
            ->will($this->returnValue(''));
        $this->driverInterface = $this->getMock('\Magento\Framework\Filesystem\DriverInterface');
        $this->write->expects($this->any())->method('getDriver')
            ->will($this->returnValue($this->driverInterface));

        $this->model = new MaintenanceMode($this->filesystem);
    }

    public function testIsOnInitial()
    {
        $this->driverInterface->expects($this->any())->method('isExists')
            ->with(MaintenanceMode::FLAG_FILENAME)
            ->will($this->returnValue(false));
        $this->assertFalse($this->model->isOn());
    }

    public function testisOnWithoutIP()
    {
        $mapIsExists = [
            [MaintenanceMode::FLAG_FILENAME, true],
            [MaintenanceMode::IP_FILENAME, false]
        ];
        $this->driverInterface->expects($this->any())->method('isExists')->will(($this->returnValueMap($mapIsExists)));
        $this->assertTrue($this->model->isOn());
    }

    public function testisOnWithIP()
    {
        $mapIsExists = [
            [MaintenanceMode::FLAG_FILENAME, true],
            [MaintenanceMode::IP_FILENAME, true]
        ];
        $this->driverInterface->expects($this->any())->method('isExists')->will(($this->returnValueMap($mapIsExists)));
        $this->assertFalse($this->model->isOn());
    }

    public function testisOnWithIPNoMaintenance()
    {
        $mapIsExists = [
            [MaintenanceMode::FLAG_FILENAME, false],
            [MaintenanceMode::IP_FILENAME, true]
        ];
        $this->driverInterface->expects($this->any())->method('isExists')->will(($this->returnValueMap($mapIsExists)));
        $this->assertFalse($this->model->isOn());
    }

    public function testMaintenanceModeOn()
    {
        $mapIsExists = [
            [MaintenanceMode::FLAG_FILENAME, true]
        ];
        $this->driverInterface->expects($this->any())->method('isExists')->will($this->returnValueMap($mapIsExists));
        $this->write->expects($this->any())->method('touch')->will($this->returnValue(true));
        $this->model->set(true);
        $this->assertTrue($this->model->isOn());
    }

    public function testMaintenanceModeOff()
    {
        $this->write->expects($this->any())->method('delete')->will($this->returnValue(true));
        $this->model->set(false);
        $this->driverInterface->expects($this->any())->method('isExists')
            ->with(MaintenanceMode::FLAG_FILENAME)
            ->will(($this->returnValue(false)));
        $this->assertFalse($this->model->isOn());
    }

    public function testSetAddresses()
    {
        $mapIsExists = [
            [MaintenanceMode::FLAG_FILENAME, true],
            [MaintenanceMode::IP_FILENAME, true]
        ];
        $this->driverInterface->expects($this->any())->method('isExists')->will($this->returnValueMap($mapIsExists));
        $this->write->expects($this->any())->method('writeFile')
            ->with(MaintenanceMode::IP_FILENAME)
            ->will($this->returnValue(true));

        $this->write->expects($this->any())->method('readFile')
            ->with(MaintenanceMode::IP_FILENAME)
            ->will($this->returnValue(''));

        $this->model->setAddresses('');
        $this->assertEquals([''], $this->model->getAddressInfo());
    }

    public function testSetSingleAddresses()
    {
        $mapIsExists = [
            [MaintenanceMode::FLAG_FILENAME, true],
            [MaintenanceMode::IP_FILENAME, true]
        ];
        $this->driverInterface->expects($this->any())->method('isExists')->will($this->returnValueMap($mapIsExists));
        $this->write->expects($this->any())->method('delete')->will($this->returnValueMap($mapIsExists));

        $this->write->expects($this->any())->method('writeFile')
            ->will($this->returnValue(10));

        $this->write->expects($this->any())->method('readFile')
            ->with(MaintenanceMode::IP_FILENAME)
            ->will($this->returnValue('address1'));

        $this->model->setAddresses("address1");
        $this->assertEquals(["address1"], $this->model->getAddressInfo());
    }

    public function testOnSetMultipleAddresses()
    {
        $mapIsExists = [
            [MaintenanceMode::FLAG_FILENAME, true],
            [MaintenanceMode::IP_FILENAME, true]
        ];
        $this->driverInterface->expects($this->any())->method('isExists')->will($this->returnValueMap($mapIsExists));
        $this->write->expects($this->any())->method('delete')->will($this->returnValueMap($mapIsExists));

        $this->write->expects($this->any())->method('writeFile')
            ->will($this->returnValue(10));

        $this->write->expects($this->any())->method('readFile')
            ->with(MaintenanceMode::IP_FILENAME)
            ->will($this->returnValue('address1,10.50.60.123'));

        $expectedArray = ["address1", "10.50.60.123"];
        $this->model->setAddresses("address1,10.50.60.123");
        $this->assertEquals($expectedArray, $this->model->getAddressInfo());
        $this->assertFalse($this->model->isOn("address1"));
        $this->assertTrue($this->model->isOn("address3"));
    }

    public function testOffSetMultipleAddresses()
    {
        $mapIsExists = [
            [MaintenanceMode::FLAG_FILENAME, false],
            [MaintenanceMode::IP_FILENAME, true]
        ];
        $this->driverInterface->expects($this->any())->method('isExists')->will($this->returnValueMap($mapIsExists));
        $this->write->expects($this->any())->method('delete')->will($this->returnValueMap($mapIsExists));

        $this->write->expects($this->any())->method('readFile')
            ->with(MaintenanceMode::IP_FILENAME)
            ->will($this->returnValue('address1,10.50.60.123'));

        $expectedArray = ["address1", "10.50.60.123"];
        $this->model->setAddresses("address1,10.50.60.123");
        $this->assertEquals($expectedArray, $this->model->getAddressInfo());
        $this->assertFalse($this->model->isOn("address1"));
        $this->assertFalse($this->model->isOn("address3"));
    }
}
