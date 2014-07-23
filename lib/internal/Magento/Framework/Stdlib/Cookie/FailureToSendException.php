<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Stdlib\Cookie;

/**
 * FailureToSendException is thrown when trying to set a cookie but the response has already been sent, making it
 * impossible to send any cookie information back to the client.
 */
class FailureToSendException extends \Exception
{

}
