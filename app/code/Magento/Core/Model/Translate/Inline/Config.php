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

class Config implements \Magento\Translate\Inline\ConfigInterface
{
    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Helper\Data $helper
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Helper\Data $helper
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_helper = $helper;
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

    /**
     * Check whether allowed client ip for inline translation
     *
     * @param mixed $store
     * @return bool
     */
    public function isDevAllowed($store = null)
    {
        return $this->_helper->isDevAllowed($store);
    }
}
