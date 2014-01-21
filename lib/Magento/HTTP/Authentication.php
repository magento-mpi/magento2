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
 * Library for working with HTTP authentication
 */
class Authentication
{
    /**
     * Request object
     *
     * @var \Magento\App\RequestInterface
     */
    protected $request;

    /**
     * Response object
     *
     * @var \Magento\App\ResponseInterface
     */
    protected $response;

    /**
     * @param \Magento\App\RequestInterface $httpRequest
     * @param \Magento\App\ResponseInterface $httpResponse
     */
    public function __construct(
        \Magento\App\RequestInterface $httpRequest,
        \Magento\App\ResponseInterface $httpResponse
    ) {
        $this->request = $httpRequest;
        $this->response = $httpResponse;
    }

    /**
     * Extract "login" and "password" credentials from HTTP-request
     *
     * Returns plain array with 2 items: login and password respectively
     *
     * @return array
     */
    public function getCredentials()
    {
        $server = $this->request->getServer();
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
        } elseif (!empty($server['HTTP_AUTHORIZATION'])) {
            /**
             * IIS Note: for HTTP authentication to work with IIS,
             * the PHP directive cgi.rfc2616_headers must be set to 0 (the default value).
             */
            $auth = $server['HTTP_AUTHORIZATION'];
            list($user, $pass) = explode(':', base64_decode(substr($auth, strpos($auth, " ") + 1)));
        } elseif (!empty($server['Authorization'])) {
            $auth = $server['Authorization'];
            list($user, $pass) = explode(':', base64_decode(substr($auth, strpos($auth, " ") + 1)));
        }

        return array($user, $pass);
    }

    /**
     * Set "auth failed" headers to the specified response object
     *
     * @param string $realm
     * @return void
     */
    public function setAuthenticationFailed($realm)
    {
        $this->response->setHeader('HTTP/1.1', '401 Unauthorized')
            ->setHeader('WWW-Authenticate', 'Basic realm="' . $realm . '"')
            ->setBody('<h1>401 Unauthorized</h1>');
    }
}
