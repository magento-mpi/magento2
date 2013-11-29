<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogPermissions\App\Backend;

use \Magento\CatalogPermissions\App\ConfigInterface;

/**
 * Global configs
 */
class Config implements ConfigInterface
{
    /**
     * Core store config
     *
     * @var \Magento\Core\Model\ConfigInterface
     */
    protected $_coreConfig;

    /**
     * @param \Magento\Core\Model\ConfigInterface $coreStoreConfig
     */
    public function __construct(\Magento\Core\Model\ConfigInterface $coreStoreConfig)
    {
        $this->_coreConfig = $coreStoreConfig;
    }

    /**
     * Check, whether permissions are enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_coreConfig->getValue(ConfigInterface::XML_PATH_ENABLED, 'default');
    }
}
