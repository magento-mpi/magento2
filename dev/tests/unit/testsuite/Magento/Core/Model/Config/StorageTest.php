<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Core\Model\Config\Storage
 */
class Magento_Core_Model_Config_StorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Config\Storage
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

    protected function setUp()
    {
        $this->_configMock = $this->getMock('Magento\Core\Model\ConfigInterface',
            array(), array(), '', false, false);
        $this->_resourcesConfigMock = $this->getMock('Magento\Core\Model\Config\Resource',
            array(), array(), '', false, false);
        $this->_cacheMock = $this->getMock('Magento\Core\Model\Config\Cache',
            array(), array(), '', false, false);
        $this->_loaderMock = $this->getMock('Magento\Core\Model\Config\Loader',
            array(), array(), '', false, false);
        $this->_factoryMock = $this->getMock('Magento\Core\Model\Config\BaseFactory',
            array(), array(), '', false, false);
        $this->_model = new \Magento\Core\Model\Config\Storage($this->_cacheMock, $this->_loaderMock,
            $this->_factoryMock, $this->_resourcesConfigMock);
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
        $mockConfigBase = $this->getMockBuilder('Magento\Core\Model\Config\Base')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_cacheMock->expects($this->once())->method('load')->will($this->returnValue(false));
        $this->_factoryMock->expects($this->once())->method('create')->will($this->returnValue($mockConfigBase));
        $this->_loaderMock->expects($this->once())->method('load');
        $this->_cacheMock->expects($this->once())->method('save');
        $this->_resourcesConfigMock->expects($this->once())
            ->method('setConfig')
            ->with($this->equalTo($mockConfigBase));
        $this->_model->getConfiguration();
    }

    public function testRemoveCache()
    {
        $this->_cacheMock->expects($this->once())->method('clean');
        $this->_model->removeCache();
    }
}