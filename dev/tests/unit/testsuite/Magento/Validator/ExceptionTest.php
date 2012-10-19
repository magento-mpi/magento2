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
 * Test case for Magento_Validator_Exception
 */
class Magento_Validator_ExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Testing Magento_Validator_Exception::getMessage
     */
    public function testGetMessage()
    {
        $expectedMessage = 'error1' . PHP_EOL . 'error2' . PHP_EOL . 'error3';
        $messages = array(
            'field1' => array(
                'error1',
                'error2'
            ),
            'field2' => array(
                'error3'
            )
        );
        $exception = new Magento_Validator_Exception($messages);
        $this->assertEquals($expectedMessage, $exception->getMessage());
    }
}
