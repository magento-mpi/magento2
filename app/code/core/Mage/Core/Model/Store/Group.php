<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Store group model
 *
 * @method Mage_Core_Model_Resource_Store_Group _getResource()
 * @method Mage_Core_Model_Resource_Store_Group getResource()
 * @method Mage_Core_Model_Store_Group setWebsiteId(int $value)
 * @method string getName()
 * @method Mage_Core_Model_Store_Group setName(string $value)
 * @method Mage_Core_Model_Store_Group setRootCategoryId(int $value)
 * @method Mage_Core_Model_Store_Group setDefaultStoreId(int $value)
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Core_Model_Store_Group extends Mage_Core_Model_Abstract
{
    const ENTITY         = 'store_group';
    const CACHE_TAG      = 'store_group';

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
     * @var array
     */
    protected $_stores;

    /**
     * Group store ids array
     *
     * @var array
     */
    protected $_storeIds = array();

    /**
     * Group store codes array
     *
     * @var array
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
     * @var Mage_Core_Model_Store
     */
    protected $_defaultStore;

    /**
     * Website model
     *
     * @var Mage_Core_Model_Website
     */
    protected $_website;

    /**
     * @var bool
     */
    private $_isReadOnly = false;

    /**
     * init model
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Core_Model_Resource_Store_Group');
    }

    /**
     * Load store collection and set internal data
     *
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
     * @param array $stores
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
     * @return Mage_Core_Model_Resource_Store_Collection
     */
    public function getStoreCollection()
    {
        return Mage::getModel('Mage_Core_Model_Store')
            ->getCollection()
            ->addGroupFilter($this->getId());
    }

    /**
     * Retrieve wersite store objects
     *
     * @return array
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
     * @return array
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
     * @return Mage_Core_Model_Store
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
     * Set website model
     *
     * @param Mage_Core_Model_Website $website
     */
    public function setWebsite(Mage_Core_Model_Website $website)
    {
        $this->_website = $website;
    }

    /**
     * Retrieve website model
     *
     * @return Mage_Core_Model_Website
     */
    public function getWebsite()
    {
        if (is_null($this->getWebsiteId())) {
            return false;
        }
        if (is_null($this->_website)) {
            $this->_website = Mage::app()->getWebsite($this->getWebsiteId());
        }
        return $this->_website;
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

    public function getDefaultStoreId()
    {
        return $this->_getData('default_store_id');
    }

    public function getRootCategoryId()
    {
        return $this->_getData('root_category_id');
    }

    public function getWebsiteId()
    {
        return $this->_getData('website_id');
    }

    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
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
}
