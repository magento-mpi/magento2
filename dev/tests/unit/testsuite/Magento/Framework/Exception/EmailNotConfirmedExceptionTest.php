<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Exception;

/**
 * Class EmailNotConfirmedExceptionTest
 *
 * @package Magento\Framework\Exception
 */
class EmailNotConfirmedExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $emailNotConfirmedException = new EmailNotConfirmedException(
            EmailNotConfirmedException::EMAIL_NOT_CONFIRMED,
            ['consumer_id' => 1, 'resources' => 'record2']
        );
        $this->assertSame('Email not confirmed', $emailNotConfirmedException->getMessage());
    }
}
