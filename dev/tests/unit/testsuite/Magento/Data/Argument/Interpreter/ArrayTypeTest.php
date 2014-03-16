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
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Data\Argument\InterpreterInterface
     */
    protected $_itemInterpreter;

    /**
     * @var ArrayType
     */
    protected $_model;

    protected function setUp()
    {
        $this->_itemInterpreter = $this->getMockForAbstractClass('Magento\Data\Argument\InterpreterInterface');
        $this->_model = new ArrayType($this->_itemInterpreter);
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
            'non-array item' => array(array('item' => 'non-array')),
        );
    }

    /**
     * @param array $input
     * @param array $expected
     *
     * @dataProvider evaluateDataProvider
     */
    public function testEvaluate(array $input, array $expected)
    {
        $this->_itemInterpreter->expects($this->any())
            ->method('evaluate')
            ->will($this->returnCallback(function ($input) {
                return '-' . $input['value'] . '-';
            }));
        $actual = $this->_model->evaluate($input);
        $this->assertSame($expected, $actual);
    }

    public function evaluateDataProvider()
    {
        return array(
            'empty array items' => array(
                array('item' => array()),
                array(),
            ),
            'absent array items' => array(
                array(),
                array(),
            ),
            'present array items' => array(
                array(
                    'item' => array(
                        'key1' => array('value' => 'value 1'),
                        'key2' => array('value' => 'value 2'),
                        'key3' => array('value' => 'value 3'),
                    ),
                ),
                array(
                    'key1' => '-value 1-',
                    'key2' => '-value 2-',
                    'key3' => '-value 3-',
                ),
            ),
        );
    }
}
