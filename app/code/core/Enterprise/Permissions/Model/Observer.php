<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @package    Enterprise_Permissions
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Permissions observer
 *
 */
class Enterprise_Permissions_Model_Observer
{
    /**
     * @var Mage_Core_Model_Mysql4_Store_Group_Collection
     */
    protected $_storeGroupCollection;

    /**
     * Put websites/stores permissions data after loading admin role
     *
     * If all permissions are allowed, all possible websites / store groups / stores will be set
     * If only websites selected, all their store groups and stores will be set as well
     *
     * @param  Varien_Event_Observer $observer
     * @return Enterprise_Permissions_Model_Observer
     */
    public function addDataAfterRoleLoad($observer)
    {
        $object = $observer->getEvent()->getObject();

        $isAllPermissions = (bool)(int)$object->getData('is_all_permissions');
        $object->setIsAllPermissions($isAllPermissions);

        $storeGroupIds = array();

        if ($isAllPermissions) {
            // set all website ids
            $object->setWebsiteIds(array_keys(Mage::app()->getWebsites(true)));

            // set all store groups
            foreach ($this->_getAllStoreGroups() as $storeGroup) {
                $storeGroupIds[] = $storeGroup->getId();
            }
            $object->setStoreGroupIds($storeGroupIds);
        }
        else {
            // set selected website ids
            $websiteIds = ($object->getData('website_ids') != '' ? explode(',', $object->getData('website_ids')) : array());
            $object->setWebsiteIds($websiteIds);

            // set either the set store group ids or all of allowed websites
            if ($object->getData('store_group_ids') != '') {
                $object->setStoreGroupIds(explode(',', $object->getData('store_group_ids')));
            }
            else {
                if ($websiteIds) {
                    foreach ($this->_getAllStoreGroups() as $storeGroup) {
                        if (in_array($storeGroup->getWebsiteId(), $websiteIds)) {
                            $storeGroupIds[] = $storeGroup->getId();
                        }
                    }
                }
                $object->setStoreGroupIds($storeGroupIds);
            }
        }

        // determine and set store ids
        $storeIds = array();
        foreach (Mage::app()->getStores(true) as $store) {
            if (in_array($store->getGroupId(), $storeGroupIds)) {
                $storeIds[] = $store->getId();
            }
        }
        $object->setStoreIds($storeIds);

        return $this;
    }

    /**
     * Get all store groups
     *
     * @return Mage_Core_Model_Mysql4_Store_Group_Collection
     */
    protected function _getAllStoreGroups()
    {
        if (null === $this->_storeGroupCollection) {
            $this->_storeGroupCollection = Mage::getModel('core/store_group')->getCollection()->setLoadDefault(true);
        }
        return $this->_storeGroupCollection;
    }

    /**
     * Transform array of website ids and array of store group ids into comma-separated strings
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Permissions_Model_Observer
     */
    public function setDataBeforeRoleSave($observer)
    {
        $object = $observer->getEvent()->getObject();
        $websiteIds    = $object->getWebsiteIds();
        $storeGroupIds = $object->getStoreGroupIds();
        if (is_array($websiteIds)) {
            $object->setWebsiteIds(implode(',', $websiteIds));
        }
        if (is_array($storeGroupIds)) {
            $object->setStoreGroupIds(implode(',', $storeGroupIds));
        }
        return $this;
    }

    /**
     * Prepare role object permissions data before saving
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Permissions_Model_Observer
     */
    public function prepareRoleSave($observer)
    {
        $object = $observer->getEvent()->getObject();
        $request = $observer->getEvent()->getRequest();

        $isAll = (int)$request->getPost('permissions_is_all_sites');
        $websiteIds = (array)$request->getPost('website_ids');
        $storeGroupIds = (array)$request->getPost('store_group_ids');

        $object->setIsAllPermissions($isAll);
        if (!$isAll) {
            $object->setWebsiteIds($websiteIds)->setStoreGroupIds($storeGroupIds);
        }
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Permissions_Model_Observer
     */
    public function checkConfigAccess($observer)
    {
        if( !Mage::helper('permissions')->isSuperAdmin() ) {
            $website = $observer->getEvent()->getWebsite();
            $store   = $observer->getEvent()->getStore();
            if( !Mage::helper('permissions')->hasConfigAccess($website, $store) ) {
                if( $url = Mage::helper('permissions')->getConfigRedirectUrl() ) {
                    $observer->getEvent()->getResponse()->setRedirect($url);
                } else {
                    $observer->getEvent()->getResponse()->setRedirect(Mage::getUrl('adminhtml/index/denied'));
                }
            }
        }

        return $this;
    }
}
