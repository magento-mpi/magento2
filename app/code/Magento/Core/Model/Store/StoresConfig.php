<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Store;

class StoresConfig
{
    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\App\ConfigInterface
     */
    protected $_config;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\ConfigInterface $config
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\ConfigInterface $config
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
        /** @var $store \Magento\Core\Model\Store */
        foreach ($stores as $store) {
            $value = $this->_config->getValue($path, 'store', $store->getCode());
            $storeValues[$store->getId()] = $value;
        }
        return $storeValues;
    }
}
