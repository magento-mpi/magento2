<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Object;

class MapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Object\Mapper
     */
    protected $mapper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fromMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $toMock;

    protected function setUp()
    {
        $this->fromMock = $this->getMock('Magento\Object', array(), array(), '', false);
        $this->toMock = $this->getMock('Magento\Object', array(), array(), '', false);
        $this->mapper = new \Magento\Object\Mapper();
    }

    public function testAccumulateByMapWhenToIsArrayFromIsObject()
    {
        $map['key'] = 'value';
        $to['key'] = 'value';
        $default['new_key'] = 'value';
        $this->fromMock->expects($this->once())->method('hasData')->with('key')->will($this->returnValue(true));
        $this->fromMock->expects($this->once())->method('getData')->with('key')->will($this->returnValue('value'));
        $expected['key'] = 'value';
        $expected['value'] = 'value';
        $expected['new_key'] = 'value';
        $this->assertEquals($expected, $this->mapper->accumulateByMap($this->fromMock, $to, $map, $default));
    }

    public function testAccumulateByMapWhenToAndFromAreObjects()
    {
        $from = array(
            $this->fromMock,
            'getData'
        );
        $to = array(
            $this->toMock,
            'setData'
        );
        $default = array(0);
        $map['key'] = array('value');
        $this->fromMock->expects($this->once())->method('hasData')->with('key')->will($this->returnValue(false));
        $this->fromMock->expects($this->once())->method('getData')->with('key')->will($this->returnValue(true));
        $this->assertEquals($this->toMock, $this->mapper->accumulateByMap($from, $to, $map, $default));

    }

    public function testAccumulateByMapWhenFromIsArrayToIsObject()
    {
        $map['key'] = 'value';
        $from['key'] = 'value';
        $default['new_key'] = 'value';
        $this->toMock->expects($this->exactly(2))->method('setData');
        $this->assertEquals($this->toMock, $this->mapper->accumulateByMap($from, $this->toMock, $map, $default));

    }

    public function testAccumulateByMapFromAndToAreArrays()
    {
        $from['value'] = 'value';
        $map[false] = 'value';
        $to['key'] = 'value';
        $default['new_key'] = 'value';
        $expected['key'] = 'value';
        $expected['value'] = 'value';
        $expected['new_key'] = 'value';
        $this->assertEquals($expected, $this->mapper->accumulateByMap($from, $to, $map, $default));

    }
}
