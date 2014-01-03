<?php
/**
 * Input service exception
 * The top level data (code and message) is consistent across all Input Exceptions.
 * InputException is inherently build to contain aggregates.  All failure specifics are stored in params.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service\Exception;

class AuthorizationException extends \Magento\Service\Exception\Exception
{
    const NO_RECORD_ACCESS = 0;
}
