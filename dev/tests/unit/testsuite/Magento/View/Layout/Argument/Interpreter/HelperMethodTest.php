<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Argument\Interpreter;

class HelperMethodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var \Magento\View\Layout\Argument\Interpreter\NamedParams|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_interpreter;

    /**
     * @var HelperMethod
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
        $this->_interpreter = $this->getMock(
            'Magento\View\Layout\Argument\Interpreter\NamedParams', array(), array(), '', false
        );
        $this->_model = new HelperMethod($this->_objectManager, $this->_interpreter);
    }

    public function testEvaluate()
    {
        $input = array(
            'value' => 'some text',
            'helper' => 'Magento\View\Layout\Argument\Interpreter\Decorator\HelperTest::help'
        );

        $evaluatedValue = array('value' => 'some text (evaluated)');
        $this->_interpreter->expects($this->once())
            ->method('evaluate')
            ->with($input)
            ->will($this->returnValue($evaluatedValue));

        $this->_objectManager->expects($this->once())
            ->method('get')
            ->with('Magento\View\Layout\Argument\Interpreter\Decorator\HelperTest')
            ->will($this->returnValue($this));

        $expected = 'some text (evaluated) (updated)';
        $actual = $this->_model->evaluate($input);
        $this->assertSame($expected, $actual);
    }

    public function help($input)
    {
        $this->assertSame('some text (evaluated)', $input);
        return $input . ' (updated)';
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Helper method name in format "\Class\Name::methodName" is expected
     */
    public function testEvaluateWrongHelper()
    {
        $input = array(
            'value' => 'some text',
            'helper' => 'Helper'
        );
        $this->_model->evaluate($input);
    }
}
