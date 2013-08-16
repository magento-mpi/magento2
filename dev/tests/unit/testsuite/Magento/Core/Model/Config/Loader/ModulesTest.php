<?php
/**
 * Test class for Magento_Core_Model_Config_Loader_Modules
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Config_Loader_ModulesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Config_Loader_Modules
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_readerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_loaderMock;

    protected function setUp()
    {
        $this->_configMock = $this->getMock('Magento_Core_Model_Config_Primary', array(), array(), '', false, false);
        $this->_resourceMock = $this->getMock('Magento_Core_Model_Config_Resource', array(), array(), '', false, false);
        $this->_readerMock = $this->getMock('Magento_Core_Model_Config_Modules_Reader', array(), array(), '', false);
        $this->_loaderMock = $this->getMock('Magento_Core_Model_Config_Loader_Local', array(), array(), '', false);

        $this->_model = new Magento_Core_Model_Config_Loader_Modules(
            $this->_configMock,
            $this->_resourceMock,
            $this->_readerMock,
            $this->_loaderMock
        );
    }

    public function testLoad()
    {
        $configMock = $this->getMock('Magento_Core_Model_Config_Base', array(), array(), '', false);

        $configMock->expects($this->once())->method('extend')->with($this->_configMock);

        $this->_resourceMock->expects($this->any())
            ->method('getResourceConnectionModel')->with('core')->will($this->returnValue('mysql'));

        $this->_readerMock->expects($this->once())
            ->method('loadModulesConfiguration')
            ->with(array('config.xml', 'config.mysql.xml'), $configMock);

        $this->_loaderMock->expects($this->once())->method('load')->with($configMock);

        $configMock->expects($this->once())->method('applyExtends');

        $this->_resourceMock->expects($this->once())->method('setConfig')->with($configMock);

        $this->_model->load($configMock);
    }
}
