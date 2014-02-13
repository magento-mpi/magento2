<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\App\Arguments;

class ArgumentInterpreterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Arguments\ArgumentInterpreter
     */
    private $object;

    /**
     * @var \Magento\App\Arguments|\PHPUnit_Framework_MockObject_MockObject
     */
    private $arguments;

    protected function setUp()
    {
        $this->arguments = $this->getMock('\Magento\App\Arguments', array('get'), array(), '', false);
        $const = $this->getMock('\Magento\Data\Argument\Interpreter\Constant', array('evaluate'), array(), '', false);
        $const->expects($this->once())
            ->method('evaluate')
            ->with(array('value' => 'FIXTURE_INIT_PARAMETER'))
            ->will($this->returnValue('init_param_value'))
        ;
        $this->object = new ArgumentInterpreter($this->arguments, $const);
    }

    public function testEvaluate()
    {
        $expected = 'test_value';
        $this->arguments->expects($this->once())
            ->method('get')
            ->with('init_param_value')
            ->will($this->returnValue($expected));
        $this->assertEquals($expected, $this->object->evaluate(array('value' => 'FIXTURE_INIT_PARAMETER')));
    }

    /**
     * @expectedException \Magento\Data\Argument\MissingOptionalValueException
     * @expectedExceptionMessage Value of application argument 'init_param_value' is not defined.
     */
    public function testEvaluateException()
    {
        $this->arguments->expects($this->once())
            ->method('get')
            ->with('init_param_value')
            ->will($this->returnValue(null));
        $this->object->evaluate(array('value' => 'FIXTURE_INIT_PARAMETER'));
    }
} 
