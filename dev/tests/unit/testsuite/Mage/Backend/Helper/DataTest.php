<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Helper_Data
     */
    protected $_helper;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    public function setUp()
    {
        $this->_configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false, false);
        $this->_helper = new Mage_Backend_Helper_Data($this->_configMock,
            $this->getMock('Mage_Core_Helper_Context', array(), array(), '', false, false)
        );
    }

    public function testGetAreaFrontNameReturnsDefaultValueWhenCustomNotSet()
    {
        $this->_configMock->expects($this->at(0))->method('getValue')
            ->with(Mage_Backend_Helper_Data::XML_PATH_USE_CUSTOM_ADMIN_PATH, 'default')
            ->will($this->returnValue(false));

        $this->_configMock->expects($this->at(1))->method('getNode')
            ->with(Mage_Backend_Helper_Data::XML_PATH_BACKEND_FRONTNAME)
            ->will($this->returnValue('backend'));

        $this->assertEquals('backend', $this->_helper->getAreaFrontName());
    }

    public function testGetAreaFrontNameReturnsDefaultValueWhenCustomIsSet()
    {
        $this->_configMock->expects($this->at(0))->method('getValue')
            ->with(Mage_Backend_Helper_Data::XML_PATH_USE_CUSTOM_ADMIN_PATH, 'default')
            ->will($this->returnValue(true));

        $this->_configMock->expects($this->at(1))->method('getValue')
            ->with(Mage_Backend_Helper_Data::XML_PATH_CUSTOM_ADMIN_PATH, 'default')
            ->will($this->returnValue('control'));

        $this->assertEquals('control', $this->_helper->getAreaFrontName());
    }

    public function testGetAreaFrontNameReturnsEmptyStringIfAreaFrontNameDoesntExist()
    {
        $this->_configMock->expects($this->at(0))->method('getValue')
            ->with(Mage_Backend_Helper_Data::XML_PATH_USE_CUSTOM_ADMIN_PATH, 'default')
            ->will($this->returnValue(false));

        $this->_configMock->expects($this->at(1))->method('getNode')
            ->with(Mage_Backend_Helper_Data::XML_PATH_BACKEND_FRONTNAME)
            ->will($this->returnValue(null));


        $this->assertNotNull($this->_helper->getAreaFrontName());
        $this->assertEmpty($this->_helper->getAreaFrontName());
    }

    public function testClearAreaFrontName()
    {
        $this->_configMock->expects($this->exactly(2))->method('getNode');
        $this->_configMock->expects($this->exactly(2))->method('getValue');
        $this->_helper->getAreaFrontName();
        $this->_helper->clearAreaFrontName();
        $this->_helper->getAreaFrontName();
    }

    public function testGetAreaFrontNameReturnsValueFromCache()
    {
        $this->_configMock->expects($this->once())->method('getNode');
        $this->_configMock->expects($this->once())->method('getValue');
        $this->_helper->getAreaFrontName();
        $this->_helper->getAreaFrontName();
    }
}
