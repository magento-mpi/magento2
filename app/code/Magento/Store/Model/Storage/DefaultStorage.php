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

class DefaultStorage implements \Magento\Store\Model\StoreManagerInterface
{
    /**
     * Application store object
     *
     * @var \Magento\Store\Model\Store
     */
    protected $_store;

    /**
     * Application website object
     *
     * @var \Magento\Store\Model\Website
     */
    protected $_website;

    /**
     * Application website object
     *
     * @var \Magento\Store\Model\Group
     */
    protected $_group;

    /**
     * @param \Magento\Store\Model\StoreFactory $storeFactory
     * @param \Magento\Store\Model\Website\Factory $websiteFactory
     * @param \Magento\Store\Model\Group\Factory $groupFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Store\Model\Website\Factory $websiteFactory,
        \Magento\Store\Model\Group\Factory $groupFactory
    ) {

        $this->_store = $storeFactory->create();
        $this->_store->setId(\Magento\Store\Model\Store::DISTRO_STORE_ID);
        $this->_store->setCode(\Magento\Store\Model\Store::DEFAULT_CODE);
        $this->_website = $websiteFactory->create();
        $this->_group = $groupFactory->create();
    }

    /**
     * Allow or disallow single store mode
     *
     * @param bool $value
     * @return void
     */
    public function setIsSingleStoreModeAllowed($value)
    {
        //not applicable for default storage
    }

    /**
     * Check if store has only one store view
     *
     * @return bool
     */
    public function hasSingleStore()
    {
        return false;
    }

    /**
     * Check if system is run in the single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return false;
    }

    /**
     * Retrieve application store object
     *
     * @param null|string|bool|int|\Magento\Store\Model\Store $storeId
     * @return \Magento\Store\Model\Store
     */
    public function getStore($storeId = null)
    {
        return $this->_store;
    }

    /**
     * Retrieve stores array
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return \Magento\Store\Model\Store[]
     */
    public function getStores($withDefault = false, $codeKey = false)
    {
        return array();
    }

    /**
     * Retrieve application website object
     *
     * @param null|bool|int|string|\Magento\Store\Model\Website $websiteId
     * @return \Magento\Store\Model\Website
     * @throws \Magento\Store\Model\Exception
     */
    public function getWebsite($websiteId = null)
    {
        if ($websiteId instanceof \Magento\Store\Model\Website) {
            return $websiteId;
        }

        return $this->_website;
    }

    /**
     * Get loaded websites
     *
     * @param bool $withDefault
     * @param bool|string $codeKey
     * @return \Magento\Store\Model\Website[]
     */
    public function getWebsites($withDefault = false, $codeKey = false)
    {
        $websites = array();

        if ($withDefault) {
            $key = $codeKey ? $this->_website->getCode() : $this->_website->getId();
            $websites[$key] = $this->_website;
        }

        return $websites;
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
        if ($groupId instanceof \Magento\Store\Model\Group) {
            return $groupId;
        }

        return $this->_group;
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
        $groups = array();

        if ($withDefault) {
            $key = $codeKey ? $this->_group->getCode() : $this->_group->getId();
            $groups[$key] = $this->_group;
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
        //not applicable for default storage
    }

    /**
     * Retrieve default store for default group and website
     *
     * @return \Magento\Store\Model\Store|null
     */
    public function getDefaultStoreView()
    {
        return null;
    }

    /**
     *  Unset website by id from app cache
     *
     * @param null|bool|int|string|\Magento\Store\Model\Website $websiteId
     * @return void
     */
    public function clearWebsiteCache($websiteId = null)
    {
        //not applicable for default storage
    }

    /**
     * Get either default or any store view
     *
     * @return \Magento\Store\Model\Store|null
     */
    public function getAnyStoreView()
    {
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

    }

    /**
     * @return void
     * @throws \Magento\Store\Model\Exception
     */
    public function throwStoreException()
    {
        //not applicable for default storage
    }

    /**
     * Get current store code
     *
     * @return string
     */
    public function getCurrentStore()
    {
        return $this->_store->getCode();
    }
}
