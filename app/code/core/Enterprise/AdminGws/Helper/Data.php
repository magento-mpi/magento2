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
    protected $_roleIsAll             = true;
    protected $_roleWebsites          = array();
    protected $_roleRelevantWebsites  = array();
    protected $_roleStoreGroups       = array();
    protected $_roleStores            = array();
    protected $_disallowedWebsites    = array();
    protected $_disallowedStores      = array();
    protected $_allowedRootCategories = null;
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
            $this->_roleWebsites = $role->getGwsWebsites();
            $this->_roleStoreGroups = $role->getGwsStoreGroups();
            $this->_roleStores = $role->getGwsStores();
            $this->_roleRelevantWebsites = $role->getGwsRelevantWebsites();

            // find role disallowed data
            foreach (Mage::app()->getWebsites(true) as $websiteId => $website) {
                if (!in_array($websiteId, $this->_roleRelevantWebsites)) {
                    $this->_disallowedWebsites[] = $websiteId;
                }
            }
            foreach (Mage::app()->getStores(true) as $storeId => $store) {
                if (!in_array($storeId, $this->_roleStores)) {
                    $this->_disallowedStores[] = $store;

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
        return !empty($this->_roleWebsites);
    }

    /**
     * Checks whether GWS permissions on store level
     *
     * @return boolean
     */
    public function getIsStoreLevel()
    {
        return empty($this->_roleWebsites);
    }

    /**
     * Get allowed store ids
     *
     * @return array
     */
    public function getStoreIds()
    {
        return $this->_roleStores;
    }

    /**
     * Get allowed store group ids
     *
     * @return array
     */
    public function getStoreGroupIds()
    {
        return $this->_roleStoreGroups;
    }

    /**
     * Get allowed website ids
     *
     * @return array
     */
    public function getWebsiteIds()
    {
        return $this->_roleWebsites;
    }

    /**
     * Get website ids of allowed store groups
     *
     * @return array
     */
    public function getRelevantWebsiteIds()
    {
        return $this->_roleRelevantWebsites;
    }

    /**
     * Get website IDs that are not allowed
     *
     * @return array
     */
    public function getDisallowedWebsiteIds()
    {
        return $this->_disallowedWebsites;
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
            foreach ($this->_roleStoreGroups as $groupId) {
                $categoryIds[] = Mage::app()->getGroup($groupId)->getRootCategoryId();
            }
            foreach (Mage::getResourceModel('catalog/category_collection')->addIdFilter($categoryIds) as $category) {
                $this->_allowedRootCategories[$category->getId()] = $category->getPath();
            }
        }
        return $this->_allowedRootCategories;
    }

    /**
     * Check exclusive category access
     *
     * @param string $categoryPath
     * @return boolean
     */
    public function hasExclusiveCategoryAccess($categoryPath)
    {
        if ($this->getIsAll()) {
            return true;
        }

        if (!$this->getIsWebsiteLevel()) {
            return false;
        }

        if (!is_array($categoryPath)) {
            $categoryPath = explode('/', $categoryPath);
        }

        if (count(array_intersect(
                $categoryPath,
                array_keys($this->getAllowedRootCategories())
            )) == 0) {
            return false;
        }

        foreach ($this->getDisallowedStores() as $store) {
            if (in_array($store->getRootCategoryId(), $categoryPath)) {
                return false;
            }
        }

        return true;
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
        $websitesToCompare = $this->_roleRelevantWebsites;
        if ($isExplicit) {
            $websitesToCompare = $this->_roleWebsites;
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
            return count(array_intersect($storeId, $this->_roleStores)) > 0;
        }
        return in_array($storeId, $this->_roleStores);
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
            return count(array_intersect($storeGroupId, $this->_roleStoreGroups)) > 0;
        }
        return in_array($storeGroupId, $this->_roleStoreGroups);
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
               (count(array_intersect($this->_roleWebsites, $websiteIds)) === count($websiteIds) &&
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
               (count(array_intersect($this->_roleStores, $storeIds)) === count($storeIds) &&
                $this->getIsWebsiteLevel());
    }

    /**
     * Set new store group ids
     *
     * @param array $newValues
     */
    public function updateStoreGroupIds($newValues)
    {
        $this->_roleStoreGroups = $newValues;
        $this->_role->setGwsStoreGroups($newValues);
    }

    /**
     * Set new store ids
     *
     * @param array $newValues
     */
    public function updateStoreIds($newValues)
    {
        $this->_roleStores = $newValues;
        $this->_role->setGwsStores($newValues);
    }

    /**
     * Find a store group by id
     *
     * @param int|string $findGroupId
     * @return Mage_Core_Model_Store_Group|null
     */
    public function getGroup($findGroupId)
    {
        foreach (Mage::app()->getWebsites() as $website) {
            foreach ($website->getGroups() as $groupId =>$group) {
                if ($findGroupId == $groupId) {
                    return $group;
                }
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
        if (!is_array($ids) && false !== strpos($ids, $separator)) {
            return explode($separator, $ids);
        }
        return $ids;
    }
}
