<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Data\Argument\Interpreter;

class ArrayTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Data\Argument\InterpreterInterface
     */
    protected $_interpreter;

    /**
     * @var ArrayType
     */
    protected $_model;

    protected function setUp()
    {
        $this->_interpreter = $this->getMockForAbstractClass('Magento\Data\Argument\InterpreterInterface');
        $this->_model = new ArrayType($this->_interpreter);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Array items are expected
     *
     * @dataProvider evaluateExceptionDataProvider
     */
    public function testEvaluateException($inputData)
    {
        $this->_model->evaluate($inputData);
    }

    public function evaluateExceptionDataProvider()
    {
        return array(
            'no item' => array(array()),
            'non-array item' => array(array('item' => 'non-array')),
        );
    }

    public function testEvaluate()
    {
        $this->_interpreter->expects($this->any())
            ->method('evaluate')
            ->will($this->returnCallback(function ($input) {
                return '-' . $input['value'] . '-';
            }));
        $input = array(array('value' => 'value 1'), array('value' => 'value 2'), array('value' => 'value 3'));
        $expected = array('-value 1-', '-value 2-', '-value 3-');
        $actual = $this->_model->evaluate(array('item' => $input));
        $this->assertSame($expected, $actual);
    }
}
