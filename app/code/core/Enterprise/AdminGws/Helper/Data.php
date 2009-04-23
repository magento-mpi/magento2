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
     * Set ACL role and determine its limitations
     *
     * @param Mage_Admin_Model_Roles $role
     */
    public function setRole($role)
    {
        if ($role) {
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
                    $this->_disallowedStores[] = $storeId;
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
     * Get allowed store ids
     *
     * @return array
     */
    public function getStoreIds()
    {
        return $this->_roleStores;
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
     * Check whether specified website ID is allowed
     *
     * @param string $websiteId
     * @param bool $isExplicit
     * @return bool
     */
    public function hasWebsiteAccess($websiteId, $isExplicit = false)
    {
        if ($isExplicit) {
            return in_array($websiteId, $this->_roleWebsites);
        }
        return in_array($websiteId, $this->_roleRelevantWebsites);
    }

    /**
     * Check whether specified store ID is allowed
     *
     * @param string $storeId
     * @return bool
     */
    public function hasStoreAccess($storeId)
    {
        return in_array($storeId, $this->_roleStores);
    }
}
