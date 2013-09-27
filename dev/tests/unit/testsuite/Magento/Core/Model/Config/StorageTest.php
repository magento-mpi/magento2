<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Core_Model_Config_Storage
 */
class Magento_Core_Model_Config_StorageTest extends PHPUnit_Framework_TestCase
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
        $this->_loaderMock = $this->getMock('Magento_Core_Model_Config_Loader',
            array(), array(), '', false, false);
        $this->_factoryMock = $this->getMock('Magento_Core_Model_Config_BaseFactory',
            array(), array(), '', false, false);
        $this->_model = new Magento_Core_Model_Config_Storage($this->_cacheMock, $this->_loaderMock,
            $this->_factoryMock);
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
        $mockConfigBase = $this->getMockBuilder('Magento_Core_Model_Config_Base')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_cacheMock->expects($this->once())->method('load')->will($this->returnValue(false));
        $this->_factoryMock->expects($this->once())->method('create')->will($this->returnValue($mockConfigBase));
        $this->_loaderMock->expects($this->once())->method('load');
        $this->_cacheMock->expects($this->once())->method('save');
        $this->_model->getConfiguration();
    }

    public function testRemoveCache()
    {
        $this->_cacheMock->expects($this->once())->method('clean');
        $this->_model->removeCache();
    }
}