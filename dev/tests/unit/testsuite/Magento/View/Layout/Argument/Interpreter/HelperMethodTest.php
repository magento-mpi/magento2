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
            'helper' => __CLASS__ . '::help'
        );

        $evaluatedValue = array('value' => 'some text (evaluated)');
        $this->_interpreter->expects($this->once())
            ->method('evaluate')
            ->with($input)
            ->will($this->returnValue($evaluatedValue));

        $this->_objectManager->expects($this->once())
            ->method('get')
            ->with(__CLASS__)
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
     * @param string $helperMethod
     * @param string $expectedExceptionMessage
     *
     * @dataProvider evaluateExceptionDataProvider
     */
    public function testEvaluateException($helperMethod, $expectedExceptionMessage)
    {
        $this->setExpectedException('\InvalidArgumentException', $expectedExceptionMessage);
        $input = array(
            'value' => 'some text',
            'helper' => $helperMethod
        );
        $this->_model->evaluate($input);
    }

    public function evaluateExceptionDataProvider()
    {
        $nonExistingHelper = __CLASS__ . '::non_existing';
        return array(
            'wrong method format' => array(
                'help', 'Helper method name in format "\Class\Name::methodName" is expected'
            ),
            'non-existing method' => array(
                $nonExistingHelper, "Helper method '$nonExistingHelper' does not exist"
            ),
        );
    }
}
