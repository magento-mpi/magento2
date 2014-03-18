<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Model;

class Config implements \Magento\App\Config\ScopeConfigInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\App\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Store\Model\Resource\Store\Collection
     */
    protected $_storeCollection;

    /**
     * @var \Magento\Store\Model\Resource\Store\CollectionFactory
     */
    protected $_factory;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
    }

    /**
     * Retrieve store config value
     *
     * @param string $path
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return string|null
     */
    public function getConfig($path, $store = null)
    {
        return $this->_storeManager->getStore($store)->getConfig($path);
    }

    /**
     * Retrieve store config flag
     *
     * @param string $path
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function getConfigFlag($path, $store = null)
    {
        $flag = strtolower($this->getConfig($path, $store));
        return !empty($flag) && 'false' !== $flag;
    }
}
