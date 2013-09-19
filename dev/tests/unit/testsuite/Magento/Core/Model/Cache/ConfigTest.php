<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Cache;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Cache\Config\Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storage;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\Cache\Config\Data
     */
    protected $_model;

    public function setUp()
    {
        $this->_storage = $this->getMock('Magento\Core\Model\Cache\Config\Data', array('get'), array(), '', false);
        $this->_model = new \Magento\Core\Model\Cache\Config($this->_storage);
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
