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
    protected $_model;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dirs;

    /**
     * @var string
     */
    protected $varDirPath;

    public function setUp()
    {
        $this->varDirPath = BP .'/'. Filesystem::VAR_DIR;
        $this->_dirs = $this->getMock(
            '\Magento\Framework\App\Filesystem\DirectoryList',
            array('getDir'),
            array(),
            '',
            false
        );

        $this->_dirs->expects(
            $this->any()
        )->method(
                'getDir'
        )->will(
                $this->returnValue($this->varDirPath)
        );

        $this->_model = new MaintenanceMode($this->_dirs);
    }

    public function tearDown()
    {

        $flagFile = $this->varDirPath . '/' . MaintenanceMode::FLAG_FILENAME;
        $ipFile = $this->varDirPath . '/' . MaintenanceMode::IP_FILENAME;
        if (file_exists($flagFile)) {
            unlink( $flagFile);
        }
        if (file_exists($ipFile)) {
            unlink($ipFile);
        }
        unset($this->_model);
    }

    public function testMaintenanceModeOnAndOff()
    {
        $this->assertFalse($this->_model->getStatusInfo());
        $this->assertFalse($this->_model->isOn());

        $this->_model->turnOn();
        $expectedArray = [];
        $this->assertEquals($expectedArray, $this->_model->getStatusInfo());
        $this->assertTrue($this->_model->isOn());

        $this->_model->turnOff();
        $this->assertFalse($this->_model->isOn());
    }

    public function testMaintenanceModeOnAndOffWithOneAddress()
    {
        $this->_model->turnOn(["address1"]);
        $this->assertTrue($this->_model->isOn());
        $expectedArray = ["address1"];
        $this->assertEquals($expectedArray, $this->_model->getStatusInfo());

        $this->_model->turnOff(["address1"]);
        $this->assertFalse($this->_model->getStatusInfo());
    }

    public function testMaintenanceModeOnAndOffWithTwoAddress()
    {
        $this->_model->turnOn(["address1", "address2"]);
        $this->assertTrue($this->_model->isOn());
        $this->assertTrue($this->_model->isOn("address3"));
        $this->assertFalse($this->_model->isOn("address2"));
        $expectedArray = ["address1","address2"];
        $this->assertEquals($expectedArray, $this->_model->getStatusInfo());

        $this->_model->turnOff();
        $this->assertFalse($this->_model->isOn());
        $this->assertFalse($this->_model->getStatusInfo());
    }
}
