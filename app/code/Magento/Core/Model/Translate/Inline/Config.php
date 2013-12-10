<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Inline Translation config
 */
namespace Magento\Core\Model\Translate\Inline;

class Config implements ConfigInterface
{
    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     */
    public function __construct(\Magento\Core\Model\Store\Config $coreStoreConfig)
    {
        $this->_coreStoreConfig = $coreStoreConfig;
    }

    /**
     * Check whether inline translation is enabled
     *
     * @param int|null $store
     * @return bool
     */
    public function isActive($store = null)
    {
        return $this->_coreStoreConfig->getConfigFlag('dev/translate_inline/active', $store);
    }
}
