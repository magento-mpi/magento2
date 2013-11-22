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
namespace Magento\TestFramework;

class Cookie extends \Magento\Core\Model\Cookie
{
    /**
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\StoreManager $storeManager,
        \Magento\App\RequestInterface $request = null,
        \Magento\App\ResponseInterface $response = null
    ) {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $request = $request ?: $objectManager->get('Magento\App\RequestInterface');
        $response = $response ?: $objectManager->get('Magento\App\ResponseInterface');
        parent::__construct($request, $response, $coreStoreConfig, $storeManager);
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
     * @return \Magento\TestFramework\Cookie
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function set($name, $value, $period = 0, $path = '', $domain = '', $secure = '', $httponly = '')
    {
        $_COOKIE[$name] = $value;
        return $this;
    }
}
