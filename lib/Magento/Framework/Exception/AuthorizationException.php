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

class AuthorizationException extends LocalizedException
{
    const NOT_AUTHORIZED = 'Consumer ID %consumer_id is not authorized to access %resources';
}
