<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PersistentHistory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Persistent helper
 */
namespace Magento\PersistentHistory\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    const XML_PATH_PERSIST_WISHLIST             = 'persistent/options/wishlist';
    const XML_PATH_PERSIST_ORDERED_ITEMS        = 'persistent/options/recently_ordered';
    const XML_PATH_PERSIST_COMPARE_PRODUCTS     = 'persistent/options/compare_current';
    const XML_PATH_PERSIST_COMPARED_PRODUCTS    = 'persistent/options/compare_history';
    const XML_PATH_PERSIST_VIEWED_PRODUCTS      = 'persistent/options/recently_viewed';
    const XML_PATH_PERSIST_CUSTOMER_AND_SEGM    = 'persistent/options/customer';

    /**
     * Name of config file
     *
     * @var string
     */
    protected $_configFileName = 'persistent.xml';

    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Config $coreConfig
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Config $coreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Retrieve path for config file
     *
     * @return string
     */
    public function getPersistentConfigFilePath()
    {
        return $this->_coreConfig->getModuleDir('etc', $this->_getModuleName()) . DS . $this->_configFileName;
    }

    /**
     * Check whether wishlist is persist
     *
     * @param int|string|\Magento\Core\Model\Store $store
     * @return bool
     */
    public function isWishlistPersist($store = null)
    {
        return $this->_coreStoreConfig->getConfigFlag(self::XML_PATH_PERSIST_WISHLIST, $store);
    }

    /**
     * Check whether ordered items is persist
     *
     * @param int|string|\Magento\Core\Model\Store $store
     * @return bool
     */
    public function isOrderedItemsPersist($store = null)
    {
        return $this->_coreStoreConfig->getConfigFlag(self::XML_PATH_PERSIST_ORDERED_ITEMS, $store);
    }

    /**
     * Check whether compare products is persist
     *
     * @param int|string|\Magento\Core\Model\Store $store
     * @return bool
     */
    public function isCompareProductsPersist($store = null)
    {
        return $this->_coreStoreConfig->getConfigFlag(self::XML_PATH_PERSIST_COMPARE_PRODUCTS, $store);
    }

    /**
     * Check whether compared products is persist
     *
     * @param int|string|\Magento\Core\Model\Store $store
     * @return bool
     */
    public function isComparedProductsPersist($store = null)
    {
        return $this->_coreStoreConfig->getConfigFlag(self::XML_PATH_PERSIST_COMPARED_PRODUCTS, $store);
    }

    /**
     * Check whether viewed products is persist
     *
     * @param int|string|\Magento\Core\Model\Store $store
     * @return bool
     */
    public function isViewedProductsPersist($store = null)
    {
        return $this->_coreStoreConfig->getConfigFlag(self::XML_PATH_PERSIST_VIEWED_PRODUCTS, $store);
    }

    /**
     * Check whether customer and segments is persist
     *
     * @param int|string|\Magento\Core\Model\Store $store
     * @return bool
     */
    public function isCustomerAndSegmentsPersist($store = null)
    {
        return $this->_coreStoreConfig->getConfigFlag(self::XML_PATH_PERSIST_CUSTOMER_AND_SEGM, $store);
    }
}
