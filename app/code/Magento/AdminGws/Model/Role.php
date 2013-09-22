<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdminGws
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Permissions checker
 *
 */
namespace Magento\AdminGws\Model;

class Role extends \Magento\Object
{
    /**
     * Store ACL role model instance
     *
     * @var \Magento\User\Model\Role
     */
    protected $_adminRole;

    /**
     * Storage for disallowed entities and their ids
     */
    protected $_disallowedWebsiteIds    = array();
    protected $_disallowedStores        = array();
    protected $_disallowedStoreIds      = array();
    protected $_disallowedStoreGroupIds = array();
    protected $_disallowedStoreGroups   = array();

    /**
     * Storage for categories which are used in allowed store groups
     *
     * @var array
     */
    protected $_allowedRootCategories;

    /**
     * Storage for categories which are not used in
     * disallowed store groups
     *
     * @var array
     */
    protected $_exclusiveRootCategories;

    /**
     * Storage for exclusive checked categories
     * using category path as key
     * @var array
     */
    protected $_exclusiveAccessToCategory = array();

    /**
     * @var Magento_Core_Model_StoreManager|null
     */
    protected $_storeManager = null;

    /**
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_StoreManager $storeManager,
        array $data = array()
    ) {
        parent::__construct($data);
        $this->_storeManager = $storeManager;
    }

    /**
     * Set ACL role and determine its limitations
     *
     * @param \Magento\User\Model\Role $role
     */
    public function setAdminRole($role)
    {
        if ($role) {
            $this->_adminRole = $role;

            // find role disallowed data
            foreach ($this->_storeManager->getWebsites(true) as $websiteId => $website) {
                if (!in_array($websiteId, $this->getRelevantWebsiteIds())) {
                    $this->_disallowedWebsiteIds[] = $websiteId;
                }
            }
            foreach ($this->_storeManager->getStores(true) as $storeId => $store) {
                if (!in_array($storeId, $this->getStoreIds())) {
                    $this->_disallowedStores[] = $store;
                    $this->_disallowedStoreIds[] = $storeId;
                }
            }
            foreach ($this->_storeManager->getGroups(true) as $groupId => $group) {
                if (!in_array($groupId, $this->getStoreGroupIds())) {
                    $this->_disallowedStoreGroups[] = $group;
                    $this->_disallowedStoreGroupIds[] = $groupId;
                }
            }
        }
    }

    /**
     * Check whether GWS permissions are applicable
     *
     * True if all permissions are allowed or core
     * admin role model is not defined yet. So in result we can't restrict some
     * low level functionality.
     *
     * @return bool
     */
    public function getIsAll()
    {
        if ($this->_adminRole) {
            return $this->_adminRole->getGwsIsAll();
        }

        return true;
    }

    /**
     * Checks whether GWS permissions on website level
     *
     * @return boolean
     */
    public function getIsWebsiteLevel()
    {
        $_websiteIds = $this->getWebsiteIds();
        return !empty($_websiteIds);
    }

    /**
     * Checks whether GWS permissions on store level
     *
     * @return boolean
     */
    public function getIsStoreLevel()
    {
        $_websiteIds = $this->getWebsiteIds();
        return empty($_websiteIds);
    }

    /**
     * Get allowed store ids from core admin role object.
     * If role model is not defined yeat use default value as empty array.
     *
     * @return array
     */
    public function getStoreIds()
    {
        if ($this->_adminRole) {
            return $this->_adminRole->getGwsStores();
        }

        return array();
    }

    /**
     * Set allowed store ids for the core admin role object in session.
     * If role model is not defined yeat do nothing.
     *
     * @param mixed $value
     * @return array
     */
    public function setStoreIds($value)
    {
        if ($this->_adminRole) {
            return $this->_adminRole->setGwsStores($value);
        }

        return $this;
    }

    /**
     * Get allowed store group ids from core admin role object.
     * If role model is not defined yeat use default value as empty array.
     *
     * @return array
     */
    public function getStoreGroupIds()
    {
        if ($this->_adminRole) {
            return $this->_adminRole->getGwsStoreGroups();
        }

        return array();
    }

    /**
     * Set allowed store group ids for the core admin role object in session.
     * If role model is not defined yeat do nothing.
     *
     * @param mixed $value
     * @return array
     */
    public function setStoreGroupIds($value)
    {
        if ($this->_adminRole) {
            return $this->_adminRole->setGwsStoreGroups($value);
        }

        return $this;
    }

    /**
     * Get allowed website ids from core admin role object.
     * If role model is not defined yeat use default value as empty array.
     *
     * @return array
     */
    public function getWebsiteIds()
    {
        if ($this->_adminRole) {
            return $this->_adminRole->getGwsWebsites();
        }

        return array();
    }

    /**
     * Get website ids of allowed store groups
     *
     * @return array
     */
    public function getRelevantWebsiteIds()
    {
        if ($this->_adminRole) {
            return $this->_adminRole->getGwsRelevantWebsites();
        }

        return array();
    }

    /**
     * Get website IDs that are not allowed
     *
     * @return array
     */
    public function getDisallowedWebsiteIds()
    {
        return $this->_disallowedWebsiteIds;
    }

