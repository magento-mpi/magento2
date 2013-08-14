<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Core_Model_Config_Loader_Db
 */
class Magento_Core_Model_Config_Loader_DbTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Config_Loader_Db
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dbUpdaterMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modulesConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    protected function setUp()
    {
        $this->_modulesConfigMock = $this->getMock('Magento_Core_Model_Config_Modules',
            array(), array(), '', false, false
        );
        $this->_dbUpdaterMock = $this->getMock('Magento_Core_Model_Db_UpdaterInterface',
            array(), array(), '', false, false
        );
        $this->_resourceMock = $this->getMock(
            'Magento_Core_Model_Resource_Config', array(), array(), '', false, false
        );
        $this->_factoryMock = $this->getMock(
            'Magento_Core_Model_Config_BaseFactory', array(), array(), '', false, false
        );

        $this->_model = new Magento_Core_Model_Config_Loader_Db(
            $this->_modulesConfigMock,
            $this->_resourceMock,
            $this->_dbUpdaterMock,
            $this->_factoryMock
        );
    }

    protected function tearDown()
    {
        unset($this->_dbUpdaterMock);
        unset($this->_modulesConfigMock);
        unset($this->_resourceMock);
        unset($this->_factoryMock);
        unset($this->_model);
    }

    public function testLoadWithReadConnection()
    {
        $this->_resourceMock->expects($this->once())->method('getReadConnection')->will($this->returnValue(true));
        $this->_dbUpdaterMock->expects($this->once())->method('updateScheme');

        $configData = new Magento_Simplexml_Config();
        $configMock = $this->getMock('Magento_Core_Model_Config_Base', array(), array(), '', false, false);
        $this->_modulesConfigMock->expects($this->once())->method('getNode')->will($this->returnValue('config_node'));
        $this->_factoryMock->expects($this->once())->method('create')
            ->with('config_node')
            ->will($this->returnValue($configData));

        $configMock->expects($this->once())->method('extend')->with($configData);

        $this->_resourceMock->expects($this->once())->method('loadToXml')->with($configMock);

        $this->_model->load($configMock);
    }

    public function testLoadWithoutReadConnection()
    {
        $this->_resourceMock->expects($this->once())->method('getReadConnection')->will($this->returnValue(false));
        $this->_dbUpdaterMock->expects($this->never())->method('updateScheme');

        $configMock = $this->getMock('Magento_Core_Model_Config_Base', array(), array(), '', false, false);
        $configMock->expects($this->never())->method('extend');
        $this->_resourceMock->expects($this->never())->method('loadToXml');

        $this->_model->load($configMock);
    }
}
