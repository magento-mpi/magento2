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
class Magento_Core_Helper_Http extends Magento_Core_Helper_Abstract
{
    const XML_NODE_REMOTE_ADDR_HEADERS  = 'global/remote_addr_headers';

    /**
     * Remote address cache
     *
     * @var string
     */
    protected $_remoteAddr;

    /**
     * Core string
     *
     * @var Magento_Core_Helper_String
     */
    protected $_coreString = null;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * @param Magento_Core_Helper_String $coreString
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Core_Helper_String $coreString,
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Config $coreConfig
    ) {
        $this->_coreString = $coreString;
        $this->_coreConfig = $coreConfig;
        parent::__construct($context);
    }

    /**
     * Extract "login" and "password" credentials from HTTP-request
     *
     * Returns plain array with 2 items: login and password respectively
     *
     * @param Zend_Controller_Request_Http $request
     * @return array
     */
    public function getHttpAuthCredentials(Zend_Controller_Request_Http $request)
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
     * @param Zend_Controller_Response_Http $response
     * @param string $realm
     */
    public function failHttpAuthentication(Zend_Controller_Response_Http $response, $realm)
    {
        $response->setHeader('HTTP/1.1', '401 Unauthorized')
            ->setHeader('WWW-Authenticate', 'Basic realm="' . $realm . '"')
            ->setBody('<h1>401 Unauthorized</h1>')
        ;
    }

    /**
     * Retrieve Remote Addresses Additional check Headers
     *
     * @return array
     */
    public function getRemoteAddrHeaders()
    {
        $headers = array();
        $element = $this->_coreConfig->getNode(self::XML_NODE_REMOTE_ADDR_HEADERS);
        if ($element instanceof Magento_Core_Model_Config_Element) {
            foreach ($element->children() as $node) {
                $headers[] = (string)$node;
            }
        }

        return $headers;
    }

    /**
     * Retrieve Client Remote Address
     *
     * @param bool $ipToLong converting IP to long format
     * @return string IPv4|long
     */
    public function getRemoteAddr($ipToLong = false)
    {
        if (is_null($this->_remoteAddr)) {
            $headers = $this->getRemoteAddrHeaders();
            foreach ($headers as $var) {
                if ($this->_getRequest()->getServer($var, false)) {
                    $this->_remoteAddr = $_SERVER[$var];
                    break;
                }
            }

            if (!$this->_remoteAddr) {
                $this->_remoteAddr = $this->_getRequest()->getServer('REMOTE_ADDR');
            }
        }

        if (!$this->_remoteAddr) {
            return false;
        }

        return $ipToLong ? ip2long($this->_remoteAddr) : $this->_remoteAddr;
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
     * Retrieve HTTP "clean" value
     *
     * @param string $var
     * @param boolean $clean clean non UTF-8 characters
     * @return string
     */
    protected function _getHttpCleanValue($var, $clean = true)
    {
        $value = $this->_getRequest()->getServer($var, '');
        if ($clean) {
            $value = $this->_coreString->cleanString($value);
        }

        return $value;
    }

    /**
     * Retrieve HTTP HOST
     *
     * @param boolean $clean clean non UTF-8 characters
     * @return string
     */
    public function getHttpHost($clean = true)
    {
        return $this->_getHttpCleanValue('HTTP_HOST', $clean);
    }

    /**
     * Retrieve HTTP USER AGENT
     *
     * @param boolean $clean clean non UTF-8 characters
     * @return string
     */
    public function getHttpUserAgent($clean = true)
    {
        return $this->_getHttpCleanValue('HTTP_USER_AGENT', $clean);
    }

    /**
     * Retrieve HTTP ACCEPT LANGUAGE
     *
     * @param boolean $clean clean non UTF-8 characters
     * @return string
     */
    public function getHttpAcceptLanguage($clean = true)
    {
        return $this->_getHttpCleanValue('HTTP_ACCEPT_LANGUAGE', $clean);
    }

    /**
     * Retrieve HTTP ACCEPT CHARSET
     *
     * @param boolean $clean clean non UTF-8 characters
     * @return string
     */
    public function getHttpAcceptCharset($clean = true)
    {
        return $this->_getHttpCleanValue('HTTP_ACCEPT_CHARSET', $clean);
    }

    /**
     * Retrieve HTTP REFERER
     *
     * @param boolean $clean clean non UTF-8 characters
     * @return string
     */
    public function getHttpReferer($clean = true)
    {
        return $this->_getHttpCleanValue('HTTP_REFERER', $clean);
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
