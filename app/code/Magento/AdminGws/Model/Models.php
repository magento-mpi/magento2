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
 * Models limiter
 *
 */
namespace Magento\AdminGws\Model;

class Models extends \Magento\AdminGws\Model\Observer\AbstractObserver
{
    /**
     * Admin gws data
     *
     * @var \Magento\AdminGws\Helper\Data
     */
    protected $_adminGwsData = null;
    /**
     * Catalog category factory
     *
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory = null;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager = null;

    /**
     * @param \Magento\AdminGws\Helper\Data $adminGwsData
     * @param \Magento\AdminGws\Model\Role $role
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\AdminGws\Helper\Data $adminGwsData,
        \Magento\AdminGws\Model\Role $role,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($role);
        $this->_adminGwsData = $adminGwsData;
        $this->_categoryFactory = $categoryFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * Limit CMS page save
     *
     * @param \Magento\Cms\Model\Page $model
     */
    public function cmsPageSaveBefore($model)
    {
        $originalStoreIds = $model->getResource()->lookupStoreIds($model->getId());
        if ($model->getId() && !$this->_role->hasStoreAccess($originalStoreIds)) {
            $this->_throwSave();
        }

        if (!$this->_role->hasExclusiveStoreAccess($originalStoreIds)) {
            $this->_throwSave();
        }
        $model->setData('stores', $this->_forceAssignToStore($this->_updateSavingStoreIds(
            $model->getData('stores'), $originalStoreIds
        )));
    }

    /**
     * Limit CMS block save
     *
     * @param \Magento\Cms\Model\Block $model
     */
    public function cmsBlockSaveBefore($model)
    {
        $originalStoreIds = $model->getResource()->lookupStoreIds($model->getId());
        if ($model->getId() && !$this->_role->hasStoreAccess($originalStoreIds)) {
            $this->_throwSave();
        }

        if (!$this->_role->hasExclusiveStoreAccess($originalStoreIds)) {
            $this->_throwSave();
        }

        $model->setData('stores', $this->_forceAssignToStore($this->_updateSavingStoreIds(
            $model->getData('stores'), $originalStoreIds
        )));
    }

    /**
     * Limit Rule entity saving
     *
     * @param \Magento\Rule\Model\Rule $model
     *
     * @return void
     */
    public function ruleSaveBefore($model)
    {
        // Deny creating new rule entity if role has no allowed website ids
        if (!$model->getId() && !$this->_role->getIsWebsiteLevel()) {
            $this->_throwSave();
        }

        $websiteIds = (array)$model->getOrigData('website_ids');
        // Deny saving rule entity if role has no exclusive access to assigned to rule entity websites
        // Check if original websites list is empty implemented to deny saving target rules for all GWS limited users
        if ($model->getId() && (!$this->_role->hasExclusiveAccess($websiteIds) || empty($websiteIds))) {
            $this->_throwSave();
        }
    }

    /**
     * Limit Reward Exchange Rate entity saving
     *
     * @param \Magento\Reward\Model\Resource\Reward\Rate $model
     * @return void
     */
    public function rewardRateSaveBefore($model)
    {
        // Deny creating new Reward Exchange Rate entity if role has no allowed website ids
        if (!$model->getId() && !$this->_role->getIsWebsiteLevel()) {
            $this->_throwSave();
        }

        // Deny saving Reward Rate entity if role has no exclusive access to assigned to Rate entity website
        // Check if original websites list is empty implemented to deny saving target Rate for all GWS limited users
        if (!$this->_role->hasExclusiveAccess((array)$model->getData('website_id'))
            || ($model->getId() && !$this->_role->hasExclusiveAccess((array)$model->getOrigData('website_id')))
        ) {
            $this->_throwSave();
        }
    }

    /**
     * Limit Reward Exchange Rate entity delete
     *
     * @param \Magento\Reward\Model\Resource\Reward\Rate $model
     * @return void
     */
    public function rewardRateDeleteBefore($model)
    {
        if (!$this->_role->getIsWebsiteLevel()) {
            $this->_throwDelete();
        }

        $websiteIds = (array)$model->getData('website_id');
        if (!$this->_role->hasExclusiveAccess($websiteIds)) {
            $this->_throwDelete();
        }
    }

    /**
     * Validate rule before delete
     *
     * @param \Magento\Rule\Model\Rule $model
     * @return void
     */
    public function ruleDeleteBefore($model)
    {
        $originalWebsiteIds = (array)$model->getOrigData('website_ids');

        // Deny deleting rule entity if role has no exclusive access to assigned to rule entity websites
        // Check if original websites list is empty implemented to deny deleting target rules for all GWS limited users
        if (!$this->_role->hasExclusiveAccess($originalWebsiteIds) || empty($originalWebsiteIds)) {
            $this->_throwDelete();
        }
    }

