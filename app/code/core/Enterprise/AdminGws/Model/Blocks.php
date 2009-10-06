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
 * @category    Enterprise
 * @package     Enterprise_AdminGws
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Blocks limiter
 *
 */
class Enterprise_AdminGws_Model_Blocks extends Enterprise_AdminGws_Model_Observer_Abstract
{
    /**
     * Check whether category can be moved
     *
     * @param Varien_Event_Observer $observer
     */
    public function catalogCategoryIsMoveable($observer)
    {
        if ($this->_role->getIsAll()) { // because observer is passed through directly
            return;
        }
        $category = $observer->getEvent()->getOptions()->getCategory();
        if (!$this->_role->hasExclusiveCategoryAccess($category->getData('path'))) {
            $observer->getEvent()->getOptions()->setIsMoveable(false);
        }
    }

    /**
     * Check whether sub category can be added
     *
     * @param Varien_Event_Observer $observer
     */
    public function catalogCategoryCanBeAdded($observer)
    {
        if ($this->_role->getIsAll()) { // because observer is passed through directly
            return;
        }

        $category = $observer->getEvent()->getCategory();
        /*
         * we can do checking only if we have current category
         */
        if ($category) {
            $categoryPath = $category->getPath();
            /*
             * If admin user has exclusive access to current category
             * he can add sub categories to it
             */
            if ($this->_role->hasExclusiveCategoryAccess($categoryPath)) {
                $observer->getEvent()->getOptions()->setIsAllow(true);
            } else {
                $observer->getEvent()->getOptions()->setIsAllow(false);
            }
        }
    }

    /**
     * Check whether root category can be added
     * Note: only user with full access can add root categories
     *
     * @param Varien_Event_Observer $observer
     */
    public function catalogRootCategoryCanBeAdded($observer)
    {
        if ($this->_role->getIsAll()) { // because observer is passed through directly
            return;
        }

        //if user has website or store restrictions he can't add root category
        $observer->getEvent()->getOptions()->setIsAllow(false);
    }

    /**
     * Restrict customer grid container
     *
     * @param Varien_Event_Observer $observer
     */
    public function widgetCustomerGridContainer($observer)
    {
        if (!$this->_role->getWebsiteIds()) {
            $observer->getEvent()->getBlock()->removeButton('add');
        }
    }

    /**
     * Restrict system stores page container
     *
     * @param Varien_Event_Observer $observer
     */
    public function widgetManageStores($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $block->removeButton('add');
        if (!$this->_role->getWebsiteIds()) {
            $block->removeButton('add_group');
            $block->removeButton('add_store');
        }
    }

    /**
     * Restrict product grid container
     *
     * @param Varien_Event_Observer $observer
     */
    public function widgetProductGridContainer($observer)
    {
        if (!$this->_role->getWebsiteIds()) {
            $observer->getEvent()->getBlock()->removeButton('add_new');
        }
    }

    /**
     * Restrict event grid container
     *
     * @param Varien_Event_Observer $observer
     */
    public function widgetCatalogEventGridContainer($observer)
    {
        if (!$this->_role->getWebsiteIds()) {
            $observer->getEvent()->getBlock()->removeButton('add');
        }
    }

    /**
     * Remove product attribute add button
     *
     * @param Varien_Event_Observer $observer
     */
    public function removeCatalogProductAttributeAddButton($observer)
    {
        $observer->getEvent()->getBlock()->removeButton('add');
    }

    /**
     * Remove product attribute save buttons
     *
     * @param Varien_Event_Observer $observer
     */
    public function removeCatalogProductAttributeButtons($observer)
    {
        $observer->getEvent()->getBlock()
            ->removeButton('save')
            ->removeButton('save_and_edit_button')
            ->removeButton('delete');
    }

    /**
     * Remove product attribute create button on product edit page
     *
     * @param Varien_Event_Observer $observer
     */
    public function disallowCreateAttributeButtonDisplay($observer)
    {
        if ($this->_role->getIsAll()) { // because observer is passed through directly
            return;
        }

        $observer->getEvent()->getBlock()->setCanShow(false);
    }

