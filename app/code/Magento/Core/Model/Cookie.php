<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model;

/**
 * Core cookie model
 */
class Cookie
{
    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_httpRequest;

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param Store\Config $coreStoreConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        Store\Config $coreStoreConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->_httpRequest = $request;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_storeManager = $storeManager;
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
     * Set cookie
     *
     * @param string $name The cookie name
     * @param string $value The cookie value
     * @param int $period Lifetime period
     * @param string $path
     * @param string $domain
     * @param bool|int|string $secure
     * @param bool|string $httponly
     * @return \Magento\Core\Model\Cookie
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
     * @return \Magento\Core\Model\Cookie
     */
    public function renew($name, $period = 0, $path = '', $domain = '', $secure = '', $httponly = '')
    {
        if ($period === null) {
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
     * @param string $name The cookie name
     * @return mixed
     */
    public function get($name = null)
    {
        return $this->_getRequest()->getCookie($name, false);
    }
}
