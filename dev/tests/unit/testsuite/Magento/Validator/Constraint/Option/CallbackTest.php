<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Validator
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test case for \Magento\Validator\Constraint\Option\Callback
 */
namespace Magento\Validator\Constraint\Option;

class CallbackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Value for test
     */
    const TEST_VALUE = 'test';

    /**
     * Test getValue method
     *
     * @dataProvider getValueDataProvider
     *
     * @param callable $callback
     * @param mixed $expectedResult
     * @param null $arguments
     * @param bool $createInstance
     */
    public function testGetValue($callback, $expectedResult, $arguments = null, $createInstance = false)
    {
        $option = new \Magento\Validator\Constraint\Option\Callback($callback, $arguments, $createInstance);
        $this->assertEquals($expectedResult, $option->getValue());
    }

    /**
     * Data provider for testGetValue
     */
    public function getValueDataProvider()
    {
        $functionName = create_function('', 'return "Value from function";');
        $closure = function () {
            return 'Value from closure';
        };

        $mock = $this->getMockBuilder('Foo')
            ->setMethods(array('getValue'))
            ->getMock();
        $mock->expects($this->once())
            ->method('getValue')
            ->with('arg1', 'arg2')
            ->will($this->returnValue('Value from mock'));

        return array(
            array($functionName, 'Value from function'),
            array($closure, 'Value from closure'),
            array(array($this, 'getTestValue'), self::TEST_VALUE),
            array(array(__CLASS__, 'getTestValueStatically'), self::TEST_VALUE),
            array(array($mock, 'getValue'), 'Value from mock', array('arg1', 'arg2')),
            array(array('Magento\Validator\Test\Callback', 'getId'), \Magento\Validator\Test\Callback::ID, null, true)
        );
    }

    /**
     * Get TEST_VALUE from static scope
     */
    static public function getTestValueStatically()
    {
        return self::TEST_VALUE;
    }

    /**
     * Get TEST_VALUE
     */
    public function getTestValue()
    {
        return self::TEST_VALUE;
    }

    /**
     * Test setArguments method
     *
     * @dataProvider setArgumentsDataProvider
     *
     * @param mixed $value
     * @param mixed $expectedValue
     */
    public function testSetArguments($value, $expectedValue)
    {
        $option = new \Magento\Validator\Constraint\Option\Callback(
            function () {
            }
        );
        $option->setArguments($value);
        $this->assertAttributeEquals($expectedValue, '_arguments', $option);
    }

    /**
     * Data provider for testGetValue
     */
    public function setArgumentsDataProvider()
    {
        return array(
            array('baz', array('baz')),
            array(array('foo', 'bar'), array('foo', 'bar'))
        );
    }

    /**
     * Test getValue method raises \InvalidArgumentException
     *
     * @dataProvider getValueExceptionDataProvider
     *
     * @param mixed $callback
     * @param string $expectedMessage
     * @param bool $createInstance
     */
    public function testGetValueException($callback, $expectedMessage, $createInstance = false)
    {
        $option = new \Magento\Validator\Constraint\Option\Callback($callback, null, $createInstance);
        $this->setExpectedException('InvalidArgumentException', $expectedMessage);
        $option->getValue();
    }

    /**
     * Data provider for testGetValueException
     *
     * @return array
     */
    public function getValueExceptionDataProvider()
    {
        return array(
            array(
                array('Not_Existing_Callback_Class', 'someMethod'),
                'Class "Not_Existing_Callback_Class" was not found'
            ),
            array(
                array($this, 'notExistingMethod'),
                'Callback does not callable'
            ),
            array(
                array('object' => $this, 'method' => 'getTestValue'),
                'Callback does not callable'
            ),
            array(
                'unknown_function',
                'Callback does not callable'
            ),
            array(
                new \stdClass(),
                'Callback does not callable'
            ),
            array(
                array($this, 'getTestValue'),
                'Callable expected to be an array with class name as first element',
                true,
            )
        );
    }
}
