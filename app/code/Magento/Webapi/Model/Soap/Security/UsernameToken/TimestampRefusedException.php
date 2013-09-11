<?php
/**
 * \Exception for case of refused timestamp (SOAP WS-Security).
 *
 * {license_notice}
 *
 * @see http://docs.oasis-open.org/wss-m/wss/v1.1.1/os/wss-UsernameTokenProfile-v1.1.1-os.html
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Soap\Security\UsernameToken;

class TimestampRefusedException extends \RuntimeException
{
}
