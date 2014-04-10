<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Model;

use Magento\Store\Model\StoreManagerInterface;

class StoresConfig
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_config;

    /**
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $config
    ) {
        $this->_storeManager = $storeManager;
        $this->_config = $config;
    }

    /**
     * Retrieve store Ids for $path with checking
     *
     * return array($storeId => $pathValue)
     *
     * @param string $path
     * @return array
     */
    public function getStoresConfigByPath($path)
    {
        $stores = $this->_storeManager->getStores(true);
        $storeValues = array();
        /** @var $store \Magento\Store\Model\Store */
        foreach ($stores as $store) {
            $value = $this->_config->getValue($path, ScopeInterface::SCOPE_STORE, $store->getCode());
            $storeValues[$store->getId()] = $value;
        }
        return $storeValues;
    }
}
