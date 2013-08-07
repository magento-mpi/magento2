<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Queue_Model_Config_GearmanTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var Enterprise_Queue_Model_Config_Gearman
     */
    protected $_config;

    protected function setUp()
    {
        $this->_configMock = $this->getMock('Mage_Core_Model_Config_Modules', array(), array(), '', false);
        $this->_config = new Enterprise_Queue_Model_Config_Gearman($this->_configMock);
    }

    public function testGetServers()
    {
        $this->_configMock->expects($this->once())->method('getNode')
            ->with(Enterprise_Queue_Model_Config_Gearman::XML_PATH_QUEUE_ADAPTER_GEARMAN_SERVERS)
            ->will($this->returnValue('127.0.0.1:4730'));

        $this->assertEquals('127.0.0.1:4730', $this->_config->getServers());
    }
}
