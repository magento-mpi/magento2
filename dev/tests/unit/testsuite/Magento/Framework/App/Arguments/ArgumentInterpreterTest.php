<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Arguments;

class ArgumentInterpreterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\Arguments\ArgumentInterpreter
     */
    private $object;

    protected function setUp()
    {
        $const = $this->getMock('\Magento\Framework\Data\Argument\Interpreter\Constant', array('evaluate'), array(), '', false);
        $const->expects(
            $this->once()
        )->method(
            'evaluate'
        )->with(
            array('value' => 'FIXTURE_INIT_PARAMETER')
        )->will(
            $this->returnValue('init_param_value')
        );
        $this->object = new ArgumentInterpreter($const);
    }

    public function testEvaluate()
    {
        $expected = array('argument' => 'init_param_value');
        $this->assertEquals($expected, $this->object->evaluate(array('value' => 'FIXTURE_INIT_PARAMETER')));
    }
}
