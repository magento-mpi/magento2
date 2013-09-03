<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Store_Config implements Magento_Core_Model_Store_ConfigInterface
{
    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig = null;

    /**
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
    }

    /**
     * Retrieve store config value
     *
     * @param string $path
     * @param mixed $store
     * @return mixed
     */
    public function getConfig($path, $store = null)
    {
        return $this->_coreStoreConfig->getConfig($path, $store);
    }

    /**
     * Retrieve store config flag
     *
     * @param string $path
     * @param mixed $store
     * @return bool
     */
    public function getConfigFlag($path, $store = null)
    {
        return $this->_coreStoreConfig->getConfigFlag($path, $store);
    }
}
