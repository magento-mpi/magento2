<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Store\Model;

class StoreManager implements \Magento\Store\Model\StoreManagerInterface
{
    /**
     * Store storage factory model
     *
     * @var \Magento\Store\Model\StorageFactory
     */
    protected $_factory;

    /**
     * Event manager
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Request model
     *
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * Default store code
     *
     * @var string
     */
    protected $_currentStore = null;

    /**
     * Flag is single store mode allowed
     *
     * @var bool
     */
    protected $_isSingleStoreAllowed = true;

    /**
     * Requested scope code
     *
     * @var string
     */
    protected $_scopeCode;

    /**
     * Requested scope type
     *
     * @var string
     */
    protected $_scopeType;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Magento\Store\Model\StorageFactory $factory
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Core\Helper\Data $helper
     * @param string $scopeCode
     * @param string $scopeType
     */
    public function __construct(
        \Magento\Store\Model\StorageFactory $factory,
        \Magento\App\RequestInterface $request,
        \Magento\Core\Helper\Data $helper,
        $scopeCode = '',
        $scopeType = 'store'
    ) {
        $this->_factory = $factory;
        $this->_request = $request;
        $this->_scopeCode = $scopeCode;
        $this->_scopeType = $scopeType ?: self::SCOPE_TYPE_STORE;
        $this->_helper = $helper;
    }

    /**
     * Get storage instance
     *
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    protected function _getStorage()
    {
        $arguments = array(
            'isSingleStoreAllowed' => $this->_isSingleStoreAllowed,
            'currentStore' => $this->_currentStore,
            'scopeCode' => $this->_scopeCode,
            'scopeType' => $this->_scopeType,
        );
        return $this->_factory->get($arguments);
    }

    /**
     * Set current default store
     *
     * @param string $store
     * @return void
     */
    public function setCurrentStore($store)
    {
        $this->_currentStore = $store;
        $this->_getStorage()->setCurrentStore($store);
    }

    /**
     * @return void
     * @throws \Magento\Store\Model\Exception
     */
    public function throwStoreException()
    {
        $this->_getStorage()->throwStoreException();
    }

    /**
     * Allow or disallow single store mode
     *
     * @param bool $value
     * @return void
     */
    public function setIsSingleStoreModeAllowed($value)
    {
        $this->_isSingleStoreAllowed = $value;
        $this->_getStorage()->setIsSingleStoreModeAllowed($value);
    }

    /**
     * Check if store has only one store view
     *
     * @return bool
     */
    public function hasSingleStore()
    {
        return $this->_getStorage()->hasSingleStore();
    }

    /**
     * Check if system is run in the single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->hasSingleStore() && $this->_helper->isSingleStoreModeEnabled();
    }

    /**
     * Retrieve application store object
     *
     * @param null|string|bool|int|Store $storeId
     * @return Store
     * @throws \Magento\Store\Model\Exception
     */
    public function getStore($storeId = null)
    {
        return $this->_getStorage()->getStore($storeId);
    }

    /**
     * Retrieve stores array
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return Store[]
     */
    public function getStores($withDefault = false, $codeKey = false)
    {
        return $this->_getStorage()->getStores($withDefault, $codeKey);
    }

    /**
     * Retrieve application website object
     *
     * @param null|bool|int|string|Website $websiteId
     * @return Website
     * @throws \Magento\Store\Model\Exception
     */
    public function getWebsite($websiteId = null)
    {
        return $this->_getStorage()->getWebsite($websiteId);
    }

    /**
     * Get loaded websites
     *
     * @param bool $withDefault
     * @param bool|string $codeKey
     * @return Website[]
     */
    public function getWebsites($withDefault = false, $codeKey = false)
    {
        return $this->_getStorage()->getWebsites($withDefault, $codeKey);
    }

    /**
     * Reinitialize store list
     *
     * @return void
     */
    public function reinitStores()
    {
        $this->_getStorage()->reinitStores();
    }

    /**
     * Retrieve default store for default group and website
     *
     * @return Store
     */
    public function getDefaultStoreView()
    {
        return $this->_getStorage()->getDefaultStoreView();
    }

    /**
     * Retrieve application store group object
     *
     * @param null|\Magento\Store\Model\Store|string $groupId
     * @return \Magento\Store\Model\Store
     * @throws \Magento\Store\Model\Exception
     */
    public function getGroup($groupId = null)
    {
        return $this->_getStorage()->getGroup($groupId);
    }

    /**
     * Prepare array of store groups
     * can be filtered to contain default store group or not by $withDefault flag
     * depending on flag $codeKey array keys can be group id or group code
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return \Magento\Store\Model\Store[]
     */
    public function getGroups($withDefault = false, $codeKey = false)
    {
        return $this->_getStorage()->getGroups($withDefault, $codeKey);
    }

    /**
     *  Unset website by id from app cache
     *
     * @param null|bool|int|string|Website $websiteId
     * @return void
     */
    public function clearWebsiteCache($websiteId = null)
    {
        $this->_getStorage()->clearWebsiteCache($websiteId);
    }

    /**
     * Get either default or any store view
     *
     * @return Store|null
     */
    public function getAnyStoreView()
    {
        return $this->_getStorage()->getAnyStoreView();
    }

    /**
     * Get current store code
     *
     * @return string
     */
    public function getCurrentStore()
    {
        return $this->_getStorage()->getCurrentStore();
    }
}
