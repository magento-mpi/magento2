<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Data\Argument\Interpreter;

class StringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Number
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new String();
        $translateRenderer = $this->getMockForAbstractClass('Magento\Phrase\RendererInterface');
        $translateRenderer->expects($this->any())
            ->method('render')
            ->will($this->returnCallback(function ($input) {
                return $input . ' (translated)';
            }));
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
                array('value' => 'some value', 'translate' => true),
                'some value (translated)'
            ),
            'translation not required' => array(array('value' => 'some value', 'translate' => false), 'some value'),
        );
    }
}