    /**
     * Limit rule entity model on after load
     *
     * @param \Magento\Rule\Model\Rule $model
     *
     * @return void
     */
    public function ruleLoadAfter($model)
    {
        $websiteIds = (array)$model->getData('website_ids');

        // Set rule entity model as non-deletable if role has no exclusive access to assigned to rule entity websites
        if (!$this->_role->hasExclusiveAccess($websiteIds)) {
            $model->setIsDeleteable(false);
        }

        // Set rule entity model as readonly if role has no allowed website ids
        if (!$this->_role->getIsWebsiteLevel()) {
            $model->setIsReadonly(true);
        }
    }

    /**
     * Limit newsletter queue save
     *
     * @param \Magento\Newsletter\Model\Queue $model
     */
    public function newsletterQueueSaveBefore($model)
    {
        // force to assign to SV
        $storeIds = $model->getStores();
        if (!$storeIds || !$this->_role->hasStoreAccess($storeIds)) {
            throw new \Magento\Core\Exception(__('Please assign this entity to a store view.'));
        }

        // make sure disallowed store ids won't be modified
        $originalStores = $model->getResource()->getStores($model);
        $model->setStores($this->_updateSavingStoreIds($storeIds, $originalStores));
    }

    /**
     * Prevent loading disallowed queue
     *
     * @param \Magento\Newsletter\Model\Queque $model
     */
    public function newsletterQueueLoadAfter($model)
    {
        if (!$this->_role->hasStoreAccess($model->getStores())) {
            $this->_throwLoad();
        }
    }

