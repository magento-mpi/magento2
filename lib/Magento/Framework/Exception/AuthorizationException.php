<?php
/**
 * Authorization service exception
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Exception;

class AuthorizationException extends \Magento\Framework\Exception\Exception
{
    const UNAUTHENTICATED_USER = 0;

    const NO_RECORD_ACCESS = 1;
}
