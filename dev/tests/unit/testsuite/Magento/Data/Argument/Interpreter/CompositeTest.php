<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Data\Argument\Interpreter;

class CompositeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Data\Argument\InterpreterInterface
     */
    protected $_interpreterOne;

    /**
     * @var \Magento\Data\Argument\InterpreterInterface
     */
    protected $_interpreterTwo;

    /**
     * @var Composite
     */
    protected $_model;

    protected function setUp()
    {
        $this->_interpreterOne = $this->getMock('Magento\Data\Argument\InterpreterInterface');
        $this->_interpreterTwo = $this->getMock('Magento\Data\Argument\InterpreterInterface');
        $this->_model = new Composite(
            array('one' => $this->_interpreterOne, 'two' => $this->_interpreterTwo),
            'interpreter'
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Interpreter named 'wrong' is expected to be an argument interpreter instance
     */
    public function testConstructWrongInterpreter()
    {
        $interpreters = array(
            'correct' => $this->getMock('Magento\Data\Argument\InterpreterInterface'),
            'wrong'   => $this->getMock('Magento\ObjectManager'),
        );
        new Composite($interpreters, 'interpreter');
    }

    /**
     * @param array $input
     * @param string $expectedExceptionMessage
     *
     * @dataProvider evaluateWrongDiscriminatorDataProvider
     */
    public function testEvaluateWrongDiscriminator($input, $expectedExceptionMessage)
    {
        $this->setExpectedException('\InvalidArgumentException', $expectedExceptionMessage);
        $this->_model->evaluate($input);
    }

    public function evaluateWrongDiscriminatorDataProvider()
    {
        return array(
            'no discriminator' => array(array(), 'Value for key "interpreter" is missing in the argument data'),
            'nonexistent interpreter ' => array(
                array('interpreter' => 'nonexistent'),
                "Argument interpreter named 'nonexistent' has not been defined"
            ),
        );
    }

    public function testEvaluate()
    {
        $input = array('interpreter' => 'one', 'value' => 'test');
        $expected = array('value' => 'test (updated)');

        $this->_interpreterOne->expects($this->once())
            ->method('evaluate')
            ->with(array('value' => 'test'))
            ->will($this->returnValue($expected));
        $this->assertSame($expected, $this->_model->evaluate($input));
    }

    public function testAddInterpreter()
    {
        $input = array('interpreter' => 'new', 'value' => 'test');
        $newInterpreter = $this->getMock('Magento\Data\Argument\InterpreterInterface');
        $this->_model->addInterpreter('new', $newInterpreter);
        $newInterpreter->expects($this->once())
            ->method('evaluate')
            ->with(array('value' => 'test'));
        $this->_model->evaluate($input);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Argument interpreter named 'one' has already been defined
     *
     */
    public function testAddInterpreterException()
    {
        $newInterpreter = $this->getMock('Magento\Data\Argument\InterpreterInterface');
        $this->_model->addInterpreter('one', $newInterpreter);
    }
}
