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
 * Test case for \Magento\Validator\ValidatorException
 */
class Magento_Validator_ExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Testing \Magento\Validator\ValidatorException::getMessage
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
        $exception = new \Magento\Validator\ValidatorException($messages);
        $this->assertEquals($expectedMessage, $exception->getMessage());
    }
}
