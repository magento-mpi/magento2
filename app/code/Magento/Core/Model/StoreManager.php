<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model;

class StoreManager implements \Magento\Core\Model\StoreManagerInterface
{
    /**
     * Store storage factory model
     *
     * @var \Magento\Core\Model\Store\StorageFactory
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
     * @param Store\StorageFactory $factory
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Core\Helper\Data $helper
     * @param string $scopeCode
     * @param string $scopeType
     */
    public function __construct(
        \Magento\Core\Model\Store\StorageFactory $factory,
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
     * @return \Magento\Core\Model\Store\StorageInterface
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
     * Retrieve application store object without Store_Exception
     *
     * @param string|int|\Magento\Core\Model\Store $storeId
     * @throws \Magento\Core\Exception
     * @return \Magento\Core\Model\Store
     */
    public function getSafeStore($storeId = null)
    {
        try {
            return $this->getStore($storeId);
        } catch (\Exception $e) {
            if ($this->_getStorage()->getCurrentStore()) {
                $this->_request->setActionName('noroute');
                return new \Magento\Object();
            }

            throw new \Magento\Core\Exception(__('Requested invalid store "%1"', $storeId));
        }
    }

    /**
     * Set current default store
     *
     * @param string $store
     */
    public function setCurrentStore($store)
    {
        $this->_currentStore = $store;
        $this->_getStorage()->setCurrentStore($store);
    }

    /**
     * @throws \Magento\Core\Model\Store\Exception
     */
    public function throwStoreException()
    {
        $this->_getStorage()->throwStoreException();
    }

    /**
     * Allow or disallow single store mode
     *
     * @param bool $value
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
     * @param null|string|bool|int|\Magento\Core\Model\Store $storeId
     * @return \Magento\Core\Model\Store
     * @throws \Magento\Core\Model\Store\Exception
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
     * @return \Magento\Core\Model\Store[]
     */
    public function getStores($withDefault = false, $codeKey = false)
    {
        return $this->_getStorage()->getStores($withDefault, $codeKey);
    }

    /**
     * Retrieve application website object
     *
     * @param null|bool|int|string|\Magento\Core\Model\Website $websiteId
     * @return \Magento\Core\Model\Website
     * @throws \Magento\Core\Exception
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
     * @return \Magento\Core\Model\Website[]
     */
    public function getWebsites($withDefault = false, $codeKey = false)
    {
        return $this->_getStorage()->getWebsites($withDefault, $codeKey);
    }

    /**
     * Reinitialize store list
     */
    public function reinitStores()
    {
        $this->_getStorage()->reinitStores();
    }

    /**
     * Retrieve default store for default group and website
     *
     * @return \Magento\Core\Model\Store
     */
    public function getDefaultStoreView()
    {
        return $this->_getStorage()->getDefaultStoreView();
    }

    /**
     * Retrieve application store group object
     *
     * @param null|\Magento\Core\Model\Store\Group|string $groupId
     * @return \Magento\Core\Model\Store\Group
     * @throws \Magento\Core\Exception
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
     * @return \Magento\Core\Model\Store\Group[]
     */
    public function getGroups($withDefault = false, $codeKey = false)
    {
        return $this->_getStorage()->getGroups($withDefault, $codeKey);
    }

    /**
     *  Unset website by id from app cache
     *
     * @param null|bool|int|string|\Magento\Core\Model\Website $websiteId
     */
    public function clearWebsiteCache($websiteId = null)
    {
        $this->_getStorage()->clearWebsiteCache($websiteId);
    }

    /**
     * Get either default or any store view
     *
     * @return \Magento\Core\Model\Store|null
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
