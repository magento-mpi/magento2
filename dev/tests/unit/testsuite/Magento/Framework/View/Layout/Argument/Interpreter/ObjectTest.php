<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Layout\Argument\Interpreter;

class ObjectTest extends \PHPUnit_Framework_TestCase
{
    const EXPECTED_CLASS = 'Magento\Framework\View\Layout\Argument\Interpreter\ObjectTest';

    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Data\Argument\InterpreterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_interpreter;

    /**
     * @var Object
     */
    protected $_model;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\Framework\ObjectManager');
        $this->_model = new Object($this->_objectManager, self::EXPECTED_CLASS);
    }

    public function testEvaluate()
    {
        $input = array('value' => self::EXPECTED_CLASS);
        $this->_objectManager->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            self::EXPECTED_CLASS
        )->will(
            $this->returnValue($this)
        );

        $actual = $this->_model->evaluate($input);
        $this->assertSame($this, $actual);
    }

    /**
     * @dataProvider evaluateWrongClassDataProvider
     */
    public function testEvaluateWrongClass($input, $expectedException, $expectedExceptionMessage)
    {
        $this->setExpectedException($expectedException, $expectedExceptionMessage);
        $self = $this;
        $this->_objectManager->expects($this->any())->method('create')->will(
            $this->returnCallback(
                function ($className) use ($self) {
                    return $self->getMock($className);
                }
            )
        );

        $this->_model->evaluate($input);
    }

    public function evaluateWrongClassDataProvider()
    {
        return array(
            'no class' => array(array(), '\InvalidArgumentException', 'Object class name is missing'),
            'unexpected class' => array(
                array('value' => 'Magento\Framework\ObjectManager'),
                '\UnexpectedValueException',
                'Instance of ' . self::EXPECTED_CLASS . ' is expected'
            )
        );
    }
}
