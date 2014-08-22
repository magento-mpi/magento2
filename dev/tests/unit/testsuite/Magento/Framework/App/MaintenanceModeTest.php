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
        $this->varDirPath = BP . '/' . Filesystem::VAR_DIR;
        if (!file_exists($this->varDirPath)) {
            mkdir($this->varDirPath);
        }
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
        $this->assertFalse($this->_model->isOn());
        $this->_model->set(true);
        $this->assertTrue($this->_model->isOn());
        $this->_model->set(false);
        $this->assertFalse($this->_model->isOn());
    }

    public function testSetAddresses()
    {
        $expectedArray = [];
        $this->assertEquals($this->_model->getAddressInfo(), $expectedArray);

        $expectedArray = ["address1"];
        $this->_model->setAddresses("address1");
        $this->assertEquals($this->_model->getAddressInfo(), $expectedArray);

        $expectedArray = ["address1","address2"];
        $this->_model->setAddresses("address1,address2");
        $this->assertEquals($this->_model->getAddressInfo(), $expectedArray);
    }
}
