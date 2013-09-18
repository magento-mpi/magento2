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
class Magento_TestFramework_Cookie extends Magento_Core_Model_Cookie
{
    /**
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_Core_Controller_Response_Http $response
     */
    public function __construct(
        Magento_Core_Controller_Request_Http $request = null,
        Magento_Core_Controller_Response_Http $response = null
    ) {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $request = $request ?: $objectManager->get('Magento_Core_Controller_Request_Http');
        $response = $response ?: $objectManager->get('Magento_Core_Controller_Response_Http');
        parent::__construct($request, $response);
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
     * @return Magento_TestFramework_Cookie
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
     * @return Magento_TestFramework_Cookie
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function delete($name, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        unset($_COOKIE[$name]);
        return $this;
    }
}
