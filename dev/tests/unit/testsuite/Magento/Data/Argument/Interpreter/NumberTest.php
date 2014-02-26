<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Data\Argument\Interpreter;

class NumberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Number
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Number();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Numeric value is expected
     *
     * @dataProvider evaluateExceptionDataProvider
     */
    public function testEvaluateException($input)
    {
        $this->_model->evaluate($input);
    }

    public function evaluateExceptionDataProvider()
    {
        return array(
            'no value' => array(array()),
            'non-numeric value' => array(array('value' => 'non-numeric')),
        );
    }

    /**
     * @param array $input
     * @param bool $expected
     *
     * @dataProvider evaluateDataProvider
     */
    public function testEvaluate($input, $expected)
    {
        $actual = $this->_model->evaluate(array('value' => $input));
        $this->assertSame($expected, $actual);
    }

    public function evaluateDataProvider()
    {
        return array(
            'integer'                  => array(10, 10),
            'float'                    => array(10.5, 10.5),
            'string numeric (integer)' => array('10', '10'),
            'string numeric (float)'   => array('10.5', '10.5'),
        );
    }
}
