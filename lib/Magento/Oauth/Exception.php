<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * oAuth \Exception
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Oauth;

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
        parent::__construct($message, $code, $httpCode, $details, $name);
    }
}
