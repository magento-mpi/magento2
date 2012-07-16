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
     * @var Mage_Core_Model_Config|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    public function setUp()
    {
        $this->_configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_helper = new Mage_Backend_Helper_Data(array('config' => $this->_configMock));
    }

    public function testGetAreaFrontName()
    {
        $this->_configMock->expects($this->at(0))->method('getNode')
            ->with(Mage_Backend_Helper_Data::XML_PATH_USE_CUSTOM_ADMIN_PATH)
            ->will($this->returnValue(false));

        $this->_configMock->expects($this->at(1))->method('getNode')
            ->with(Mage_Backend_Helper_Data::XML_PATH_BACKEND_FRONTNAME)
            ->will($this->returnValue('backend'));

        $this->assertEquals('backend', $this->_helper->getAreaFrontName());
    }

    public function testGetAreaFrontNameOneTime()
    {
        $this->_configMock->expects($this->at(0))->method('getNode')
            ->with(Mage_Backend_Helper_Data::XML_PATH_USE_CUSTOM_ADMIN_PATH)
            ->will($this->returnValue(true));

        $this->_configMock->expects($this->at(1))->method('getNode')
            ->with(Mage_Backend_Helper_Data::XML_PATH_CUSTOM_ADMIN_PATH)
            ->will($this->returnValue('control'));

        $this->_configMock->expects($this->at(2))->method('getNode')
            ->with(Mage_Backend_Helper_Data::XML_PATH_BACKEND_FRONTNAME)
            ->will($this->returnValue('backend'));

        $this->_configMock->expects($this->once())->method('setNode')
            ->with(Mage_Backend_Helper_Data::XML_PATH_BACKEND_FRONTNAME, 'control', true);

        $this->assertEquals('control', $this->_helper->getAreaFrontName());
        $this->assertEquals('control', $this->_helper->getAreaFrontName());
    }

    public function testGetAreaFrontNameIfAreaIsNotExist()
    {
        $this->_configMock->expects($this->at(0))->method('getNode')
            ->with(Mage_Backend_Helper_Data::XML_PATH_USE_CUSTOM_ADMIN_PATH)
            ->will($this->returnValue(false));

        $this->_configMock->expects($this->at(1))->method('getNode')
            ->with(Mage_Backend_Helper_Data::XML_PATH_BACKEND_FRONTNAME)
            ->will($this->returnValue(null));


        $this->assertNotNull($this->_helper->getAreaFrontName());
        $this->assertEmpty($this->_helper->getAreaFrontName());
    }
}
