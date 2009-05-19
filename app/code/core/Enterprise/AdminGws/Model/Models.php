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
 * Models limiter
 *
 */
class Enterprise_AdminGws_Model_Models
{
    /**
     * @var Enterprise_AdminGws_Helper_Data
     */
    protected $_helper;

    /**
     * Initialize helper
     *
     */
    public function __construct()
    {
        $this->_helper = Mage::helper('enterprise_admingws');
    }

    /**
     * Limit CMS page save
     *
     * @param Mage_Cms_Model_Page $model
     */
    public function cmsPageSaveBefore($model)
    {
        $originalStoreIds = $model->getResource()->lookupStoreIds($model->getId());
        $model->setData('stores', $this->_forceAssignToStore($this->_updateSavingStoreIds(
            $model->getData('stores'), $originalStoreIds
        )));

        if ($model->getId() && !$this->_helper->hasStoresAccess($originalStoreIds)) {
            $this->_throwSave();
        }

        if (!$model->getId() && !$this->_helper->getIsWebsiteLevel()) {
            $this->_throwSave();
        }
    }

    /**
     * Limit CMS block save
     *
     * @param Mage_Cms_Model_Block $model
     */
    public function cmsBlockSaveBefore($model)
    {
        $originalStoreIds = $model->getResource()->lookupStoreIds($model->getId());
        if ($model->getId() && !$this->_helper->hasStoresAccess($originalStoreIds)) {
            $this->_throwSave();
        }

        if (!$model->getId() && !$this->_helper->getIsWebsiteLevel()) {
            $this->_throwSave();
        }

        $model->setData('stores', $this->_forceAssignToStore($this->_updateSavingStoreIds(
            $model->getData('stores'), $originalStoreIds
        )));
    }

    /**
     * Limit CMS Poll save
     *
     * @param Mage_Poll_Model_Poll $model
     */
    public function pollSaveBefore($model)
    {
        $originalStoreIds = $model->getResource()->lookupStoreIds($model->getId());

        if ($model->getId() && !$this->_helper->hasStoresAccess($originalStoreIds)) {
            $this->_throwSave();
        }

        if (!$model->getId() && !$this->_helper->getIsWebsiteLevel()) {
            $this->_throwSave();
        }

        $model->setData('store_ids', $this->_forceAssignToStore($this->_updateSavingStoreIds(
            $model->getData('store_ids'), $originalStoreIds
        )));
    }

    /**
     * Limit Rule save
     *
     * @param Mage_Rule_Model_Rule $model
     * @return void
     */
    public function ruleSaveBefore($model)
    {
        $originalWebsiteIds = $model->getOrigData('website_ids');
        $websiteIds = $model->getData('website_ids');

        if (!$model->getId() && !$this->_helper->getIsWebsiteLevel()) {
            $this->_throwSave();
        }

        if ($model->getId() && !$this->_helper->hasWebsitesAccess($websiteIds)) {
            $this->_throwSave();
        }

        $model->setData('website_ids', implode(',', $this->_forceAssignToWebsite(
            $this->_updateSavingWebsiteIds($websiteIds, $originalWebsiteIds)
        )));
    }

    /**
     * Limit rule model on after load
     *
     * @param Mage_Rule_Model_Rule $model
     * @return void
     */
    public function ruleLoadAfter($model)
    {
        $websiteIds = explode(',', $model->getData('website_ids'));
        if (!$this->_helper->hasExclusiveAccess($websiteIds)) {
            $model->setIsDeleteable(false);
        }

        if (!$this->_helper->getIsWebsiteLevel()) {
            $model->setIsReadonly(true);
        }
    }


    /**
     * Limit newsletter queue save
     *
     * @param Mage_Newsletter_Model_Queque $model
     * @return void
     */
    public function newsletterQueueSaveBefore($model)
    {
        $originalStores = $model->getResource()->getStores($model);
        if ($model->getId() && !$this->_helper->hasStoresAccess($originalStores)) {
            $this->_throwSave();
        }
        if ($model->getSaveStoresFlag()) {
            $model->setStores(
                $this->_forceAssignToStore($this->_updateSavingStoreIds(
                    $model->getStores(), $originalStores
            )));
        }
    }


