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
 * Blocks limiter
 *
 */
class Magento_AdminGws_Model_Blocks extends Magento_AdminGws_Model_Observer_Abstract
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Initialize helper
     *
     * @param Magento_AdminGws_Model_Role $role
     * @param Magento_Core_Model_Registry $coreRegistry\
     */
    public function __construct(
        Magento_AdminGws_Model_Role $role,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($role);
    }

    /**
     * Check whether category can be moved
     *
     * @param Magento_Event_Observer $observer
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
     * @param Magento_Event_Observer $observer
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
     * @param Magento_Event_Observer $observer
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
     * @param Magento_Event_Observer $observer
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
     * @param Magento_Event_Observer $observer
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
     * @param Magento_Event_Observer $observer
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
     * @param Magento_Event_Observer $observer
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
     * @param Magento_Event_Observer $observer
     */
    public function removeCatalogProductAttributeAddButton($observer)
    {
        $observer->getEvent()->getBlock()->removeButton('add');
    }

    /**
     * Remove product attribute save buttons
     *
     * @param Magento_Event_Observer $observer
     */
    public function removeCatalogProductAttributeButtons($observer)
    {
        $observer->getEvent()->getBlock()
            ->removeButton('save')
            ->removeButton('save_and_edit_button')
            ->removeButton('delete');
    }

    /**
     * Disable fields in tab "Main" of edit product attribute form
     *
     * @param Magento_Event_Observer $observer
     */
    public function disableCatalogProductAttributeEditTabMainFields($observer)
    {
        foreach ($observer->getEvent()->getBlock()->getForm()->getElements() as $element) {
            if ($element->getType() == 'fieldset') {
                foreach ($element->getElements() as $field) {
                    $field->setReadonly(true);
                    $field->setDisabled(true);
                }
            }
        }
    }

    /**
     * Disable fields in tab "Manage Label / Options" of edit product attribute form
     *
     * @param Magento_Event_Observer $observer
     */
    public function disableCatalogProductAttributeEditTabOptionsFields($observer)
    {
        $observer->getEvent()->getBlock()->setReadOnly(true);
    }

    /**
     * Remove product attribute create button on product edit page
     *
     * @param Magento_Event_Observer $observer
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
     * @param Magento_Event_Observer $observer
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
     * @param Magento_Event_Observer $observer
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
     * Remove customer attribute creation button from grid container
     *
     * @param Magento_Event_Observer $observer
     */
    public function removeAddNewCustomerAttributeButton($observer)
    {
        if ($this->_role->getIsAll()) { // because observer is passed through directly
            return;
        }
        $block = $observer->getEvent()->getBlock();
        $block->removeButton('add');
    }

    /**
     * Remove customer attribute deletion button from form container
     *
     * @param Magento_Event_Observer $observer
     */
    public function removeDeleteCustomerAttributeButton($observer)
    {
        if ($this->_role->getIsAll()) { // because observer is passed through directly
            return;
        }
        $block = $observer->getEvent()->getBlock();
        $block->removeButton('delete');
    }

    /**
     * Remove edit buttons on catalog events page and catalog event edit page
     *
     * @param Magento_Event_Observer $observer
     */
    public function widgetCatalogEventCategoryEditButtons($observer)
    {
        if ($this->_role->getIsAll()) { // because observer is passed through directly
            return;
        }
        $block = $observer->getEvent()->getBlock();
        /* @var $block Magento_CatalogEvent_Block_Adminhtml_Catalog_Category_Edit_Buttons */
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
     * @param Magento_Event_Observer $observer
     */
    public function restrictCatalogEventEditForm($observer)
    {
        if ($this->_role->getIsAll()) {
            return;
        }
        $setDisabled = false;
        if (!$this->_role->getIsWebsiteLevel()) {
            $setDisabled = true;
        } else {
            $categoryId = $observer->getEvent()->getBlock()->getEvent()->getCategoryId();
            $path = Mage::getResourceModel('Magento_Catalog_Model_Resource_Category')->getCategoryPathById($categoryId);
            if (!$this->_role->hasExclusiveCategoryAccess($path)) {
                $setDisabled = true;
            }

        }
        if ($setDisabled) {
            $element = $observer->getEvent()->getBlock()->getForm()
                       ->getElement('display_state_array');
            $element->setDisabled( array(Magento_CatalogEvent_Model_Event::DISPLAY_CATEGORY_PAGE,
                                         Magento_CatalogEvent_Model_Event::DISPLAY_PRODUCT_PAGE));
        }
    }

    /**
     * Set required Subscribers From field in newsletter queue form
     *
     * @param Magento_Event_Observer $observer
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
     * @param Magento_Event_Observer $observer
     */
    public function catalogProductMassUpdateWebsites($observer)
    {
        $observer->getEvent()->getBlock()->setWebsitesReadonly(!$this->_role->getIsWebsiteLevel());
    }

    /**
     * Remove 'delete' button for store-level roles on Catalog Product page
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function catalogProductPrepareMassaction($observer)
    {
        if ($this->_role->getIsStoreLevel()) {
            $massBlock = $observer->getEvent()->getBlock()->getMassactionBlock();
            /* @var $massBlock Magento_Adminhtml_Block_Widget_Grid_Massaction */
            if ($massBlock) {
                $massBlock->removeItem('delete');
            }
        }

        return $this;
    }

    /**
     * Remove control buttons if user does not have exclusive access to current model
     *
     * @param Magento_Event_Observer $observer
     * @param string $registryKey
     * @param array $buttons
     * @return Magento_AdminGws_Model_Blocks
     */
    private function _removeButtons($observer, $registryKey, $buttons = array())
    {
        /* @var $model Magento_Core_Model_Abstract */
        $model = $this->_coreRegistry->registry($registryKey);
        if ($model) {
            $storeIds = $model->getStoreId();
            if ($model->getId() && !$this->_role->hasExclusiveStoreAccess((array)$storeIds)) {
                $block = $observer->getEvent()->getBlock();
                foreach ($buttons as $buttonName) {
                    $block->removeButton($buttonName);
                }
            }
        }
        return $this;
    }

    /**
     * Remove control buttons if user does not have exclusive access to current page
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeCmsPageButtons($observer)
    {
        $this->_removeButtons($observer, 'cms_page', array('save', 'saveandcontinue', 'delete'));
        return $this;
    }

    /**
     * Remove control buttons if user does not have exclusive access to current block
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeCmsBlockButtons($observer)
    {
        $this->_removeButtons($observer, 'cms_block', array('save', 'saveandcontinue', 'delete'));
        return $this;
    }

    /**
     * Remove control buttons if user does not have exclusive access to current reward rate
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeRewardRateButtons($observer)
    {
        /* @var $model Magento_Reward_Model_Resource_Reward_Rate */
        $model = $this->_coreRegistry->registry('current_reward_rate');
        if ($model) {
            if ($model->getId() && !in_array($model->getWebsiteId(), $this->_role->getWebsiteIds())) {
                $block = $observer->getEvent()->getBlock();
                foreach (array('save', 'delete') as $buttonName) {
                    $block->removeButton($buttonName);
                }
            }
        }
        return $this;
    }

    /**
     * Remove fetch button if user doesn't have exclusive access to order
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeSalesTransactionControlButtons($observer)
    {
        $model = $this->_coreRegistry->registry('current_transaction');
        if ($model) {
            $websiteId = $model->getOrderWebsiteId();
            if (!$this->_role->hasWebsiteAccess($websiteId, true)) {
                $block = $observer->getEvent()->getBlock();
                $block->removeButton('fetch');
            }
        }
        return $this;
    }

    /**
     * Remove buttons from gift wrapping edit form for all GWS limited users
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeGiftWrappingEditButtons($observer)
    {
        // Remove delete button
        $observer->getEvent()->getBlock()->removeButton('delete');
        return $this;
    }

    /**
     * Remove 'delete' action from Gift Wrapping grid for all GWS limited users
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeGiftWrappingForbiddenMassactions($observer)
    {
        $massBlock = $observer->getEvent()->getBlock()->getMassactionBlock();
        /** @var $massBlock Magento_Adminhtml_Block_Widget_Grid_Massaction */
        if ($massBlock) {
            $massBlock->removeItem('delete');
        }
        return $this;
    }

    /**
     * Remove buttons from rating edit form (in Manage Ratings) for all GWS limited users
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeRatingEditButtons($observer)
    {
        // Remove delete button
        $observer->getEvent()->getBlock()->removeButton('delete');
        return $this;
    }

    /**
     * Remove action column and massaction functionality
     * from grid for users with limited permissions.
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
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
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
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
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeNotAllowedMassactionsForOrders($observer)
    {
        if ($this->_role->getIsWebsiteLevel()) {
            return $this;
        }
        $massBlock = $observer->getEvent()->getBlock()->getMassactionBlock();
        /* @var $massBlock Magento_Adminhtml_Block_Widget_Grid_Massaction */
        if ($massBlock) {
            $massBlock->removeItem('cancel_order')
                ->removeItem('hold_order')
                ->removeItem('unhold_order');
        }

        return $this;
    }

    /**
     * Removing buttons from revision edit page which can't be used
     * by users with limited permissions
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeRevisionEditButtons($observer)
    {
        /* @var $model Magento_Cms_Model_Page */
        $model = $this->_coreRegistry->registry('cms_page');
        if ($model && $model->getId()) {
            $storeIds = Mage::getResourceSingleton('Magento_Cms_Model_Resource_Page')
                ->lookupStoreIds($model->getPageId());
            if (!$this->_role->hasExclusiveStoreAccess($storeIds)) {
                $observer->getEvent()->getBlock()
                    ->removeButton('publish')
                    ->removeButton('save_publish');
            }
        }
    }

    /**
     * Removing publish button from preview screen to disallow
     * publishing for users with limited permissions
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removePreviewPublishButton($observer)
    {
        $model = $this->_coreRegistry->registry('cms_page');
        if ($model && $model->getId()) {
            $storeIds = Mage::getResourceSingleton('Magento_Cms_Model_Resource_Page')
                ->lookupStoreIds($model->getPageId());
            if (!$this->_role->hasExclusiveStoreAccess($storeIds)) {
                $observer->getEvent()->getBlock()
                    ->removeButton('publish');
            }
        }
    }

    /**
     * Remove control buttons for website-level roles on Manage Gift Card Accounts page
     *
     * @param Magento_Event_Observer $observer
     */
    public function removeGiftCardAccountAddButton($observer)
    {
        if (!$this->_role->getIsWebsiteLevel()) {
            $block = $observer->getEvent()->getBlock();
            if ($block) {
                $block->removeButton('add');
            }
        }
    }

    /**
     * Remove control buttons for website-level roles on Gift Card Account Edit page
     *
     * @param Magento_Event_Observer $observer
     */
    public function removeGiftCardAccountControlButtons($observer)
    {
        if (!$this->_role->getIsWebsiteLevel()) {
            $block = $observer->getEvent()->getBlock();
            if ($block) {
                $block->removeButton('delete');
                $block->removeButton('save');
                $block->removeButton('send');
            }
        }
    }

    /**
     * Remove control buttons for limited user on Manage Currency Rates
     *
     * @param Magento_Event_Observer $observer
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
     * Remove Transactional Emails edit page control buttons for limited user
     *
     * @param Magento_Event_Observer $observer
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
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeEmailTemplateGridButtons($observer)
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
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeTargetRuleGridButtons($observer)
    {
        /* @var $block Magento_TargetRule_Block_Adminhtml_Targetrule */
        $block = $observer->getEvent()->getBlock();
        if ($block) {
            $block->removeButton('add');
        }
        return $this;
    }

    /**
     * Remove buttons from TargetRule Edit/View for all GWS limited users
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeTargetRuleEditButtons($observer)
    {
        /* @var $block Magento_TargetRule_Block_Adminhtml_Targetrule_Edit */
        $block = $observer->getEvent()->getBlock();
        if ($block) {
            $block->removeButton('save');
            $block->removeButton('save_and_continue_edit');
            $block->removeButton('delete');
        }
        return $this;
    }

    /**
     * Disable Rule-based Settings for all GWS limited users
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function readonlyTargetRuleProductAttribute($observer)
    {
        /* @var $block Magento_TargetRule_Block_Adminhtml_Product */
        $block = $observer->getEvent()->getBlock();
        if ($block) {
            $access = $this->_role->hasWebsiteAccess($block->getProduct()->getWebsiteIds(), true);
            if ((!$block->getProduct()->isObjectNew() && !$access) || $block->getProduct()->isReadonly()) {
                $block->setIsReadonly(true);
            }
        }
    }

    /**
     * Validate permissions for Catalog Permission tab Settings for all GWS limited users
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function validateCatalogPermissions($observer)
    {
        /* @var $block Magento_CatalogPermissions_Block_Adminhtml_Catalog_Category_Tab_Permissions */
        $block = $observer->getEvent()->getBlock();
        if ($block) {
            /* @var $row Magento_CatalogPermissions_Block_Adminhtml_Catalog_Category_Tab_Permissions_Row */
            $row = $block->getChildBlock('row');
            if ($this->_role->getIsWebsiteLevel()) {
                $websiteIds = $this->_role->getWebsiteIds();
                $block->setAdditionConfigData(array('limited_website_ids' => $websiteIds));
            } else if ($this->_role->getIsStoreLevel()) {
                $block->getCategory()->setPermissionsReadonly(true);
                $addButton = $block->getChildBlock('add_button');
                if ($addButton) {
                    $addButton->setDisabled(true)
                        ->setClass($addButton->getClass() . ' disabled');
                }
                if ($row) {
                    $deleteButton = $row->getChildBlock('delete_button');
                    if ($deleteButton) {
                        $addButton->setDisabled(true)
                            ->setClass($deleteButton->getClass() . ' disabled');
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Remove buttons for widget instance editing if user does not have exclusive access
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeWidgetInstanceButtons($observer)
    {
        /* @var $block Magento_Widget_Block_Adminhtml_Widget_Instance_Edit */
        $block = $observer->getEvent()->getBlock();
        $widgetInstance = $block->getWidgetInstance();
        if ($widgetInstance->getId()) {
            $storeIds = $widgetInstance->getStoreIds();
            if (!$this->_role->hasExclusiveStoreAccess((array)$storeIds)) {
                $block->removeButton('save');
                $block->removeButton('save_and_edit_button');
                $block->removeButton('delete');
            }
        }
        return $this;
    }

    /**
     * Remove buttons for banner editing if user does not have exclusive access
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function validateBannerPermissions($observer)
    {
        /* @var Magento_Banner_Block_Adminhtml_Banner_Edit */
        $block = $observer->getEvent()->getBlock();
        $model = $this->_coreRegistry->registry('current_banner');
        if ($block && $model) {
            if (!$this->_role->hasExclusiveStoreAccess((array)$model->getStoreIds())) {
                $block->removeButton('reset');
                $block->removeButton('delete');
                $block->removeButton('save');
                $block->removeButton('save_and_edit_button');
            }
        }
        return $this;
    }

    /**
     * Validate permissions for Banner Content tab for all GWS limited users
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function disableAllStoreViewsContentFeild($observer)
    {
        $model = $observer->getEvent()->getModel();
        if (!$this->_role->getIsAll() && $model) {
             $model->setCanSaveAllStoreViewsContent(false);
        }
        return $this;
    }

    /**
     * Remove Save Hierarchy button if GWS permissions are applicable
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeCmsHierarchyFormButtons($observer)
    {
        if (!$this->_role->getIsAll()) {
            $observer->getEvent()->getBlock()->removeButton('save');
        }

        return $this;
    }

    /**
     * Add append restriction flag to hierarchy nodes
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function prepareCmsHierarchyNodes($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $nodes = $block->getNodes();
        if ($nodes) {
            if (is_array($nodes)) {
                $nodesAssoc = array();
                foreach ($nodes as $node) {
                    $nodesAssoc[$node['node_id']] = $node;
                }

                foreach ($nodesAssoc as $nodeId => $node) {
                    // define parent page/node
                    $parent = isset($nodesAssoc[$node['parent_node_id']]) ? $nodesAssoc[$node['parent_node_id']] : null;
                    $parentDenied = $parent !== null
                                 && isset($parent['append_denied'])
                                 && $parent['append_denied'] === true;

                    // If appending is denied for parent - deny it for child
                    if ($parentDenied || !$node['page_id']) {
                        $nodesAssoc[$nodeId]['append_denied'] = $parentDenied;
                    } else {
                        $nodesAssoc[$nodeId]['append_denied'] = !$this->_role->hasStoreAccess(
                            $node['assigned_to_stores']
                        );
                    }
                }
                $block->setNodes(array_values($nodesAssoc));
            }
        }

        return $this;
    }

    /**
     * Remove add button for all GWS limited users
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeCustomerGroupAddButton($observer)
    {
        $observer->getEvent()->getBlock()->removeButton('add');
        return $this;
    }

    /**
     * Remove control buttons for all GWS limited users
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeCustomerGroupControlButtons($observer)
    {
        $observer->getEvent()->getBlock()->removeButton('save');
        $observer->getEvent()->getBlock()->removeButton('delete');
        return $this;
    }

    /**
     * Remove control buttons for all GWS limited users with no exclusive rights
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeCatalogEventControlButtons($observer)
    {
        $block = $observer->getEvent()->getBlock();
        $eventCategoryId = $block->getEvent()->getCategoryId();
        $categoryPath = Mage::getResourceSingleton('Magento_Catalog_Model_Resource_Category')
            ->getCategoryPathById($eventCategoryId);
        if (!$this->_role->hasExclusiveCategoryAccess($categoryPath)) {
            $block->removeButton('save');
            $block->removeButton('save_and_continue');
            $block->removeButton('delete');
        }
        return $this;
    }

    /**
     * Remove add button for all GWS limited users
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeTaxRuleAddButton($observer)
    {
        $observer->getEvent()->getBlock()->removeButton('add');
        return $this;
    }

    /**
     * Remove control buttons for all GWS limited users
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeTaxRuleControlButtons($observer)
    {
        $observer->getEvent()->getBlock()
            ->removeButton('save')
            ->removeButton('save_and_continue')
            ->removeButton('delete');
        return $this;
    }

    /**
     * Remove add button for all GWS limited users
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeTaxRateAddButton($observer)
    {
        $observer->getEvent()->getBlock()->unsetChild('addButton');
        return $this;
    }

    /**
     * Remove control buttons for all GWS limited users
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeTaxRateControlButtons($observer)
    {
        $observer->getEvent()->getBlock()
            ->unsetChild('saveButton')
            ->unsetChild('deleteButton');
        return $this;
    }

    /**
     * Remove Import possibility for all GWS limited users
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeTaxRateImport($observer)
    {
        $observer->getEvent()->getBlock()->setIsReadonly(true);
        return $this;
    }

    /**
     * Remove rule entity grid buttons for users who does not have any permissions
     *
     * @param Magento_Event_Observer $observer
     *
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeRuleEntityGridButtons($observer)
    {
        /* @var $block Magento_Adminhtml_Block_Widget_Grid_Container */
        $block = $observer->getEvent()->getBlock();
        // Remove "Apply Rules" button at catalog rules grid for all GWS limited users
        if ($block) {
            $block->removeButton('apply_rules');
        }

        // Remove "Add" button if role has no allowed website ids
        if (!$this->_role->getWebsiteIds()) {
            if ($block) {
                $block->removeButton('add');
            }
        }
        return $this;
    }

    /**
     * Remove rule entity edit buttons for users who does not have any permissions or does not have full permissions
     *
     * @param Magento_Event_Observer $observer
     *
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeRuleEntityEditButtons($observer)
    {
        /* @var $block Magento_Adminhtml_Block_Widget_Grid_Container */
        $block = $observer->getEvent()->getBlock();
        if (!$block) {
             return true;
        }

        $controllerName = $block->getRequest()->getControllerName();

        // Determine rule entity object registry key
        switch ($controllerName) {
            case 'promo_catalog':
                $registryKey = 'current_promo_catalog_rule';
                break;
            case 'promo_quote':
                $registryKey = 'current_promo_quote_rule';
                break;
            case 'reminder':
                $registryKey = 'current_reminder_rule';
                break;
            case 'customersegment':
                $registryKey = 'current_customer_segment';
                break;
            default:
                $registryKey = null;
                break;
        }

        if (is_null($registryKey)) {
            return true;
        }

        /** @var $model Magento_Rule_Model_Rule */
        $model = $this->_coreRegistry->registry($registryKey);
        if ($model) {
            $websiteIds = $model->getWebsiteIds();
            $block->removeButton('save_apply');
            if ($model->getId() && !$this->_role->hasExclusiveAccess((array)$websiteIds)) {
                $block->removeButton('save');
                $block->removeButton('save_and_continue_edit');
                $block->removeButton('run_now');
                $block->removeButton('match_customers');
                $block->removeButton('delete');
            }
        }

        return $this;
    }

    /**
     * Remove button "Add RMA Attribute" for all GWS limited users
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeRmaAddAttributeButton($observer)
    {
        $observer->getEvent()->getBlock()->removeButton('add');
        return $this;
    }

    /**
     * Remove "Delete Attribute" button for all GWS limited users
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeRmaDeleteAttributeButton($observer)
    {
        $observer->getEvent()->getBlock()->removeButton('delete');
        return $this;
    }

    /**
     * Disable "Delete Attribute Option" Button for all GWS limited users
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function disableRmaAttributeDeleteOptionButton($observer)
    {
        $deleteButton = $observer->getEvent()->getBlock()->getChildBlock('delete_button');

        if ($deleteButton) {
            $deleteButton->setDisabled(true);
        }

        return $this;
    }





    /**
     * Remove add button for users who does not permissions for any site
     *
     * @deprecated after 1.11.2.0 use $this->removeRuleEntityGridButtons() instead
     *
     * @param Magento_Event_Observer $observer
     *
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removeCustomerSegmentAddButton($observer)
    {
        $this->removeRuleEntityGridButtons($observer);
        return $this;
    }

    /**
     * Remove control buttons for store-level roles on Catalog Price Rules page
     *
     * @deprecated after 1.11.2.0 use $this->removeRuleEntityGridButtons() instead
     *
     * @param Magento_Event_Observer $observer
     *
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removePromoCatalogButtons($observer)
    {
        $this->removeRuleEntityGridButtons($observer);
        return $this;
    }

    /**
     * Remove control buttons for store-level roles on Shopping Cart Price Rules page
     *
     * @deprecated after 1.11.2.0 use $this->removeRuleEntityGridButtons() instead
     *
     * @param Magento_Event_Observer $observer
     *
     * @return Magento_AdminGws_Model_Blocks
     */
    public function removePromoQuoteButtons($observer)
    {
        $this->removeRuleEntityGridButtons($observer);
        return $this;
    }

    /**
     * Disable tax class and rate editable multiselects on the "Manage Tax Rule" page
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_AdminGws_Model_Blocks
     */
    public function disableTaxRelatedMultiselects($observer)
    {
        /**
         * @var $form Magento_Data_Form
         */
        $form = $observer->getEvent()->getBlock()->getForm();
        $form->getElement('tax_customer_class')->setDisabled(true);
        $form->getElement('tax_product_class')->setDisabled(true);
        $form->getElement('tax_rate')->setDisabled(true);

        return $this;
    }

}
