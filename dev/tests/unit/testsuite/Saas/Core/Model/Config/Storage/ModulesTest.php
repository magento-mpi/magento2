<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Saas_Core_Model_Config_Storage_Modules
 */
class Saas_Core_Model_Config_Storage_ModulesTest extends PHPUnit_Framework_TestCase
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
    protected $_cacheMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_loaderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    protected function setUp()
    {
        $this->_configMock = $this->getMock('Magento_Core_Model_ConfigInterface',
            array(), array(), '', false, false);
        $this->_cacheMock = $this->getMock('Magento_Core_Model_Config_Cache',
            array(), array(), '', false, false);
        $this->_loaderMock = $this->getMock('Magento_Core_Model_Config_LoaderInterface',
            array(), array(), '', false, false);
        $this->_factoryMock = $this->getMock('Magento_Core_Model_Config_BaseFactory',
            array(), array(), '', false, false);
        $this->_model = new Saas_Core_Model_Config_Storage_Modules($this->_cacheMock, $this->_loaderMock,
            $this->_factoryMock);
    }

    protected function tearDown()
    {
        unset($this->_configMock);
        unset($this->_cacheMock);
        unset($this->_loaderMock);
        unset($this->_factoryMock);
        unset($this->_model);
    }

    public function testGetConfigurationWithData()
    {
        $this->_cacheMock->expects($this->once())->method('load')->will($this->returnValue($this->_configMock));
        $this->_factoryMock->expects($this->never())->method('create');
        $this->_loaderMock->expects($this->never())->method('load');
        $this->_cacheMock->expects($this->never())->method('save');
        $this->_model->getConfiguration();
    }

    public function testGetConfigurationWithoutData()
    {
        $mockConfigBase = $this->getMockBuilder('Magento_Core_Model_Config_Base')->disableOriginalConstructor()->getMock();
        $this->_cacheMock->expects($this->once())->method('load')->will($this->returnValue(false));
        $this->_factoryMock->expects($this->once())->method('create')->will($this->returnValue($mockConfigBase));
        $this->_loaderMock->expects($this->once())->method('load');
        $this->_cacheMock->expects($this->never())->method('save');
        $this->_model->getConfiguration();
    }

    public function testGetConfigurationWithRemoveCache()
    {
        $mockConfigBase = $this->getMockBuilder('Magento_Core_Model_Config_Base')->disableOriginalConstructor()->getMock();
        $this->_cacheMock->expects($this->once())->method('load')->will($this->returnValue($this->_configMock));
        $this->_factoryMock->expects($this->once())->method('create')->will($this->returnValue($mockConfigBase));
        $this->_loaderMock->expects($this->once())->method('load');
        $this->_cacheMock->expects($this->never())->method('save');
        $this->_model->removeCache();
        $this->_model->getConfiguration();
    }
}
