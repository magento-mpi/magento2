<?php
/**
 * Store loader
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Store\Model\Storage;

use Magento\App\State;
use Magento\Store\Model\Store;
use Magento\Store\Model\Group;
use Magento\Store\Model\Group\Factory;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\Website;
use Magento\Store\Model\Website\Factory as WebsiteFactory;

class Db implements \Magento\Store\Model\StoreManagerInterface
{
    /**
     * Flag that shows that system has only one store view
     *
     * @var bool
     */
    protected $_hasSingleStore;

    /**
     * Flag is single store mode allowed
     *
     * @var bool
     */
    protected $_isSingleStoreAllowed = true;

    /**
     * Application store object
     *
     * @var Store
     */
    protected $_store;

    /**
     * Stores cache
     *
     * @var Store[]
     */
    protected $_stores = array();

    /**
     * Application website object
     *
     * @var Website
     */
    protected $_website;

    /**
     * Websites cache
     *
     * @var Website[]
     */
    protected $_websites = array();

    /**
     * Groups cache
     *
     * @var Group[]
     */
    protected $_groups = array();

    /**
     * Config model
     *
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_config;

    /**
     * Default store code
     *
     * @var string
     */
    protected $_currentStore = null;

    /**
     * Store factory
     *
     * @var StoreFactory
     */
    protected $_storeFactory;

    /**
     * Website factory
     *
     * @var WebsiteFactory
     */
    protected $_websiteFactory;

    /**
     * Group factory
     *
     * @var Factory
     */
    protected $_groupFactory;

    /**
     * Cookie model
     *
     * @var \Magento\Stdlib\Cookie
     */
    protected $_cookie;

    /**
     * Application state model
     *
     * @var State
     */
    protected $_appState;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_helper;

    /**
     * @param StoreFactory $storeFactory
     * @param WebsiteFactory $websiteFactory
     * @param Factory $groupFactory
     * @param \Magento\App\Config\ScopeConfigInterface $config
     * @param \Magento\Stdlib\Cookie $cookie
     * @param State $appState
     * @param \Magento\Backend\Model\UrlInterface $url
     * @param \Magento\Core\Helper\Data $helper
     * @param bool $isSingleStoreAllowed
     * @param null $currentStore
     */
    public function __construct(
        StoreFactory $storeFactory,
        WebsiteFactory $websiteFactory,
        Factory $groupFactory,
        \Magento\App\Config\ScopeConfigInterface $config,
        \Magento\Stdlib\Cookie $cookie,
        State $appState,
        \Magento\Backend\Model\UrlInterface $url,
        \Magento\Core\Helper\Data $helper,
        $isSingleStoreAllowed,
        $currentStore = null
    ) {
        $this->_storeFactory = $storeFactory;
        $this->_websiteFactory = $websiteFactory;
        $this->_groupFactory = $groupFactory;
        $this->_config = $config;
        $this->_isSingleStoreAllowed = $isSingleStoreAllowed;
        $this->_appState = $appState;
        $this->_cookie = $cookie;
        $this->_url = $url;
        $this->_helper = $helper;
        if ($currentStore) {
            $this->_currentStore = $currentStore;
        }
    }

    /**
     * Get default store
     *
     * @return Store
     */
    protected function _getDefaultStore()
    {
        if (empty($this->_store)) {
            $this->_store = $this->_storeFactory->create()
                ->setId(\Magento\Store\Model\Store::DISTRO_STORE_ID)
                ->setCode(\Magento\Store\Model\Store::DEFAULT_CODE);
        }
        return $this->_store;
    }

    /**
     * Init store, group and website collections
     *
     * @return void
     */
    protected function _initStores()
    {
        $this->_store    = null;
        $this->_stores   = array();
        $this->_groups   = array();
        $this->_websites = array();

        $this->_website  = null;

        /** @var $websiteCollection \Magento\Store\Model\Resource\Website\Collection */
        $websiteCollection = $this->_websiteFactory->create()->getCollection();
        $websiteCollection->setLoadDefault(true);

        /** @var $groupCollection \Magento\Store\Model\Resource\Group\Collection */
        $groupCollection = $this->_groupFactory->create()->getCollection();
        $groupCollection->setLoadDefault(true);

        /** @var $storeCollection \Magento\Store\Model\Resource\Collection */
        $storeCollection = $this->_storeFactory->create()->getCollection();
        $storeCollection->setLoadDefault(true);

        $this->_hasSingleStore = false;
        if ($this->_isSingleStoreAllowed) {
            $this->_hasSingleStore = $storeCollection->count() < 3;
        }

        $websiteStores = array();
        $websiteGroups = array();
        $groupStores   = array();
        foreach ($storeCollection as $store) {
            /** @var $store Store */
            $store->setWebsite($websiteCollection->getItemById($store->getWebsiteId()));
            $store->setGroup($groupCollection->getItemById($store->getGroupId()));

            $this->_stores[$store->getId()] = $store;
            $this->_stores[$store->getCode()] = $store;

            $websiteStores[$store->getWebsiteId()][$store->getId()] = $store;
            $groupStores[$store->getGroupId()][$store->getId()] = $store;

            if (0 == $store->getId()) {
                $store->setUrlModel($this->_url);
            }
        }

        foreach ($groupCollection as $group) {
            /* @var $group Group */
            if (!isset($groupStores[$group->getId()])) {
                $groupStores[$group->getId()] = array();
            }
            $group->setStores($groupStores[$group->getId()]);
            $group->setWebsite($websiteCollection->getItemById($group->getWebsiteId()));

            $websiteGroups[$group->getWebsiteId()][$group->getId()] = $group;

            $this->_groups[$group->getId()] = $group;
        }

        foreach ($websiteCollection as $website) {
            /* @var $website Website */
            if (!isset($websiteGroups[$website->getId()])) {
                $websiteGroups[$website->getId()] = array();
            }
            if (!isset($websiteStores[$website->getId()])) {
                $websiteStores[$website->getId()] = array();
            }
            if ($website->getIsDefault()) {
                $this->_website = $website;
            }
            $website->setGroups($websiteGroups[$website->getId()]);
            $website->setStores($websiteStores[$website->getId()]);

            $this->_websites[$website->getId()] = $website;
            $this->_websites[$website->getCode()] = $website;
        }
    }

    /**
     * Allow or disallow single store mode
     *
     * @param bool $value
     * @return void
     */
    public function setIsSingleStoreModeAllowed($value)
    {
        $this->_isSingleStoreAllowed = (bool)$value;
    }

    /**
     * Check if store has only one store view
     *
     * @return bool
     */
    public function hasSingleStore()
    {
        return $this->_hasSingleStore;
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
        if ($this->_appState->getUpdateMode()) {
            return $this->_getDefaultStore();
        }

        if ($storeId === true && $this->hasSingleStore()) {
            return $this->_store;
        }

        if (!isset($storeId) || '' === $storeId || $storeId === true) {
            $storeId = $this->_currentStore;
        }
        if ($storeId instanceof Store) {
            return $storeId;
        }
        if (!isset($storeId)) {
            $this->throwStoreException();
        }

        if (empty($this->_stores[$storeId])) {
            $store = $this->_storeFactory->create();
            if (is_numeric($storeId)) {
                $store->load($storeId);
            } elseif (is_string($storeId)) {
                $store->load($storeId, 'code');
            }

            if (!$store->getCode()) {
                $this->throwStoreException();
            }
            $this->_stores[$store->getStoreId()] = $store;
            $this->_stores[$store->getCode()] = $store;
        }
        return $this->_stores[$storeId];
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
        $stores = array();
        foreach ($this->_stores as $store) {
            if (!$withDefault && $store->getId() == 0) {
                continue;
            }
            if ($codeKey) {
                $stores[$store->getCode()] = $store;
            } else {
                $stores[$store->getId()] = $store;
            }
        }

        return $stores;
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
        if ($websiteId === null || $websiteId === '') {
            $websiteId = $this->getStore()->getWebsiteId();
        } elseif ($websiteId instanceof Website) {
            return $websiteId;
        } elseif ($websiteId === true) {
            return $this->_website;
        }

        if (empty($this->_websites[$websiteId])) {
            $website = $this->_websiteFactory->create();
            // load method will load website by code if given ID is not a numeric value
            $website->load($websiteId);
            if (!$website->hasWebsiteId()) {
                throw new \Magento\Store\Model\Exception('Invalid website id/code requested.');
            }
            $this->_websites[$website->getWebsiteId()] = $website;
            $this->_websites[$website->getCode()] = $website;
        }
        return $this->_websites[$websiteId];
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
        $websites = array();
        if (is_array($this->_websites)) {
            foreach ($this->_websites as $website) {
                if (!$withDefault && $website->getId() == 0) {
                    continue;
                }
                if ($codeKey) {
                    $websites[$website->getCode()] = $website;
                } else {
                    $websites[$website->getId()] = $website;
                }
            }
        }
        return $websites;
    }

    /**
     * Retrieve application store group object
     *
     * @param null|Group|string $groupId
     * @return Group
     * @throws \Magento\Store\Model\Exception
     */
    public function getGroup($groupId = null)
    {
        if (is_null($groupId)) {
            $groupId = $this->getStore()->getGroupId();
        } elseif ($groupId instanceof Group) {
            return $groupId;
        }
        if (empty($this->_groups[$groupId])) {
            $group = $this->_groupFactory->create();
            if (is_numeric($groupId)) {
                $group->load($groupId);
                if (!$group->hasGroupId()) {
                    throw new \Magento\Store\Model\Exception('Invalid store group id requested.');
                }
            }
            $this->_groups[$group->getGroupId()] = $group;
        }
        return $this->_groups[$groupId];
    }

    /**
     * Prepare array of store groups
     * can be filtered to contain default store group or not by $withDefault flag
     * depending on flag $codeKey array keys can be group id or group code
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return Group[]
     */
    public function getGroups($withDefault = false, $codeKey = false)
    {
        $groups = array();
        if (is_array($this->_groups)) {
            foreach ($this->_groups as $group) {
                /** @var $group Group */
                if (!$withDefault && $group->getId() == 0) {
                    continue;
                }
                if ($codeKey) {
                    $groups[$group->getCode()] = $group;
                } else {
                    $groups[$group->getId()] = $group;
                }
            }
        }
        return $groups;
    }

    /**
     * Reinitialize store list
     *
     * @return void
     */
    public function reinitStores()
    {
        $this->_initStores();
    }

    /**
     * Retrieve default store for default group and website
     *
     * @return Store|null
     */
    public function getDefaultStoreView()
    {
        foreach ($this->getWebsites() as $_website) {
            /** @var $_website Website */
            if ($_website->getIsDefault()) {
                $_defaultStore = $this->getGroup($_website->getDefaultGroupId())->getDefaultStore();
                if ($_defaultStore) {
                    return $_defaultStore;
                }
            }
        }
        return null;
    }

    /**
     *  Unset website by id from app cache
     *
     * @param null|bool|int|string|Website $websiteId
     * @return void
     */
    public function clearWebsiteCache($websiteId = null)
    {
        if (is_null($websiteId)) {
            $websiteId = $this->getStore()->getWebsiteId();
        } elseif ($websiteId instanceof Website) {
            $websiteId = $websiteId->getId();
        } elseif ($websiteId === true) {
            $websiteId = $this->_website->getId();
        }

        if (!empty($this->_websites[$websiteId])) {
            $website = $this->_websites[$websiteId];

            unset($this->_websites[$website->getWebsiteId()]);
            unset($this->_websites[$website->getCode()]);
        }
    }

    /**
     * Get either default or any store view
     *
     * @return Store|null
     */
    public function getAnyStoreView()
    {
        $store = $this->getDefaultStoreView();
        if ($store) {
            return $store;
        }
        foreach ($this->getStores() as $store) {
            return $store;
        }

        return null;
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
    }

    /**
     * @return void
     * @throws \Magento\Store\Model\Exception
     */
    public function throwStoreException()
    {
        throw new \Magento\Store\Model\Exception('Store Manager has been initialized not properly');
    }

    /**
     * Get current store code
     *
     * @return string
     */
    public function getCurrentStore()
    {
        return $this->_currentStore;
    }
}
