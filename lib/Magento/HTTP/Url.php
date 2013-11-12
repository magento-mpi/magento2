<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\HTTP;

class Url
{
    /** @var \Magento\Core\Model\StoreManagerInterface */
    protected $_storeManager;

    public function __construct(\Magento\Core\Model\StoreManagerInterface $storeManager)
    {
        $this->_storeManager = $storeManager;
    }

    /**
     * check if URL corresponds store
     *
     * @param string $url
     * @return bool
     */
    public function isInternal($url)
    {
        if (strpos($url, 'http') === false) {
            return false;
        }

        /**
         * Url must start from base secure or base unsecure url
         */
        /** @var $store \Magento\Core\Model\Store */
        $store = $this->_storeManager->getStore();
        $unsecure = (strpos($url, $store->getBaseUrl()) === 0);
        $secure = (strpos($url, $store->getBaseUrl($store::URL_TYPE_LINK, true)) === 0);
        return $unsecure || $secure;
    }
}