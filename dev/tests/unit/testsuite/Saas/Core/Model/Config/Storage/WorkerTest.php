<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Saas_Core_Model_Config_Storage_Worker
 */
class Saas_Core_Model_Config_Storage_WorkerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Config_Storage
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
    protected $_loaderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storageModulesMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storageLocalesMock;

    protected function setUp()
    {
        $this->_configMock = $this->getMock('Magento_Core_Model_ConfigInterface',
            array(), array(), '', false, false);
        $this->_resourcesConfigMock = $this->getMock('Magento_Core_Model_Config_Resource',
            array(), array(), '', false, false);
        $this->_cacheMock = $this->getMock('Magento_Core_Model_Config_Cache',
            array(), array(), '', false, false);
        $this->_loaderMock = $this->getMock('Magento_Core_Model_Config_LoaderInterface',
            array(), array(), '', false, false);
        $this->_factoryMock = $this->getMock('Magento_Core_Model_Config_BaseFactory',
            array(), array(), '', false, false);
        $this->_storageModulesMock = $this->getMock('Saas_Core_Model_Config_Storage_Modules',
            array(), array(), '', false, false);
        $this->_storageLocalesMock = $this->getMock('Saas_Core_Model_Config_Storage_Locales',
            array(), array(), '', false, false);
        $this->_model = new Saas_Core_Model_Config_Storage_Worker(
            $this->_cacheMock,
            $this->_loaderMock,
            $this->_factoryMock,
            $this->_resourcesConfigMock,
            $this->_storageModulesMock,
            $this->_storageLocalesMock
        );
    }

    protected function tearDown()
    {
        unset($this->_configMock);
        unset($this->_resourcesConfigMock);
        unset($this->_cacheMock);
        unset($this->_loaderMock);
        unset($this->_factoryMock);
        unset($this->_storageModulesMock);
        unset($this->_storageLocalesMock);
        unset($this->_model);
    }

    public function testGetConfigurationWithData()
    {
        $this->_cacheMock->expects($this->once())->method('load')->will($this->returnValue($this->_configMock));
        $this->_factoryMock->expects($this->never())->method('create');
        $this->_loaderMock->expects($this->never())->method('load');
        $this->_cacheMock->expects($this->never())->method('save');
        $this->_resourcesConfigMock->expects($this->once())
            ->method('setConfig')
            ->with($this->equalTo($this->_configMock));
        $this->_model->getConfiguration();
    }

    public function testGetConfigurationWithoutData()
    {
        $mockConfigBase = $this->getMockBuilder('Magento_Core_Model_Config_Base')->disableOriginalConstructor()->getMock();
        $this->_cacheMock->expects($this->once())->method('load')->will($this->returnValue(false));
        $this->_factoryMock->expects($this->once())->method('create')->will($this->returnValue($mockConfigBase));
        $this->_loaderMock->expects($this->once())->method('load');
        $this->_cacheMock->expects($this->once())->method('save');
        $this->_resourcesConfigMock->expects($this->once())
            ->method('setConfig')
            ->with($this->equalTo($mockConfigBase));
        $this->_model->getConfiguration();
    }

    public function testGetConfigurationWithRemoveCache()
    {
        $this->_storageModulesMock->expects($this->once())->method('removeCache');
        $this->_storageLocalesMock->expects($this->once())->method('removeCache');
        $this->_cacheMock->expects($this->once())->method('load')->will($this->returnValue($this->_configMock));
        $mockConfigBase = $this->getMockBuilder('Magento_Core_Model_Config_Base')->disableOriginalConstructor()->getMock();
        $this->_factoryMock->expects($this->once())->method('create')->will($this->returnValue($mockConfigBase));
        $this->_loaderMock->expects($this->once())->method('load');
        $this->_cacheMock->expects($this->once())->method('save');
        $this->_resourcesConfigMock->expects($this->once())
            ->method('setConfig')
            ->with($this->equalTo($mockConfigBase));
        $this->_model->removeCache();
        $this->_model->getConfiguration();
    }
}
