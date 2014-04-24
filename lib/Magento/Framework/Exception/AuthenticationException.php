<?php
/**
 * Authentication exception
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Exception;

class AuthenticationException extends \Magento\Framework\Exception\Exception
{
    const UNKNOWN = 0;

    const EMAIL_NOT_CONFIRMED = 1;

    const INVALID_EMAIL_OR_PASSWORD = 2;
}
