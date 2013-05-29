<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Saas_Core_Model_Config_Storage
 */
class Saas_Core_Model_Config_StorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Core_Model_Config_Storage
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourcesConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_queueHandlerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventFactoryMock;

    protected function setUp()
    {
        $this->_configMock = $this->getMock('Mage_Core_Model_ConfigInterface',
            array(), array(), '', false, false);
        $this->_resourcesConfigMock = $this->getMock('Mage_Core_Model_Config_Resource',
            array(), array(), '', false, false);
        $this->_cacheMock = $this->getMock('Mage_Core_Model_Config_Cache',
            array(), array(), '', false, false);
        $this->_queueHandlerMock = $this->getMock('Enterprise_Queue_Model_Event_HandlerInterface',
            array(), array(), '', false, false);
        $this->_eventFactoryMock = $this->getMock('Varien_EventFactory',
            array('create'), array(), '', false, false);
        $this->_model = new Saas_Core_Model_Config_Storage($this->_cacheMock, $this->_resourcesConfigMock,
            $this->_queueHandlerMock, $this->_eventFactoryMock);
    }

    protected function tearDown()
    {
        unset($this->_configMock);
        unset($this->_resourcesConfigMock);
        unset($this->_cacheMock);
        unset($this->_queueHandlerMock);
        unset($this->_eventFactoryMock);
        unset($this->_model);
    }

    public function testGetConfigurationWithData()
    {
        $this->_cacheMock->expects($this->once())->method('load')->will($this->returnValue($this->_configMock));
        $this->_eventFactoryMock->expects($this->never())->method('create');
        $this->_queueHandlerMock->expects($this->never())->method('addTask');
        $this->_cacheMock->expects($this->never())->method('save');
        $this->_resourcesConfigMock->expects($this->once())
            ->method('setConfig')
            ->with($this->equalTo($this->_configMock));
        $this->_model->getConfiguration();
    }

    /**
     * @expectedException Saas_Core_Model_Config_Exception
     */
    public function testGetConfigurationWithoutData()
    {
        $varienEvent = $this->getMockBuilder('Varien_Event')->disableOriginalConstructor()->getMock();
        $this->_cacheMock->expects($this->once())->method('load')->will($this->returnValue(false));
        $this->_eventFactoryMock->expects($this->once())->method('create')->will($this->returnValue($varienEvent));
        $this->_queueHandlerMock->expects($this->once())->method('addTask');
        $this->_resourcesConfigMock->expects($this->never())->method('setConfig');
        $this->_model->getConfiguration();
    }

    public function testGetConfigurationWithRemoveCache()
    {
        $varienEvent = $this->getMockBuilder('Varien_Event')->disableOriginalConstructor()->getMock();
        $this->_cacheMock->expects($this->once())->method('load')->will($this->returnValue($this->_configMock));
        $this->_eventFactoryMock->expects($this->once())->method('create')->will($this->returnValue($varienEvent));
        $this->_queueHandlerMock->expects($this->once())->method('addTask');
        $this->_resourcesConfigMock->expects($this->once())
            ->method('setConfig')
            ->with($this->equalTo($this->_configMock));
        $this->_model->removeCache();
        $this->_model->getConfiguration();
    }
}