    /**
     * Get store IDs that are not allowed
     *
     * @return array
     */
    public function getDisallowedStoreIds()
    {
        $result = array();

        foreach ($this->_disallowedStores as $store) {
            $result[] = $store->getId();
        }

        return $result;
    }

    /**
     * Get stores that are not allowed
     *
     * @return array
     */
    public function getDisallowedStores()
    {
        return $this->_disallowedStores;
    }

    /**
     * Get root categories that are allowed in current permissions scope
     *
     * @return array
     */
    public function getAllowedRootCategories()
    {
        if ((!$this->getIsAll()) && (null === $this->_allowedRootCategories)) {
            $this->_allowedRootCategories = array();

            $categoryIds = array();
            foreach ($this->getStoreGroupIds() as $groupId) {
                $categoryIds[] = $this->getGroup($groupId)->getRootCategoryId();
            }

            $categories = \Mage::getResourceModel('Magento\Catalog\Model\Resource\Category\Collection')
                ->addIdFilter($categoryIds);
            foreach ($categories  as $category) {
                $this->_allowedRootCategories[$category->getId()] = $category->getPath();
            }
        }
        return $this->_allowedRootCategories;
    }

    /**
     * Get root categories that are allowed in current permissions scope
     *
     * @return array
     */
    public function getExclusiveRootCategories()
    {
        if ((!$this->getIsAll()) && (null === $this->_exclusiveRootCategories)) {
            $this->_exclusiveRootCategories = $this->getAllowedRootCategories();
            foreach ($this->_disallowedStoreGroups as $group) {
                $_catId = $group->getRootCategoryId();

                $pos = array_search($_catId, array_keys($this->_exclusiveRootCategories));
                if ($pos !== FALSE) {
                    unset($this->_exclusiveRootCategories[$_catId]);
                }
            }
        }
        return $this->_exclusiveRootCategories;
    }

    /**
     * Check if current user have exclusive access to specified category (by path)
     *
     * @param string $categoryPath
     * @return boolean
     */
    public function hasExclusiveCategoryAccess($categoryPath)
    {
        if (!isset($this->_exclusiveAccessToCategory[$categoryPath])) {
            /**
             * By default we grand permissions for category
             */
            $result = true;

            if (!$this->getIsAll()) {
                $categoryPathArray = explode('/', $categoryPath);
                if (count($categoryPathArray) < 2) {
                    //not grand access if category is root
                    $result = false;
                } else {
                    if (count(array_intersect(
                            $categoryPathArray,
                            array_keys($this->getExclusiveRootCategories())
                        )) == 0) {
                        $result = false;

                    }
                }
            }
            $this->_exclusiveAccessToCategory[$categoryPath] = $result;
        }

        return $this->_exclusiveAccessToCategory[$categoryPath];
    }

    /**
     * Check whether specified website ID is allowed
     *
     * @param string|int|array $websiteId
     * @param bool $isExplicit
     * @return bool
     */
    public function hasWebsiteAccess($websiteId, $isExplicit = false)
    {
        $websitesToCompare = $this->getRelevantWebsiteIds();
        if ($isExplicit) {
            $websitesToCompare = $this->getWebsiteIds();
        }
        if (is_array($websiteId)) {
            return count(array_intersect($websiteId, $websitesToCompare)) > 0;
        }
        return in_array($websiteId, $websitesToCompare);
    }

    /**
     * Check whether specified store ID is allowed
     *
     * @param string|int|array $storeId
     * @return bool
     */
    public function hasStoreAccess($storeId)
    {
        if (is_array($storeId)) {
            return count(array_intersect($storeId, $this->getStoreIds())) > 0;
        }
        return in_array($storeId, $this->getStoreIds());
    }

    /**
     * Check whether specified store group ID is allowed
     *
     * @param string|int|array $storeGroupId
     * @return bool
     */
    public function hasStoreGroupAccess($storeGroupId)
    {
        if (is_array($storeGroupId)) {
            return count(array_intersect($storeGroupId, $this->getStoreGroupIds())) > 0;
        }
        return in_array($storeGroupId, $this->getStoreGroupIds());
    }

    /**
     * Check whether website access is exlusive
     *
     * @param array $websiteIds
     * @return bool
     */
    public function hasExclusiveAccess($websiteIds)
    {
        return $this->getIsAll() ||
            (count(array_intersect($this->getWebsiteIds(), $websiteIds)) === count($websiteIds) &&
                $this->getIsWebsiteLevel());
    }

    /**
     * Check whether store access is exlusive
     *
     * @param array $storeIds
     * @return bool
     */
    public function hasExclusiveStoreAccess($storeIds)
    {
        return $this->getIsAll() ||
               (count(array_intersect($this->getStoreIds(), $storeIds)) === count($storeIds));
    }

    /**
     * Find a store group by id
     * Note: For case when we can't $this->_storeManager->getGroup() bc it will try to load
     * store group in case store group is not preloaded
     *
     * @param int|string $findGroupId
     * @return \Magento\Core\Model\Store\Group|null
     */
    public function getGroup($findGroupId)
    {
        foreach ($this->_storeManager->getGroups() as $groupId =>$group) {
            if ($findGroupId == $groupId) {
                return $group;
            }
        }
    }
}
