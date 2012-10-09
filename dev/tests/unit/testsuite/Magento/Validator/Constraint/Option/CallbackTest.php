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
     * Test getValue on existing callback object
     */
    public function testGetValueExistingInstanceCallback()
    {
        $object = new Varien_Object(array('id' => 10));
        $option = new Magento_Validator_Constraint_Option_Callback($object, 'getId');
        $this->assertEquals(10, $option->getValue());
    }

    /**
     * Test getValue on new callback object
     */
    public function testGetValueExistingNewCallback()
    {
        $option = new Magento_Validator_Constraint_Option_Callback('Callback_Stub', 'getMax');
        $this->assertEquals(3, $option->getValue());
    }

    /**
     * Test getValue exception on not existing class
     *
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Class "Not_Existing_Callback_Class" was not found
     */
    public function testGetValueUnknownClassException()
    {
        $option = new Magento_Validator_Constraint_Option_Callback('Not_Existing_Callback_Class', 'someMethod');
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
        $option = new Magento_Validator_Constraint_Option_Callback('Callback_Stub', 'notExistingMethod');
        $option->getValue();
    }
}

/**
 * Stub class for testing callback
 */
class Callback_Stub
{
    /**
     * @return int
     */
    public function getMax()
    {
        return 3;
    }
}
