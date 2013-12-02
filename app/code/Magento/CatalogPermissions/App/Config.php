<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogPermissions\App;

/**
 * Global configs
 */
class Config implements ConfigInterface
{
    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\ConfigInterface
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig
     */
    public function __construct(\Magento\Core\Model\Store\ConfigInterface $coreStoreConfig)
    {
        $this->_coreStoreConfig = $coreStoreConfig;
    }

    /**
     * Check, whether permissions are enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_coreStoreConfig->getConfigFlag(ConfigInterface::XML_PATH_ENABLED);
    }
}
