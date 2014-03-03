<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ObjectManager\Config\Argument\Interpreter;

use Magento\Stdlib\BooleanUtils;

class ObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $data
     * @param string $className
     * @param bool $isShared
     * @dataProvider evaluateDataProvider
     */
    public function testEvaluate($data, $className, $isShared)
    {
        $expected = new \StdClass;
        $factory = $this->getMock(
            '\Magento\ObjectManager\Config\Argument\ObjectFactory',
            array('create'),
            array(),
            '',
            false
        );
        $factory->expects($this->once())
            ->method('create')
            ->with($className, $isShared)
            ->will($this->returnValue($expected))
        ;
        $interpreter = new Object(new BooleanUtils, $factory);
        $this->assertSame($expected, $interpreter->evaluate($data));
    }

    /**
     * @return array
     */
    public function evaluateDataProvider()
    {
        return array(
            array(array('value' => 'Class'), 'Class', false),
            array(array('value' => 'Class', 'shared' => false), 'Class', false),
            array(array('value' => 'Class', 'shared' => 0), 'Class', false),
            array(array('value' => 'Class', 'shared' => '0'), 'Class', false),
            array(array('value' => 'Class', 'shared' => 'false'), 'Class', false),
            array(array('value' => 'Class', 'shared' => true), 'Class', true),
            array(array('value' => 'Class', 'shared' => 1), 'Class', true),
            array(array('value' => 'Class', 'shared' => '1'), 'Class', true),
            array(array('value' => 'Class', 'shared' => 'true'), 'Class', true),
        );
    }

    /**
     * @param array $data
     * @dataProvider evaluateErrorDataProvider
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Object class name is missing.
     */
    public function testEvaluateNoClass($data)
    {
        $factory = $this->getMock('\Magento\ObjectManager\Config\Argument\ObjectFactory', array(), array(), '', false);
        $interpreter = new Object(new BooleanUtils, $factory);
        $interpreter->evaluate($data);
    }

    /**
     * @return array
     */
    public function evaluateErrorDataProvider()
    {
        return array(
            array(array()),
            array(array('value' => '')),
            array(array('value' => false)),
            array(array('value' => 0)),
        );
    }
} 
