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
        $model->setData('stores', $this->_preventLoosingStoreIds($this->_updateSavingStoreIds(
            $model->getData('stores'), $originalStoreIds
        )));

        if ($model->getId() && !$this->_helper->hasStoresAccess($originalStoreIds)) {
            Mage::throwException(
               Mage::helper('enterprise_admingws')->__('You cannot change this cms page')
            );
        }

        if (!$model->getId() && !$this->_helper->getIsWebsiteLevel()) {
            Mage::throwException(
               Mage::helper('enterprise_admingws')->__('You cannot add new cms pages')
            );
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
            Mage::throwException(
               Mage::helper('enterprise_admingws')->__('You cannot change this cms block')
            );
        }

        if (!$model->getId() && !$this->_helper->getIsWebsiteLevel()) {
            Mage::throwException(
               Mage::helper('enterprise_admingws')->__('You cannot add new cms blocks')
            );
        }

        $model->setData('stores', $this->_preventLoosingStoreIds($this->_updateSavingStoreIds(
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
            Mage::throwException(
               Mage::helper('enterprise_admingws')->__('You cannot change this poll')
            );
        }

        if (!$model->getId() && !$this->_helper->getIsWebsiteLevel()) {
            Mage::throwException(
               Mage::helper('enterprise_admingws')->__('You cannot add new polls')
            );
        }

        $model->setData('store_ids', $this->_preventLoosingStoreIds($this->_updateSavingStoreIds(
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
            Mage::throwException(
               Mage::helper('enterprise_admingws')->__('You cannot add promotion rules')
            );
        }

        if ($model->getId() && !$this->_helper->hasWebsitesAccess($websiteIds)) {
            Mage::throwException(
               Mage::helper('enterprise_admingws')->__('You cannot change this promotion rule')
            );
        }

        $updatedWebsiteIds = $this->_preventLoosingWebsiteIds(
            $this->_updateSavingWebsiteIds(
                $websiteIds, $originalWebsiteIds
            )
        );

        $model->setData('website_ids', implode(',', $updatedWebsiteIds));
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
            Mage::throwException(
               Mage::helper('enterprise_admingws')->__('You cannot change this newsletter queue')
            );
        }
        if ($model->getSaveStoresFlag()) {
            $model->setStores(
                $this->_preventLoosingStoreIds($this->_updateSavingStoreIds(
                    $model->getStores(),
                    $originalStores
                )
            ));
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

        $model->setWebsiteIds($this->_preventLoosingWebsiteIds(
            $this->_updateSavingWebsiteIds($websiteIds, $origWebsiteIds)
        ));

        if ($model->getId() &&
            !$this->_helper->hasWebsitesAccess($origWebsiteIds)) {
           Mage::throwException(
               Mage::helper('enterprise_admingws')->__('You cannot change this product')
           );
        } elseif (!$model->getId() && !$this->_helper->getIsWebsiteLevel()) {
            Mage::throwException(
                Mage::helper('enterprise_admingws')->__('You cannot add new product')
            );
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
            Mage::throwException(
                Mage::helper('enterprise_admingws')->__('You cannot delete this product')
            );
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
            Mage::throwException(
                Mage::helper('enterprise_admingws')->__('You cannot delete this category')
            );
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
            Mage::throwException(
                Mage::helper('enterprise_admingws')->__('You cannot delete this customer')
            );
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
            Mage::throwException(
                Mage::helper('enterprise_admingws')->__('You cannot delete this promotion rule')
            );
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
            Mage::throwException(
                Mage::helper('enterprise_admingws')->__('You cannot delete this cms page')
            );
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
            Mage::throwException(
                Mage::helper('enterprise_admingws')->__('You cannot delete this cms block')
            );
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
            Mage::throwException(
               Mage::helper('enterprise_admingws')->__('You cannot change this customer')
            );
        } elseif (!$model->getId() && !$this->_helper->getIsWebsiteLevel()) {
            Mage::throwException(
               Mage::helper('enterprise_admingws')->__('You cannot add new customers')
            );
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
     * Catalog category initialize after loading
     *
     * @param Mage_Catalog_Model_Category $model
     * @return void
     */
    public function catalogCategorySaveBefore($model)
    {
        if ((!$this->_helper->getIsWebsiteLevel() && !$model->getId())) {
            Mage::throwException(
                Mage::helper('enterprise_admingws')->__('You cannot add new categories')
            );
        } elseif ($model->getId() && !$this->_isCategoryAllowed($model)) {
            Mage::throwException(
                Mage::helper('enterprise_admingws')->__('You cannot change this category')
            );
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
     * Prevent loosign of website from model
     *
     * @param array $websiteIds
     * @return array
     */
    protected function _preventLoosingWebsiteIds($websiteIds)
    {
        if (count(array_intersect($websiteIds, $this->_helper->getWebsiteIds())) === 0 &&
            count($this->_helper->getWebsiteIds())) {
            $websiteIds[] = current($this->_helper->getWebsiteIds());
        }

        return $websiteIds;
    }

    /**
     * Prevent loosign of store view from model
     *
     * @param array $storeIds
     * @return array
     */
    protected function _preventLoosingStoreIds($storeIds)
    {
        if (count(array_intersect($storeIds, $this->_helper->getStoreIds())) === 0 &&
            count($this->_helper->getStoreIds())) {
            $storeIds[] = current($this->_helper->getStoreIds());
        }

        return $storeIds;
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
     * Check whether specified category is allowed
     *
     * @param Mage_Catalog_Model_Category $category
     * @return bool
     */
    protected function _isCategoryAllowed($category)
    {
        if (!$category->getId()) {
            return false;
        }
        $categoryPath = $category->getPath();
        foreach ($this->_helper->getAllowedRootCategories() as $rootPath) {
            if ($categoryPath === $rootPath || 0 === strpos($categoryPath, "{$rootPath}/")) {
                return true;
            }
        }
        return false;
    }

}
