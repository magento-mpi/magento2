<?php
/**
 * Page cache environment model. Provides access to global variables
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model;

class Environment
{
    /**
     * Get value from storage
     *
     * @param array $storage - possible values $_REQUEST, $_GET, $_POST, $_COOKIE, $_SERVER
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function _getValue($storage, $key, $default)
    {
        $output = $default;
        if (true === $this->_hasKey($storage, $key)) {
            $output = $storage[$key];
        }
        return $output;
    }

    /**
     * Check if storage has key
     *
     * @param array $storage
     * @param string $key
     * @return bool
     */
    protected function _hasKey($storage, $key)
    {
        return array_key_exists($key, $storage);
    }

    /**
     * Get cookie value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getCookie($key, $default = null)
    {
        return $this->_getValue($_COOKIE, $key, $default);
    }

    /**
     * Check is cookie has key
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasCookie($key)
    {
        return $this->_hasKey($_COOKIE, $key);
    }

    /**
     * Get server value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getServer($key, $default = null)
    {
        return $this->_getValue($_SERVER, $key, $default);
    }

    /**
     * Check is server has key
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasServer($key)
    {
        return $this->_hasKey($_SERVER, $key);
    }

    /**
     * Get request value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getRequest($key, $default = null)
    {
        return $this->_getValue($_REQUEST, $key, $default);
    }

    /**
     * Check is request has key
     *
     * @param string $key
     * @return bool
     */
    public function hasRequest($key)
    {
        return $this->_hasKey($_REQUEST, $key);
    }

    /**
     * Get post value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getPost($key, $default = null)
    {
        return $this->_getValue($_POST, $key, $default);
    }

    /**
     * Check is post has key
     *
     * @param string $key
     * @return bool
     */
    public function hasPost($key)
    {
        return $this->_hasKey($_POST, $key);
    }

    /**
     * Get query value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getQuery($key, $default = null)
    {
        return $this->_getValue($_GET, $key, $default);
    }

    /**
     * Check is query has key
     *
     * @param string $key
     * @return bool
     */
    public function hasQuery($key)
    {
        return $this->_hasKey($_GET, $key);
    }
}
