<?php
namespace Magento\Exception;

class AuthorizationExceptionTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $authorizationException
            = new AuthorizationException('No access to delete this record.', AuthorizationException::NO_RECORD_ACCESS);

        $this->assertSame(AuthorizationException::NO_RECORD_ACCESS, $authorizationException->getCode());
        $this->assertStringStartsWith('No access', $authorizationException->getMessage());
    }
}
