<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout\Argument\Interpreter;

class OptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var \Magento\Data\Argument\InterpreterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_interpreter;

    /**
     * @var Options
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
        $this->_model = new Options($this->_objectManager);
    }

    public function testEvaluate()
    {
        $modelClass = 'Magento\Data\OptionSourceInterface';
        $model = $this->getMockForAbstractClass($modelClass);
        $model->expects($this->once())
            ->method('toOptionArray')
            ->will($this->returnValue(array(
                'value1' => 'label 1',
                'value2' => 'label 2',
                array('value' => 'value3', 'label' => 'label 3'),
            )));
        $this->_objectManager->expects($this->once())
            ->method('get')
            ->with($modelClass)
            ->will($this->returnValue($model));
        $input = array('model' => $modelClass);
        $expected = array(
            array('value' => 'value1', 'label' => 'label 1'),
            array('value' => 'value2', 'label' => 'label 2'),
            array('value' => 'value3', 'label' => 'label 3'),
        );
        $actual = $this->_model->evaluate($input);
        $this->assertSame($expected, $actual);
    }

    /**
     * @dataProvider evaluateWrongModelDataProvider
     */
    public function testEvaluateWrongModel($input, $expectedException, $expectedExceptionMessage)
    {
        $this->setExpectedException($expectedException, $expectedExceptionMessage);
        $this->_model->evaluate($input);
    }

    public function evaluateWrongModelDataProvider()
    {
        return array(
            'no model' => array(
                array(),
                '\InvalidArgumentException',
                'Options source model class is missing',
            ),
            'wrong model class' => array(
                array('model' => 'Magento\View\Layout\Argument\Interpreter\OptionsTest'),
                '\UnexpectedValueException',
                'Instance of the options source model is expected',
            ),
        );
    }
}
