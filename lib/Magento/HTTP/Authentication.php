<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\HTTP;

/**
 * Helper for working with HTTP authentication
 *
 */
class Authentication
{
    /**
     * Request object
     *
     * @var \Magento\Core\Controller\Request\HttpProxy
     */
    protected $_request;

    public function __construct(
        \Magento\Core\Controller\Request\HttpProxy $httpRequest
    ) {
        $this->_request = $httpRequest;
    }

    /**
     * Extract "login" and "password" credentials from HTTP-request
     *
     * Returns plain array with 2 items: login and password respectively
     *
     * @param \Magento\App\RequestInterface $request
     * @return array
     */
    public static function getCredentials(\Magento\App\RequestInterface $request)
    {
        $server = $request->getServer();
        $user = '';
        $pass = '';

        if (empty($server['HTTP_AUTHORIZATION'])) {
            foreach ($server as $k => $v) {
                if (substr($k, -18) === 'HTTP_AUTHORIZATION' && !empty($v)) {
                    $server['HTTP_AUTHORIZATION'] = $v;
                    break;
                }
            }
        }

        if (isset($server['PHP_AUTH_USER']) && isset($server['PHP_AUTH_PW'])) {
            $user = $server['PHP_AUTH_USER'];
            $pass = $server['PHP_AUTH_PW'];
        }
        /**
         * IIS Note: for HTTP authentication to work with IIS,
         * the PHP directive cgi.rfc2616_headers must be set to 0 (the default value).
         */
        elseif (!empty($server['HTTP_AUTHORIZATION'])) {
            $auth = $server['HTTP_AUTHORIZATION'];
            list($user, $pass) = explode(':', base64_decode(substr($auth, strpos($auth, " ") + 1)));
        }
        elseif (!empty($server['Authorization'])) {
            $auth = $server['Authorization'];
            list($user, $pass) = explode(':', base64_decode(substr($auth, strpos($auth, " ") + 1)));
        }

        return array($user, $pass);
    }

    /**
     * Set "auth failed" headers to the specified response object
     *
     * @param \Magento\App\ResponseInterface $response
     * @param string $realm
     */
    public static function setAuthenticationFailed(\Magento\App\ResponseInterface $response, $realm)
    {
        $response->setHeader('HTTP/1.1', '401 Unauthorized')
            ->setHeader('WWW-Authenticate', 'Basic realm="' . $realm . '"')
            ->setBody('<h1>401 Unauthorized</h1>')
        ;
    }
}
