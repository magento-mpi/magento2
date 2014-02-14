<?php
/**
 * Authentication exception
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Exception;

class AuthenticationException extends \Magento\Exception\Exception
{
    const UNKNOWN = 0;
    const EMAIL_NOT_CONFIRMED = 1;
    const INVALID_EMAIL_OR_PASSWORD = 2;
}
