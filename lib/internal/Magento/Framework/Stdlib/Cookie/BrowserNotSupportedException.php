<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Stdlib\Cookie;

/**
 * BrowserNotSupportedException is thrown when an operation cannot be performed because the request came from
 * a browser that doesn't support the required features or functions.
 *
 * This exception is only thrown if proper detection was possible.  There may be cases where the browser lacks proper
 * support for the feature or function, but this exception isn't thrown because the lack of support wasn't detected.
 */
class BrowserNotSupportedException extends \Exception
{

}