    /**
     * Remove attribute set management buttons on attribute set edit page
     *
     * @param Varien_Event_Observer $observer
     */
    public function removeAttributeSetControls($observer)
    {
        if ($this->_role->getIsAll()) { // because observer is passed through directly
            return;
        }

        $block = $observer->getEvent()->getBlock();

        $block->unsetChild('add_group_button');
        $block->unsetChild('delete_group_button');
        $block->unsetChild('save_button');
        $block->unsetChild('delete_button');
        $block->unsetChild('rename_button');

        $block->setIsReadOnly(true);
    }

    /**
     * Remove attribute set creation button on attribute set listing page
     *
     * @param Varien_Event_Observer $observer
     */
    public function removeAddNewAttributeSetButton($observer)
    {
        if ($this->_role->getIsAll()) { // because observer is passed through directly
            return;
        }

        $block = $observer->getEvent()->getBlock();

        $block->unsetChild('addButton');
    }

    /**
     * Remove edit buttons on catalog events page and catalog event edit page
     *
     * @param Varien_Event_Observer $observer
     */
    public function widgetCatalogEventCategoryEditButtons($observer)
    {
        if ($this->_role->getIsAll()) { // because observer is passed through directly
            return;
        }
        $block = $observer->getEvent()->getBlock();
        /* @var $block Enterprise_CatalogEvent_Block_Adminhtml_Catalog_Category_Edit_Buttons */
        if ($block) {
            $category = $block->getCategory();
            if ($category) {
                if ($this->_role->hasExclusiveCategoryAccess($category->getPath())) {
                    return;
                }
            }

            $block->removeAdditionalButton('add_event')
                ->removeAdditionalButton('edit_event');
        }
    }

    /**
     * Disables "Display Countdown Ticker On" checkboxes if user have not enough rights
     *
     * @param Varien_Event_Observer $observer
     */
    public function restrictCatalogEventEditForm($observer)
    {
        if ($this->_role->getIsAll()) {
            return;
        }
        $setDisabled = false;
        if (!$this->_role->getIsWebsiteLevel()) {
            $setDisabled = true;
        }
        else {
            $categoryId = $observer->getEvent()->getBlock()->getEvent()->getCategoryId();
            $path = Mage::getResourceModel('catalog/category')->getCategoryPathById($categoryId);
            if (!$this->_role->hasExclusiveCategoryAccess($path)) {
                $setDisabled = true;
            }

        }
        if ($setDisabled) {
            $element = $observer->getEvent()->getBlock()->getForm()
                       ->getElement('display_state_array');
            $element->setDisabled( array(Enterprise_CatalogEvent_Model_Event::DISPLAY_CATEGORY_PAGE,
                                         Enterprise_CatalogEvent_Model_Event::DISPLAY_PRODUCT_PAGE));
        }
    }

    /**
     * Set required Subscribers From field in newsletter queue form
     *
     * @param Varien_Event_Observer $observer
     */
    public function setIsRequiredSubscribersFromFieldForNewsletterQueueForm($observer)
    {
        $observer->getEvent()
            ->getBlock()
            ->getForm()
            ->getElement('stores')->setRequired(true)->addClass('required-entry');
    }

    /**
     * Set websites readonly flag for store-level users on mass update attributes
     *
     * @param Varien_Event_Observer $observer
     */
    public function catalogProductMassUpdateWebsites($observer)
    {
        $observer->getEvent()->getBlock()->setWebsitesReadonly(!$this->_role->getIsWebsiteLevel());
    }

    /**
     * Remove control buttons for store-level roles on Catalog Price Rules page
     *
     * @param Varien_Event_Observer $observer
     */
    public function removePromoCatalogButtons($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $block->removeButton('apply_rules');
        if ($this->_role->getIsStoreLevel()) {
            $block->removeButton('add');
        }
    }

    /**
     * Remove control buttons for store-level roles on Shopping Cart Price Rules page
     *
     * @param Varien_Event_Observer $observer
     */
    public function removePromoQuoteButtons($observer)
    {
        if ($this->_role->getIsStoreLevel()) {
            $block = $observer->getEvent()->getBlock()->removeButton('add');
        }
    }

