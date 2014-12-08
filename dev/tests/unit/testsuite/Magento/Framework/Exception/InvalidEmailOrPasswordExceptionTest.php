<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Exception;

/**
 * Class InvalidEmailOrPasswordExceptionTest
 *
 * @package Magento\Framework\Exception
 */
class InvalidEmailOrPasswordExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $exception = new InvalidEmailOrPasswordException(
            InvalidEmailOrPasswordException::INVALID_EMAIL_OR_PASSWORD,
            ['consumer_id' => 1, 'resources' => 'record2']
        );
        $this->assertSame('Invalid email or password', $exception->getMessage());
    }
}
