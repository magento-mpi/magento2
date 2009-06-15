<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_AdminGws
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Admin GWS helper
 *
 */
class Enterprise_AdminGws_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_roleIsAll               = true;
    protected $_roleWebsiteIds          = array();
    protected $_roleRelevantWebsiteIds  = array();
    protected $_roleStoreGroupIds       = array();
    protected $_roleStoreIds            = array();

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
     * @var Mage_Admin_Model_Roles
     */
    protected $_role;

    /**
     * Set ACL role and determine its limitations
     *
     * @param Mage_Admin_Model_Roles $role
     */
    public function setRole($role)
    {
        if ($role) {
            $this->_role = $role;

            // set role gws data
            $this->_roleWebsiteIds = $role->getGwsWebsites();
            $this->_roleStoreGroupIds = $role->getGwsStoreGroups();
            $this->_roleStoreIds = $role->getGwsStores();
            $this->_roleRelevantWebsiteIds = $role->getGwsRelevantWebsites();

            // find role disallowed data
            foreach (Mage::app()->getWebsites(true) as $websiteId => $website) {
                if (!in_array($websiteId, $this->_roleRelevantWebsiteIds)) {
                    $this->_disallowedWebsiteIds[] = $websiteId;
                }
            }
            foreach (Mage::app()->getStores(true) as $storeId => $store) {
                if (!in_array($storeId, $this->_roleStoreIds)) {
                    $this->_disallowedStores[] = $store;
                    $this->_disallowedStoreIds[] = $storeId;
                }
            }
            foreach (Mage::app()->getGroups(true) as $groupId => $group) {
                if (!in_array($groupId, $this->_roleStoreGroupIds)) {
                    $this->_disallowedStoreGroups[] = $group;
                    $this->_disallowedStoreGroupIds[] = $groupId;
                }
            }

            /**
             * Important to set this flag after determining all true permissions data,
             * because it will be used for limiting websites / stores collections everywhere
             */
            $this->_roleIsAll = $role->getGwsIsAll();
        }
    }

    /**
     * Check whether GWS permissions are applicable
     *
     * True if all permissions are allowed
     *
     * @return bool
     */
    public function getIsAll()
    {
        return $this->_roleIsAll;
    }

    /**
     * Checks whether GWS permissions on website level
     *
     * @return boolean
     */
    public function getIsWebsiteLevel()
    {
        return !empty($this->_roleWebsiteIds);
    }

    /**
     * Checks whether GWS permissions on store level
     *
     * @return boolean
     */
    public function getIsStoreLevel()
    {
        return empty($this->_roleWebsiteIds);
    }

    /**
     * Get allowed store ids
     *
     * @return array
     */
    public function getStoreIds()
    {
        return $this->_roleStoreIds;
    }

    /**
     * Get allowed store group ids
     *
     * @return array
     */
    public function getStoreGroupIds()
    {
        return $this->_roleStoreGroupIds;
    }

    /**
     * Get allowed website ids
     *
     * @return array
     */
    public function getWebsiteIds()
    {
        return $this->_roleWebsiteIds;
    }

    /**
     * Get website ids of allowed store groups
     *
     * @return array
     */
    public function getRelevantWebsiteIds()
    {
        return $this->_roleRelevantWebsiteIds;
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
        if ((!$this->_roleIsAll) && (null === $this->_allowedRootCategories)) {
            $this->_allowedRootCategories = array();

            $categoryIds = array();
            foreach ($this->_roleStoreGroupIds as $groupId) {
                $categoryIds[] = $this->getGroup($groupId)->getRootCategoryId();
            }

            foreach (Mage::getResourceModel('catalog/category_collection')->addIdFilter($categoryIds) as $category) {
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
        if ((!$this->_roleIsAll) && (null === $this->_exclusiveRootCategories)) {
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
        $websitesToCompare = $this->_roleRelevantWebsiteIds;
        if ($isExplicit) {
            $websitesToCompare = $this->_roleWebsiteIds;
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
            return count(array_intersect($storeId, $this->_roleStoreIds)) > 0;
        }
        return in_array($storeId, $this->_roleStoreIds);
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
            return count(array_intersect($storeGroupId, $this->_roleStoreGroupIds)) > 0;
        }
        return in_array($storeGroupId, $this->_roleStoreGroupIds);
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
               (count(array_intersect($this->_roleWebsiteIds, $websiteIds)) === count($websiteIds) &&
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
               (count(array_intersect($this->_roleStoreIds, $storeIds)) === count($storeIds) &&
                $this->getIsWebsiteLevel());
    }

    /**
     * Set new store group ids
     *
     * @param array $newValues
     */
    public function updateStoreGroupIds($newValues)
    {
        $this->_roleStoreGroupIds = $newValues;
        $this->_role->setGwsStoreGroups($newValues);
    }

    /**
     * Set new store ids
     *
     * @param array $newValues
     */
    public function updateStoreIds($newValues)
    {
        $this->_roleStoreIds = $newValues;
        $this->_role->setGwsStores($newValues);
    }

    /**
     * Find a store group by id
     * Note: For case when we can't Mage::app()->getGroup() bc it will try to load
     * store group in case store group is not preloaded
     *
     * @param int|string $findGroupId
     * @return Mage_Core_Model_Store_Group|null
     */
    public function getGroup($findGroupId)
    {
        foreach (Mage::app()->getGroups() as $groupId =>$group) {
            if ($findGroupId == $groupId) {
                return $group;
            }
        }
    }

    /**
     * Transform comma-separeated ids string into array
     *
     * @param mixed $ids
     * @return mixed
     */
    public function explodeIds($ids, $separator = ',')
    {
        if (empty($ids) && $ids !== 0 && $ids !== '0') {
            return array();
        }
        if (!is_array($ids)) {
            return explode($separator, $ids);
        }
        return $ids;
    }
}
