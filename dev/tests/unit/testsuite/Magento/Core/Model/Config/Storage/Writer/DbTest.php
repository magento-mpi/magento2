<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Core_Model_Config_Storage_Writer_Db
 */
class Magento_Core_Model_Config_Storage_Writer_DbTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Config_Storage_Writer_Db
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceMock;


    protected function setUp()
    {
        $this->_resourceMock = $this->getMock('Magento_Core_Model_Resource_Config', array(), array(), '', false, false);
        $this->_model = new Magento_Core_Model_Config_Storage_Writer_Db($this->_resourceMock);
    }

    protected function tearDown()
    {
        unset($this->_resourceMock);
        unset($this->_model);
    }

    public function testDelete()
    {
        $this->_resourceMock->expects($this->once())
            ->method('deleteConfig')
            ->with('test/path', 'store', 1);
        $this->_model->delete('test/path/', 'store', 1);
    }

    public function testDeleteWithDefaultParams()
    {
        $this->_resourceMock->expects($this->once())
            ->method('deleteConfig')
            ->with('test/path', Magento_Core_Model_Store::DEFAULT_CODE, 0);
        $this->_model->delete('test/path');
    }

    public function testSave()
    {
        $this->_resourceMock->expects($this->once())
            ->method('saveConfig')
            ->with('test/path', 'test_value', 'store', 1);
        $this->_model->save('test/path/', 'test_value', 'store', 1);
    }

    public function testSaveWithDefaultParams()
    {
        $this->_resourceMock->expects($this->once())
            ->method('saveConfig')
            ->with('test/path', 'test_value', Magento_Core_Model_Store::DEFAULT_CODE, 0);
        $this->_model->save('test/path', 'test_value');
    }
}