    /**
     * Catalog product initialize after loading
     *
     * @param \Magento\Catalog\Model\Product $model
     * @return void
     */
    public function catalogProductLoadAfter($model)
    {
        if (!$model->getId()) {
            return;
        }

        if (!$this->_role->hasWebsiteAccess($model->getWebsiteIds())) {
            $this->_throwLoad();
        }

        //var_dump($this->_role->hasExclusiveAccess($model->getWebsiteIds()));
        //echo "|";
        if (!$this->_role->hasExclusiveAccess($model->getWebsiteIds())) {
            //echo "here?";
            $model->unlockAttributes();

            $attributes = $model->getAttributes();
            foreach ($attributes as $attribute) {
                /* @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
                if ($attribute->isScopeGlobal() ||
                    ($attribute->isScopeWebsite() && count($this->_role->getWebsiteIds())==0) ||
                    !in_array($model->getStore()->getId(), $this->_role->getStoreIds())) {
                    $model->lockAttribute($attribute->getAttributeCode());
                }
            }

            $model->setInventoryReadonly(true);
            $model->setRelatedReadonly(true);
            $model->setCrosssellReadonly(true);
            $model->setUpsellReadonly(true);
            $model->setWebsitesReadonly(true);
            $model->lockAttribute('website_ids');
            $model->setOptionsReadonly(true);
            $model->setCompositeReadonly(true);
            if (!in_array($model->getStore()->getId(), $this->_role->getStoreIds())) {
                $model->setAttributesConfigurationReadonly(true);
            }
            $model->setDownloadableReadonly(true);
            $model->setGiftCardReadonly(true);
            $model->setIsDeleteable(false);
            $model->setIsDuplicable(false);
            $model->unlockAttribute('category_ids');

            foreach ($model->getCategoryCollection() as $category) {
                $path = implode("/", array_reverse($category->getPathIds()));
                if(!$this->_role->hasExclusiveCategoryAccess($path)) {
                    $model->setCategoriesReadonly(true);
                    $model->lockAttribute('category_ids');
                    break;
                }
            }

            if (!$this->_role->hasStoreAccess($model->getStoreIds())) {
                $model->setIsReadonly(true);
            }
        } else {
            /*
             * We should check here amount of websites to which admin user assigned
             * and not to those product itself. So if admin user assigned
             * only to one website we will disable ability to unassign product
             * from this one website
             */
            if (count($this->_role->getWebsiteIds()) == 1) {
                $model->setWebsitesReadonly(true);
                $model->lockAttribute('website_ids');
            }
        }
    }

    /**
     * Catalog product validate before saving
     *
     * @param \Magento\Catalog\Model\Product $model
     */
    public function catalogProductSaveBefore($model)
    {
        // no creating products
        if (!$model->getId() && !$this->_role->getIsWebsiteLevel()) {
            $this->_throwSave();
        }

        // Disallow saving in scope of wrong store.
        // Checking store_ids bc we should check exclusive product rights on
        // all assigned stores not only on current one.
        if (($model->getId() || !$this->_role->getIsWebsiteLevel()) &&
            !$this->_role->hasStoreAccess($model->getStoreIds())) {
            $this->_throwSave();
        }

        $websiteIds     = $this->_adminGwsData->explodeIds($model->getWebsiteIds());
        $origWebsiteIds = $model->getResource()->getWebsiteIds($model);

        if ($this->_role->getIsWebsiteLevel()) {
            // must assign to website
            $model->setWebsiteIds($this->_forceAssignToWebsite(
                $this->_updateSavingWebsiteIds($websiteIds, $origWebsiteIds)
            ));
        }

        // must not assign to wrong website
        if ($model->getId() && !$this->_role->hasWebsiteAccess($model->getWebsiteIds())) {
            $this->_throwSave();
        }
    }

    /**
     * Catalog product validate after
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\AdminGws\Model\Models
     */
    public function catalogProductValidateAfter(\Magento\Event\Observer $observer)
    {
        if ($this->_role->getIsAll()) {
            return;
        }

        /* @var $product \Magento\Catalog\Model\Product */
        $product = $observer->getEvent()->getProduct();
        $this->_forceAssignToWebsite($product->getWebsiteIds());
    }

    /**
     * Catalog product validate before delete
     *
     * @param \Magento\Catalog\Model\Product $model
     */
    public function catalogProductDeleteBefore($model)
    {
        // deleting only in exclusive mode
        if (!$this->_role->hasExclusiveAccess($model->getWebsiteIds())) {
            $this->_throwDelete();
        }
    }

    /**
     * Catalog Product Review before save
     *
     * @param  \Magento\Review\Model\Review
     */
    public function catalogProductReviewSaveBefore($model){
        $reviewStores = $model->getStores();
        $storeIds = $this->_role->getStoreIds();

        $allowedIds = array_intersect($reviewStores, $storeIds);

        if (empty($allowedIds)) {
            $this->_throwSave();
        }
    }

    /**
     * Catalog Product Review before delete
     *
     * @param  \Magento\Review\Model\Review
     */
    public function catalogProductReviewDeleteBefore($model){
        $reviewStores = $model->getStores();
        $storeIds = $this->_role->getStoreIds();

        $allowedIds = array_intersect($reviewStores, $storeIds);

        if (empty($allowedIds)) {
            $this->_throwDelete();
        }
    }

    /**
     * Catalog category validate before delete
     *
     * @param \Magento\Catalog\Model\Product $model
     * @return void
     */
    public function catalogCategoryDeleteBefore($model)
    {
        // no deleting in store group level mode
        if ($this->_role->getIsStoreLevel()) {
            $this->_throwDelete();
        }

        // no deleting category from disallowed path (no deleting root categories at all)
        if (!$this->_role->hasExclusiveCategoryAccess($model->getPath())) {
            $this->_throwDelete();
        }
    }

    /**
     * Validate customer before delete
     *
     * @param \Magento\Customer\Model\Customer $model
     * @return void
     */
    public function customerDeleteBefore($model)
    {
        if (!in_array($model->getWebsiteId(), $this->_role->getWebsiteIds())) {
            $this->_throwDelete();
        }
    }

    /**
     * Save correct website list in giftwrapping
     *
     * @param \Magento\GiftWrapping\Model\Wrapping $model
     * @return \Magento\AdminGws\Model\Models
     */
    public function giftWrappingSaveBefore($model)
    {
        if (!$model->isObjectNew()) {
            $roleWebsiteIds = $this->_role->getRelevantWebsiteIds();
            // Website list that was assigned to current giftwrapping previously
            $origWebsiteIds = (array)$model->getResource()->getWebsiteIds($model->getId());
            // Website list that admin is currently trying to assign to current giftwrapping
            $postWebsiteIds = array_intersect((array)$model->getWebsiteIds(), $roleWebsiteIds);

            $websiteIds = array_merge(array_diff($origWebsiteIds, $roleWebsiteIds), $postWebsiteIds);

            $model->setWebsiteIds($websiteIds);
        }
        return $this;
    }

    /**
     * Save correct store list in rating (while Managing Ratings)
     *
     * @param \Magento\Rating\Model\Rating $model
     * @return \Magento\AdminGws\Model\Models
     */
    public function ratingSaveBefore($model)
    {
        if (!$model->isObjectNew()) {
            $roleStoreIds = $this->_role->getStoreIds();
            // Store list that was assigned to current rating previously
            $origStoreIds = (array)$model->getResource()->getStores($model->getId());
            // Store list that admin is currently trying to assign to current rating
            $postStoreIds = array_intersect((array)$model->getStores(), $roleStoreIds);

            $storeIds = array_merge(array_diff($origStoreIds, $roleStoreIds), $postStoreIds);

            $model->setStores($storeIds);
        }

    }

    /**
     * Validate cms page before delete
     *
     * @param \Magento\Cms\Model\Page $model
     * @return void
     */
    public function cmsPageDeleteBefore($model)
    {
        $originalStoreIds = $model->getResource()->lookupStoreIds($model->getId());
        if (!$this->_role->hasExclusiveStoreAccess($originalStoreIds)) {
            $this->_throwDelete();
        }
    }

    /**
     * Validate cms page before delete
     *
     * @param \Magento\Cms\Model\Page $model
     * @return void
     */
    public function cmsBlockDeleteBefore($model)
    {
        $originalStoreIds = $model->getResource()->lookupStoreIds($model->getId());
        if (!$this->_role->hasExclusiveStoreAccess($originalStoreIds)) {
            $this->_throwDelete();
        }
    }

    /**
     * Customer validate after load
     *
     * @param \Magento\Customer\Model\Customer $model
     * @return void
     */
    public function customerLoadAfter($model)
    {
        if (!$this->_role->hasWebsiteAccess($model->getWebsiteId(), true)) {
            $model->setIsReadonly(true);
            $model->setIsDeleteable(false);
        }
    }

    /**
     * Customer validate before save
     *
     * @param \Magento\Customer\Model\Customer $model
     * @return void
     */
    public function customerSaveBefore($model)
    {
        if ($model->getId() && !$this->_role->hasWebsiteAccess($model->getWebsiteId(), true)) {
            $this->_throwSave();
        } elseif (!$model->getId() && !$this->_role->getIsWebsiteLevel()) {
            $this->_throwSave();
        }
    }

    /**
     * Customer attribute validate before save
     *
     * @param \Magento\Customer\Model\Attribute $model
     * @return void
     */
    public function customerAttributeSaveBefore($model)
    {
        foreach (array_keys($model->getData()) as $key) {
            $isScopeKey = (strpos($key, 'scope_') === 0);
            if (!$isScopeKey && $key != $model->getIdFieldName()) {
                $model->unsetData($key);
            }
        }
        $modelWebsiteId = ($model->getWebsite() ? $model->getWebsite()->getId() : null);
        if (!$modelWebsiteId || !$this->_role->hasWebsiteAccess($modelWebsiteId, true)) {
            $this->_throwSave();
        }
    }

    /**
     * Customer attribute validate before delete
     *
     * @param \Magento\Customer\Model\Attribute $model
     * @return void
     */
    public function customerAttributeDeleteBefore($model)
    {
        $this->_throwDelete();
    }

    /**
     * Order validate after load
     *
     * @param \Magento\Sales\Model\Order $model
     * @return void
     */
    public function salesOrderLoadAfter($model)
    {
        if (!in_array($model->getStore()->getWebsiteId(), $this->_role->getWebsiteIds())) {
            $model->setActionFlag(\Magento\Sales\Model\Order::ACTION_FLAG_CANCEL, false)
                ->setActionFlag(\Magento\Sales\Model\Order::ACTION_FLAG_CREDITMEMO, false)
                ->setActionFlag(\Magento\Sales\Model\Order::ACTION_FLAG_EDIT, false)
                ->setActionFlag(\Magento\Sales\Model\Order::ACTION_FLAG_HOLD, false)
                ->setActionFlag(\Magento\Sales\Model\Order::ACTION_FLAG_INVOICE, false)
                ->setActionFlag(\Magento\Sales\Model\Order::ACTION_FLAG_REORDER, false)
                ->setActionFlag(\Magento\Sales\Model\Order::ACTION_FLAG_SHIP, false)
                ->setActionFlag(\Magento\Sales\Model\Order::ACTION_FLAG_UNHOLD, false)
                ->setActionFlag(\Magento\Sales\Model\Order::ACTION_FLAG_COMMENT, false);
        }
    }

    /**
     * Order validate before save
     *
     * @param \Magento\Sales\Model\Order $model
     * @return void
     */
    public function salesOrderBeforeSave($model)
    {
        if (!$this->_role->hasWebsiteAccess($model->getStore()->getWebsiteId(), true)) {
            throw new \Magento\Core\Exception(
                __('You can create an order in an active store only.')
            );
        }
    }

    /**
     * Catalog category initialize after loading
     *
     * @param \Magento\Catalog\Model\Category $model
     * @return void
     */
    public function catalogCategoryLoadAfter($model)
    {
        if (!$model->getId()) {
            return;
        }

        if (!$this->_role->hasExclusiveCategoryAccess($model->getPath())) {
            $model->unlockAttributes();
            $attributes = $model->getAttributes();
            $hasWebsites = count($this->_role->getWebsiteIds()) > 0;
            $hasStoreAccess = $this->_role->hasStoreAccess($model->getResource()->getStoreId());
            foreach ($attributes as $attribute) {
                /* @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
                if ($attribute->isScopeGlobal() ||
                    ($attribute->isScopeWebsite() && !$hasWebsites) ||
                    !$hasStoreAccess) {
                    $model->lockAttribute($attribute->getAttributeCode());
                }
            }
            $model->setProductsReadonly(true);
            $model->setPermissionsReadonly(true);
            $model->setOptimizationReadonly(true);
            $model->setIsDeleteable(false);
            if (!$this->_role->hasStoreAccess($model->getResource()->getStoreId())) {
                $model->setIsReadonly(true);
            }
        }
    }

    /**
     * Validate catalog category save
     *
     * @param \Magento\Catalog\Model\Category $model
     */
    public function catalogCategorySaveBefore($model)
    {
        if (!$model->getId()) {
            return;
        }

        // No saving to wrong stores
        if (!$this->_role->hasStoreAccess($model->getStoreIds())) {
            $this->_throwSave();
        }

        // No saving under disallowed root categories
        $categoryPath = $model->getPath();
        $allowed = false;
        foreach ($this->_role->getAllowedRootCategories() as $rootPath) {
            if ($categoryPath != $rootPath) {
                if (0 === strpos($categoryPath, "{$rootPath}/")) {
                    $allowed = true;
                }
            } else {
                if ($this->_role->hasExclusiveCategoryAccess($rootPath)) {
                    $allowed = true;
                }
            }

            if ($allowed) {
                break;
            }
        }

        if (!$allowed) {
            $this->_throwSave();
        }
    }

    /**
     * Validate catalog event save
     *
     * @param \Magento\CatalogEvent\Model\Event $model
     */
    public function catalogEventSaveBefore($model)
    {
        $category = $this->_categoryFactory->create()->load($model->getCategoryId());
        if (!$category->getId()) {
            $this->_throwSave();
        }

        // save event only for exclusive categories
        $rootFound = false;
        foreach ($this->_role->getAllowedRootCategories() as $rootPath) {
            if ($category->getPath() === $rootPath || 0 === strpos($category->getPath(), "{$rootPath}/")) {
                $rootFound = true;
                break;
            }
        }
        if (!$rootFound) {
            $this->_throwSave();
        }

        // in non-exclusive mode allow to change the image only
        if ($model->getId()) {
            if (!$this->_role->hasExclusiveCategoryAccess($category->getPath())) {
                foreach (array_keys($model->getData()) as $key) {
                    if ($model->dataHasChangedFor($key) && $key !== 'image') {
                         $model->setData($key, $model->getOrigData($key));
                    }
                }
            }
        }
    }

    /**
     * Validate catalog event delete
     *
     * @param \Magento\CatalogEvent\Model\Event $model
     */
    public function catalogEventDeleteBefore($model)
    {
        // delete only in exclusive mode
        $category = $this->_categoryFactory->create()->load($model->getCategoryId());
        if (!$category->getId()) {
            $this->_throwDelete();
        }
        if (!$this->_role->hasExclusiveCategoryAccess($category->getPath())) {
            $this->_throwDelete();
        }
    }

    /**
     * Validate catalog event load
     *
     * @param \Magento\CatalogEvent\Model\Event $model
     */
    public function catalogEventLoadAfter($model)
    {
        $category = $this->_categoryFactory->create()->load($model->getCategoryId());
        if (!$this->_role->hasExclusiveCategoryAccess($category->getPath())) {
            $model->setIsReadonly(true);
            $model->setIsDeleteable(false);
            $model->setImageReadonly(true);
            if ($this->_role->hasStoreAccess($model->getStoreId())) {
                $model->setImageReadonly(false);
            }
        }
    }

    /**
     * Make websites read-only
     *
     * @param \Magento\Core\Model\Website $model
     */
    public function coreWebsiteLoadAfter($model)
    {
        $model->isReadOnly(true);
    }

    /**
     * Disallow saving websites
     *
     * @param \Magento\Core\Model\Website $model
     */
    public function coreWebsiteSaveBefore($model)
    {
        $this->_throwSave();
    }

    /**
     * Disallow deleting websites
     *
     * @param \Magento\Core\Model\Website $model
     */
    public function coreWebsiteDeleteBefore($model)
    {
        $this->_throwDelete();
    }

    /**
     * Set store group or store read-only
     *
     * @param \Magento\Core\Model\Store|\Magento\Core\Model\Store\Group $model
     */
    public function coreStoreGroupLoadAfter($model)
    {
        if ($this->_role->hasWebsiteAccess($model->getWebsiteId(), true)) {
            return;
        }
        $model->isReadOnly(true);
    }

    /**
     * Disallow saving store group or store
     *
     * @param \Magento\Core\Model\Store|\Magento\Core\Model\Store\Group $model
     */
    public function coreStoreGroupSaveBefore($model)
    {
        if ($this->_role->hasWebsiteAccess($model->getWebsiteId(), true)) {
            return;
        }
        $this->_throwSave();
    }

    /**
     * Update role store group ids in helper and role
     *
     * @param \Magento\Event\Observer $observer
     */
    public function coreStoreGroupSaveAfter($observer)
    {
        if ($this->_role->getIsAll()) {
            return;
        }
        $model = $observer->getEvent()->getStoreGroup();
        if ($model->getId() && !$this->_role->hasStoreGroupAccess($model->getId())) {
            $this->_role->setStoreGroupIds(array_unique(array_merge(
                $this->_role->getStoreGroupIds(), array($model->getId())
            )));
        }
    }

    /**
     * Update role store ids in helper and role
     *
     * @param \Magento\Event\Observer $observer
     */
    public function coreStoreSaveAfter($observer)
    {
        if ($this->_role->getIsAll()) {
            return;
        }
        $model = $observer->getEvent()->getStoreGroup();
        if ($model->getId() && !$this->_role->hasStoreAccess($model->getId())) {
            $this->_role->setStoreIds(array_unique(array_merge(
                $this->_role->getStoreIds(), array($model->getId())
            )));
        }
    }

    /**
     * Disallow deleting store group or store
     *
     * @param \Magento\Core\Model\Store|\Magento\Core\Model\Store\Group $model
     */
    public function coreStoreGroupDeleteBefore($model)
    {
        if ($model->getId() && $this->_role->hasWebsiteAccess($model->getWebsiteId(), true)) {
            return;
        }
        $this->_throwDelete();
    }

    /**
     * Prevent loading disallowed urlrewrites
     *
     * @param \Magento\Core\Model\Url\Rewrite $model
     */
    public function coreUrlRewriteLoadAfter($model)
    {
        if (!$model->getId()) {
            return;
        }
        if (!$this->_role->hasStoreAccess($model->getStoreId())) {
            $this->_throwLoad();
        }
    }

    /**
     * Check whether order may be saved
     *
     * @param \Magento\Sales\Model\AbstractModel $model
     */
    public function salesOrderSaveBefore($model)
    {
        $this->_salesEntitySaveBefore($this->_storeManager->getStore($model->getStoreId())->getWebsiteId());
    }

    /**
     * Check whether order entity may be saved
     *
     * Invoice, shipment, creditmemo (address & item?)
     *
     * @param \Magento\Sales\Model\AbstractModel $model
     */
    public function salesOrderEntitySaveBefore($model)
    {
        $this->_salesEntitySaveBefore(
            $this->_storeManager->getStore($model->getOrder()->getStoreId())->getWebsiteId()
        );
    }

    /**
     * Check whether order transaction may be saved
     *
     * @param \Magento\Sales\Model\Order\Payment\Transaction $model
     */
    public function salesOrderTransactionSaveBefore($model)
    {
        $websiteId = $model->getOrderWebsiteId();
            if (!$this->_role->hasWebsiteAccess($websiteId, true)) {
                $this->_throwSave();
            }
    }

    /**
     * Check whether order transaction can be loaded
     *
     * @param \Magento\Sales\Model\Order\Payment\Transaction $model
     */
    public function salesOrderTransactionLoadAfter($model)
    {
        if (!$this->_role->hasWebsiteAccess($model->getOrderWebsiteId())) {
            $this->_throwLoad();
        }
    }

    /**
     * Disallow attribute save method when role scope is not 'all'
     *
     * @param \Magento\Sales\Model\AbstractModel $model
     */
    public function catalogEntityAttributeSaveBefore($model)
    {
        $this->_throwSave();
    }

    /**
     * Disallow attribute delete method when role scope is not 'all'
     *
     * @param \Magento\Sales\Model\AbstractModel $model
     */
    public function catalogEntityAttributeDeleteBefore($model)
    {
        $this->_throwDelete();
    }

    /**
     * Disallow attribute set save method when role scope is not 'all'
     *
     * @param \Magento\Sales\Model\AbstractModel $model
     */
    public function eavEntityAttributeSetSaveBefore($model)
    {
        $this->_throwSave();
    }

    /**
     * Disallow attribute set delete method when role scope is not 'all'
     *
     * @param \Magento\Sales\Model\AbstractModel $model
     */
    public function eavEntityAttributeSetDeleteBefore($model)
    {
        $this->_throwDelete();
    }

    /**
     * Disallow attribute option delete method when role scope is not 'all'
     *
     * @param \Magento\Sales\Model\AbstractModel $model
     */
    public function eavEntityAttributeOptionDeleteBefore($model)
    {
        $this->_throwDelete();
    }

    /**
     * Disallow attribute group delete method when role scope is not 'all'
     *
     * @param \Magento\Sales\Model\AbstractModel $model
     */
    public function eavEntityAttributeGroupDeleteBefore($model)
    {
        $this->_throwDelete();
    }

    /**
     * Disallow attribute group save method when role scope is not 'all'
     *
     * @param \Magento\Sales\Model\AbstractModel $model
     */
    public function eavEntityAttributeGroupSaveBefore($model)
    {
        $this->_throwSave();
    }

    /**
     * Disallow attribute option save method when role scope is not 'all'
     *
     * @param \Magento\Sales\Model\AbstractModel $model
     */
    public function eavEntityAttributeOptionSaveBefore($model)
    {
        $this->_throwSave();
    }

    /**
     * Generic sales entity before save logic
     *
     * @param int $websiteId
     */
    protected function _salesEntitySaveBefore($websiteId)
    {
        if ($this->_role->getIsStoreLevel()) {
            $this->_throwSave();
        }

        if (!$this->_role->hasWebsiteAccess($websiteId, true)) {
            $this->_throwSave();
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
            array_intersect($newIds, $this->_role->getStoreIds()),
            array_intersect($origIds, $this->_role->getDisallowedStoreIds())
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
            array_intersect($newIds, $this->_role->getWebsiteIds()),
            array_intersect($origIds, $this->_role->getDisallowedWebsiteIds())
        ));
    }

    /**
     * Prevent loosing disallowed websites from model
     *
     * @param array $websiteIds
     * @throws \Magento\Core\Exception
     * @return array
     */
    protected function _forceAssignToWebsite($websiteIds)
    {
        if (count(array_intersect($websiteIds, $this->_role->getWebsiteIds())) === 0 &&
            count($this->_role->getWebsiteIds())) {
            throw new \Magento\Core\Exception(__('Please assign this item to a store view.'));
        }
        return $websiteIds;
    }

    /**
     * Prevent losing disallowed store views from model
     *
     * @param array $storeIds
     * @throws \Magento\Core\Exception
     * @return array
     */
    protected function _forceAssignToStore($storeIds)
    {
        if (count(array_intersect($storeIds, $this->_role->getStoreIds())) === 0 &&
            count($this->_role->getStoreIds())) {
            throw new \Magento\Core\Exception(__('Please assign this item to a store view.'));
        }
        return $storeIds;
    }

    /**
     * @throws \Magento\Core\Exception
     */
    protected function _throwSave()
    {
        throw new \Magento\Core\Exception(
            __('You need more permissions to save this item.')
        );
    }

    /**
     * @throws \Magento\Core\Exception
     */
    protected function _throwDelete()
    {
        throw new \Magento\Core\Exception(
            __('You need more permissions to delete this item.')
        );
    }

    /**
     * @throws \Magento\AdminGws\Controller\Exception
     */
    private function _throwLoad()
    {
        throw new \Magento\AdminGws\Controller\Exception(__('You need more permissions to view this item.'));
    }

    /**
     * Validate widget instance availability after load
     *
     * @param \Magento\Widget\Model\Widget\Instance $model
     */
    public function widgetInstanceLoadAfter($model)
    {
        if (in_array(0, $model->getStoreIds())) {
            return;
        }
        if (!$this->_role->hasStoreAccess($model->getStoreIds())) {
            $this->_throwLoad();
        }
    }

    /**
     * Validate widget instance before save
     *
     * @param \Magento\Widget\Model\Widget\Instance $model
     */
    public function widgetInstanceSaveBefore($model)
    {
        $originalStoreIds = $model->getResource()->lookupStoreIds($model->getId());
        if ($model->getId() && !$this->_role->hasStoreAccess($originalStoreIds)) {
            $this->_throwSave();
        }
        if (!$this->_role->hasExclusiveStoreAccess($originalStoreIds)) {
            $this->_throwSave();
        }
        $model->setData('stores', $this->_forceAssignToStore(
            $this->_updateSavingStoreIds($model->getStoreIds(), $originalStoreIds)
        ));
    }

    /**
     * Validate widget instance before delete
     *
     * @param \Magento\Widget\Model\Widget\Instance $model
     */
    public function widgetInstanceDeleteBefore($model)
    {
        $originalStoreIds = $model->getResource()->lookupStoreIds($model->getId());
        if (!$this->_role->hasExclusiveStoreAccess($originalStoreIds)) {
            $this->_throwDelete();
        }
    }

    /**
     * Validate banner before save
     *
     * @param \Magento\Banner\Model\Banner $model
     */
    public function bannerSaveBefore($model)
    {
        if (!$this->_role->hasExclusiveStoreAccess((array)$model->getStoreIds())) {
            $this->_throwSave();
        }
    }

    /**
     * Validate banner before edit
     *
     * @param \Magento\Banner\Model\Banner $model
     */
    public function bannerLoadAfter($model)
    {
        if ($model->getId()) {
            $bannerStoreIds = (array)$model->getStoreIds();
            $model->setCanSaveAllStoreViewsContent(false);
            if (!$this->_role->hasExclusiveStoreAccess((array)$model->getStoreIds())) {
                //Set flag readonly for using in blocks to disable form elements
                $model->setIsReadonly(true);
            }
            if (in_array(0, $bannerStoreIds)) {
                return;
            }
            if (!$this->_role->hasStoreAccess($bannerStoreIds)) {
                $this->_throwLoad();
            }
        }
    }

    /**
     * Validate banner before delete
     *
     * @param \Magento\Banner\Model\Banner $model
     */
    public function bannerDeleteBefore($model)
    {
        if (!$this->_role->hasExclusiveStoreAccess((array)$model->getStoreIds())) {
            $this->_throwDelete();
        }
    }

    /**
     * Validate Gift Card Account before save
     *
     * @param \Magento\Banner\Model\Banner $model
     */
    public function giftCardAccountSaveBefore($model)
    {
        if (!$this->_role->hasWebsiteAccess($model->getWebsiteId(), true)) {
            $this->_throwSave();
        }
    }

    /**
     * Validate Gift Card Account before delete
     *
     * @param \Magento\Banner\Model\Banner $model
     */
    public function giftCardAccountDeleteBefore($model)
    {
        if (!$this->_role->hasWebsiteAccess($model->getWebsiteId(), true)) {
            $this->_throwDelete();
        }
    }

    /**
     * Validate Gift Card Account after load
     *
     * @param \Magento\Banner\Model\Banner $model
     */
    public function giftCardAccountLoadAfter($model)
    {
        if (!$this->_role->hasWebsiteAccess($model->getWebsiteId())) {
            $this->_throwLoad();
        }
    }

    /**
     * Validate Gift Registry Type before save
     *
     * @param \Magento\GiftRegistry\Model\Type $model
     * @return void
     */
    public function giftRegistryTypeSaveBefore($model)
    {

        // it's not allowed to create not form super user
        if (!$model->getId()) {
            $this->_throwSave();
        }

        $model->setData(array(
            'meta_xml' => $model->getOrigData('meta_xml'),
            'code' => $model->getOrigData('model')
        ));
    }

    /**
     * Validate Gift Registry Type before delete
     *
     * @param \Magento\GiftRegistry\Model\Type $model
     * @return void
     */
    public function giftRegistryTypeDeleteBefore($model)
    {
       $this->_throwDelete();
    }





    /**
     * Limit customer segment save
     *
     * @deprecated after 1.12.0.0 use $this->ruleSaveBefore() instead
     *
     * @param \Magento\CustomerSegment\Model\Segment $model
     * @return void
     */
    public function customerSegmentSaveBefore($model)
    {
        $this->ruleSaveBefore($model);
    }

    /**
     * Validate customer segment before delete
     *
     * @deprecated after 1.12.0.0 use $this->ruleDeleteBefore() instead
     *
     * @param \Magento\CustomerSegment\Model\Segment $model
     * @return void
     */
    public function customerSegmentDeleteBefore($model)
    {
        $this->ruleDeleteBefore($model);
    }

    /**
     * Limit customer segment model on after load
     *
     * @deprecated after 1.12.0.0 use $this->ruleLoadAfter() instead
     *
     * @param \Magento\CustomerSegment\Model\Segment $model
     * @return void
     */
    public function customerSegmentLoadAfter($model)
    {
        $this->ruleLoadAfter($model);
    }
}