    /**
     * Remove control buttons if user does not have exclusive access to current page
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_AdminGws_Model_Blocks
     */
    public function removeCmsPageButtons($observer)
    {
        $model = Mage::registry('cms_page');
        if ($model) {
            $storeIds = $model->getStoreId();
            if ($model->getId() && !$this->_role->hasExclusiveStoreAccess((array)$storeIds)) {
                $block = $observer->getEvent()->getBlock();
                $block->removeButton('save');
                $block->removeButton('saveandcontinue');
                $block->removeButton('delete');
            }
        }

        return $this;
    }

    /**
     * Remove control buttons if user does not have exclusive access to current block
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_AdminGws_Model_Blocks
     */
    public function removeCmsBlockButtons($observer)
    {
        $model = Mage::registry('cms_block');
        if ($model) {
            $storeIds = $model->getStoreId();
            if ($model->getId() && !$this->_role->hasExclusiveStoreAccess((array)$storeIds)) {
                $block = $observer->getEvent()->getBlock();
                $block->removeButton('save');
                $block->removeButton('saveandcontinue');
                $block->removeButton('delete');
            }
        }

        return $this;
    }

    /**
     * Remove control buttons if user does not have exclusive access to current poll
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_AdminGws_Model_Blocks
     */
    public function removePollButtons($observer)
    {
        $model = Mage::registry('poll_data');
        if ($model) {
            $storeIds = $model->getStoreIds();
            if ($model->getId() && !$this->_role->hasExclusiveStoreAccess((array)$storeIds)) {
                $block = $observer->getEvent()->getBlock();
                $block->removeButton('save');
                $block->removeButton('delete');
            }
        }

        return $this;
    }

    /**
     * Remove control buttons if user does not have exclusive access to current tag
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_AdminGws_Model_Blocks
     */
    public function removeTagButtons($observer)
    {
        $model = Mage::registry('current_tag');
        if ($model && $model->getId()) {
            $storeIds = (array)$model->getVisibleInStoreIds();
            $storeIds = array_filter($storeIds); // remove admin store with id 0
            if (!$this->_role->hasExclusiveStoreAccess((array)$storeIds)) {
                $block = $observer->getEvent()->getBlock();
                $block->removeButton('save');
                $block->removeButton('save_and_edit_button');
                $block->removeButton('delete');
            }
        }

        return $this;
    }

    /**
     * Remove buttons from staging grid for all GWS limited users
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_AdminGws_Model_Blocks
     */
    public function  removeStagingGridButtons($observer)
    {
        $observer->getEvent()->getBlock()->removeButton('add');

        return $this;
    }

    /**
     * Remove buttons from staging edit form for all GWS limited users
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_AdminGws_Model_Blocks
     */
    public function  removeStagingEditButtons($observer)
    {
        $observer->getEvent()->getBlock()
            ->unsetChild('merge_button')
            ->unsetChild('save_button')
            ->unsetChild('reset_status_button')
            ->unsetChild('unschedule_button')
            ->unsetChild('create_button');

        return $this;
    }

    /**
     * Remove buttons from backup grid for all GWS limited users
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_AdminGws_Model_Blocks
     */
    public function removeStagingBackupGridActions($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $block->setMassactionIdField(false);
        $column = $block->getColumn('action');
        if ($column) {
            $column->setActions(array());
        }

        return $this;
    }

    /**
     * Remove buttons from backup edit form for all GWS limited users
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_AdminGws_Model_Blocks
     */
    public function removeStagingBackupEditButtons($observer)
    {
        $observer->getEvent()->getBlock()
            ->unsetChild('rollback_button')
            ->unsetChild('delete_button');

        return $this;
    }

    /**
     * Remove action column and massaction functionality
     * from grid for users with limited permissions.
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_AdminGws_Model_Blocks
     */
    public function removeProcessListButtons($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $block->setMassactionIdField(false);
        $column = $block->getColumn('action');
        if ($column) {
            $column->setActions(array());
        }

        return $this;
    }

    /**
     * Remove buttons for save and reindex on process edit page.
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_AdminGws_Model_Blocks
     */
    public function removeProcessEditButtons($observer)
    {
        $observer->getEvent()->getBlock()
            ->removeButton('save')
            ->removeButton('reindex');

        return $this;
    }

