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
 * oAuth Exception
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Oauth_Exception extends Magento_Webapi_Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param int $httpCode
     * @param array $details
     */
    public function __construct($message, $code = 0, $httpCode = self::HTTP_UNAUTHORIZED, array $details = array())
    {
        parent::__construct($message, $code, $httpCode, $details);
    }
}
