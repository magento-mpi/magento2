<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class, which overwrites native Magento cookie model in order to not set cookies during testing
 */
class Magento_Test_Cookie extends Mage_Core_Model_Cookie
{
    /**
     * Cookies collection
     *
     * @var array
     */
    protected $_cookies = array();

    /**
     * Dummy function instead of original that sets cookie.
     * This one does nothing - in test environment we cannot set cookies.
     * Function only collects them
     *
     * @param string $name The cookie name
     * @param string $value The cookie value
     * @param int $period Lifetime period
     * @param string $path
     * @param string $domain
     * @param int|bool $secure
     * @param bool $httponly
     * @return Magento_Test_Cookie
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function set($name, $value, $period = null, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        $this->_cookies[$name] = $value;
        return $this;
    }

    /**
     * Dummy function instead of original that gets cookie.
     * This one does nothing - in test environment we cannot get cookies.
     * Function only retrieve data from collected cookies
     *
     * @param string $name The cookie name
     * @return mixed
     */
    public function get($name = null)
    {
        return isset($this->_cookies[$name])? $this->_cookies[$name] : parent::get($name);
    }

    /**
     * Dummy function instead of original that deletes cookie. This one does nothing - in test environment we cannot
     * set cookies.
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     * @param int|bool $secure
     * @param int|bool $httponly
     * @return Magento_Test_Cookie
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function delete($name, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        unset($this->_cookies[$name]);
        return $this;
    }
}
