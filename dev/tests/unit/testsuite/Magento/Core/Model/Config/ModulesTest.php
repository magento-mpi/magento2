<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_ModulesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Config_Modules
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storageMock;

    protected function setUp()
    {
        $this->_configMock = $this->getMock('Magento_Core_Model_ConfigInterface');
        $this->_storageMock = $this->getMock('Magento_Core_Model_Config_StorageInterface');
        $this->_storageMock->expects($this->any())->method('getConfiguration')
            ->will($this->returnValue($this->_configMock));
        $this->_model = new Magento_Core_Model_Config_Modules($this->_storageMock);
    }

    protected function tearDown()
    {
        unset($this->_configMock);
        unset($this->_storageMock);
        unset($this->_model);
    }

    public function testGetNode()
    {
        $path = 'some_path';
        $result = 'some_result';
        $this->_configMock->expects($this->once())->method('getNode')->with($path)->will($this->returnValue($result));
        $this->assertEquals($result, $this->_model->getNode($path));
    }

    public function testSetNode()
    {
        $path = 'some_path';
        $value = 'some_value';
        $this->_configMock->expects($this->once())->method('setNode')
            ->with($path, $value, true);
        $this->_model->setNode($path, $value);
    }

    public function testGetXpath()
    {
        $path = 'some_path';
        $result = 'some_result';
        $this->_configMock->expects($this->once())->method('getXpath')->with($path)->will($this->returnValue($result));
        $this->assertEquals($result, $this->_model->getXpath($path));
    }
}