    /**
     * Catalog product initialize after loading
     *
     * @param Mage_Catalog_Model_Product $model
     * @return void
     */
    public function catalogProductLoadAfter($model)
    {
        if (!$this->_helper->hasExclusiveAccess($model->getWebsiteIds())) {
            $model->unlockAttributes();

            $attributes = $model->getAttributes();
            foreach ($attributes as $attribute) {
                /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
                if ($attribute->isScopeGlobal() ||
                    ($attribute->isScopeWebsite() && count($this->_helper->getWebsiteIds())==0) ||
                    !in_array($model->getStore()->getId(), $this->_helper->getStoreIds())) {
                    $model->lockAttribute($attribute->getAttributeCode());
                }
            }

            $model->setInventoryReadonly(true);
            $model->setRelatedReadonly(true);
            $model->setCrosssellReadonly(true);
            $model->setUpsellReadonly(true);
            $model->setWebsitesReadonly(true);
            $model->lockAttribute('website_ids');
            $model->setCategoriesReadonly(true);
            $model->setOptionsReadonly(true);
            $model->setCompositeReadonly(true);
            $model->setDownloadableReadonly(true);
            $model->setGiftCardReadonly(true);
            $model->setIsDeleteable(false);
            $model->setIsDuplicable(false);
            if (!$this->_helper->hasStoreAccess($model->getStoreId())) {
                $model->setIsReadonly(true);
            }
        } else {
            if (count($model->getWebsiteIds()) == 1) {
                $model->setWebsitesReadonly(true);
                $model->lockAttribute('website_ids');
            }
        }
    }



    /**
     * Catalog product validate before saving
     *
     * @param Mage_Catalog_Model_Product $model
     * @return void
     */
    public function catalogProductSaveBefore($model)
    {
        $websiteIds = $model->getWebsiteIds();
        $origWebsiteIds = $model->getResource()->getWebsiteIds($model);

        $model->setWebsiteIds($this->_forceAssignToWebsite(
            $this->_updateSavingWebsiteIds($websiteIds, $origWebsiteIds)
        ));

        if ($model->getId() &&
            !$this->_helper->hasWebsitesAccess($origWebsiteIds)) {
            $this->_throwSave();
        } elseif (!$model->getId() && !$this->_helper->getIsWebsiteLevel()) {
            $this->_throwSave();
        }

    }

    /**
     * Catalog product validate before delete
     *
     * @param Mage_Catalog_Model_Product $model
     * @return void
     */
    public function catalogProductDeleteBefore($model)
    {
        if (!$this->_helper->hasExclusiveAccess($model->getWebsiteIds())) {
            $this->_throwDelete();
        }
    }

    /**
     * Catalog category validate before delete
     *
     * @param Mage_Catalog_Model_Product $model
     * @return void
     */
    public function catalogCategoryDeleteBefore($model)
    {
        if (!$this->_helper->hasExclusiveCategoryAccess($model->getPath())) {
            $this->_throwDelete();
        }
    }


    /**
     * Validate customer before delete
     *
     * @param Mage_Customer_Model_Customer $model
     * @return void
     */
    public function customerDeleteBefore($model)
    {
        if (!in_array($model->getWebsiteId(), $this->_helper->getWebsiteIds())) {
            $this->_throwDelete();
        }
    }

    /**
     * Validate rule before delete
     *
     * @param Mage_Rule_Model_Rule $model
     * @return void
     */
    public function ruleDeleteBefore($model)
    {
        $originalWebsiteIds = $model->getOrigData('website_ids');
        if (!$this->_helper->hasExclusiveAccess($originalWebsiteIds)) {
            $this->_throwDelete();
        }
    }

    /**
     * Validate cms page before delete
     *
     * @param Mage_Cms_Model_Page $model
     * @return void
     */
    public function cmsPageDeleteBefore($model)
    {
        $originalStoreIds = $model->getResource()->lookupStoreIds($model->getId());
        if (!$this->_helper->hasExclusiveStoreAccess($originalStoreIds)) {
            $this->_throwDelete();
        }
    }

