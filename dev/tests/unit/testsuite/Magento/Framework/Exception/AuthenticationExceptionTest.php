<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Exception;

/**
 * Class AuthenticationExceptionTest
 *
 * @package Magento\Framework\Exception
 */
class AuthenticationExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $authenticationException = new AuthenticationException(
            AuthenticationException::AUTHENTICATION_ERROR,
            ['consumer_id' => 1, 'resources' => 'record2']
        );
        $this->assertSame('An authentication error occurred.', $authenticationException->getMessage());
    }
} 