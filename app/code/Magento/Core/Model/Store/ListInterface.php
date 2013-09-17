<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
interface Magento_Core_Model_Store_ListInterface
{
    /**
     * Allow or disallow single store mode
     *
     * @param bool $value
     */
    public function setIsSingleStoreModeAllowed($value);

    /**
     * Check if store has only one store view
     *
     * @return bool
     */
    public function hasSingleStore();

    /**
     * Retrieve application store object
     *
     * @param null|string|bool|int|Magento_Core_Model_Store $storeId
     * @return Magento_Core_Model_Store
     * @throws Magento_Core_Model_Store_Exception
     */
    public function getStore($storeId = null);

    /**
     * Retrieve stores array
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return Magento_Core_Model_Store[]
     */
    public function getStores($withDefault = false, $codeKey = false);

    /**
     * Retrieve application website object
     *
     * @param null|bool|int|string|Magento_Core_Model_Website $websiteId
     * @return Magento_Core_Model_Website
     * @throws Magento_Core_Exception
     */
    public function getWebsite($websiteId = null);

    /**
     * Get loaded websites
     *
     * @param bool $withDefault
     * @param bool|string $codeKey
     * @return Magento_Core_Model_Website[]
     */
    public function getWebsites($withDefault = false, $codeKey = false);

    /**
     * Reinitialize store list
     */
    public function reinitStores();

    /**
     * Retrieve default store for default group and website
     *
     * @return Magento_Core_Model_Store
     */
    public function getDefaultStoreView();

    /**
     * Retrieve application store group object
     *
     * @param null|Magento_Core_Model_Store_Group|string $groupId
     * @return Magento_Core_Model_Store_Group
     * @throws Magento_Core_Exception
     */
    public function getGroup($groupId = null);

    /**
     * Prepare array of store groups
     * can be filtered to contain default store group or not by $withDefault flag
     * depending on flag $codeKey array keys can be group id or group code
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return Magento_Core_Model_Store_Group[]
     */
    public function getGroups($withDefault = false, $codeKey = false);

    /**
     *  Unset website by id from app cache
     *
     * @param null|bool|int|string|Magento_Core_Model_Website $websiteId
     */
    public function clearWebsiteCache($websiteId = null);

    /**
     * Get either default or any store view
     *
     * @return Magento_Core_Model_Store|null
     */
    public function getAnyStoreView();

    /**
     * Set current default store
     *
     * @param string $store
     */
    public function setCurrentStore($store);

    /**
     * Get current store code
     *
     * @return string
     */
    public function getCurrentStore();

    /**
     * @throws Magento_Core_Model_Store_Exception
     */
    public function throwStoreException();
}
