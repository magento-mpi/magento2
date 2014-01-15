<?php
/**
 * Invalid state change exception
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Exception;

class InvalidStateChangeException extends \Magento\Exception\Exception
{
    const UNKNOWN = 0;
    const ALREADY_ACTIVE = 1;
    const CONFIRMATION_NOT_NEEDED = 2;
}
