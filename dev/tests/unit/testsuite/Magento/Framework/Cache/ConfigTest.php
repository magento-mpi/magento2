<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Cache;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Cache\Config\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storage;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Cache\Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_storage = $this->getMock('Magento\Framework\Cache\Config\Data', array('get'), array(), '', false);
        $this->_model = new \Magento\Framework\Cache\Config($this->_storage);
    }

    public function testGetTypes()
    {
        $this->_storage->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            'types',
            array()
        )->will(
            $this->returnValue(array('val1', 'val2'))
        );
        $result = $this->_model->getTypes();
        $this->assertCount(2, $result);
    }

    public function testGetType()
    {
        $this->_storage->expects(
            $this->once()
        )->method(
            'get'
        )->with(
            'types/someType',
            array()
        )->will(
            $this->returnValue(array('someTypeValue'))
        );
        $result = $this->_model->getType('someType');
        $this->assertCount(1, $result);
    }
}