    /**
     * Validate cms page before delete
     *
     * @param Mage_Cms_Model_Page $model
     * @return void
     */
    public function cmsBlockDeleteBefore($model)
    {
        $originalStoreIds = $model->getResource()->lookupStoreIds($model->getId());
        if (!$this->_helper->hasExclusiveStoreAccess($originalStoreIds)) {
            $this->_throwDelete();
        }
    }

    /**
     * Customer validate after load
     *
     * @param Mage_Customer_Model_Customer $model
     * @return void
     */
    public function customerLoadAfter($model)
    {
        if (!$this->_helper->hasWebsiteAccess($model->getWebsiteId(), true)) {
            $model->setIsReadonly(true);
            $model->setIsDeleteable(false);
        }
    }

    /**
     * Customer validate before save
     *
     * @param Mage_Customer_Model_Customer $model
     * @return void
     */
    public function customerSaveBefore($model)
    {
        if ($model->getId() && !$this->_helper->hasWebsiteAccess($model->getWebsiteId(), true)) {
            $this->_throwSave();
        } elseif (!$model->getId() && !$this->_helper->getIsWebsiteLevel()) {
            $this->_throwSave();
        }
    }

    /**
     * Order validate after load
     *
     * @param Mage_Sales_Model_Order $model
     * @return void
     */
    public function salesOrderLoadAfter($model)
    {
        if (!in_array($model->getStore()->getWebsiteId(), $this->_helper->getWebsiteIds())) {
            $model->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_CANCEL, false)
                ->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_CREDITMEMO, false)
                ->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_EDIT, false)
                ->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_HOLD, false)
                ->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_INVOICE, false)
                ->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_REORDER, false)
                ->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_SHIP, false)
                ->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_UNHOLD, false)
                ->setActionFlag(Mage_Sales_Model_Order::ACTION_FLAG_COMMENT, false);
        }
    }

    /**
     * Order validate before save
     *
     * @param Mage_Sales_Model_Order $model
     * @return void
     */
    public function salesOrderBeforeSave($model)
    {
        if (!$this->_helper->hasWebsiteAccess($model->getStore()->getWebsiteId(), true)) {
            Mage::throwException(
                Mage::helper('enterprise_admingws')->__('You cannot create order in dissalowed store')
            );
        }
    }

    /**
     * Catalog category initialize after loading
     *
     * @param Mage_Catalog_Model_Category $model
     * @return void
     */
    public function catalogCategoryLoadAfter($model)
    {
        if (!$this->_helper->hasExclusiveCategoryAccess($model->getPath())) {
            $model->unlockAttributes();
            $attributes = $model->getAttributes();
            foreach ($attributes as $attribute) {
                /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
                if ($attribute->isScopeGlobal() ||
                    ($attribute->isScopeWebsite() && count($this->_helper->getWebsiteIds())==0) ||
                    !in_array($model->getStore()->getId(), $this->_helper->getStoreIds())) {
                    $model->lockAttribute($attribute->getAttributeCode());
                }
            }
            $model->setProductsReadonly(true);
            $model->setIsDeleteable(false);
            if (!$this->_helper->hasStoreAccess($model->getStoreId())) {
                $model->setIsReadonly(true);
            }
        }
    }

    /**
     * Validate catalog category save
     *
     * @param Mage_Catalog_Model_Category $model
     */
    public function catalogCategorySaveBefore($model)
    {
        if ((!$this->_helper->getIsWebsiteLevel() && !$model->getId())) {
            $this->_throwSave();
        } elseif ($model->getId()) {
            $categoryPath = $category->getPath();
            foreach ($this->_helper->getAllowedRootCategories() as $rootPath) {
                if (!($categoryPath === $rootPath || 0 === strpos($categoryPath, "{$rootPath}/"))) {
                    $this->_throwSave();
                }
            }
        }
    }

    /**
     * Remove "All Store Views" information from CMS page or block model
     *
     * @param Varien_Object $model
     */
    public function cmsPageBlockLoadAfter($model)
    {
        if ($storeIds = $model->getData('store_id')) {
            $model->setData('store_id', array_intersect($this->_helper->getStoreIds(), $storeIds));
        }
    }

    /**
     * Check whether category can be moved
     *
     * @param Varien_Event_Observer $observer
     */
    public function catalogCategoryMoveBefore($observer)
    {
        $parentCategory = $observer->getEvent()->getParent();
        $currentCategory = $observer->getEvent()->getCategory();

        foreach (array($parentCategory, $currentCategory) as $category) {
            if (!$this->_helper->hasExclusiveCategoryAccess($category->getPath())) {
                $this->_throwSave();
            }
        }
    }

    /**
     * Check whether category can be moved
     *
     * @param Varien_Event_Observer $observer
     */
    public function catalogCategoryIsMoveable($observer)
    {
        $category = $observer->getEvent()->getOptions()->getCategory();
        if (!$this->_helper->hasExclusiveCategoryAccess($category->getData('path'))) {
            $observer->getEvent()->getOptions()->setIsMoveable(false);
        }
    }

    /**
     * Check whether category can be added
     *
     * @param Varien_Event_Observer $observer
     */
    public function catalogCategoryCanBeAdded($observer)
    {
        if (!$this->_helper->getIsAll()) {
            $observer->getEvent()->getOptions()->setIsAllow(false);
        }
    }


    /**
     * Check whether catalog permissions can be edited per category
     *
     * @param Varien_Event_Observer $observer
     */
    public function catalogCategoryIsCatalogPermissionsAllowed($observer)
    {
        if (!$this->_helper->getIsAll() && !$this->_helper->hasExclusiveCategoryAccess(
            $observer->getEvent()->getOptions()->getCategory()->getPath())) {
            $observer->getEvent()->getOptions()->setIsAllowed(false);
        }
    }

    /**
     * Limit incoming store IDs to allowed and add disallowed original stores
     *
     * @param array $newIds
     * @param array $origIds
     * @return array
     */
    protected function _updateSavingStoreIds($newIds, $origIds)
    {
        return array_unique(array_merge(
            array_intersect($newIds, $this->_helper->getStoreIds()),
            array_intersect($origIds, $this->_helper->getDisallowedStoreIds())
        ));
    }

    /**
     * Limit incoming website IDs to allowed and add disallowed original websites
     *
     * @param array $newIds
     * @param array $origIds
     * @return array
     */
    protected function _updateSavingWebsiteIds($newIds, $origIds)
    {
        return array_unique(array_merge(
            array_intersect($newIds, $this->_helper->getWebsiteIds()),
            array_intersect($origIds, $this->_helper->getDisallowedWebsiteIds())
        ));
    }

    /**
     * Prevent losigng disallowed websites from model
     *
     * @param array $websiteIds
     * @throws Mage_Core_Exception
     * @return array
     */
    protected function _forceAssignToWebsite($websiteIds)
    {
        if (count(array_intersect($websiteIds, $this->_helper->getWebsiteIds())) === 0 &&
            count($this->_helper->getWebsiteIds())) {
            Mage::throwException(Mage::helper('enterprise_admingws')->__('This item must be assigned to a website.'));
        }
        return $websiteIds;
    }

    /**
     * Prevent losing disallowed store views from model
     *
     * @param array $storeIds
     * @throws Mage_Core_Exception
     * @return array
     */
    protected function _forceAssignToStore($storeIds)
    {
        if (count(array_intersect($storeIds, $this->_helper->getStoreIds())) === 0 &&
            count($this->_helper->getStoreIds())) {
            Mage::throwException(Mage::helper('enterprise_admingws')->__('This item must be assigned to a store view.'));
        }
        return $storeIds;
    }

    /**
     * @throws Mage_Core_Exception
     */
    private function _throwSave()
    {
        Mage::throwException(Mage::helper('enterprise_admingws')->__('Not enough permissions to save this item.'));
    }

    /**
     * @throws Mage_Core_Exception
     */
    private function _throwDelete()
    {
        Mage::throwException(Mage::helper('enterprise_admingws')->__('Not enough permissions to delete this item.'));
    }
}
