<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Validator
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test case for Magento_Validator_Constraint_Option_Callback
 */
class Magento_Validator_Constraint_Option_CallbackTest extends PHPUnit_Framework_TestCase
{
    /**
     * Value for test
     */
    const TEST_VALUE = 'test';

    /**
     * Test callback with 2 arguments passed
     */
    public function testSetArgumentsAsArray()
    {
        $callbackMock = $this->getMockBuilder('stdClass')
            ->setMethods(array('checkArguments'))
            ->getMock();
        $callbackMock->expects($this->once())
            ->method('checkArguments')
            ->with(true, 'test')
            ->will($this->returnValue(1));

        $option = new Magento_Validator_Constraint_Option_Callback(array($callbackMock, 'checkArguments'),
            array(true, 'test'));
        $this->assertEquals(1, $option->getValue());
    }

    /**
     * Test callback with 1 arguments passed
     */
    public function testSetArgumentsAsSingleValue()
    {
        $callbackMock = $this->getMockBuilder('stdClass')
            ->setMethods(array('checkArguments'))
            ->getMock();
        $callbackMock->expects($this->once())
            ->method('checkArguments')
            ->with('test')
            ->will($this->returnValue(1));

        $option = new Magento_Validator_Constraint_Option_Callback(array($callbackMock, 'checkArguments'), 'test');
        $this->assertEquals(1, $option->getValue());
    }

    /**
     * Test getValue on class callback
     */
    public function testGetValueFromClassStaticMethod()
    {
        $option = new Magento_Validator_Constraint_Option_Callback(array(
            __CLASS__, 'getTestValueStatically'
        ));
        $this->assertEquals(self::TEST_VALUE, $option->getValue());
    }

    /**
     * Get TEST_VALUE from static scope
     */
    static public function getTestValueStatically()
    {
        return self::TEST_VALUE;
    }

    /**
     * Test getValue exception on not existing class
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Class "Not_Existing_Callback_Class" was not found
     */
    public function testGetValueUnknownClassException()
    {
        $option = new Magento_Validator_Constraint_Option_Callback(array('Not_Existing_Callback_Class', 'someMethod'));
        $option->getValue();
    }

    /**
     * Test getValue exception on invalid callback
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Callback does not callable
     */
    public function testGetValueNotCallableException()
    {
        $option = new Magento_Validator_Constraint_Option_Callback(array($this, 'notExistingMethod'));
        $option->getValue();
    }
}
