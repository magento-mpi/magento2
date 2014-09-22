<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Validator;

use \Magento\Framework\Exception\InputException;

/**
 * Test case for \Magento\Framework\Validator\ValidatorException
 */
class ExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Testing \Magento\Framework\Validator\ValidatorException::getMessage
     */
    public function testGetMessage()
    {
        $expectedMessage = 'error1' . PHP_EOL . 'error2' . PHP_EOL . 'error3';
        $messages = array('field1' => array('error1', 'error2'), 'field2' => array('error3'));
        $exception = new \Magento\Framework\Validator\ValidatorException(
            InputException::DEFAULT_MESSAGE,
            [],
            null,
            $messages
        );
        $this->assertEquals($expectedMessage, $exception->getMessage());
    }
}
