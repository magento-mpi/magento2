<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cookie;

interface Manager
{
    /**
     * Postpone cookie expiration time if cookie value defined
     *
     * @param string $name The cookie name
     * @param int $period Lifetime period
     * @param string $path
     * @param string $domain
     * @param int|bool $secure
     * @param int|bool $httponly
     * @return \Magento\Cookie\Manager
     */
    public function renew($name, $period = null, $path = null, $domain = null, $secure = null, $httponly = null);

    /**
     * Retrieve cookie or false if not exists
     *
     * @param string $name The cookie name
     * @return mixed
     */
    public function get($name = null);

    /**
     * Delete cookie
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     * @param int|bool $secure
     * @param int|bool $httponly
     * @return \Magento\Cookie\Manager
     */
    public function delete($name, $path = null, $domain = null, $secure = null, $httponly = null);

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
     * @return \Magento\Cookie\Manager
     */
    public function set($name, $value, $period = null, $path = null, $domain = null, $secure = null, $httponly = null);
}