    /**
     * Removing not allowed massactions for user with store level permissions.
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_AdminGws_Model_Blocks
     */
    public function removeNotAllowedMassactionsForOrders($observer)
    {
        if ($this->_role->getIsWebsiteLevel()) {
            return $this;
        }
        $massBlock = $observer->getEvent()->getBlock()->getMassactionBlock();
        /* @var $massBlock Mage_Adminhtml_Block_Widget_Grid_Massaction */
        if ($massBlock) {
            $massBlock->removeItem('cancel_order')
                ->removeItem('hold_order')
                ->removeItem('unhold_order');
        }

        return $this;
    }

    /**
     * Removing buttons from revision edit page wich can't be used
     * by users with limited permissions
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_AdminGws_Model_Blocks
     */
    public function removeRevisionEditButtons($observer)
    {
        $observer->getEvent()->getBlock()
            ->removeButton('publish')
            ->removeButton('save_publish');
    }

    /**
     * Removing publish button from preview screen to disallow
     * publishing for users with limited permissions
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_AdminGws_Model_Blocks
     */
    public function removePreviewPublishButton($observer)
    {
        $observer->getEvent()->getBlock()
            ->removeButton('publish');
    }

    /**
     * Remove massactions for limited user
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_AdminGws_Model_Blocks
     */
    public function removeTagGridActions($observer)
    {
        $massBlock = $observer->getEvent()->getBlock()->getMassactionBlock();
        /* @var $massBlock Mage_Adminhtml_Block_Widget_Grid_Massaction */
        if ($massBlock) {
            $massBlock->removeItem('delete');
        }

        return $this;
    }

    /**
     * Remove control buttons for store-level roles on Manage Gift Card Accounts page
     *
     * @param Varien_Event_Observer $observer
     */
    public function removeGiftCardAccountAddButton($observer)
    {
        if ($this->_role->getIsStoreLevel()) {
            $block = $observer->getEvent()->getBlock();
            if ($block) {
                $block->removeButton('add');
            }
        }
    }

    /**
     * Remove control buttons for limited user on Manage Currency Rates
     *
     * @param Varien_Event_Observer $observer
     */
    public function removeManageCurrencyRatesButtons($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block) {
            $block->unsetChild('save_button')
                ->unsetChild('import_button')
                ->unsetChild('import_services');
        }
    }

    /**
     * Remove add button for limited user on Transactional Emails
     *
     * @param Varien_Event_Observer $observer
     */
    public function removeTransactionalEmailsAddButton($observer)
    {
        $observer->getEvent()->getBlock()->unsetChild('add_button');
    }

    /**
     * Remove Transactional Emails edit page control buttons for limited user
     *
     * @param Varien_Event_Observer $observer
     */
    public function removeTransactionalEmailsEditButtons($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block) {
            $block->unsetChild('save_button')
                ->unsetChild('delete_button');
        }
    }

    /**
     * Remove buttons from transactional email template grid for all GWS limited users
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_AdminGws_Model_Blocks
     */
    public function  removeEmailTemplateGridButtons($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block) {
            $block->unsetChild('add_button');
        }

        return $this;
    }

    /**
     * Remove buttons from TargetRule grid for all GWS limited users
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_AdminGws_Model_Blocks
     */
    public function removeTargetRuleGridButtons($observer)
    {
        /* @var $block Enterprise_TargetRule_Block_Adminhtml_Targetrule */
        $block = $observer->getEvent()->getBlock();
        if ($block) {
            $block->removeButton('add');
        }
        return $this;
    }

    /**
     * Remove buttons from TargetRule Edit/View for all GWS limited users
     *
     * @param Varien_Event_Observer $observer
     * @return Enterprise_AdminGws_Model_Blocks
     */
    public function removeTargetRuleEditButtons($observer)
    {
        /* @var $block Enterprise_TargetRule_Block_Adminhtml_Targetrule_Edit */
        $block = $observer->getEvent()->getBlock();
        if ($block) {
            $block->removeButton('save');
            $block->removeButton('save_and_continue_edit');
            $block->removeButton('delete');
        }
        return $this;
    }
}
