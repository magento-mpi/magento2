<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Core Http Helper
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Helper;

class Http extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * Core string
     *
     * @var \Magento\Core\Helper\String
     */
    protected $_coreString = null;

    /**
     * @param \Magento\Core\Helper\String $coreString
     * @param \Magento\Core\Helper\Context $context
     */
    public function __construct(
        \Magento\Core\Helper\String $coreString,
        \Magento\Core\Helper\Context $context
    ) {
        $this->_coreString = $coreString;
        parent::__construct($context);
    }

    /**
     * Extract "login" and "password" credentials from HTTP-request
     *
     * Returns plain array with 2 items: login and password respectively
     *
     * @param \Zend_Controller_Request_Http $request
     * @return array
     */
    public function getHttpAuthCredentials(\Zend_Controller_Request_Http $request)
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
     * @param \Zend_Controller_Response_Http $response
     * @param string $realm
     */
    public function failHttpAuthentication(\Zend_Controller_Response_Http $response, $realm)
    {
        $response->setHeader('HTTP/1.1', '401 Unauthorized')
            ->setHeader('WWW-Authenticate', 'Basic realm="' . $realm . '"')
            ->setBody('<h1>401 Unauthorized</h1>')
        ;
    }

    /**
     * Retrieve Server IP address
     *
     * @param bool $ipToLong converting IP to long format
     * @return string IPv4|long
     */
    public function getServerAddr($ipToLong = false)
    {
        $address = $this->_getRequest()->getServer('SERVER_ADDR');
        if (!$address) {
            return false;
        }
        return $ipToLong ? ip2long($address) : $address;
    }

    /**
     * Returns the REQUEST_URI taking into account
     * platform differences between Apache and IIS
     *
     * @param boolean $clean clean non UTF-8 characters
     * @return string
     */
    public function getRequestUri($clean = false)
    {
        $uri = $this->_getRequest()->getRequestUri();
        if ($clean) {
            $uri = $this->_coreString->cleanString($uri);
        }
        return $uri;
    }

    /**
     * Validate IP address
     *
     * @param string $address
     * @return boolean
     */
    public function validateIpAddr($address)
    {
        return preg_match('#^(1?\d{1,2}|2([0-4]\d|5[0-5]))(\.(1?\d{1,2}|2([0-4]\d|5[0-5]))){3}$#', $address);
    }
}
