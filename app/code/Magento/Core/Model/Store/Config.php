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

class Config implements \Magento\Core\Model\Store\ConfigInterface
{
    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
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
        return $this->_storeManager->getStore($store)->getConfig($path);
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
        $flag = strtolower($this->getConfig($path, $store));
        return !empty($flag) && 'false' !== $flag;
    }
}
