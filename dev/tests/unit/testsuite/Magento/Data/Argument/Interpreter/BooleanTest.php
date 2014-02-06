<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Data\Argument\Interpreter;

class BooleanTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Boolean
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Boolean();
    }

    /**
     * @dataProvider evaluateExceptionDataProvider
     */
    public function testEvaluateException($input, $expectedExceptionMessage)
    {
        $this->setExpectedException('\InvalidArgumentException', $expectedExceptionMessage);
        $this->_model->evaluate($input);
    }

    public function evaluateExceptionDataProvider()
    {
        return array(
            'no value' => array(array(), 'Boolean value is missing'),
            'non-boolean value' => array(
                array('value' => 'non-boolean'),
                'Value is expected to be boolean or boolean string'
            ),
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
            'boolean "true"'         => array(true, true),
            'boolean "false"'        => array(false, false),
            'boolean string "true"'  => array('true', true),
            'boolean string "false"' => array('false', false),
            'boolean numeric "1"'    => array(1, true),
            'boolean numeric "0"'    => array(0, false),
            'boolean string "1"'     => array('1', true),
            'boolean string "0"'     => array('0', false),
            'boolean string "on"'    => array('on', true),
            'boolean string "off"'   => array('off', false),
            'boolean string "yes"'   => array('yes', true),
            'boolean string "no"'    => array('no', false),
            'boolean empty string'   => array('', false),
        );
    }
}
