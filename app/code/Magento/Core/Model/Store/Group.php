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
 * Store group model
 *
 * @method \Magento\Core\Model\Resource\Store\Group _getResource()
 * @method \Magento\Core\Model\Resource\Store\Group getResource()
 * @method \Magento\Core\Model\Store\Group setWebsiteId(int $value)
 * @method string getName()
 * @method string getCode()
 * @method \Magento\Core\Model\Store\Group setName(string $value)
 * @method \Magento\Core\Model\Store\Group setRootCategoryId(int $value)
 * @method \Magento\Core\Model\Store\Group setDefaultStoreId(int $value)
 */
namespace Magento\Core\Model\Store;

use Magento\Core\Model\Website;

class Group extends \Magento\Core\Model\AbstractModel implements \Magento\Object\IdentityInterface
{
    const ENTITY         = 'store_group';
    const CACHE_TAG      = 'store_group';

    /**
     * @var bool
     */
    protected $_cacheTag = true;

    /**
     * @var string
     */
    protected $_eventPrefix = 'store_group';

    /**
     * @var string
     */
    protected $_eventObject = 'store_group';

    /**
     * Group Store collection array
     *
     * @var \Magento\Core\Model\Resource\Store\Collection[]
     */
    protected $_stores;

    /**
     * Group store ids array
     *
     * @var int[]
     */
    protected $_storeIds = array();

    /**
     * Group store codes array
     *
     * @var string[]
     */
    protected $_storeCodes = array();

    /**
     * The number of stores in a group
     *
     * @var int
     */
    protected $_storesCount = 0;

    /**
     * Group default store
     *
     * @var \Magento\Core\Model\Store
     */
    protected $_defaultStore;

    /**
     * @var bool
     */
    private $_isReadOnly = false;

    /**
     * @var \Magento\Core\Model\Resource\Config\Data
     */
    protected $_configDataResource;

    /**
     * @var \Magento\Core\Model\Store
     */
    protected $_store;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\Resource\Config\Data $configDataResource
     * @param \Magento\Core\Model\Store $store
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\Resource\Config\Data $configDataResource,
        \Magento\Core\Model\Store $store,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_configDataResource = $configDataResource;
        $this->_store = $store;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }


    /**
     * Init model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Core\Model\Resource\Store\Group');
    }

    /**
     * Load store collection and set internal data
     *
     * @return void
     */
    protected function _loadStores()
    {
        $this->_stores = array();
        $this->_storesCount = 0;
        foreach ($this->getStoreCollection() as $store) {
            $this->_stores[$store->getId()] = $store;
            $this->_storeIds[$store->getId()] = $store->getId();
            $this->_storeCodes[$store->getId()] = $store->getCode();
            if ($this->getDefaultStoreId() == $store->getId()) {
                $this->_defaultStore = $store;
            }
            $this->_storesCount ++;
        }
    }

    /**
     * Set website stores
     *
     * @param \Magento\Core\Model\Store[] $stores
     * @return void
     */
    public function setStores($stores)
    {
        $this->_stores = array();
        $this->_storesCount = 0;
        foreach ($stores as $store) {
            $this->_stores[$store->getId()] = $store;
            $this->_storeIds[$store->getId()] = $store->getId();
            $this->_storeCodes[$store->getId()] = $store->getCode();
            if ($this->getDefaultStoreId() == $store->getId()) {
                $this->_defaultStore = $store;
            }
            $this->_storesCount ++;
        }
    }

    /**
     * Retrieve new (not loaded) Store collection object with group filter
     *
     * @return \Magento\Core\Model\Resource\Store\Collection
     */
    public function getStoreCollection()
    {
        return $this->_store
            ->getCollection()
            ->addGroupFilter($this->getId());
    }

    /**
     * Retrieve website store objects
     *
     * @return \Magento\Core\Model\Resource\Store\Collection[]
     */
    public function getStores()
    {
        if (is_null($this->_stores)) {
            $this->_loadStores();
        }
        return $this->_stores;
    }

    /**
     * Retrieve website store ids
     *
     * @return int[]
     */
    public function getStoreIds()
    {
        if (is_null($this->_stores)) {
            $this->_loadStores();
        }
        return $this->_storeIds;
    }

    /**
     * Retrieve website store codes
     *
     * @return array
     */
    public function getStoreCodes()
    {
        if (is_null($this->_stores)) {
            $this->_loadStores();
        }
        return $this->_storeCodes;
    }

    /**
     * @return int
     */
    public function getStoresCount()
    {
        if (is_null($this->_stores)) {
            $this->_loadStores();
        }
        return $this->_storesCount;
    }

    /**
     * Retrieve default store model
     *
     * @return \Magento\Core\Model\Store
     */
    public function getDefaultStore()
    {
        if (!$this->hasDefaultStoreId()) {
            return false;
        }
        if (is_null($this->_stores)) {
            $this->_loadStores();
        }
        return $this->_defaultStore;
    }

    /**
     * Get most suitable store by locale
     * If no store with given locale is found - default store is returned
     * If group has no stores - null is returned
     *
     * @param string $locale
     * @return \Magento\Core\Model\Store|null
     */
    public function getDefaultStoreByLocale($locale)
    {
        if ($this->getDefaultStore() && $this->getDefaultStore()->getLocaleCode() == $locale) {
            return $this->getDefaultStore();
        } else {
            $stores = $this->getStoresByLocale($locale);
            if (count($stores)) {
                return $stores[0];
            } else {
                return $this->getDefaultStore() ? $this->getDefaultStore() : null;
            }
        }
    }

    /**
     * Retrieve list of stores with given locale
     *
     * @param string $locale
     * @return \Magento\Core\Model\Store[]
     */
    public function getStoresByLocale($locale)
    {
        $stores = array();
        foreach ($this->getStores() as $store) {
            /* @var $store \Magento\Core\Model\Store */
            if ($store->getLocaleCode() == $locale) {
                $stores[] = $store;
            }
        }
        return $stores;
    }

    /**
     * Set relation to the website
     *
     * @param Website $website
     * @return void
     */
    public function setWebsite(Website $website)
    {
        $this->setWebsiteId($website->getId());
    }

    /**
     * Retrieve website model
     *
     * @return Website|bool
     */
    public function getWebsite()
    {
        if (is_null($this->getWebsiteId())) {
            return false;
        }
        return $this->_storeManager->getWebsite($this->getWebsiteId());
    }

    /**
     * Is can delete group
     *
     * @return bool
     */
    public function isCanDelete()
    {
        if (!$this->getId()) {
            return false;
        }

        return $this->getWebsite()->getDefaultGroupId() != $this->getId();
    }

    /**
     * @return mixed
     */
    public function getDefaultStoreId()
    {
        return $this->_getData('default_store_id');
    }

    /**
     * @return mixed
     */
    public function getRootCategoryId()
    {
        return $this->_getData('root_category_id');
    }

    /**
     * @return mixed
     */
    public function getWebsiteId()
    {
        return $this->_getData('website_id');
    }

    /**
     * @return $this
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        $this->_configDataResource->clearStoreData($this->getStoreIds());
        return parent::_beforeDelete();
    }

    /**
     * Get/Set isReadOnly flag
     *
     * @param bool $value
     * @return bool
     */
    public function isReadOnly($value = null)
    {
        if (null !== $value) {
            $this->_isReadOnly = (bool)$value;
        }
        return $this->_isReadOnly;
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return array(self::CACHE_TAG . '_' . $this->getId());
    }
}
