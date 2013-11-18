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
 * Core cookie model
 *
 * @category   Magento
 * @package    Magento_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model;

class Cookie
{
    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_httpRequest;

    /**
     * @var \Magento\App\ResponseInterface
     */
    protected $_httpResponse;

    /**
     * Core store config
     *
     * @var \Magento\Session\ConfigInterface
     */
    protected $_sessionConfig;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\App\ResponseInterface $response
     * @param \Magento\Session\ConfigInterface $sessionConfig
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\App\ResponseInterface $response,
        \Magento\Session\ConfigInterface $sessionConfig
    ) {
        $this->_httpRequest = $request;
        $this->_httpResponse = $response;
        $this->_sessionConfig = $sessionConfig;
    }

    /**
     * Retrieve Request object
     *
     * @return \Magento\App\RequestInterface
     */
    protected function _getRequest()
    {
        return $this->_httpRequest;
    }

    /**
     * Retrieve Response object
     *
     * @return \Magento\App\ResponseInterface
     */
    protected function _getResponse()
    {
        return $this->_httpResponse;
    }

    /**
     * Retrieve Domain for cookie
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->_sessionConfig->getCookieDomain();
    }

    /**
     * Retrieve Path for cookie
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_sessionConfig->getCookiePath();
    }

    /**
     * Retrieve cookie lifetime
     *
     * @return int
     */
    public function getDefaultLifetime()
    {
        return $this->_sessionConfig->getCookieLifetime();
    }

    /**
     * Retrieve use HTTP only flag
     *
     * @return bool
     */
    public function getHttponly()
    {
        return $this->_sessionConfig->getCookieHttpOnly();
    }

    /**
     * Is https secure request
     * Use secure on adminhtml only
     *
     * @return bool
     */
    public function isSecure()
    {
        return $this->_sessionConfig->getCookieSecure();
    }

    /**
     * Set cookie
     *
     * @param string $name The cookie name
     * @param string $value The cookie value
     * @param int $period Lifetime period
     * @param string $path
     * @param string $domain
     * @param int|bool $secure
     * @param bool $httponly
     * @return \Magento\Core\Model\Cookie
     */
    public function set($name, $value, $period = null, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        /**
         * Check headers sent
         */
        if (!$this->_getResponse()->canSendHeaders(false)) {
            return $this;
        }

        if ($period === true) {
            $period = 3600 * 24 * 365;
        } elseif (is_null($period)) {
            $period = $this->getDefaultLifetime();
        }

        if ($period == 0) {
            $expire = 0;
        }
        else {
            $expire = time() + $period;
        }
        if (is_null($path)) {
            $path = $this->getPath();
        }
        if (is_null($domain)) {
            $domain = $this->getDomain();
        }
        if (is_null($secure)) {
            $secure = $this->isSecure();
        }
        if (is_null($httponly)) {
            $httponly = $this->getHttponly();
        }

        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);

        return $this;
    }

    /**
     * Postpone cookie expiration time if cookie value defined
     *
     * @param string $name The cookie name
     * @param int $period Lifetime period
     * @param string $path
     * @param string $domain
     * @param int|bool $secure
     * @return \Magento\Core\Model\Cookie
     */
    public function renew($name, $period = null, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        if (($period === null) && !$this->getDefaultLifetime()) {
            return $this;
        }
        $value = $this->_getRequest()->getCookie($name, false);
        if ($value !== false) {
            $this->set($name, $value, $period, $path, $domain, $secure, $httponly);
        }
        return $this;
    }

    /**
     * Retrieve cookie or false if not exists
     *
     * @param string $neme The cookie name
     * @return mixed
     */
    public function get($name = null)
    {
        return $this->_getRequest()->getCookie($name, false);
    }

    /**
     * Delete cookie
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     * @param int|bool $secure
     * @param int|bool $httponly
     * @return \Magento\Core\Model\Cookie
     */
    public function delete($name, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        /**
         * Check headers sent
         */
        if (!$this->_getResponse()->canSendHeaders(false)) {
            return $this;
        }

        if (is_null($path)) {
            $path = $this->getPath();
        }
        if (is_null($domain)) {
            $domain = $this->getDomain();
        }
        if (is_null($secure)) {
            $secure = $this->isSecure();
        }
        if (is_null($httponly)) {
            $httponly = $this->getHttponly();
        }

        setcookie($name, null, null, $path, $domain, $secure, $httponly);
        return $this;
    }
}
