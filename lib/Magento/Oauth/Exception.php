<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Oauth;

/**
 * oAuth \Exception
 */
class Exception extends \Magento\Webapi\Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param int $httpCode
     * @param array $details
     * @param string $name
     */
    public function __construct(
        $message,
        $code = 0,
        $httpCode = self::HTTP_UNAUTHORIZED,
        array $details = array(),
        $name = 'oauth'
    ) {
        parent::__construct($message, $code, $httpCode, $details, null, $name);
    }
}
