<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Data\Argument\Interpreter;

class StringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Data\Argument\Interpreter\String
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_booleanUtils;

    protected function setUp()
    {
        $this->_booleanUtils = $this->getMock('\Magento\Framework\Stdlib\BooleanUtils');
        $this->_booleanUtils->expects(
            $this->any()
        )->method(
            'toBoolean'
        )->will(
            $this->returnValueMap(array(array('true', true), array('false', false)))
        );
        $this->_model = new String($this->_booleanUtils);
        $translateRenderer = $this->getMockForAbstractClass('Magento\Phrase\RendererInterface');
        $translateRenderer->expects($this->any())->method('render')->will(
            $this->returnCallback(
                function ($input) {
                    return end($input) . ' (translated)';
                }
            )
        );
        \Magento\Phrase::setRenderer($translateRenderer);
    }

    /**
     * @param array $input
     * @param bool $expected
     *
     * @dataProvider evaluateDataProvider
     */
    public function testEvaluate($input, $expected)
    {
        $actual = $this->_model->evaluate($input);
        $this->assertSame($expected, $actual);
    }

    public function evaluateDataProvider()
    {
        return array(
            'no value' => array(array(), ''),
            'with value' => array(array('value' => 'some value'), 'some value'),
            'translation required' => array(
                array('value' => 'some value', 'translate' => 'true'),
                'some value (translated)'
            ),
            'translation not required' => array(array('value' => 'some value', 'translate' => 'false'), 'some value')
        );
    }

    /**
     * @param array $input
     *
     * @dataProvider evaluateExceptionDataProvider
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage String value is expected
     */
    public function testEvaluateException($input)
    {
        $this->_model->evaluate($input);
    }

    public function evaluateExceptionDataProvider()
    {
        return array('not a string' => array(array('value' => 123)));
    }
}
