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

class Data extends \Magento\App\Helper\AbstractHelper
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
     * @var \Magento\Module\Dir\Reader
     */
    protected $_modulesReader;

    /**
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Module\Dir\Reader $modulesReader
     * @param \Magento\App\Config\ScopeConfigInterface $storeConfig
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Module\Dir\Reader $modulesReader,
        \Magento\App\Config\ScopeConfigInterface $storeConfig
    ) {
        parent::__construct($context);
        $this->_modulesReader = $modulesReader;
        $this->_storeConfig = $storeConfig;
    }

    /**
     * Retrieve path for config file
     *
     * @return string
     */
    public function getPersistentConfigFilePath()
    {
        return $this->_modulesReader->getModuleDir('etc', $this->_getModuleName()) . '/' . $this->_configFileName;
    }

    /**
     * Check whether wishlist is persist
     *
     * @param int|string|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function isWishlistPersist($store = null)
    {
        return $this->_storeConfig->isSetFlag(self::XML_PATH_PERSIST_WISHLIST, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $store);
    }

    /**
     * Check whether ordered items is persist
     *
     * @param int|string|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function isOrderedItemsPersist($store = null)
    {
        return $this->_storeConfig->isSetFlag(self::XML_PATH_PERSIST_ORDERED_ITEMS, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $store);
    }

    /**
     * Check whether compare products is persist
     *
     * @param int|string|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function isCompareProductsPersist($store = null)
    {
        return $this->_storeConfig->isSetFlag(self::XML_PATH_PERSIST_COMPARE_PRODUCTS, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $store);
    }

    /**
     * Check whether compared products is persist
     *
     * @param int|string|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function isComparedProductsPersist($store = null)
    {
        return $this->_storeConfig->isSetFlag(self::XML_PATH_PERSIST_COMPARED_PRODUCTS, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $store);
    }

    /**
     * Check whether viewed products is persist
     *
     * @param int|string|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function isViewedProductsPersist($store = null)
    {
        return $this->_storeConfig->isSetFlag(self::XML_PATH_PERSIST_VIEWED_PRODUCTS, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $store);
    }

    /**
     * Check whether customer and segments is persist
     *
     * @param int|string|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function isCustomerAndSegmentsPersist($store = null)
    {
        return $this->_storeConfig->isSetFlag(self::XML_PATH_PERSIST_CUSTOMER_AND_SEGM, \Magento\Store\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $store);
    }
}
