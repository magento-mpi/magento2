<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Cache_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Cache_Config_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storage;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|Magento_Core_Model_Cache_Config_Data
     */
    protected $_model;

    public function setUp()
    {
        $this->_storage = $this->getMock('Magento_Core_Model_Cache_Config_Data', array('get'), array(), '', false);
        $this->_model = new Magento_Core_Model_Cache_Config($this->_storage);
    }

    public function testGetTypes()
    {
        $this->_storage->expects($this->once())->method('get')->with('types', array())
            ->will($this->returnValue(array('val1' , 'val2')));
        $result = $this->_model->getTypes();
        $this->assertCount(2, $result);
    }

    public function testGetType()
    {
        $this->_storage->expects($this->once())->method('get')->with('types/someType', array())
            ->will($this->returnValue(array('someTypeValue')));
        $result = $this->_model->getType('someType');
        $this->assertCount(1, $result);
    }
}
