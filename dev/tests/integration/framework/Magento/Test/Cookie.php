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
 * Replacement for the native cookie model that doesn't send cookie headers in testing environment
 */
class Magento_Test_Cookie extends Mage_Core_Model_Cookie
{
    /**
     * Request instance
     *
     * @var Magento_Test_Request
     */
    private $_request;

    /**
     * Response instance
     *
     * @var Magento_Test_Response
     */
    private $_response;

    /**
     * Retrieve a request instance suitable for the testing environment
     *
     * @return Magento_Test_Request
     */
    protected function _getRequest()
    {
        if (!$this->_request) {
            $this->_request = new Magento_Test_Request();
        }
        return $this->_request;
    }

    /**
     * Retrieve a request instance suitable for the testing environment
     *
     * @return Magento_Test_Response
     */
    protected function _getResponse()
    {
        if (!$this->_response) {
            $this->_response = new Magento_Test_Response();
        }
        return $this->_response;
    }

    /**
     * Dummy function, which sets value directly to $_COOKIE super-global array instead of calling setcookie()
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
        $_COOKIE[$name] = $value;
        return $this;
    }

    /**
     * Dummy function, which removes value directly from $_COOKIE super-global array instead of calling setcookie()
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
        unset($_COOKIE[$name]);
        return $this;
    }
}
