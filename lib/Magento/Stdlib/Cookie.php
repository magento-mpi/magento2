<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Stdlib;

/**
 * Core cookie model
 */
class Cookie
{
    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_httpRequest;

    /**
     * @param \Magento\App\RequestInterface $request
     */
    public function __construct(
        \Magento\App\RequestInterface $request
    ) {
        $this->_httpRequest = $request;
    }

    /**
     * Set cookie
     *
     * @param string $name The cookie name
     * @param string $value The cookie value
     * @param int $period Lifetime period
     * @param string $path
     * @param string $domain
     * @param bool|int|string $secure
     * @param bool|string $httponly
     * @return \Magento\Stdlib\Cookie
     */
    public function set($name, $value, $period = 0, $path = '', $domain = '', $secure = '', $httponly = '')
    {
        /**
         * Check headers sent
         */
        if (headers_sent()) {
            return $this;
        }

        if ($period === true) {
            $period = 3600 * 24 * 365;
        }

        if ($period == 0) {
            $expire = 0;
        } else {
            $expire = time() + $period;
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
     * @param bool|int|string $secure
     * @param string|bool $httponly
     * @return \Magento\Stdlib\Cookie
     */
    public function renew($name, $period = 0, $path = '', $domain = '', $secure = '', $httponly = '')
    {
        if ($period === null) {
            return $this;
        }
        $value = $this->_httpRequest->getCookie($name, false);
        if ($value !== false) {
            $this->set($name, $value, $period, $path, $domain, $secure, $httponly);
        }
        return $this;
    }

    /**
     * Retrieve cookie or false if not exists
     *
     * @param string $name The cookie name
     * @return mixed
     */
    public function get($name = null)
    {
        return $this->_httpRequest->getCookie($name, false);
    }
}
