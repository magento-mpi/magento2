<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Stdlib\Cookie;

/**
 * CookieSizeLimitReachedException is thrown when detecting that a browser limit, or potential browser limit has been
 * reached regarding cookie limits.
 *
 * Limits can include the amount of data stored in an individual cookie as well as the number of cookies
 * set for the domain.
 */
class CookieSizeLimitReachedException extends \Exception
{

}
