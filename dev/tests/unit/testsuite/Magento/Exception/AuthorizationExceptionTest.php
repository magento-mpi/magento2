<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Exception;

class AuthorizationExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $authorizationException = new AuthorizationException(
            AuthorizationException::NOT_AUTHORIZED,
            ['consumer_id' => 1, 'resources' => 'record2']
        );
        $this->assertSame('Consumer ID 1 is not authorized to access record2', $authorizationException->getMessage());
    }
}
