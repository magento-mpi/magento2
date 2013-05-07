<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Enterprise_Queue_Model_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var Enterprise_queue_Model_Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_model = new Enterprise_Queue_Model_Config($this->_configMock);
    }

    protected function tearDown()
    {
        unset($this->_configMock);
        unset($this->_model);
    }

    public function testGetTaskParamsReturnsArrayOfConfiguration()
    {
        $expected = array('test' => 1);
        $nodeMock = $this->getMock('stdClass', array('asArray'), array(), '', false);
        $nodeMock->expects($this->once())->method('asArray')->will($this->returnValue($expected));
        $this->_configMock->expects($this->once())->method('getNode')->will($this->returnValue($nodeMock));
        $this->assertEquals($expected, $this->_model->getTaskParams());
    }

    public function testGetTaskWithoutConfigReturnsEmptyArray()
    {
        $this->_configMock->expects($this->once())->method('getNode')->will($this->returnValue(false));
        $this->assertEquals(array(), $this->_model->getTaskParams());
    }
}
